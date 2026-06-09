<?php

namespace Tests\Unit;

use App\Models\DetallePedidoModel;
use App\Models\MetodoPagoModel;
use App\Models\PagoModel;
use App\Models\PedidoModel;
use App\Services\RegistrarPagoService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests unitarios para RegistrarPagoService.
 *
 * Cubre: validación del pedido, detección de duplicado,
 * errores de validación del modelo y cálculo del total.
 * La persistencia (transacción) requiere tests de integración con BD real.
 */
class RegistrarPagoServiceTest extends CIUnitTestCase
{
    // =========================================================================
    // Helpers
    // =========================================================================

    private function makeService(
        ?PedidoModel $pedidoModel = null,
        ?PagoModel $pagoModel = null,
        ?DetallePedidoModel $detallePedidoModel = null,
        ?MetodoPagoModel $metodoPagoModel = null
    ): RegistrarPagoService {
        $defaultDetalle = $this->createMock(DetallePedidoModel::class);
        $defaultDetalle->method('getDetallesPorPedido')->willReturn([]);

        $defaultMetodo = $this->createMock(MetodoPagoModel::class);
        $defaultMetodo->method('find')->willReturn(['id_metodo_pago' => 1, 'nombre' => 'Efectivo']);

        return new RegistrarPagoService(
            $pedidoModel     ?? $this->createMock(PedidoModel::class),
            $pagoModel       ?? $this->createMock(PagoModel::class),
            $metodoPagoModel ?? $defaultMetodo,
            $detallePedidoModel ?? $defaultDetalle,
        );
    }

    // =========================================================================
    // Validación del pedido
    // =========================================================================

    public function test_registrar_falla_cuando_pedido_no_existe(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn(null);

        $resultado = $this->makeService(pedidoModel: $pedidoModel)->registrar(999, 1);

        $this->assertFalse($resultado['ok']);
        $this->assertSame('El pedido no es válido para registrar pago.', $resultado['error']);
    }

    public function test_registrar_falla_cuando_pedido_esta_abierto(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn([
            'id_pedido'    => 4,
            'fecha_cierre' => null,
        ]);

        $resultado = $this->makeService(pedidoModel: $pedidoModel)->registrar(4, 1);

        $this->assertFalse($resultado['ok']);
        $this->assertSame('El pedido no es válido para registrar pago.', $resultado['error']);
    }

    // =========================================================================
    // Detección de pago duplicado
    // =========================================================================

    public function test_registrar_detecta_pago_duplicado(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn([
            'id_pedido'    => 8,
            'fecha_cierre' => '2026-05-01 09:00:00',
        ]);

        $pagoModel = $this->createMock(PagoModel::class);
        $pagoModel->method('getPagoPorPedido')->willReturn(['id_pago' => 17]);

        $resultado = $this->makeService(pedidoModel: $pedidoModel, pagoModel: $pagoModel)->registrar(8, 1);

        $this->assertFalse($resultado['ok']);
        $this->assertNull($resultado['error']);
        $this->assertSame(17, $resultado['pagoExistenteId']);
    }

    // =========================================================================
    // Validación del modelo
    // =========================================================================

    public function test_registrar_retorna_errores_de_validacion_del_modelo(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn([
            'id_pedido'    => 2,
            'fecha_cierre' => '2026-05-01 08:00:00',
        ]);

        $pagoModel = $this->createMock(PagoModel::class);
        $pagoModel->method('getPagoPorPedido')->willReturn(null);
        $pagoModel->method('validate')->willReturn(false);
        $pagoModel->method('errors')->willReturn(['id_metodo_pago' => 'Seleccioná un método de pago.']);

        $detallePedidoModel = $this->createMock(DetallePedidoModel::class);
        $detallePedidoModel->method('getDetallesPorPedido')->willReturn([
            ['subtotal' => 10.0],
        ]);

        // Método de pago con nombre inválido para que la fábrica no encuentre estrategia
        // y el flujo llegue directamente a validarDatosRegistro()
        $metodoPagoModel = $this->createMock(MetodoPagoModel::class);
        $metodoPagoModel->method('find')->willReturn(['id_metodo_pago' => 1, 'nombre' => 'sin_estrategia']);

        $resultado = $this->makeService($pedidoModel, $pagoModel, $detallePedidoModel, $metodoPagoModel)
            ->registrar(2, 1);

        $this->assertFalse($resultado['ok']);
        $this->assertArrayHasKey('id_metodo_pago', $resultado['errors']);
        $this->assertNull($resultado['error']);
    }

    // =========================================================================
    // Cálculo del total (verificado indirectamente a través del registro)
    // =========================================================================

    public function test_registrar_calcula_total_desde_los_detalles_no_del_formulario(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn([
            'id_pedido'    => 10,
            'fecha_cierre' => '2026-05-01 11:00:00',
        ]);

        $detalles = [
            ['subtotal' => 12.5],
            ['subtotal' => 7.0],
        ];
        $totalEsperado = 19.5;

        $pagoModel = $this->createMock(PagoModel::class);
        $pagoModel->method('getPagoPorPedido')->willReturn(null);
        $pagoModel->method('validate')->willReturnCallback(
            function (array $data) use ($totalEsperado) {
                $this->assertSame($totalEsperado, $data['monto']);
                return false; // detener aquí; no llegamos a la transacción
            }
        );
        $pagoModel->method('errors')->willReturn([]);

        $detallePedidoModel = $this->createMock(DetallePedidoModel::class);
        $detallePedidoModel->method('getDetallesPorPedido')->willReturn($detalles);

        $this->makeService($pedidoModel, $pagoModel, $detallePedidoModel)->registrar(10, 1);
    }
}
