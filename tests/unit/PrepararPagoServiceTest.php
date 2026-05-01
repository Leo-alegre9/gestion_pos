<?php

namespace Tests\Unit;

use App\Models\DetallePedidoModel;
use App\Models\MetodoPagoModel;
use App\Models\PagoModel;
use App\Models\PedidoModel;
use App\Services\PrepararPagoService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests unitarios para PrepararPagoService.
 *
 * Cubre: validación del pedido, detección de pago duplicado,
 * cálculo de total y el camino normal de preparación.
 */
class PrepararPagoServiceTest extends CIUnitTestCase
{
    // =========================================================================
    // Helpers
    // =========================================================================

    private function makeService(
        ?PedidoModel $pedidoModel = null,
        ?PagoModel $pagoModel = null,
        ?MetodoPagoModel $metodoPagoModel = null,
        ?DetallePedidoModel $detallePedidoModel = null
    ): PrepararPagoService {
        return new PrepararPagoService(
            $pedidoModel        ?? $this->createMock(PedidoModel::class),
            $pagoModel          ?? $this->createMock(PagoModel::class),
            $metodoPagoModel    ?? $this->createMock(MetodoPagoModel::class),
            $detallePedidoModel ?? $this->createMock(DetallePedidoModel::class),
        );
    }

    // =========================================================================
    // Validación del pedido
    // =========================================================================

    public function test_preparar_falla_cuando_pedido_no_existe(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('getPedidoConDetalles')->willReturn(null);

        $resultado = $this->makeService(pedidoModel: $pedidoModel)->preparar(999);

        $this->assertFalse($resultado['ok']);
        $this->assertSame('El pedido no existe.', $resultado['error']);
        $this->assertNull($resultado['data']);
        $this->assertNull($resultado['pagoExistenteId']);
    }

    public function test_preparar_falla_cuando_pedido_esta_abierto(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('getPedidoConDetalles')->willReturn([
            'id_pedido'    => 5,
            'fecha_cierre' => null,
        ]);

        $resultado = $this->makeService(pedidoModel: $pedidoModel)->preparar(5);

        $this->assertFalse($resultado['ok']);
        $this->assertSame('El pedido debe cerrarse antes de registrar el pago.', $resultado['error']);
    }

    // =========================================================================
    // Detección de pago duplicado
    // =========================================================================

    public function test_preparar_indica_pago_existente_cuando_ya_fue_pagado(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('getPedidoConDetalles')->willReturn([
            'id_pedido'    => 7,
            'fecha_cierre' => '2026-05-01 12:00:00',
        ]);

        $pagoModel = $this->createMock(PagoModel::class);
        $pagoModel->method('getPagoPorPedido')->willReturn(['id_pago' => 42]);

        $resultado = $this->makeService(pedidoModel: $pedidoModel, pagoModel: $pagoModel)->preparar(7);

        $this->assertFalse($resultado['ok']);
        $this->assertNull($resultado['error']);
        $this->assertSame(42, $resultado['pagoExistenteId']);
    }

    // =========================================================================
    // Cálculo del total
    // =========================================================================

    public function test_calcular_total_suma_subtotales_correctamente(): void
    {
        $service = $this->makeService();

        $items = [
            ['subtotal' => 10.0],
            ['subtotal' => 5.5],
            ['subtotal' => 3.25],
        ];

        $this->assertSame(18.75, $service->calcularTotal($items));
    }

    public function test_calcular_total_con_items_vacios_devuelve_cero(): void
    {
        $this->assertSame(0.0, $this->makeService()->calcularTotal([]));
    }

    // =========================================================================
    // Camino normal
    // =========================================================================

    public function test_preparar_ok_devuelve_datos_completos(): void
    {
        $pedido  = ['id_pedido' => 3, 'fecha_cierre' => '2026-05-01 10:00:00'];
        $items   = [['subtotal' => 20.0], ['subtotal' => 4.5]];
        $metodos = [['id_metodo_pago' => 1, 'nombre' => 'Efectivo']];

        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('getPedidoConDetalles')->willReturn($pedido);

        $pagoModel = $this->createMock(PagoModel::class);
        $pagoModel->method('getPagoPorPedido')->willReturn(null);

        $detallePedidoModel = $this->createMock(DetallePedidoModel::class);
        $detallePedidoModel->method('getDetallesPorPedido')->willReturn($items);

        $metodoPagoModel = $this->createMock(MetodoPagoModel::class);
        $metodoPagoModel->method('getActivos')->willReturn($metodos);

        $resultado = $this->makeService($pedidoModel, $pagoModel, $metodoPagoModel, $detallePedidoModel)->preparar(3);

        $this->assertTrue($resultado['ok']);
        $this->assertNull($resultado['error']);
        $this->assertNull($resultado['pagoExistenteId']);
        $this->assertSame($pedido, $resultado['data']['pedido']);
        $this->assertSame($items, $resultado['data']['items']);
        $this->assertSame(24.5, $resultado['data']['total']);
        $this->assertSame($metodos, $resultado['data']['metodos']);
    }
}
