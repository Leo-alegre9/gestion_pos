<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\PagoModel;
use App\Models\DetallePedidoModel;

/**
 * RegistrarPagoController
 *
 * Contiene toda la lógica de negocio para registrar el pago de un pedido.
 * No maneja rutas directamente: es invocado por PagoController::procesarRegistroDePago().
 *
 * Responsabilidades:
 *   - Validar que el pedido sea apto para recibir un pago.
 *   - Construir un objeto pedido enriquecido con sus líneas de detalle.
 *   - Calcular el total desde los subtotales de cada línea (server-side).
 *   - Persistir el registro de pago en la base de datos.
 *   - Actualizar el estado del pedido a 'pagado'.
 */
class RegistrarPagoController extends BaseController
{
    protected PedidoModel $pedidoModel;
    protected PagoModel $pagoModel;
    protected DetallePedidoModel $detallePedidoModel;

    public function __construct()
    {
        $this->pedidoModel        = new PedidoModel();
        $this->pagoModel          = new PagoModel();
        $this->detallePedidoModel = new DetallePedidoModel();
    }

    // =========================================================================
    // PUNTO DE ENTRADA
    // =========================================================================

    /**
     * Orquesta el registro completo del pago de un pedido.
     *
     * Flujo de trabajo:
     *   1. Validar que el pedido exista y esté en estado cerrado.
     *   2. Detectar si ya existe un pago registrado (evitar duplicados).
     *   3. Construir el objeto pedido enriquecido con sus líneas de detalle.
     *   4. Calcular el total sumando los subtotales de cada línea de detalle.
     *   5. Obtener el método de pago seleccionado en el formulario.
     *   6. Persistir el pago en la base de datos.
     *   7. Marcar el pedido como pagado actualizando su estado.
     *   8. Redirigir al comprobante del pago recién registrado.
     *
     * @param int $idPedido ID del pedido que se está pagando (viene de la ruta).
     */
    public function store(int $idPedido)
    {
        // 1. Validar que el pedido existe y está cerrado
        if (!$this->esPedidoValidoParaPago($idPedido)) {
            return redirect()->to('/pedidos')->with('error', 'El pedido no es válido para registrar pago.');
        }

        // 2. Detectar pago duplicado
        $pagoExistente = $this->buscarPagoExistente($idPedido);
        if ($pagoExistente) {
            return redirect()->to('/pagos/comprobante/' . $pagoExistente['id_pago']);
        }

        // 3. Construir el pedido enriquecido con sus líneas de detalle
        $pedido = $this->construirPedidoConSusDetalles($idPedido);

        // 4. Calcular el total a pagar desde los subtotales de cada detalle
        $totalAPagar = $this->calcularTotalDesdeDetallesDelPedido($pedido['detalles']);

        // 5. Obtener el método de pago seleccionado
        $idMetodoPago = $this->obtenerMetodoPagoDelFormulario();

        // 6. Persistir el pago y obtener su ID
        $idPago = $this->persistirPagoEnBaseDeDatos($idPedido, $idMetodoPago, $totalAPagar);
        if (!$idPago) {
            return redirect()->back()->withInput()->with('errors', $this->pagoModel->errors());
        }

        // 7. Marcar el pedido como pagado
        $this->marcarPedidoComoPagado($idPedido);

        // 8. Redirigir al comprobante
        return redirect()->to('/pagos/comprobante/' . $idPago)
            ->with('success', 'Pago registrado correctamente.');
    }

    // =========================================================================
    // PASO 1 — Validación del pedido
    // =========================================================================

    /**
     * Verifica que el pedido exista en la base de datos y que tenga
     * fecha de cierre registrada. Solo los pedidos cerrados pueden ser pagados.
     *
     * @param int $idPedido ID del pedido a validar.
     * @return bool true si el pedido existe y está cerrado; false en caso contrario.
     */
    private function esPedidoValidoParaPago(int $idPedido): bool
    {
        $pedido = $this->pedidoModel->find($idPedido);
        return $pedido !== null && !empty($pedido['fecha_cierre']);
    }

    // =========================================================================
    // PASO 2 — Detección de pago duplicado
    // =========================================================================

    /**
     * Busca si ya existe un pago registrado para el pedido indicado.
     * Previene el registro doble de un mismo pago.
     *
     * @param int $idPedido ID del pedido a consultar.
     * @return array|null Datos del pago existente, o null si no hay ninguno.
     */
    private function buscarPagoExistente(int $idPedido): ?array
    {
        return $this->pagoModel->getPagoPorPedido($idPedido);
    }

    // =========================================================================
    // PASO 3 — Construcción del pedido enriquecido
    // =========================================================================

    /**
     * Carga el pedido desde la base de datos y le incorpora sus líneas de
     * detalle como el atributo `detalles`. El resultado es un pedido completo
     * desde el cual se puede leer el subtotal de cada ítem individualmente.
     *
     * Ejemplo del array resultante:
     *   [
     *     'id_pedido'  => 12,
     *     'estado_nombre' => 'cerrado',
     *     ...
     *     'detalles'   => [
     *       ['id_detalle_pedido' => 1, 'cantidad' => 2, 'subtotal' => 16.00, ...],
     *       ['id_detalle_pedido' => 2, 'cantidad' => 1, 'subtotal' => 8.50,  ...],
     *     ],
     *   ]
     *
     * @param int $idPedido ID del pedido a construir.
     * @return array Pedido con su atributo 'detalles' poblado.
     */
    private function construirPedidoConSusDetalles(int $idPedido): array
    {
        $pedido             = $this->pedidoModel->getPedidoConDetalles($idPedido);
        $pedido['detalles'] = $this->detallePedidoModel->getDetallesPorPedido($idPedido);
        return $pedido;
    }

    // =========================================================================
    // PASO 4 — Cálculo del total
    // =========================================================================

    /**
     * Suma los subtotales de cada línea de detalle del pedido para obtener
     * el monto total a cobrar.
     *
     * El cálculo se realiza server-side a partir de los datos del pedido,
     * sin depender del valor enviado por el formulario HTML (más seguro y
     * coherente con el estado real de la orden).
     *
     * @param array $detalles Líneas de detalle, cada una con el campo `subtotal`.
     * @return float Suma de todos los subtotales.
     */
    private function calcularTotalDesdeDetallesDelPedido(array $detalles): float
    {
        return (float) array_sum(array_column($detalles, 'subtotal'));
    }

    // =========================================================================
    // PASO 5 — Obtención del método de pago
    // =========================================================================

    /**
     * Lee el ID del método de pago enviado por el formulario HTML (POST).
     *
     * Usa service('request') en lugar de $this->request porque este controller
     * se instancia con `new` desde PagoController, por lo que CI4 nunca llama
     * initController() sobre él y $this->request permanece null.
     *
     * @return int ID del método de pago seleccionado por el cajero.
     */
    private function obtenerMetodoPagoDelFormulario(): int
    {
        return (int) service('request')->getPost('id_metodo_pago');
    }

    // =========================================================================
    // PASO 6 — Persistencia del pago
    // =========================================================================

    /**
     * Construye el registro de pago con los datos calculados y lo inserta
     * en la tabla `pagos`. Valida el registro contra las reglas del modelo
     * antes de insertar.
     *
     * @param int   $idPedido     ID del pedido que se está pagando.
     * @param int   $idMetodoPago ID del método de pago elegido.
     * @param float $monto        Total calculado desde las líneas de detalle.
     * @return int|false ID del pago insertado, o false si la validación falla.
     */
    private function persistirPagoEnBaseDeDatos(int $idPedido, int $idMetodoPago, float $monto): int|false
    {
        $registroPago = [
            'id_pedido'      => $idPedido,
            'id_metodo_pago' => $idMetodoPago,
            'monto'          => $monto,
            'fecha_pago'     => date('Y-m-d H:i:s'),
        ];

        if (!$this->pagoModel->validate($registroPago)) {
            return false;
        }

        $this->pagoModel->insert($registroPago);
        return (int) $this->pagoModel->getInsertID();
    }

    // =========================================================================
    // PASO 7 — Actualización del estado del pedido
    // =========================================================================

    /**
     * Actualiza el estado del pedido a 'pagado'.
     * Obtiene (o crea si no existe) el registro del estado 'pagado'
     * antes de aplicar el cambio.
     *
     * @param int $idPedido ID del pedido a marcar como pagado.
     */
    private function marcarPedidoComoPagado(int $idPedido): void
    {
        $idEstadoPagado = $this->obtenerOCrearEstadoPagado();
        $this->pedidoModel->update($idPedido, ['id_estado_pedido' => $idEstadoPagado]);
    }

    /**
     * Busca el ID del estado 'pagado' en la tabla `estados_pedido`.
     * Si el estado no existe, lo inserta y devuelve el ID generado.
     * Esto permite que el sistema funcione aunque el estado no haya
     * sido creado previamente en la base de datos.
     *
     * @return int ID del estado 'pagado'.
     */
    private function obtenerOCrearEstadoPagado(): int
    {
        $db  = \Config\Database::connect();
        $row = $db->table('estados_pedido')->where('nombre', 'pagado')->get()->getRowArray();
        if ($row) {
            return (int) $row['id_estado_pedido'];
        }
        $db->table('estados_pedido')->insert(['nombre' => 'pagado']);
        return (int) $db->insertID();
    }
}
