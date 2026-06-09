<?php

namespace App\Services;

use App\Models\DetallePedidoModel;
use App\Models\PedidoModel;
use App\Models\ProductoModel;
use App\Models\StockModel;

/**
 * AgregarDetalleService
 *
 * Encapsula la lógica de negocio de agregarProductoAlPedido:
 * valida el pedido, el producto, la cantidad y el stock disponible
 * antes de insertar el ítem en detalle_pedidos.
 *
 * No recibe objetos HTTP ni devuelve views ni redirects.
 * Retorna arrays de resultado que el controlador convierte en respuesta HTTP.
 */
class AgregarDetalleService
{
    protected PedidoModel $pedidoModel;
    protected ProductoModel $productoModel;
    protected DetallePedidoModel $detallePedidoModel;
    protected StockModel $stockModel;

    public function __construct(
        PedidoModel $pedidoModel,
        ProductoModel $productoModel,
        DetallePedidoModel $detallePedidoModel,
        StockModel $stockModel
    ) {
        $this->pedidoModel        = $pedidoModel;
        $this->productoModel      = $productoModel;
        $this->detallePedidoModel = $detallePedidoModel;
        $this->stockModel         = $stockModel;
    }

    // =========================================================================
    // PUNTO DE ENTRADA
    // =========================================================================

    /**
     * Incorpora un ítem al detalle de un pedido activo.
     *
     * Flujo:
     *   1. Validar que la cantidad sea mayor a cero.
     *   2. Verificar que el pedido exista y esté abierto (sin fecha_cierre).
     *   3. Verificar que el producto exista y esté activo.
     *   4. Verificar stock disponible si el producto tiene control de inventario.
     *   5. Calcular subtotal e insertar el ítem en detalle_pedidos.
     *
     * @param int $idPedido   ID del pedido donde se agrega el producto.
     * @param int $idProducto ID del producto a agregar.
     * @param int $cantidad   Cantidad a agregar (debe ser > 0).
     * @return array {
     *   ok:        bool        — true si el ítem fue insertado exitosamente.
     *   idDetalle: int|null    — ID del detalle insertado cuando ok=true.
     *   error:     string|null — mensaje de error cuando ok=false.
     * }
     */
    public function agregar(int $idPedido, int $idProducto, int $cantidad): array
    {
        // 1. Validar cantidad positiva
        if ($cantidad <= 0) {
            return $this->falla('La cantidad debe ser mayor a cero.');
        }

        // 2. Verificar que el pedido exista y esté abierto
        $pedido = $this->pedidoModel->find($idPedido);
        if (!$pedido || $pedido['fecha_cierre'] !== null) {
            return $this->falla('El pedido no existe o ya está cerrado.');
        }

        // 3. Verificar que el producto exista y esté activo
        $producto = $this->productoModel->find($idProducto);
        if (!$producto || !$producto['activo']) {
            return $this->falla('El producto no existe o no está activo.');
        }

        // 4. Verificar stock disponible (sólo si el producto tiene registro de stock)
        $stock = $this->stockModel->getStockPorProducto($idProducto);
        if ($stock !== null && (int) $stock['cantidad_disponible'] < $cantidad) {
            return $this->falla('Stock insuficiente para este producto.');
        }

        // 5. Calcular subtotal e insertar
        $precioUnit = (float) $producto['precio_venta'];
        $data = [
            'id_pedido'       => $idPedido,
            'id_producto'     => $idProducto,
            'cantidad'        => $cantidad,
            'precio_unitario' => $precioUnit,
            'subtotal'        => round($precioUnit * $cantidad, 2),
            'observaciones'   => null,
        ];

        $this->detallePedidoModel->insert($data);
        $idDetalle = (int) $this->detallePedidoModel->getInsertID();

        if ($idDetalle === 0) {
            return $this->falla('Error al registrar el detalle del pedido.');
        }

        return ['ok' => true, 'idDetalle' => $idDetalle, 'error' => null];
    }

    // =========================================================================
    // UTILIDADES
    // =========================================================================

    private function falla(string $error): array
    {
        return ['ok' => false, 'idDetalle' => null, 'error' => $error];
    }
}
