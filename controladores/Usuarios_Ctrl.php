<?php

class Usuarios_Ctrl
{
    public $M_Usuario = null;

    public function __construct()
    {
        $this->M_Usuario = new M_Usuarios();
    }

    public function crear($f3)
    {
        $_usuario = new M_Usuarios();
        $_usuario->load(['usuario = ? OR correo = ?', $f3->get('POST.usuario'), $f3->get('POST.correo')]);
        $msg = "";
        $id = 0;
        if ($_usuario->loaded() > 0) {
            $msg = "El nombre de usuario o correo que intenta usar se encuentran registrados.";
        } else {
            $this->M_Usuario->set('usuario', $f3->get('POST.usuario'));
            $this->M_Usuario->set('clave', md5($f3->get('POST.clave')));
            $this->M_Usuario->set('nombre', $f3->get('POST.nombre'));
            $this->M_Usuario->set('telefono', $f3->get('POST.telefono'));
            $this->M_Usuario->set('correo', $f3->get('POST.correo'));
            $this->M_Usuario->set('activo', $f3->get('POST.activo'));
            $this->M_Usuario->save();
            $id = $this->M_Usuario->get('id');
            $msg = "Usuario creado";
        }
        echo json_encode([
            'mensaje' => $msg,
            'info' => [
                'id' => $id
            ]
        ]);
    }

    public function consultar($f3)
    {
        $usuario_id = $f3->get('PARAMS.usuario_id');
        $this->M_Usuario->load(['id = ?', $usuario_id]);
        $msg = "";
        $item = array();
        if ($this->M_Usuario->loaded() > 0) {
            $msg = "Usuario encontrado.";
            $item = $this->M_Usuario->cast();
        } else {
            $msg = "El Usuario no existe.";
        }
        echo json_encode([
            'mensaje' => $msg,
            'info' => [
                'item' => $item
            ]
        ]);
    }

    public function listado($f3)
    {
        $result = $this->M_Usuario->find(['nombre LIKE ?', '%' . $f3->get('POST.texto') . '%']);
        $items = array();
        foreach ($result as $producto) {
            $items[] = $producto->cast();
        }
        echo json_encode([
            'mensaje' => count($items) > 0 ? '' : 'Aún no hay registros para mostrar.',
            'info' => [
                'items' => $items,
                'total' => count($items)
            ]
        ]);
    }

    public function eliminar($f3)
    {
        $usuario_id = $f3->get('POST.usuario_id');
        $this->M_Usuario->load(['id = ?', $usuario_id]);
        $msg = "";
        if ($this->M_Usuario->loaded() > 0) {
            $msg = "Usuario eliminado.";
            $this->M_Usuario->erase();
        } else {
            $msg = "El Usuario no existe.";
        }
        echo json_encode([
            'mensaje' => $msg,
            'info' => []
        ]);
    }

    public function actualizar($f3)
    {
        $usuario_id = $f3->get('PARAMS.usuario_id');
        $this->M_Usuario->load(['id = ?', $usuario_id]);
        $msg = "";
        $info = array();
        if ($this->M_Usuario->loaded() > 0) {
            $_usuario = new M_Usuarios();
            $_usuario->load(['(usuario = ? OR correo = ?) AND id <> ?', $f3->get('POST.usuario'), $f3->get('POST.correo'), $usuario_id]);
            if ($_usuario->loaded() > 0) {
                $msg = "El registro no se pudo modificar debido a que el nombre usuario o correo se encuentra uso por otro usuario.";
                $info['id'] = 0;
            } else {
                $this->M_Usuario->set('usuario', $f3->get('POST.usuario'));
                if(md5($f3->get('POST.clave')) != $this->M_Usuario->get('clave') && $f3->get('POST.clave') != '') {
                    $this->M_Usuario->set('clave', md5($f3->get('POST.clave')));
                }
                $this->M_Usuario->set('nombre', $f3->get('POST.nombre'));
                $this->M_Usuario->set('telefono', $f3->get('POST.telefono'));
                $this->M_Usuario->set('correo', $f3->get('POST.correo'));
                $this->M_Usuario->set('activo', $f3->get('POST.activo'));
                $this->M_Usuario->save();
                $msg = "Usuario actualizado.";
                $info['id'] = $this->M_Usuario->get('id');
            }
        } else {
            $msg = "El usuario no existe.";
            $info['id'] = 0;
        }
        echo json_encode([
            'mensaje' => $msg,
            'info' => $info
        ]);
    }
}
