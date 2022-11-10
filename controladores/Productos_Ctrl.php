<?php

class Productos_Ctrl
{
    public $M_Producto = null;

    public function __construct()
    {
        $this->M_Producto = new M_Productos();
    }

    public function crear($f3)
    {
        $_producto = new M_Productos();
        $_producto->load(['codigo = ?', $f3->get('POST.codigo')]);
        $id = 0;
        $msg = "";
        if ($_producto->loaded() > 0) {
            $msg = "El código que intenta usar está usado por otro producto.";
        } else {
            $this->M_Producto->set('codigo', $f3->get('POST.codigo'));
            $this->M_Producto->set('nombre', $f3->get('POST.nombre'));
            $this->M_Producto->set('stock', $f3->get('POST.stock'));
            $this->M_Producto->set('precio', $f3->get('POST.precio'));
            $this->M_Producto->set('activo', $f3->get('POST.activo'));
            $this->M_Producto->set('descripcion', $f3->get('POST.descripcion'));
            $this->M_Producto->set('categoria', $f3->get('POST.id_categoria'));
            $this->M_Producto->save(); 
            $id = $this->M_Producto->get('id');
            if($id > 0) {
                $this->M_Producto->set('imagen', $this->Guardar_Imagen($f3->get('POST.imagen')));
                $this->M_Producto->update(); 
            }
            
            $msg = "Producto creado.";
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
        $producto_id = $f3->get('PARAMS.producto_id');
        $this->M_Producto->load(['id = ?', $producto_id]);
        $msg = "";
        $item = array();
        if ($this->M_Producto->loaded() > 0) {
            $msg = "Producto encontrado.";
            $item = $this->M_Producto->cast();
            $item['precio'] = round($item['precio']);
        } else {
            $msg = "El producto no existe.";
        }
        echo json_encode([
            'mensaje' => $msg,
            'info' => [
                'item' => $item
            ]
        ]);
    }

    public function consultar_categoria($f3)
    {
        $categoria_id = $f3->get('PARAMS.categoria_id');
        $result =  $this->M_Producto->find(['categoria = ?', $categoria_id]);
        $msg = "";
        $item = array();
        $items = array();
       // echo $f3->get('DB')->log();
        foreach ($result as $producto) {
            $msg = "Productos cargados.";
            $item = $producto->cast();
            $item['imagen'] = !empty($item['imagen']) ? 'http://192.168.100.94/pedidosApp-back/' . $item['imagen'] : 'http://via.placeholder.com/300x300';
            $items[] = $item;
        } 
        echo json_encode([
            'mensaje' => count($items) > 0 ? '' : 'Aún no hay registros para mostrar.',
            'info' => [
                'items' => $items,
                'total' => count($items)
            ]
        ]);
    }

    public function listado($f3)
    {
        $result = $this->M_Producto->find(['nombre LIKE ?', '%' . $f3->get('POST.texto') . '%']);
        $items = array();
        foreach ($result as $producto) {
            $item = $producto->cast();
            $item['imagen'] = !empty($item['imagen']) ? 'http://192.168.100.94/pedidosApp-back/' . $item['imagen'] : 'http://via.placeholder.com/300x300';
            $items[] = $item;
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
        $producto_id = $f3->get('POST.producto_id');
        $this->M_Producto->load(['id = ?', $producto_id]);
        $msg = "";
        if ($this->M_Producto->loaded() > 0) {
            $msg = "Producto eliminado.";
            $this->M_Producto->erase();
        } else {
            $msg = "El producto no existe.";
        }
        echo json_encode([
            'mensaje' => $msg,
            'info' => []
        ]);
    }

    public function actualizar($f3)
    {
        $producto_id = $f3->get('PARAMS.producto_id');
        $this->M_Producto->load(['id = ?', $producto_id]);
        $msg = "";
        $info = array();
        if ($this->M_Producto->loaded() > 0) {
            $_producto = new M_Productos();
            $_producto->load(['codigo = ? AND id <> ?', $f3->get('POST.codigo'), $producto_id]);
            if ($_producto->loaded() > 0) {
                $msg = "El registro no se pudo modificar debido a que el código se encuentra uso por otro producto.";
                $info['id'] = 0;
            } else {
                $this->M_Producto->set('codigo', $f3->get('POST.codigo'));
                $this->M_Producto->set('nombre', $f3->get('POST.nombre'));
                $this->M_Producto->set('stock', $f3->get('POST.stock'));
                $this->M_Producto->set('precio', $f3->get('POST.precio'));
                $this->M_Producto->set('activo', $f3->get('POST.activo'));
                $this->M_Producto->set('imagen', $this->Guardar_Imagen($f3->get('POST.imagen')));
                $this->M_Producto->set('descripcion', $f3->get('POST.descripcion'));
                $this->M_Producto->set('categoria', $f3->get('POST.id_categoria'));
                $this->M_Producto->save();

                $msg = "Producto actualizado.";
                $info['id'] = $this->M_Producto->get('id');
            }
        } else {
            $msg = "El Producto no existe.";
            $info['id'] = 0;
        }
        echo json_encode([
            'mensaje' => $msg,
            'info' => $info
        ]);
    }

    public function Guardar_Imagen($contenido) 
    {
        $nombre_imagen = '';
        if(!empty($contenido)) {
            $contenido = explode('base64,', $contenido);
            $imagen = $contenido[1];
            $nombre_imagen = 'imagenes/' . time() . '.jpg';
            file_put_contents($nombre_imagen, base64_decode($imagen));
        }
        return $nombre_imagen;
    }
}
