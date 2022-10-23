<?php

include './lib/push.php';

class Inicio_Ctrl
{
    public function Obtener_Totales($f3)
    {
        $M_Usuarios = new M_Usuarios();
        $M_Clientes = new M_Clientes();
        $M_Pedidos = new M_Pedidos();
        $M_Productos = new M_Productos();
        
        echo json_encode([
            'mensaje' => '',
            'info' => [
                'pedidos' => $M_Pedidos->count(),
                'productos' => $M_Productos->count(),
                'clientes' => $M_Clientes->count(),
                'usuarios' => $M_Usuarios->count(),
            ]
        ]);
    }

    public function Test_Notificacion($f3)
    {
        $r = Push::android(['mtitle' => "Notificacion de prueba", 'mdesc' => "Esta es una notificacion " . date("Y-m-d H:i:s")], "cF9sTXuOgK8:APA91bHXmXomzt8nCarRiXyYjThLKvgMw7vKPFuT6URnB9IL4yO5SfeYLdfLXMouiW_cUo3eHpyTRWX8M7_utnix7IQwKUIkSY8YGOn8eWPA0N8ZY_SRvRW9U-VLX2lhzlR4nmMLfLEw");
        var_dump($r);
    }
}
