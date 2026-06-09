<?php

namespace Tests\Unit;

use App\Models\MesaModel;
use App\Models\PedidoModel;
use App\Services\CerrarPedidoService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests unitarios: cerrarPedido()
 *
 * Verifica el comportamiento de CerrarPedidoService::cerrar(),
 * que registra el cierre de un pedido validando:
 *   - Que el pedido exista.
 *   - Que el pedido esté abierto (sin fecha_cierre).
 *   - Que el cierre en BD sea exitoso.
 *   - Que la mesa asociada pase a estado 'libre' al cerrar.
 *
 * Todos los modelos se reemplazan por mocks para aislar la lógica de negocio
 * sin necesitar conexión a la base de datos.
 */
class CerrarPedidoTest extends CIUnitTestCase
{
    // =========================================================================
    // Helpers
    // =========================================================================

    private function makeService(
        ?PedidoModel $pedidoModel = null,
        ?MesaModel $mesaModel = null
    ): CerrarPedidoService {
        return new CerrarPedidoService(
            $pedidoModel ?? $this->createMock(PedidoModel::class),
            $mesaModel   ?? $this->createMock(MesaModel::class),
        );
    }

    /** Pedido abierto sin mesa asociada. */
    private function pedidoAbierto(int $idPedido = 1, ?int $idMesa = null): array
    {
        return ['id_pedido' => $idPedido, 'fecha_cierre' => null, 'id_mesa' => $idMesa];
    }

    // =========================================================================
    // Cierre exitoso
    // =========================================================================

    public function test_cerrar_pedido_abierto_retorna_ok(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoAbierto());
        $pedidoModel->method('cerrarPedido')->willReturn(true);

        $resultado = $this->makeService(pedidoModel: $pedidoModel)->cerrar(1);

        $this->assertTrue($resultado['ok']);
        $this->assertNull($resultado['error']);
    }

    // =========================================================================
    // Pedido no encontrado
    // =========================================================================

    public function test_cerrar_pedido_inexistente_retorna_error(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn(null);

        $resultado = $this->makeService(pedidoModel: $pedidoModel)->cerrar(9999);

        $this->assertFalse($resultado['ok']);
        $this->assertSame('El pedido no existe.', $resultado['error']);
    }

    // =========================================================================
    // Pedido ya cerrado
    // =========================================================================

    public function test_cerrar_pedido_ya_cerrado_retorna_error(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn([
            'id_pedido'    => 2,
            'fecha_cierre' => '2026-06-01 21:00:00', // ya fue cerrado
            'id_mesa'      => null,
        ]);
        // cerrarPedido() no debe ser llamado si el pedido ya está cerrado
        $pedidoModel->expects($this->never())->method('cerrarPedido');

        $resultado = $this->makeService(pedidoModel: $pedidoModel)->cerrar(2);

        $this->assertFalse($resultado['ok']);
        $this->assertSame('El pedido ya fue cerrado.', $resultado['error']);
    }

    // =========================================================================
    // Error de persistencia en BD
    // =========================================================================

    public function test_cerrar_pedido_falla_en_bd_retorna_error(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoAbierto());
        $pedidoModel->method('cerrarPedido')->willReturn(false); // falla en BD

        $resultado = $this->makeService(pedidoModel: $pedidoModel)->cerrar(1);

        $this->assertFalse($resultado['ok']);
        $this->assertStringContainsString('Error al cerrar', $resultado['error']);
    }

    // =========================================================================
    // Liberación de la mesa al cerrar
    // =========================================================================

    public function test_cerrar_pedido_con_mesa_libera_la_mesa(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoAbierto(idMesa: 5));
        $pedidoModel->method('cerrarPedido')->willReturn(true);

        $mesaModel = $this->createMock(MesaModel::class);
        // Debe llamarse exactamente una vez con estado 'libre'
        $mesaModel->expects($this->once())
            ->method('update')
            ->with(5, ['estado' => 'libre']);

        $resultado = $this->makeService($pedidoModel, $mesaModel)->cerrar(1);

        $this->assertTrue($resultado['ok']);
    }

    public function test_cerrar_pedido_sin_mesa_no_actualiza_ninguna_mesa(): void
    {
        $pedidoModel = $this->createMock(PedidoModel::class);
        $pedidoModel->method('find')->willReturn($this->pedidoAbierto(idMesa: null));
        $pedidoModel->method('cerrarPedido')->willReturn(true);

        $mesaModel = $this->createMock(MesaModel::class);
        // update() no debe ser llamado si no hay mesa asociada
        $mesaModel->expects($this->never())->method('update');

        $resultado = $this->makeService($pedidoModel, $mesaModel)->cerrar(1);

        $this->assertTrue($resultado['ok']);
    }
}
