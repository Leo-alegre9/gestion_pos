<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\PagoModel;
use App\Models\DetallePedidoModel;

/**
 * PagoController
 *
 * Capa HTTP del módulo de pagos. Es el único punto de entrada de las rutas de pagos.
 * No contiene lógica de negocio: delega la preparación al PrepararPagoController
 * y el registro al RegistrarPagoController. Su única responsabilidad es
 * coordinar la respuesta HTTP (renderizar vistas o redirigir).
 */
class PagoController extends BaseController
{
    protected PedidoModel $pedidoModel;
    protected PagoModel $pagoModel;
    protected DetallePedidoModel $detallePedidoModel;
    protected PrepararPagoController $prepararPago;
    protected RegistrarPagoController $registrarPago;

    public function __construct()
    {
        $this->pedidoModel        = new PedidoModel();
        $this->pagoModel          = new PagoModel();
        $this->detallePedidoModel = new DetallePedidoModel();
        $this->prepararPago       = new PrepararPagoController();
        $this->registrarPago      = new RegistrarPagoController();
    }

    // =========================================================================
    // RUTAS PÚBLICAS  (HTTP entry points)
    // =========================================================================

    /**
     * [GET] pagos/formulario/{idPedido}
     *
     * Muestra el formulario de pago para un pedido cerrado.
     *
     * Flujo:
     *   1. Delega la validación y la recopilación de datos al PrepararPagoController.
     *   2. Si la preparación falla (pedido inexistente, abierto o ya pagado),
     *      redirige al destino indicado por el preparador.
     *   3. Si la preparación es exitosa, renderiza el formulario con todos
     *      los datos del pedido, sus ítems, el total y los métodos de pago.
     *
     * @param int $idPedido ID del pedido cerrado que se desea pagar.
     */
    public function mostrarFormularioDePago(int $idPedido)
    {
        $resultado = $this->prepararPago->preparar($idPedido);

        if (!$resultado['success']) {
            return redirect()->to($resultado['redirect'])->with('error', $resultado['error']);
        }

        return view('pagos/pagar', $this->construirDatosParaVistaDePago($resultado['data']));
    }

    /**
     * [POST] pagos/procesar/{idPedido}
     *
     * Procesa el registro del pago de un pedido.
     * Delega toda la lógica de negocio al RegistrarPagoController
     * y retransmite su respuesta HTTP tal cual.
     *
     * @param int $idPedido ID del pedido que se está pagando.
     */
    public function procesarRegistroDePago(int $idPedido)
    {
        return $this->registrarPago->store($idPedido);
    }

    /**
     * [GET] pagos/comprobante/{idPago}
     *
     * Muestra el comprobante de un pago registrado.
     *
     * Flujo:
     *   1. Busca el pago por su ID (incluye nombre del método de pago).
     *   2. Carga las líneas de detalle del pedido asociado (con nombre de producto).
     *   3. Calcula el total sumando los subtotales de cada línea.
     *   4. Carga los datos generales del pedido.
     *   5. Renderiza la vista del comprobante.
     *
     * @param int $idPago ID del pago del que se quiere mostrar el comprobante.
     */
    public function mostrarComprobanteDePago(int $idPago)
    {
        $pago = $this->buscarPagoPorId($idPago);
        if (!$pago) {
            return redirect()->to('/pedidos')->with('error', 'Comprobante no encontrado.');
        }

        $items  = $this->cargarItemsDelPedidoConNombreProducto($pago['id_pedido']);
        $total  = $this->sumarSubtotalesDeItems($items);
        $pedido = $this->pedidoModel->getPedidoConDetalles($pago['id_pedido']);

        return view('pagos/recibo', $this->construirDatosParaVistaDeComprobante($pago, $pedido, $items, $total));
    }

    // =========================================================================
    // APOYO PARA mostrarFormularioDePago
    // =========================================================================

    /**
     * Construye el array de datos que la vista `pagos/pagar` necesita,
     * añadiendo el título de la página y los datos del usuario en sesión.
     *
     * @param array $datos Datos preparados por PrepararPagoController::preparar().
     *                     Contiene: 'pedido', 'items', 'total', 'metodos'.
     * @return array Array listo para ser pasado a view().
     */
    private function construirDatosParaVistaDePago(array $datos): array
    {
        return [
            'titulo'  => 'Registrar Pago',
            'pedido'  => $datos['pedido'],
            'items'   => $datos['items'],
            'total'   => $datos['total'],
            'metodos' => $datos['metodos'],
            'user'    => $this->obtenerDatosDeUsuarioEnSesion(),
        ];
    }

    // =========================================================================
    // APOYO PARA mostrarComprobanteDePago
    // =========================================================================

    /**
     * Busca un pago por su ID incluyendo el nombre del método de pago (JOIN).
     *
     * @param int $idPago ID del pago a buscar.
     * @return array|null Datos del pago con 'metodo_nombre', o null si no existe.
     */
    private function buscarPagoPorId(int $idPago): ?array
    {
        return $this->pagoModel->getPagoConMetodo($idPago);
    }

    /**
     * Carga todas las líneas de detalle de un pedido enriquecidas con
     * el nombre del producto correspondiente (JOIN con `productos`).
     *
     * @param int $idPedido ID del pedido cuyos ítems se quieren cargar.
     * @return array Lista de líneas de detalle con el campo `nombre` del producto.
     */
    private function cargarItemsDelPedidoConNombreProducto(int $idPedido): array
    {
        return $this->detallePedidoModel
            ->select('detalle_pedidos.*, productos.nombre', false)
            ->join('productos', 'productos.id_producto = detalle_pedidos.id_producto')
            ->where('detalle_pedidos.id_pedido', $idPedido)
            ->findAll();
    }

    /**
     * Calcula el importe total sumando el campo `subtotal` de cada ítem.
     *
     * @param array $items Lista de ítems, cada uno con el campo `subtotal`.
     * @return float Suma de todos los subtotales.
     */
    private function sumarSubtotalesDeItems(array $items): float
    {
        return (float) array_sum(array_column($items, 'subtotal'));
    }

    /**
     * Construye el array de datos que la vista `pagos/recibo` necesita.
     *
     * @param array $pago   Datos del pago (con método de pago).
     * @param array $pedido Datos del pedido asociado.
     * @param array $items  Líneas de detalle del pedido.
     * @param float $total  Total calculado desde los subtotales.
     * @return array Array listo para ser pasado a view().
     */
    private function construirDatosParaVistaDeComprobante(array $pago, array $pedido, array $items, float $total): array
    {
        return [
            'titulo' => 'Comprobante de Pago',
            'pago'   => $pago,
            'pedido' => $pedido,
            'items'  => $items,
            'total'  => $total,
            'user'   => $this->obtenerDatosDeUsuarioEnSesion(),
        ];
    }

    // =========================================================================
    // UTILIDADES COMUNES
    // =========================================================================

    /**
     * Lee el nombre y rol del usuario autenticado desde la sesión PHP.
     * Devuelve valores por defecto si la sesión no tiene datos (ej. desarrollo).
     *
     * @return array Con keys 'name' (string) y 'role' (string).
     */
    private function obtenerDatosDeUsuarioEnSesion(): array
    {
        return [
            'name' => session('nombre')    ?? 'Administrador',
            'role' => session('rol_nombre') ?? 'Admin',
        ];
    }
}
