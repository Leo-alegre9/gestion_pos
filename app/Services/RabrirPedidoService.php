<?php

namespace App\Services;

use App\Models\MesaModel;
use App\Models\PagoModel;
use App\Models\PedidoModel;

/**
 * RabrirPedidoService
 *
 * Encapsula la lógica de negocio de la reapertura de un pedido:
 * verifica que esté cerrado, que no tenga pago registrado,
 * elimina la fecha de cierre, restablece el estado a 'abierto'
 * y vuelve la mesa a 'ocupada' si aplica.
 *
 * No recibe objetos HTTP ni devuelve views ni redirects.
 * Retorna arrays de resultado que el controlador convierte en respuesta HTTP.
 */
class RabrirPedidoService
{
    protected PedidoModel $pedidoModel;
    protected PagoModel $pagoModel;
    protected MesaModel $mesaModel;

    public function __construct(
        PedidoModel $pedidoModel,
        PagoModel $pagoModel,
        MesaModel $mesaModel
    ) {
        $this->pedidoModel = $pedidoModel;
        $this->pagoModel   = $pagoModel;
        $this->mesaModel   = $mesaModel;
    }

    // =========================================================================
    // PUNTO DE ENTRADA
    // =========================================================================

    /**
     * Reabre un pedido cerrado, siempre que no tenga pago registrado.
     *
     * Flujo:
     *   1. Verificar que el pedido exista.
     *   2. Verificar que el pedido esté cerrado (con fecha_cierre).
     *   3. Verificar que no exista un pago asociado.
     *   4. Actualizar el pedido: limpiar fecha_cierre y restablecer estado a 'abierto'.
     *   5. Marcar la mesa como 'ocupada' si el pedido tiene una asignada.
     *
     * @param int $idPedido ID del pedido a reabrir.
     * @return array {
     *   ok:    bool        — true si el pedido fue reabierto exitosamente.
     *   error: string|null — mensaje de error cuando ok=false.
     * }
     */
    public function reabrir(int $idPedido): array
    {
        // 1. Verificar que el pedido exista
        $pedido = $this->pedidoModel->find($idPedido);
        if (!$pedido) {
            return $this->falla('El pedido no existe.');
        }

        // 2. Verificar que el pedido esté cerrado
        if ($pedido['fecha_cierre'] === null) {
            return $this->falla('El pedido ya está abierto.');
        }

        // 3. Verificar que no tenga pago registrado
        $pago = $this->pagoModel->getPagoPorPedido($idPedido);
        if ($pago) {
            return $this->falla('No se puede reabrir un pedido que ya tiene pago registrado.');
        }

        // 4. Reabrir el pedido: limpiar fecha_cierre y cambiar estado a 'abierto'
        $idEstadoAbierto = $this->obtenerOCrearEstado('abierto');
        $this->pedidoModel->update($idPedido, [
            'fecha_cierre'     => null,
            'id_estado_pedido' => $idEstadoAbierto,
        ]);

        // 5. Volver la mesa a 'ocupada' si el pedido tiene una asignada
        if (!empty($pedido['id_mesa'])) {
            $this->mesaModel->update($pedido['id_mesa'], ['estado' => 'ocupada']);
        }

        return ['ok' => true, 'error' => null];
    }

    // =========================================================================
    // UTILIDADES
    // =========================================================================

    /**
     * Busca el ID del estado indicado en `estados_pedido`.
     * Si no existe, lo inserta y devuelve el ID generado.
     * Método protected para permitir su reemplazo en tests unitarios.
     */
    protected function obtenerOCrearEstado(string $nombre): int
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
        return ['ok' => false, 'error' => $error];
    }
}
