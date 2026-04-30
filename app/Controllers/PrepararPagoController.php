<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\PagoModel;
use App\Models\MetodoPagoModel;
use App\Models\DetallePedidoModel;

/**
 * PrepararPagoController
 *
 * Concentra la lógica de preparación previa al pago: validaciones de estado
 * del pedido, detección de pagos ya registrados y recopilación de todos los
 * datos que el formulario de pago necesita para renderizarse.
 *
 * Es invocado por PagoController::mostrarFormularioDePago() y no expone rutas.
 */
class PrepararPagoController extends BaseController
{
    protected PedidoModel $pedidoModel;
    protected PagoModel $pagoModel;
    protected MetodoPagoModel $metodoPagoModel;
    protected DetallePedidoModel $detallePedidoModel;

    public function __construct()
    {
        $this->pedidoModel        = new PedidoModel();
        $this->pagoModel          = new PagoModel();
        $this->metodoPagoModel    = new MetodoPagoModel();
        $this->detallePedidoModel = new DetallePedidoModel();
    }

    // =========================================================================
    // PUNTO DE ENTRADA
    // =========================================================================

    /**
     * Coordina la preparación completa del formulario de pago.
     *
     * Flujo:
     *   1. Validar que el pedido exista y esté cerrado.
     *   2. Verificar que no haya un pago ya registrado para ese pedido.
     *   3. Recopilar todos los datos necesarios para el formulario.
     *
     * @param int $idPedido ID del pedido que se desea pagar.
     * @return array Estructura de resultado con las keys:
     *   - 'success'  (bool)        true si la preparación fue exitosa.
     *   - 'data'     (array|null)  Datos para la vista ('pedido','items','total','metodos').
     *   - 'error'    (string|null) Mensaje de error si success es false.
     *   - 'redirect' (string|null) Ruta de redirección si success es false.
     */
    public function preparar(int $idPedido): array
    {
        // 1. Validar pedido
        if (!$this->esPedidoAptoPagoParaPago($idPedido)) {
            return $this->respuestaError(session()->getFlashdata('error'),'/pedidos');
        }

        // 2. Detectar pago previo
        $pagoExistente = $this->buscarPagoYaRegistrado($idPedido);
        if ($pagoExistente) {
            return $this->respuestaError(null,'/pagos/comprobante/' . $pagoExistente['id_pago']);
        }

        // 3. Recopilar datos para el formulario
        $datos = $this->recopilarDatosParaFormulario($idPedido);

        return [
            'success'  => true,
            'data'     => $datos,
            'error'    => null,
            'redirect' => null,
        ];
    }

    // =========================================================================
    // PASO 1 — Validación del pedido
    // =========================================================================

    /**
     * Verifica que el pedido exista y que tenga fecha de cierre registrada.
     * Un pedido debe estar cerrado antes de poder registrar su pago.
     * Almacena el mensaje de error en flashdata si la validación falla.
     *
     * @param int $idPedido ID del pedido a validar.
     * @return bool true si el pedido existe y está cerrado.
     */
    public function esPedidoAptoPagoParaPago(int $idPedido): bool
    {
        $pedido = $this->pedidoModel->getPedidoConDetalles($idPedido);

        if (!$pedido) {
            session()->setFlashdata('error', 'El pedido no existe.');
            return false;
        }

        if (!$pedido['fecha_cierre']) {
            session()->setFlashdata('error', 'El pedido debe cerrarse antes de registrar el pago.');
            return false;
        }

        return true;
    }

    // =========================================================================
    // PASO 2 — Detección de pago previo
    // =========================================================================

    /**
     * Consulta si ya existe un pago registrado para el pedido indicado.
     * Evita que el formulario se muestre cuando el pago ya fue procesado.
     *
     * @param int $idPedido ID del pedido a consultar.
     * @return array|null Datos del pago existente, o null si no hay ninguno.
     */
    public function buscarPagoYaRegistrado(int $idPedido): ?array
    {
        return $this->pagoModel->getPagoPorPedido($idPedido);
    }

    // =========================================================================
    // PASO 3 — Recopilación de datos para el formulario
    // =========================================================================

    /**
     * Obtiene todos los datos que el formulario de pago necesita:
     * el pedido completo, sus ítems con subtotales, el total calculado
     * y la lista de métodos de pago activos disponibles para seleccionar.
     *
     * @param int $idPedido ID del pedido para el que se está preparando el pago.
     * @return array Con keys: 'pedido', 'items', 'total', 'metodos'.
     */
    public function recopilarDatosParaFormulario(int $idPedido): array
    {
        $items = $this->detallePedidoModel->getDetallesPorPedido($idPedido);

        return [
            'pedido'  => $this->pedidoModel->getPedidoConDetalles($idPedido),
            'items'   => $items,
            'total'   => $this->calcularTotalDesdeItems($items),
            'metodos' => $this->metodoPagoModel->getActivos(),
        ];
    }

    /**
     * Suma los subtotales de cada ítem para obtener el total del pedido.
     *
     * @param array $items Lista de ítems con el campo `subtotal`.
     * @return float Total calculado.
     */
    private function calcularTotalDesdeItems(array $items): float
    {
        return (float) array_sum(array_column($items, 'subtotal'));
    }

    // =========================================================================
    // UTILIDADES
    // =========================================================================

    /**
     * Construye la estructura de respuesta de error estándar para preparar().
     *
     * @param string|null $error    Mensaje de error a mostrar al usuario.
     * @param string      $redirect Ruta a la que redirigir.
     * @return array
     */
    private function respuestaError(?string $error, string $redirect): array
    {
        return [
            'success'  => false,
            'data'     => null,
            'error'    => $error,
            'redirect' => $redirect,
        ];
    }
}
