<?php

namespace App\Services;

use App\Models\PagoModel;
use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;

/**
 * ComprobanteService
 *
 * Recopila todos los datos necesarios para mostrar el comprobante de un pago.
 * No recibe objetos HTTP ni devuelve views ni redirects.
 * Retorna arrays de resultado que el controlador convierte en respuesta HTTP.
 */
class ComprobanteService
{
    protected PagoModel $pagoModel;
    protected PedidoModel $pedidoModel;
    protected DetallePedidoModel $detallePedidoModel;

    public function __construct(
        PagoModel $pagoModel,
        PedidoModel $pedidoModel,
        DetallePedidoModel $detallePedidoModel
    ) {
        $this->pagoModel          = $pagoModel;
        $this->pedidoModel        = $pedidoModel;
        $this->detallePedidoModel = $detallePedidoModel;
    }

    /**
     * Recopila todos los datos del comprobante para un pago dado.
     *
     * @param int $idPago ID del pago a mostrar.
     * @return array {
     *   ok:     bool        — false si el pago no existe.
     *   pago:   array|null  — datos del pago con nombre del método.
     *   pedido: array|null  — datos del pedido con mesa y usuario.
     *   items:  array       — líneas de detalle con nombre de producto.
     *   total:  float       — suma de subtotales de los ítems.
     * }
     */
    public function obtenerDatos(int $idPago): array
    {
        $pago = $this->pagoModel->getPagoConMetodo($idPago);
        if (!$pago) {
            return ['ok' => false, 'pago' => null, 'pedido' => null, 'items' => [], 'total' => 0.0];
        }

        $items  = $this->detallePedidoModel->getDetallesPorPedido($pago['id_pedido']);
        $pedido = $this->pedidoModel->getPedidoConDetalles($pago['id_pedido']);

        return [
            'ok'    => true,
            'pago'  => $pago,
            'pedido'=> $pedido,
            'items' => $items,
            'total' => (float) array_sum(array_column($items, 'subtotal')),
        ];
    }
}
