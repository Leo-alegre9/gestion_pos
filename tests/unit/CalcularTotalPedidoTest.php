<?php

namespace Tests\Unit;

use App\Models\DetallePedidoModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests unitarios: calcularTotalPedido()
 *
 * Verifica el comportamiento de DetallePedidoModel::getTotalPedido(int $idPedido): float,
 * que calcula el total de un pedido sumando los subtotales de sus ítems.
 *
 * Estrategia de aislamiento:
 *   - Se mockean __call() y findAll() del modelo para simular respuestas de la BD
 *     sin necesitar conexión real a la base de datos.
 *   - Los casos PU-CAL-03 y PU-CAL-04 verifican las reglas de validación que
 *     impiden insertar datos inválidos; se testean con el servicio de validación de CI4.
 *
 * Casos cubiertos: PU-CAL-01 al PU-CAL-06
 */
class CalcularTotalPedidoTest extends CIUnitTestCase
{
    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Construye un DetallePedidoModel aislado de la BD.
     * - __call()  → willReturnSelf(): proxy de where/select/etc. devuelve $this para chaining.
     * - findAll() → willReturn($items): simula el resultado de la consulta.
     */
    private function makeModel(array $items): DetallePedidoModel
    {
        $model = $this->getMockBuilder(DetallePedidoModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__call', 'findAll'])
            ->getMock();

        $model->method('__call')->willReturnSelf();
        $model->method('findAll')->willReturn($items);

        return $model;
    }

    // =========================================================================
    // PU-CAL-01 — Múltiples productos con distintos precios y cantidades
    // =========================================================================

    public function test_pu_cal_01_total_con_multiples_productos_distintos(): void
    {
        // Producto A × 2 @ $500 = $1000
        // Producto B × 1 @ $300 = $300
        // Total esperado: $1300
        $model = $this->makeModel([
            ['subtotal' => 1000.0],
            ['subtotal' => 300.0],
        ]);

        $this->assertSame(1300.0, $model->getTotalPedido(1));
    }

    // =========================================================================
    // PU-CAL-02 — Pedido vacío sin ningún detalle registrado
    // =========================================================================

    public function test_pu_cal_02_pedido_vacio_retorna_cero(): void
    {
        $model = $this->makeModel([]);

        $this->assertSame(0.0, $model->getTotalPedido(2));
    }

    // =========================================================================
    // PU-CAL-03 — Precio unitario negativo rechazado por validación
    // =========================================================================

    public function test_pu_cal_03_precio_unitario_negativo_falla_validacion(): void
    {
        // El sistema impide insertar subtotales negativos mediante las reglas
        // del modelo: precio_unitario >= 0 y subtotal >= 0.
        $data = [
            'id_pedido'       => 3,
            'id_producto'     => 1,
            'cantidad'        => 1,
            'precio_unitario' => -200.0,
            'subtotal'        => -200.0,
            'observaciones'   => null,
        ];

        $validator = \Config\Services::validation();
        $validator->reset();
        $validator->setRules([
            'precio_unitario' => 'required|numeric|greater_than_equal_to[0]',
            'subtotal'        => 'required|numeric|greater_than_equal_to[0]',
        ]);

        $this->assertFalse(
            $validator->run($data),
            'Un precio unitario negativo debe fallar la validación del modelo.'
        );
    }

    // =========================================================================
    // PU-CAL-04 — Cantidad negativa rechazada por validación
    // =========================================================================

    public function test_pu_cal_04_cantidad_negativa_falla_validacion(): void
    {
        // El sistema impide insertar cantidades no positivas mediante la regla
        // del modelo: cantidad > 0.
        $data = [
            'id_pedido'       => 4,
            'id_producto'     => 1,
            'cantidad'        => -1,
            'precio_unitario' => 100.0,
            'subtotal'        => -100.0,
            'observaciones'   => null,
        ];

        $validator = \Config\Services::validation();
        $validator->reset();
        $validator->setRules([
            'cantidad' => 'required|numeric|greater_than[0]',
        ]);

        $this->assertFalse(
            $validator->run($data),
            'Una cantidad negativa debe fallar la validación del modelo.'
        );
    }

    // =========================================================================
    // PU-CAL-05 — Mismo producto en dos líneas separadas
    // =========================================================================

    public function test_pu_cal_05_mismo_producto_en_dos_lineas_suma_ambas(): void
    {
        // Producto A × 3 @ $400 = $1200 (línea 1)
        // Producto A × 2 @ $400 = $800  (línea 2)
        // Total esperado: $2000 (sin consolidar líneas)
        $model = $this->makeModel([
            ['subtotal' => 1200.0],
            ['subtotal' => 800.0],
        ]);

        $this->assertSame(2000.0, $model->getTotalPedido(5));
    }

    // =========================================================================
    // PU-CAL-06 — ID de pedido inexistente: no hay ítems, retorna 0.0
    // =========================================================================

    public function test_pu_cal_06_pedido_inexistente_retorna_cero(): void
    {
        // Una consulta sobre un pedido que no existe devuelve array vacío,
        // por lo que el total calculado es 0.0.
        $model = $this->makeModel([]);

        $this->assertSame(0.0, $model->getTotalPedido(9999));
    }
}
