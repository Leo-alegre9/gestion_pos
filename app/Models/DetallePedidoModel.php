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
}
