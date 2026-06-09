<?php

namespace Tests\Unit;

use App\Models\DetallePedidoModel;
use App\Models\PedidoModel;
use App\Models\ProductoModel;
use App\Models\StockModel;
use App\Services\AgregarDetalleService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests unitarios: agregarProductoAlPedido()
 *
 * Verifica el comportamiento de AgregarDetalleService::agregar(),
 * que incorpora un ítem al detalle de un pedido activo validando:
 *   - Que la cantidad sea positiva.
 *   - Que el pedido exista y esté abierto (sin fecha_cierre).
 *   - Que el producto exista y esté activo.
 *   - Que haya stock suficiente si el producto tiene control de inventario.
 *
 * Todos los modelos se reemplazan por mocks para aislar la lógica de negocio
 * sin necesitar conexión a la base de datos.
 *
 * Casos cubiertos: PU-AGR-01 al PU-AGR-06
 */
class AgregarProductoAlPedidoTest extends CIUnitTestCase
{
    // =========================================================================
    // Helpers
    // =========================================================================

    private function makeService(
        ?PedidoModel $pedidoModel = null,
        ?ProductoModel $productoModel = null,
        ?DetallePedidoModel $detallePedidoModel = null,
        ?StockModel $stockModel = null
    ): AgregarDetalleService {
        return new AgregarDetalleService(
            $pedidoModel        ?? $this->createMock(PedidoModel::class),
            $productoModel      ?? $this->createMock(ProductoModel::class),
            $detallePedidoModel ?? $this->createMock(DetallePedidoModel::class),
            $stockModel         ?? $this->createMock(StockModel::class),
        );
    }

    /** Construye un mock de pedido abierto estándar. */
    private function pedidoAbierto(int $idPedido = 1): array
    {
        return ['id_pedido' => $idPedido, 'fecha_cierre' => null];
    }

    /** Construye un mock de producto activo con precio estándar. */
    private function productoActivo(int $idProducto = 10, float $precio = 250.0): array
    {
        return ['id_producto' => $idProducto, 'activo' => 1, 'precio_venta' => $precio];
    }

    // =========================================================================
    // PU-AGR-01 — Producto válido en pedido abierto: inserción exitosa
    // =========================================================================

    public function test_pu_agr_01_item_valido_inserta_en_detalle_pedidos(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoAbierto());

        $productoModel = $this->createMock(ProductoModel::class);
        $productoModel->method('find')->willReturn($this->productoActivo());

        $stockModel = $this->createMock(StockModel::class);
        $stockModel->method('getStockPorProducto')->willReturn(null); // sin control de stock

        $detallePedidoModel = $this->createMock(DetallePedidoModel::class);
        $detallePedidoModel->method('insert')->willReturn(true);
        $detallePedidoModel->method('getInsertID')->willReturn(99);

        $resultado = $this->makeService($pedidoModel, $productoModel, $detallePedidoModel, $stockModel)
            ->agregar(1, 10, 2);

        $this->assertTrue($resultado['ok']);
        $this->assertSame(99, $resultado['idDetalle']);
        $this->assertNull($resultado['error']);
    }

    // =========================================================================
    // PU-AGR-02 — Producto inexistente: no se modifica el pedido
    // =========================================================================

    public function test_pu_agr_02_producto_inexistente_retorna_error(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoAbierto());

        $productoModel = $this->createMock(ProductoModel::class);
        $productoModel->method('find')->willReturn(null); // producto 9999 no existe

        $resultado = $this->makeService(pedidoModel: $pedidoModel, productoModel: $productoModel)
            ->agregar(1, 9999, 1);

        $this->assertFalse($resultado['ok']);
        $this->assertNull($resultado['idDetalle']);
        $this->assertStringContainsString('no existe', $resultado['error']);
    }

    // =========================================================================
    // PU-AGR-03 — Cantidad igual a cero: error de validación
    // =========================================================================

    public function test_pu_agr_03_cantidad_cero_retorna_error_de_validacion(): void
    {
        // La validación de cantidad se ejecuta antes de consultar pedido o producto,
        // por lo que no se necesitan mocks específicos para estas entidades.
        $resultado = $this->makeService()->agregar(1, 10, 0);

        $this->assertFalse($resultado['ok']);
        $this->assertNull($resultado['idDetalle']);
        $this->assertStringContainsString('mayor a cero', $resultado['error']);
    }

    // =========================================================================
    // PU-AGR-04 — Cantidad negativa: error de validación
    // =========================================================================

    public function test_pu_agr_04_cantidad_negativa_retorna_error_de_validacion(): void
    {
        $resultado = $this->makeService()->agregar(1, 10, -3);

        $this->assertFalse($resultado['ok']);
        $this->assertNull($resultado['idDetalle']);
        $this->assertStringContainsString('mayor a cero', $resultado['error']);
    }

    // =========================================================================
    // PU-AGR-05 — Pedido cerrado: no se registra el ítem
    // =========================================================================

    public function test_pu_agr_05_pedido_cerrado_retorna_error(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn([
            'id_pedido'    => 2,
            'fecha_cierre' => '2026-06-01 20:00:00', // pedido ya cerrado
        ]);

        $resultado = $this->makeService(pedidoModel: $pedidoModel)->agregar(2, 10, 1);

        $this->assertFalse($resultado['ok']);
        $this->assertNull($resultado['idDetalle']);
        $this->assertStringContainsString('cerrado', $resultado['error']);
    }

    // =========================================================================
    // PU-AGR-06 — Stock insuficiente: no se registra el ítem
    // =========================================================================

    public function test_pu_agr_06_stock_insuficiente_retorna_error(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoAbierto());

        $productoModel = $this->createMock(ProductoModel::class);
        $productoModel->method('find')->willReturn($this->productoActivo(15, 100.0));

        $stockModel = $this->createMock(StockModel::class);
        // stock disponible: 5 unidades; cantidad solicitada: 100
        $stockModel->method('getStockPorProducto')->willReturn(['cantidad_disponible' => 5]);

        $resultado = $this->makeService(pedidoModel: $pedidoModel, productoModel: $productoModel, stockModel: $stockModel)
            ->agregar(1, 15, 100);

        $this->assertFalse($resultado['ok']);
        $this->assertNull($resultado['idDetalle']);
        $this->assertStringContainsString('Stock insuficiente', $resultado['error']);
    }
}
