<?php

namespace App\Models;

use CodeIgniter\Model;

class DetallePedidoModel extends Model
{
    protected $table            = 'detalle_pedidos';
    protected $primaryKey       = 'id_detalle_pedido';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pedido',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'observaciones'
    ];

    // Validation
    protected $validationRules      = [
        'id_pedido'       => 'required|is_natural_no_zero',
        'id_producto'     => 'required|is_natural_no_zero',
        'cantidad'        => 'required|numeric|greater_than[0]',
        'precio_unitario' => 'required|numeric|greater_than_equal_to[0]',
        'subtotal'        => 'required|numeric|greater_than_equal_to[0]',
        'observaciones'   => 'permit_empty|max_length[255]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Obtiene todos los detalles de un pedido con información del producto.
     * 
     * @param int $idPedido ID del pedido
     * @return array Lista de detalles con nombre del producto
     */
    public function getDetallesPorPedido(int $idPedido): array
    {
        return $this
            ->select('detalle_pedidos.*, productos.nombre')
            ->join('productos', 'productos.id_producto = detalle_pedidos.id_producto', 'left')
            ->where('detalle_pedidos.id_pedido', $idPedido)
            ->findAll();
    }

    /**
     * Calcula el total de un pedido sumando los subtotales de sus detalles.
     * 
     * @param int $idPedido ID del pedido
     * @return float Total del pedido
     */
    public function getTotalPedido(int $idPedido): float
    {
        $items = $this->where('id_pedido', $idPedido)->findAll();
        return (float) array_sum(array_column($items, 'subtotal'));
    }
}
