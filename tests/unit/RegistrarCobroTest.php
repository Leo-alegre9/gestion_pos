<?php

namespace Tests\Unit;

use App\Models\DetallePedidoModel;
use App\Models\MetodoPagoModel;
use App\Models\PagoModel;
use App\Models\PedidoModel;
use App\Services\RegistrarPagoService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests unitarios: registrarCobro()
 *
 * Verifica el comportamiento de RegistrarPagoService::registrar(),
 * que registra el pago de un pedido cerrado validando:
 *   - Que el pedido exista y esté cerrado (con fecha_cierre).
 *   - Que no exista un pago previo para el pedido.
 *   - Que el método de pago exista y sea válido.
 *   - Que el monto se calcule server-side desde los subtotales del pedido.
 *
 * La persistencia (transacción) se abstrae mockeando persistirPago() (protected)
 * para aislar la lógica de validación sin necesitar conexión a la BD.
 *
 * Casos cubiertos: PU-REG-01 al PU-REG-07
 */
class RegistrarCobroTest extends CIUnitTestCase
{
    // =========================================================================
    // Helpers
    // =========================================================================

    private function makeService(
        ?PedidoModel $pedidoModel = null,
        ?PagoModel $pagoModel = null,
        ?MetodoPagoModel $metodoPagoModel = null,
        ?DetallePedidoModel $detallePedidoModel = null
    ): RegistrarPagoService {
        $defaultDetalle = $this->createMock(DetallePedidoModel::class);
        $defaultDetalle->method('getDetallesPorPedido')->willReturn([]);

        return new RegistrarPagoService(
            $pedidoModel        ?? $this->createMock(PedidoModel::class),
            $pagoModel          ?? $this->createMock(PagoModel::class),
            $metodoPagoModel    ?? $this->createMock(MetodoPagoModel::class),
            $detallePedidoModel ?? $defaultDetalle,
        );
    }

    /** Pedido cerrado estándar para reutilizar en distintos tests. */
    private function pedidoCerrado(int $idPedido = 1): array
    {
        return ['id_pedido' => $idPedido, 'fecha_cierre' => '2026-06-01 20:00:00'];
    }

    /** Servicio con persistirPago() reemplazado para evitar transacción real con BD. */
    private function makeServiceConPersistenciaMockeada(
        PedidoModel $pedidoModel,
        PagoModel $pagoModel,
        MetodoPagoModel $metodoPagoModel,
        DetallePedidoModel $detallePedidoModel,
        array $resultadoPersistencia
    ): RegistrarPagoService {
        $service = $this->getMockBuilder(RegistrarPagoService::class)
            ->setConstructorArgs([$pedidoModel, $pagoModel, $metodoPagoModel, $detallePedidoModel])
            ->onlyMethods(['persistirPago'])
            ->getMock();

        $service->method('persistirPago')->willReturn($resultadoPersistencia);

        return $service;
    }

    // =========================================================================
    // PU-REG-01 — Cobro válido sobre pedido cerrado con método de pago activo
    // =========================================================================

    public function test_pu_reg_01_cobro_valido_retorna_id_pago_generado(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoCerrado());

        $pagoModel = $this->createMock(PagoModel::class);
        $pagoModel->method('getPagoPorPedido')->willReturn(null);
        $pagoModel->method('validate')->willReturn(true);

        $metodoPagoModel = $this->createMock(MetodoPagoModel::class);
        $metodoPagoModel->method('find')->willReturn(['id_metodo_pago' => 1, 'nombre' => 'Efectivo']);

        $detallePedidoModel = $this->createMock(DetallePedidoModel::class);
        $detallePedidoModel->method('getDetallesPorPedido')->willReturn([['subtotal' => 500.0]]);

        $service = $this->makeServiceConPersistenciaMockeada(
            $pedidoModel,
            $pagoModel,
            $metodoPagoModel,
            $detallePedidoModel,
            ['ok' => true, 'idPago' => 55, 'error' => null, 'errors' => null, 'pagoExistenteId' => null]
        );

        $resultado = $service->registrar(1, 1);

        $this->assertTrue($resultado['ok']);
        $this->assertSame(55, $resultado['idPago']);
        $this->assertNull($resultado['error']);
    }

    // =========================================================================
    // PU-REG-02 — Pedido inexistente: no se registra ningún pago
    // =========================================================================

    public function test_pu_reg_02_pedido_inexistente_retorna_error(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn(null);

        $resultado = $this->makeService(pedidoModel: $pedidoModel)->registrar(9999, 1);

        $this->assertFalse($resultado['ok']);
        $this->assertNull($resultado['idPago']);
        $this->assertNotNull($resultado['error']);
    }

    // =========================================================================
    // PU-REG-03 — Pedido ya cobrado: retorna ID del pago existente sin duplicar
    // =========================================================================

    public function test_pu_reg_03_pedido_ya_cobrado_retorna_id_pago_existente(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoCerrado(2));

        $pagoModel = $this->createMock(PagoModel::class);
        $pagoModel->method('getPagoPorPedido')->willReturn(['id_pago' => 17]);
        // insert() no debe ser llamado (no se duplica el pago)
        $pagoModel->expects($this->never())->method('insert');

        $resultado = $this->makeService(pedidoModel: $pedidoModel, pagoModel: $pagoModel)
            ->registrar(2, 1);

        $this->assertFalse($resultado['ok']);
        $this->assertNull($resultado['error']);
        $this->assertSame(17, $resultado['pagoExistenteId']);
    }

    // =========================================================================
    // PU-REG-04 — Método de pago inexistente: no se registra el pago
    // =========================================================================

    public function test_pu_reg_04_metodo_pago_inexistente_retorna_error(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoCerrado());

        $pagoModel = $this->createMock(PagoModel::class);
        $pagoModel->method('getPagoPorPedido')->willReturn(null);

        $metodoPagoModel = $this->createMock(MetodoPagoModel::class);
        $metodoPagoModel->method('find')->willReturn(null); // método 9999 no existe

        $detallePedidoModel = $this->createMock(DetallePedidoModel::class);
        $detallePedidoModel->method('getDetallesPorPedido')->willReturn([['subtotal' => 100.0]]);

        $resultado = $this->makeService($pedidoModel, $pagoModel, $metodoPagoModel, $detallePedidoModel)
            ->registrar(1, 9999);

        $this->assertFalse($resultado['ok']);
        $this->assertNull($resultado['idPago']);
        $this->assertNotNull($resultado['error']);
    }

    // =========================================================================
    // PU-REG-05 — Pedido abierto (sin fecha de cierre): no se puede cobrar
    // =========================================================================

    public function test_pu_reg_05_pedido_abierto_retorna_error(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn([
            'id_pedido'    => 3,
            'fecha_cierre' => null, // pedido aún no cerrado
        ]);

        $resultado = $this->makeService(pedidoModel: $pedidoModel)->registrar(3, 1);

        $this->assertFalse($resultado['ok']);
        $this->assertNull($resultado['idPago']);
        $this->assertNotNull($resultado['error']);
    }

    // =========================================================================
    // PU-REG-06 — El monto se calcula server-side desde los subtotales del pedido
    // =========================================================================

    public function test_pu_reg_06_monto_calculado_es_igual_al_total_de_items(): void
    {
        $totalEsperado = 350.0; // 200 + 150

        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoCerrado());

        $pagoModel = $this->createMock(PagoModel::class);
        $pagoModel->method('getPagoPorPedido')->willReturn(null);
        $pagoModel->expects($this->once())->method('validate')->willReturnCallback(
            function (array $registro) use ($totalEsperado) {
                // El monto en el registro debe coincidir exactamente con la suma de subtotales
                $this->assertSame($totalEsperado, $registro['monto']);
                return false; // detener aquí; la persistencia requiere BD real
            }
        );
        $pagoModel->method('errors')->willReturn([]);

        $metodoPagoModel = $this->createMock(MetodoPagoModel::class);
        $metodoPagoModel->method('find')->willReturn(['id_metodo_pago' => 1, 'nombre' => 'Efectivo']);

        $detallePedidoModel = $this->createMock(DetallePedidoModel::class);
        $detallePedidoModel->method('getDetallesPorPedido')->willReturn([
            ['subtotal' => 200.0],
            ['subtotal' => 150.0],
        ]);

        $this->makeService($pedidoModel, $pagoModel, $metodoPagoModel, $detallePedidoModel)
            ->registrar(1, 1);
        // La aserción se ejecuta dentro del callback de validate()
    }

    // =========================================================================
    // PU-REG-07 — El monto persistido es el total del pedido, no el que abona el cliente
    // =========================================================================

    public function test_pu_reg_07_monto_persistido_no_acepta_monto_del_cliente(): void
    {
        // El servicio no recibe un $monto del cliente; lo calcula internamente.
        // Aunque el cliente abonara más (ej. vuelto), el monto persistido es el total del pedido.
        $totalDelPedido = 150.0; // 100 + 50

        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoCerrado());

        $pagoModel = $this->createMock(PagoModel::class);
        $pagoModel->method('getPagoPorPedido')->willReturn(null);
        $pagoModel->expects($this->once())->method('validate')->willReturnCallback(
            function (array $registro) use ($totalDelPedido) {
                // Verificar que el monto registrado es el total calculado del pedido
                $this->assertSame($totalDelPedido, $registro['monto']);
                // Confirmar que no existe ningún campo "monto_cliente" o "monto_abonado"
                $this->assertArrayNotHasKey('monto_cliente', $registro);
                return false;
            }
        );
        $pagoModel->method('errors')->willReturn([]);

        $metodoPagoModel = $this->createMock(MetodoPagoModel::class);
        // Método Tarjeta — el cliente podría pagar una cifra mayor
        $metodoPagoModel->method('find')->willReturn(['id_metodo_pago' => 2, 'nombre' => 'Tarjeta']);

        $detallePedidoModel = $this->createMock(DetallePedidoModel::class);
        $detallePedidoModel->method('getDetallesPorPedido')->willReturn([
            ['subtotal' => 100.0],
            ['subtotal' => 50.0],
        ]);

        $this->makeService($pedidoModel, $pagoModel, $metodoPagoModel, $detallePedidoModel)
            ->registrar(1, 2);
        // La aserción se ejecuta dentro del callback de validate()
    }
}
