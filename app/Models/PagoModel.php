<?php

namespace App\Models;

use CodeIgniter\Model;

class PagoModel extends Model
{
    protected $table      = 'pagos';
    protected $primaryKey = 'id_pago';
    protected $returnType = 'array';
    protected $allowedFields = ['id_pedido', 'id_metodo_pago', 'monto', 'fecha_pago'];
    protected $useTimestamps = false;

    protected $validationRules = [
        'id_pedido'      => 'required|integer|greater_than[0]',
        'id_metodo_pago' => 'required|integer|greater_than[0]',
        'monto'          => 'required|decimal|greater_than[0]',
    ];

    protected $validationMessages = [
        'id_pedido'      => ['required' => 'El pedido es requerido.'],
        'id_metodo_pago' => ['required' => 'Seleccioná un método de pago.'],
        'monto'          => [
            'required'     => 'El monto es requerido.',
            'greater_than' => 'El monto debe ser mayor a cero.',
        ],
    ];

    public function getPagoPorPedido(int $idPedido): ?array
    {
        return $this->select('pagos.*, metodos_pago.nombre as metodo_nombre')
            ->join('metodos_pago', 'metodos_pago.id_metodo_pago = pagos.id_metodo_pago')
            ->where('pagos.id_pedido', $idPedido)
            ->first();
    }

    public function getPagoConMetodo(int $idPago): ?array
    {
        return $this->select('pagos.*, metodos_pago.nombre as metodo_nombre')
            ->join('metodos_pago', 'metodos_pago.id_metodo_pago = pagos.id_metodo_pago')
            ->where('pagos.id_pago', $idPago)
            ->first();
    }

    public function getTotalPagadoPorFecha(string $fecha): float
    {
        $result = $this->selectSum('monto')
            ->where('DATE(fecha_pago)', $fecha)
            ->first();
        return (float)($result['monto'] ?? 0);
    }
}
