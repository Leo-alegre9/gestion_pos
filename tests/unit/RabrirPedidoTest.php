<?php

namespace Tests\Unit;

use App\Models\MesaModel;
use App\Models\PagoModel;
use App\Models\PedidoModel;
use App\Services\RabrirPedidoService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests unitarios: reabrirPedido()
 *
 * Verifica el comportamiento de RabrirPedidoService::reabrir(),
 * que reabre un pedido cerrado validando:
 *   - Que el pedido exista.
 *   - Que el pedido esté cerrado (con fecha_cierre).
 *   - Que no tenga pago registrado.
 *   - Que la mesa asociada pase a estado 'ocupada' al reabrir.
 *
 * obtenerOCrearEstado() (protected) se mockea para aislar la lógica
 * sin necesitar conexión a la base de datos.
 */
class RabrirPedidoTest extends CIUnitTestCase
{
    // =========================================================================
    // Helpers
    // =========================================================================

    private function makeService(
        ?PedidoModel $pedidoModel = null,
        ?PagoModel $pagoModel = null,
        ?MesaModel $mesaModel = null
    ): RabrirPedidoService {
        return new RabrirPedidoService(
            $pedidoModel ?? $this->createMock(PedidoModel::class),
            $pagoModel   ?? $this->createMock(PagoModel::class),
            $mesaModel   ?? $this->createMock(MesaModel::class),
        );
    }

    /**
     * Construye un RabrirPedidoService con obtenerOCrearEstado() mockeado
     * para evitar la consulta real a la BD en los tests de reapertura exitosa.
     */
    private function makeServiceConEstadoMockeado(
        PedidoModel $pedidoModel,
        PagoModel $pagoModel,
        MesaModel $mesaModel,
        int $idEstadoAbierto = 1
    ): RabrirPedidoService {
        $service = $this->getMockBuilder(RabrirPedidoService::class)
            ->setConstructorArgs([$pedidoModel, $pagoModel, $mesaModel])
            ->onlyMethods(['obtenerOCrearEstado'])
            ->getMock();

        $service->method('obtenerOCrearEstado')->willReturn($idEstadoAbierto);

        return $service;
    }

    /** Pedido cerrado estándar para reutilizar en distintos tests. */
    private function pedidoCerrado(int $idPedido = 1, ?int $idMesa = null): array
    {
        return [
            'id_pedido'    => $idPedido,
            'fecha_cierre' => '2026-06-01 21:00:00',
            'id_mesa'      => $idMesa,
        ];
    }

    // =========================================================================
    // Reapertura exitosa
    // =========================================================================

    public function test_reabrir_pedido_cerrado_sin_pago_retorna_ok(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoCerrado());
        $pedidoModel->method('update')->willReturn(true);

        $pagoModel = $this->createMock(PagoModel::class);
        $pagoModel->method('getPagoPorPedido')->willReturn(null); // sin pago

        $service = $this->makeServiceConEstadoMockeado(
            $pedidoModel,
            $pagoModel,
            $this->createMock(MesaModel::class)
        );

        $resultado = $service->reabrir(1);

        $this->assertTrue($resultado['ok']);
        $this->assertNull($resultado['error']);
    }

    // =========================================================================
    // Pedido no encontrado
    // =========================================================================

    public function test_reabrir_pedido_inexistente_retorna_error(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn(null);

        $resultado = $this->makeService(pedidoModel: $pedidoModel)->reabrir(9999);

        $this->assertFalse($resultado['ok']);
        $this->assertSame('El pedido no existe.', $resultado['error']);
    }

    // =========================================================================
    // Pedido ya abierto
    // =========================================================================

    public function test_reabrir_pedido_ya_abierto_retorna_error(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn([
            'id_pedido'    => 3,
            'fecha_cierre' => null, // pedido aún abierto
            'id_mesa'      => null,
        ]);

        $resultado = $this->makeService(pedidoModel: $pedidoModel)->reabrir(3);

        $this->assertFalse($resultado['ok']);
        $this->assertSame('El pedido ya está abierto.', $resultado['error']);
    }

    // =========================================================================
    // Pedido con pago registrado
    // =========================================================================

    public function test_reabrir_pedido_con_pago_registrado_retorna_error(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoCerrado());
        // update() no debe ser llamado si hay pago
        $pedidoModel->expects($this->never())->method('update');

        $pagoModel = $this->createMock(PagoModel::class);
        $pagoModel->method('getPagoPorPedido')->willReturn(['id_pago' => 17]); // ya tiene pago

        $resultado = $this->makeService(pedidoModel: $pedidoModel, pagoModel: $pagoModel)
            ->reabrir(1);

        $this->assertFalse($resultado['ok']);
        $this->assertStringContainsString('pago registrado', $resultado['error']);
    }

    // =========================================================================
    // Actualización de la mesa al reabrir
    // =========================================================================

    public function test_reabrir_pedido_con_mesa_marca_la_mesa_como_ocupada(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoCerrado(idMesa: 7));
        $pedidoModel->method('update')->willReturn(true);

        $pagoModel = $this->createMock(PagoModel::class);
        $pagoModel->method('getPagoPorPedido')->willReturn(null);

        $mesaModel = $this->createMock(MesaModel::class);
        // Debe llamarse exactamente una vez con estado 'ocupada'
        $mesaModel->expects($this->once())
            ->method('update')
            ->with(7, ['estado' => 'ocupada']);

        $service = $this->makeServiceConEstadoMockeado($pedidoModel, $pagoModel, $mesaModel);

        $resultado = $service->reabrir(1);

        $this->assertTrue($resultado['ok']);
    }

    public function test_reabrir_pedido_sin_mesa_no_actualiza_ninguna_mesa(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoCerrado(idMesa: null));
        $pedidoModel->method('update')->willReturn(true);

        $pagoModel = $this->createMock(PagoModel::class);
        $pagoModel->method('getPagoPorPedido')->willReturn(null);

        $mesaModel = $this->createMock(MesaModel::class);
        // update() no debe ser llamado si no hay mesa asociada
        $mesaModel->expects($this->never())->method('update');

        $service = $this->makeServiceConEstadoMockeado($pedidoModel, $pagoModel, $mesaModel);

        $resultado = $service->reabrir(1);

        $this->assertTrue($resultado['ok']);
    }
}
