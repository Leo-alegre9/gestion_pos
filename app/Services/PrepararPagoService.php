<?php

namespace App\Services;

use App\Models\PedidoModel;
use App\Models\PagoModel;
use App\Models\MetodoPagoModel;
use App\Models\DetallePedidoModel;

/**
 * PrepararPagoService
 *
 * Lógica de negocio de la fase de preparación del formulario de pago.
 * Valida que el pedido esté apto, detecta pagos ya registrados y
 * recopila los datos que el formulario necesita para renderizarse.
 *
 * No recibe objetos HTTP ni devuelve views ni redirects.
 * Retorna arrays de resultado que el controlador convierte en respuesta HTTP.
 */
class PrepararPagoService
{
    protected PedidoModel $pedidoModel;
    protected PagoModel $pagoModel;
    protected MetodoPagoModel $metodoPagoModel;
    protected DetallePedidoModel $detallePedidoModel;

    public function __construct(
        PedidoModel $pedidoModel,
        PagoModel $pagoModel,
        MetodoPagoModel $metodoPagoModel,
        DetallePedidoModel $detallePedidoModel
    ) {
        $this->pedidoModel        = $pedidoModel;
        $this->pagoModel          = $pagoModel;
        $this->metodoPagoModel    = $metodoPagoModel;
        $this->detallePedidoModel = $detallePedidoModel;
    }

    // =========================================================================
    // PUNTO DE ENTRADA
    // =========================================================================

    /**
     * Coordina la preparación completa del formulario de pago.
     *
     * Flujo:
     *   1. Verificar que el pedido exista y esté cerrado.
     *   2. Detectar si ya existe un pago registrado (evitar duplicados).
     *   3. Recopilar pedido, ítems, total y métodos de pago disponibles.
     *
     * @param int $idPedido ID del pedido que se desea pagar.
     * @return array {
     *   ok:              bool         — true si el pedido está listo para pagar.
     *   error:           string|null  — mensaje de error cuando ok=false.
     *   pagoExistenteId: int|null     — non-null indica pago previo registrado.
     *   data:            array|null   — {pedido, items, total, metodos} cuando ok=true.
     * }
     */
    public function preparar(int $idPedido): array
    {
        // 1. Verificar que el pedido existe y está cerrado
        $pedido = $this->pedidoModel->getPedidoConDetalles($idPedido);

        if (!$pedido) {
            return $this->falla('El pedido no existe.');
        }

        if (!$pedido['fecha_cierre']) {
            return $this->falla('El pedido debe cerrarse antes de registrar el pago.');
        }

        // 2. Detectar pago previo
        $pagoExistente = $this->pagoModel->getPagoPorPedido($idPedido);
        if ($pagoExistente) {
            return [
                'ok'             => false,
                'error'          => null,
                'pagoExistenteId'=> (int) $pagoExistente['id_pago'],
                'data'           => null,
            ];
        }

        // 3. Recopilar datos para el formulario
        $items = $this->detallePedidoModel->getDetallesPorPedido($idPedido);

        return [
            'ok'             => true,
            'error'          => null,
            'pagoExistenteId'=> null,
            'data'           => [
                'pedido'  => $pedido,
                'items'   => $items,
                'total'   => $this->calcularTotal($items),
                'metodos' => $this->metodoPagoModel->getActivos(),
            ],
        ];
    }

    // =========================================================================
    // UTILIDADES
    // =========================================================================

    /**
     * Suma los subtotales de cada ítem para obtener el total del pedido.
     *
     * @param array $items Líneas de detalle con el campo `subtotal`.
     * @return float Total calculado.
     */
    public function calcularTotal(array $items): float
    {
        return (float) array_sum(array_column($items, 'subtotal'));
    }

    private function falla(string $error): array
    {
        return ['ok' => false, 'error' => $error, 'pagoExistenteId' => null, 'data' => null];
    }
}
