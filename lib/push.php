<?php

class Push
{

    // Android
    public static $API_ACCESS_KEY = 'AAAAjZrlIoE:APA91bGEZPVH3k-iJgX1IUSln22Zl7BVjn_esrFOMwZHGUuxpoSbEPgJmfm3fiKSMwlQ20S1jhfVuuszBjvVwTYFhWksFgeuE4B5yXH7IERv_Mi_RyMij4KH4aHLsrTo7Lj-JpdIDX3S';

    // iOS
    public static $certificado_ios_clave = 'XXXXXX';
    public static $certificado_ios = 'certificado.pem';

    public static function android($data, $token_dispositivo)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $message = array(
            'title' => $data['mtitle'],
            'message' => $data['mdesc'],
            'msgcnt' => 1,
            'vibrate' => 1
        );

        $message = array_merge($message, $data);

        $headers = array(
            'Authorization: key=' . self::$API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $fields = array(
            'registration_ids' => array($token_dispositivo),
            'data' => $message,
        );
        return self::useCurl($url, $headers, json_encode($fields));
    }
    
    public static function iOS($data, $deviceToken, $badge = 1)
    {
        $body = array();
        $body['aps'] = array(
            'title' => $data['mtitle'],
            'alert' => $data['mdesc'],
            'sound' => 'default',
            'badge' => $badge
        );
        $payload = json_encode($body);
        $inner = chr(1) . pack('n', 32) . pack('H*', $deviceToken)
            . chr(2) . pack('n', strlen($payload)) . $payload
            . chr(3) . pack('n', 4) . pack('N', time())
            . chr(4) . pack('n', 4) . pack('N', time() + 86400)
            . chr(5) . pack('n', 1) . chr(10);

        $msg = chr(2) . pack('N', strlen($inner)) . $inner;
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', realpath("./cert") . DIRECTORY_SEPARATOR . self::$certificado_ios);
        stream_context_set_option($ctx, 'ssl', 'passphrase', self::$certificado_ios_clave);
        $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        /** 
         * URL de Pruebas
         * ssl://gateway.sandbox.push.apple.com:2195
         **/
        if (!$fp) {
            exit("Failed to connect: $err $errstr" . PHP_EOL);
        }
        stream_set_blocking($fp, 0);
        $result = fwrite($fp, $msg, strlen($msg));
        fclose($fp);
    }

    public static function useCurl($url, $headers, $fields = null)
    {
        $ch = curl_init();
        if ($url) {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            if ($fields) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            }
            $result = curl_exec($ch);
            if ($result === false) {
                die('Curl failed: ' . curl_error($ch));
            }
            curl_close($ch);
            return $result;
        }
    }
}
