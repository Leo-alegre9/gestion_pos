# Guía de Tests Unitarios — BarPOS

## Descripción general

Esta guía documenta el conjunto de tests unitarios del sistema BarPOS, organizados según los servicios de negocio que verifican. Todos los tests corren en memoria sin necesitar base de datos activa, gracias al uso de mocks de PHPUnit.

---

## Requisitos previos

| Requisito | Versión mínima |
|---|---|
| PHP | 8.1 |
| Composer | 2.x |
| PHPUnit (incluido en dev-dependencies) | 10.x |

Instalar dependencias si aún no están instaladas:

```bash
composer install
```

---

## Ejecutar los tests

### Toda la suite de tests unitarios

```bash
php vendor/bin/phpunit tests/unit/
```

### Con salida descriptiva (recomendado)

```bash
php vendor/bin/phpunit tests/unit/ --testdox
```

### Un archivo específico

```bash
php vendor/bin/phpunit tests/unit/CalcularTotalPedidoTest.php --testdox
php vendor/bin/phpunit tests/unit/AgregarProductoAlPedidoTest.php --testdox
php vendor/bin/phpunit tests/unit/RegistrarCobroTest.php --testdox
php vendor/bin/phpunit tests/unit/CerrarPedidoTest.php --testdox
php vendor/bin/phpunit tests/unit/RabrirPedidoTest.php --testdox
```

### Un test específico por nombre

```bash
php vendor/bin/phpunit tests/unit/CerrarPedidoTest.php --filter test_cerrar_pedido_con_mesa_libera_la_mesa
```

### Todos los tests del proyecto

```bash
php vendor/bin/phpunit
```

---

## Arquitectura de los tests

Cada test unitario sigue el mismo patrón:

1. **Arrange** — se construyen mocks de los modelos con `createMock()` o `getMockBuilder()`.
2. **Act** — se llama al método del servicio bajo prueba.
3. **Assert** — se verifica el array de resultado devuelto.

Los servicios reciben sus dependencias por constructor (inyección de dependencias), lo que permite reemplazarlas por mocks sin tocar la base de datos.

---

## Archivos de test

### `CalcularTotalPedidoTest.php`

**Servicio:** `DetallePedidoModel::getTotalPedido(int $idPedido): float`

Calcula el total de un pedido sumando los subtotales de sus ítems.

**Técnica de aislamiento:** `getMockBuilder()->onlyMethods(['__call', 'findAll'])` — se intercepta el proxy del query builder de CI4 (`__call`) para devolver datos de prueba sin consultar la BD.

| ID | Nombre del test | Escenario |
|---|---|---|
| PU-CAL-01 | `test_pu_cal_01_total_con_multiples_productos_distintos` | Dos productos distintos → suma correcta |
| PU-CAL-02 | `test_pu_cal_02_pedido_vacio_retorna_cero` | Sin ítems → `0.0` |
| PU-CAL-03 | `test_pu_cal_03_precio_unitario_negativo_falla_validacion` | Precio negativo → la validación del modelo lo rechaza |
| PU-CAL-04 | `test_pu_cal_04_cantidad_negativa_falla_validacion` | Cantidad negativa → la validación del modelo lo rechaza |
| PU-CAL-05 | `test_pu_cal_05_mismo_producto_en_dos_lineas_suma_ambas` | Mismo producto en dos líneas → suma sin consolidar |
| PU-CAL-06 | `test_pu_cal_06_pedido_inexistente_retorna_cero` | ID inexistente → query vacía → `0.0` |

---

### `AgregarProductoAlPedidoTest.php`

**Servicio:** `AgregarDetalleService::agregar(int $idPedido, int $idProducto, int $cantidad): array`

Incorpora un ítem al detalle de un pedido activo, validando pedido, producto, cantidad y stock.

**Técnica de aislamiento:** `createMock()` sobre `PedidoModel`, `ProductoModel`, `DetallePedidoModel` y `StockModel`.

| ID | Nombre del test | Escenario |
|---|---|---|
| PU-AGR-01 | `test_pu_agr_01_item_valido_inserta_en_detalle_pedidos` | Todo válido → ítem insertado, retorna `idDetalle` |
| PU-AGR-02 | `test_pu_agr_02_producto_inexistente_retorna_error` | Producto no existe → error, pedido sin cambios |
| PU-AGR-03 | `test_pu_agr_03_cantidad_cero_retorna_error_de_validacion` | Cantidad = 0 → error de validación |
| PU-AGR-04 | `test_pu_agr_04_cantidad_negativa_retorna_error_de_validacion` | Cantidad negativa → error de validación |
| PU-AGR-05 | `test_pu_agr_05_pedido_cerrado_retorna_error` | Pedido ya cerrado → error, ítem no registrado |
| PU-AGR-06 | `test_pu_agr_06_stock_insuficiente_retorna_error` | Stock < cantidad solicitada → error de stock |

---

### `RegistrarCobroTest.php`

**Servicio:** `RegistrarPagoService::registrar(int $idPedido, int $idMetodoPago): array`

Registra el pago de un pedido cerrado dentro de una transacción atómica.

**Técnica de aislamiento:**
- `createMock()` para validaciones tempranas (pedido, pago duplicado, método).
- `getMockBuilder()->onlyMethods(['persistirPago'])` para el caso exitoso (PU-REG-01), evitando la transacción real con BD.
- `expects()->once()->willReturnCallback()` para verificar que el monto se calcula server-side (PU-REG-06/07).

| ID | Nombre del test | Escenario |
|---|---|---|
| PU-REG-01 | `test_pu_reg_01_cobro_valido_retorna_id_pago_generado` | Pedido cerrado + método activo → pago registrado, retorna `idPago` |
| PU-REG-02 | `test_pu_reg_02_pedido_inexistente_retorna_error` | Pedido no existe → error, sin pago registrado |
| PU-REG-03 | `test_pu_reg_03_pedido_ya_cobrado_retorna_id_pago_existente` | Pago ya existe → retorna `pagoExistenteId`, sin duplicado |
| PU-REG-04 | `test_pu_reg_04_metodo_pago_inexistente_retorna_error` | Método de pago no existe → error de validación |
| PU-REG-05 | `test_pu_reg_05_pedido_abierto_retorna_error` | Pedido sin `fecha_cierre` → error |
| PU-REG-06 | `test_pu_reg_06_monto_calculado_es_igual_al_total_de_items` | Monto en el registro = suma de subtotales del pedido |
| PU-REG-07 | `test_pu_reg_07_monto_persistido_no_acepta_monto_del_cliente` | El servicio no acepta monto externo; siempre calcula server-side |

---

### `CerrarPedidoTest.php`

**Servicio:** `CerrarPedidoService::cerrar(int $idPedido): array`

Registra el cierre del pedido y libera la mesa asociada.

**Técnica de aislamiento:** `createMock()` sobre `PedidoModel` y `MesaModel`. Se usa `expects($this->never())` / `expects($this->once())` para verificar que `update()` se llama (o no) sobre la mesa.

| Nombre del test | Escenario |
|---|---|
| `test_cerrar_pedido_abierto_retorna_ok` | Pedido abierto → cierre exitoso |
| `test_cerrar_pedido_inexistente_retorna_error` | ID no existe → error |
| `test_cerrar_pedido_ya_cerrado_retorna_error` | `fecha_cierre` no es null → error, no llama a `cerrarPedido()` |
| `test_cerrar_pedido_falla_en_bd_retorna_error` | `cerrarPedido()` retorna false → error de BD |
| `test_cerrar_pedido_con_mesa_libera_la_mesa` | Tiene `id_mesa` → `MesaModel::update()` llamado con `estado = 'libre'` |
| `test_cerrar_pedido_sin_mesa_no_actualiza_ninguna_mesa` | Sin `id_mesa` → `MesaModel::update()` nunca llamado |

---

### `RabrirPedidoTest.php`

**Servicio:** `RabrirPedidoService::reabrir(int $idPedido): array`

Reabre un pedido cerrado sin pago, limpia `fecha_cierre` y vuelve la mesa a ocupada.

**Técnica de aislamiento:**
- `createMock()` para los casos de error temprano (pedido no existe, ya abierto, tiene pago).
- `getMockBuilder()->onlyMethods(['obtenerOCrearEstado'])` para los casos exitosos, evitando la consulta a `estados_pedido` en la BD.

| Nombre del test | Escenario |
|---|---|
| `test_reabrir_pedido_cerrado_sin_pago_retorna_ok` | Pedido cerrado sin pago → reapertura exitosa |
| `test_reabrir_pedido_inexistente_retorna_error` | ID no existe → error |
| `test_reabrir_pedido_ya_abierto_retorna_error` | `fecha_cierre` es null → error |
| `test_reabrir_pedido_con_pago_registrado_retorna_error` | Tiene pago → error, `update()` nunca llamado |
| `test_reabrir_pedido_con_mesa_marca_la_mesa_como_ocupada` | Tiene `id_mesa` → `MesaModel::update()` con `estado = 'ocupada'` |
| `test_reabrir_pedido_sin_mesa_no_actualiza_ninguna_mesa` | Sin `id_mesa` → `MesaModel::update()` nunca llamado |

---

## Servicios creados para los tests

Los tests de `AgregarProductoAlPedido`, `CerrarPedido` y `RabrirPedido` requirieron extraer la lógica de los controladores a servicios independientes:

| Servicio | Fuente original | Descripción |
|---|---|---|
| `AgregarDetalleService` | `PedidoController::agregarDetalle()` | Agrega un ítem al detalle de un pedido activo |
| `CerrarPedidoService` | `PedidoController::cerrarPedido()` | Cierra un pedido y libera la mesa |
| `RabrirPedidoService` | `PedidoController::reabrirPedido()` | Reabre un pedido cerrado sin pago |

---

## Resultado esperado

Al ejecutar `php vendor/bin/phpunit tests/unit/ --testdox` la salida debe mostrar:

```
Agregar Producto Al Pedido   6 ✔
Calcular Total Pedido        6 ✔
Cerrar Pedido                6 ✔
Health                       2 ✔
Preparar Pago Service        6 ✔
Rabrir Pedido                6 ✔
Registrar Cobro              7 ✔
Registrar Pago Service       5 ✔
─────────────────────────────────
Total                       44 tests, 0 fallos
```

> El warning `No code coverage driver available` es esperado si no está instalado Xdebug o PCOV. No afecta los resultados.
