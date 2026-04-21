<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoModel extends Model
{
    protected $table         = 'pedidos';
    protected $primaryKey    = 'id_pedido';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'id_mesa',
        'id_usuario',
        'id_estado_pedido',
        'tipo_pedido',
        'fecha_apertura',
        'fecha_cierre',
        'observaciones'
    ];

    // Deshabilitar timestamps integrados, CodeIgniter intentaría actualizarlos sino
    protected $useTimestamps = false;

    /**
     * Devuelve el pedido que se encuentra abierto actualmente para una mesa específica.
     * Un pedido abierto no tiene 'fecha_cierre'.
     * 
     * @param int $idMesa ID de la mesa a buscar.
     * @return array|null El primer elemento de matriz (pedido) sin fecha_cierre o nulo si no hay ninguno.
     */
    public function getPedidoActivoPorMesa(int $idMesa): ?array
    {
        return $this->where('id_mesa', $idMesa)
            ->where('fecha_cierre', null)
            ->first();
    }
}