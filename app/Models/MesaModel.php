<?php

namespace App\Models;

use CodeIgniter\Model;

class MesaModel extends Model
{
    protected $table            = 'mesas';
    protected $primaryKey       = 'id_mesa';
    protected $returnType       = 'array';
    protected $allowedFields    = ['numero', 'capacidad', 'estado'];

    protected $useTimestamps    = false;

    // Reglas de validación para operaciones CRUD en la tabla de mesas
    protected $validationRules = [
        'numero'    => 'required|integer|is_unique[mesas.numero,id_mesa,{id_mesa}]',
        'capacidad' => 'permit_empty|integer|greater_than_equal_to[1]',
        'estado'    => 'required|in_list[libre,ocupada,reservada,inactiva]',
    ];

    // Mensajes de validación personalizados para mejorar UX
    protected $validationMessages = [
        'numero' => [
            'required' => 'El número de mesa es requerido.',
            'integer' => 'El número de mesa debe ser un número entero.',
            'is_unique' => 'Ya existe una mesa con este número.',
        ],
        'capacidad' => [
            'integer' => 'La capacidad debe ser un número entero.',
            'greater_than_equal_to' => 'La capacidad debe ser al menos 1.',
        ],
        'estado' => [
            'required' => 'El estado es requerido.',
            'in_list' => 'El estado debe ser: libre, ocupada, reservada o inactiva.',
        ],
    ];

    /**
     * Obtiene todas las mesas incluyendo información sobre si tienen un pedido activo.
     * Realiza un LEFT JOIN con la tabla de pedidos para verificar si hay alguno sin cerrar.
     * 
     * @return array Lista de mesas con los datos del pedido si corresponde.
     */
    public function getMesasConPedidoActivo(): array
    {
        return $this->select('
                mesas.id_mesa,
                mesas.numero,
                mesas.capacidad,
                mesas.estado,
                pedidos.id_pedido,
                pedidos.fecha_apertura
            ')
            ->join('pedidos', 'pedidos.id_mesa = mesas.id_mesa AND pedidos.fecha_cierre IS NULL', 'left')
            ->orderBy('mesas.numero', 'ASC')
            ->findAll();
    }

    /**
     * Cuenta la cantidad de mesas agrupadas por su estado actual.
     * Utilizado en el Dashboard y la vista de Mesas para métricas rápidas.
     * 
     * @return array Un arreglo asociativo con los totales por cada estado.
     */
    public function contarPorEstado(): array
    {
        $rows = $this->select('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->findAll();

        $resultado = [
            'libre' => 0,
            'ocupada' => 0,
            'reservada' => 0,
            'inactiva' => 0,
        ];

        foreach ($rows as $row) {
            $resultado[$row['estado']] = (int) $row['total'];
        }

        return $resultado;
    }
}