<?php

class Categoria_Ctrl
{
    public $M_Categoria = null;

    public function __construct()
    {
        $this->M_Categoria = new M_Categoria();
    }

    public function listado($f3)
    {
        $result = $this->M_Categoria->find();
        $items = array();
        foreach ($result as $categoria) {
            $items[] = $categoria->cast();
        }
        echo json_encode([
            'mensaje' => count($items) > 0 ? '' : 'Aún no hay registros para mostrar.',
            'info' => [
                'items' => $items,
                'total' => count($items)
            ]
        ]);
    }

}
