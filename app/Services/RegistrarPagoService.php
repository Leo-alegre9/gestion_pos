<?php

namespace App\Services;

use App\Models\PedidoModel;
use App\Models\PagoModel;
use App\Models\MetodoPagoModel;
use App\Models\DetallePedidoModel;
use App\Services\Pagos\EstrategiaPagoFactory;

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
     * Registra el pago de un pedido cerrado.
     *
     * Flujo:
     *   1. Calcular el total desde los subtotales de cada detalle (server-side).
     *   2. Obtener el MetodoPago desde MetodoPagoModel.
     *   3. Obtener la estrategia correspondiente desde EstrategiaPagoFactory.
     *   4. Ejecutar validar() sobre el monto.
     *   5. Ejecutar procesar() con el monto.
     *   6. Validar los datos del registro contra las reglas del modelo.
     *   7. Persistir el pago y actualizar el estado del pedido en una transacción.
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
        // 0. Verificar que el pedido exista y esté cerrado
        $pedido = $this->pedidoModel->find($idPedido);
        if (!$pedido || $pedido['fecha_cierre'] === null) {
            return $this->falla('El pedido no es válido para registrar pago.');
        }

        // 0b. Detectar pago previo para evitar duplicados
        $pagoExistente = $this->pagoModel->getPagoPorPedido($idPedido);
        if ($pagoExistente) {
            return [
                'ok'             => false,
                'idPago'         => null,
                'error'          => null,
                'errors'         => null,
                'pagoExistenteId'=> (int) $pagoExistente['id_pago'],
            ];
        }

        // 1. Calcular total server-side
        $detalles = $this->detallePedidoModel->getDetallesPorPedido($idPedido);
        $total    = (float) array_sum(array_column($detalles, 'subtotal'));

        // 2. Verificar que el método de pago exista
        $metodoPago = $this->metodoPagoModel->find($idMetodoPago);
        if ($metodoPago === null) {
            return $this->falla('El método de pago no es válido.');
        }

        // 3-5. Aplicar estrategia si existe una mapeada para este método
        $estrategia = EstrategiaPagoFactory::crear($metodoPago['nombre']);

        if ($estrategia !== null) {
            // 4. Validar el monto según la estrategia
            if (!$estrategia->validar($total)) {
                return $this->falla('El monto no es válido para el método de pago seleccionado.');
            }

            // 5. Procesar según la estrategia
            $resultadoEstrategia = $estrategia->procesar($total);

            if (!($resultadoEstrategia['ok'] ?? false)) {
                return $this->falla($resultadoEstrategia['error'] ?? 'Error al procesar el pago.');
            }
        }

        // 6. Construir y validar los datos del registro (flujo original intacto)
        $registro = [
            'id_pedido'      => $idPedido,
            'id_metodo_pago' => $idMetodoPago,
            'monto'          => $total,
            'fecha_pago'     => date('Y-m-d H:i:s'),
        ];

        $errorEnValidacion = $this->validarDatosRegistro($registro);

        if ($errorEnValidacion !== null) {
            return $errorEnValidacion;
        }

        // 7. Insertar el pago y actualizar el estado del pedido en la base de datos
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
    protected function persistirPago(array $registro, int $idPedido): array
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
