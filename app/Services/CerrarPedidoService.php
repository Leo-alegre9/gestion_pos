<?php

namespace App\Services;

use App\Models\MesaModel;
use App\Models\PedidoModel;

/**
 * CerrarPedidoService
 *
 * Encapsula la lógica de negocio del cierre de un pedido:
 * verifica que exista y esté abierto, lo cierra y libera la mesa asociada.
 *
 * No recibe objetos HTTP ni devuelve views ni redirects.
 * Retorna arrays de resultado que el controlador convierte en respuesta HTTP.
 */
class CerrarPedidoService
{
    protected PedidoModel $pedidoModel;
    protected MesaModel $mesaModel;

    public function __construct(PedidoModel $pedidoModel, MesaModel $mesaModel)
    {
        $this->pedidoModel = $pedidoModel;
        $this->mesaModel   = $mesaModel;
    }

    // =========================================================================
    // PUNTO DE ENTRADA
    // =========================================================================

    /**
     * Cierra un pedido registrando la fecha/hora de cierre y liberando su mesa.
     *
     * Flujo:
     *   1. Verificar que el pedido exista.
     *   2. Verificar que el pedido esté abierto (sin fecha_cierre).
     *   3. Llamar a PedidoModel::cerrarPedido() para registrar el cierre.
     *   4. Actualizar el estado de la mesa a 'libre' si el pedido tiene una asignada.
     *
     * @param int $idPedido ID del pedido a cerrar.
     * @return array {
     *   ok:    bool        — true si el pedido fue cerrado exitosamente.
     *   error: string|null — mensaje de error cuando ok=false.
     * }
     */
    public function cerrar(int $idPedido): array
    {
        // 1. Verificar que el pedido exista
        $pedido = $this->pedidoModel->find($idPedido);
        if (!$pedido) {
            return $this->falla('El pedido no existe.');
        }

        // 2. Verificar que el pedido esté abierto
        if ($pedido['fecha_cierre'] !== null) {
            return $this->falla('El pedido ya fue cerrado.');
        }

        // 3. Registrar el cierre
        $ok = $this->pedidoModel->cerrarPedido($idPedido);
        if (!$ok) {
            return $this->falla('Error al cerrar el pedido. Intentá nuevamente.');
        }

        // 4. Liberar la mesa si el pedido tiene una asignada
        if (!empty($pedido['id_mesa'])) {
            $this->mesaModel->update($pedido['id_mesa'], ['estado' => 'libre']);
        }

        return ['ok' => true, 'error' => null];
    }

    // =========================================================================
    // UTILIDADES
    // =========================================================================

    private function falla(string $error): array
    {
        return ['ok' => false, 'error' => $error];
    }
}
