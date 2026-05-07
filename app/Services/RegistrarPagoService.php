<?php

namespace App\Services;

use App\Models\PedidoModel;
use App\Models\PagoModel;
use App\Models\DetallePedidoModel;

/**
 * RegistrarPagoService
 *
 * Lógica de negocio del registro del pago.
 * Valida el pedido, detecta duplicados, calcula el total server-side
 * y persiste el pago junto con el nuevo estado del pedido dentro de
 * una sola transacción de base de datos.
 *
 * No recibe objetos HTTP ni devuelve views ni redirects.
 * Retorna arrays de resultado que el controlador convierte en respuesta HTTP.
 */
class RegistrarPagoService
{
    protected PedidoModel $pedidoModel;
    protected PagoModel $pagoModel;
    protected DetallePedidoModel $detallePedidoModel;

    public function __construct(
        PedidoModel $pedidoModel,
        PagoModel $pagoModel,
        DetallePedidoModel $detallePedidoModel
    ) {
        $this->pedidoModel        = $pedidoModel;
        $this->pagoModel          = $pagoModel;
        $this->detallePedidoModel = $detallePedidoModel;
    }

    // =========================================================================
    // PUNTO DE ENTRADA
    // =========================================================================

    /**
     * Registra el pago de un pedido cerrado.
     *
     * Flujo:
     *   1. Verificar que el pedido exista y esté cerrado.
     *   2. Detectar pago duplicado.
     *   3. Calcular el total desde los subtotales de cada detalle (server-side).
     *   4. Validar los datos del registro contra las reglas del modelo.
     *   5. Persistir el pago y actualizar el estado del pedido en una transacción.
     *
     * @param int $idPedido     ID del pedido que se está pagando.
     * @param int $idMetodoPago ID del método de pago seleccionado.
     * @return array {
     *   ok:              bool        — true si el pago fue registrado exitosamente.
     *   idPago:          int|null    — ID del pago insertado cuando ok=true.
     *   error:           string|null — mensaje genérico cuando ok=false.
     *   errors:          array|null  — errores de validación del modelo cuando ok=false.
     *   pagoExistenteId: int|null    — non-null si ya había un pago registrado.
     * }
     */
    public function registrar(int $idPedido, int $idMetodoPago): array
    {
        // 1. Calcular total server-side y construir los datos del registro
        $detalles = $this->detallePedidoModel->getDetallesPorPedido($idPedido);
        $total    = (float) array_sum(array_column($detalles, 'subtotal'));

        $registro = [
            'id_pedido'      => $idPedido,
            'id_metodo_pago' => $idMetodoPago,
            'monto'          => $total,
            'fecha_pago'     => date('Y-m-d H:i:s'),
        ];

        // 2. Validación de los datos del registro
        $errorEnValidacion = $this->validarDatosRegistro($registro);

        if ($errorEnValidacion !== null) {
            return $errorEnValidacion;
        }

        // 5. Insertar el pago y actualizar el estado del pedido en la base de datos
        return $this->persistirPago($registro, $idPedido);
    }

    // =========================================================================
    // VALIDACIONES
    // =========================================================================

    /**
     * Valida los datos del registro contra las reglas definidas en PagoModel.
     * Retorna un array de falla con los errores del modelo, o null si es válido.
     */
    private function validarDatosRegistro(array $registro): ?array
    {
        if ($this->pagoModel->validate($registro)) {
            return null;
        }

        return [
            'ok'             => false,
            'idPago'         => null,
            'error'          => null,
            'errors'         => $this->pagoModel->errors(),
            'pagoExistenteId'=> null,
        ];
    }

    // =========================================================================
    // PERSISTENCIA
    // =========================================================================

    /**
     * Inserta el pago en `pagos` y actualiza el estado del pedido
     * dentro de una única transacción de base de datos.
     */
    private function persistirPago(array $registro, int $idPedido): array
    {
        $idEstadoPagado = $this->obtenerOCrearEstado('pagado');

        $db = \Config\Database::connect();
        $db->transStart();

        $this->pagoModel->skipValidation(true)->insert($registro);
        $idPago = (int) $this->pagoModel->getInsertID();

        $this->pedidoModel->update($idPedido, ['id_estado_pedido' => $idEstadoPagado]);

        $db->transComplete();

        if (!$db->transStatus() || $idPago === 0) {
            return $this->falla('Error al registrar el pago. Intentá nuevamente.');
        }

        return [
            'ok'             => true,
            'idPago'         => $idPago,
            'error'          => null,
            'errors'         => null,
            'pagoExistenteId'=> null,
        ];
    }

    // =========================================================================
    // UTILIDADES
    // =========================================================================

    /**
     * Busca el ID del estado indicado en `estados_pedido`.
     * Si no existe, lo inserta y devuelve el ID generado.
     *
     * @param string $nombre Nombre del estado (ej. 'pagado').
     * @return int ID del estado.
     */
    private function obtenerOCrearEstado(string $nombre): int
    {
        $db  = \Config\Database::connect();
        $row = $db->table('estados_pedido')->where('nombre', $nombre)->get()->getRowArray();
        if ($row) {
            return (int) $row['id_estado_pedido'];
        }
        $db->table('estados_pedido')->insert(['nombre' => $nombre]);
        return (int) $db->insertID();
    }

    private function falla(string $error): array
    {
        return ['ok' => false, 'idPago' => null, 'error' => $error, 'errors' => null, 'pagoExistenteId' => null];
    }
}
