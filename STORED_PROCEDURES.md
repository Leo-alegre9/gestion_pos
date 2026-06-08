# Procedimientos Almacenados — BarPOS

Base de datos: **MariaDB** (XAMPP)  
Archivo SQL: `stored_procedures.sql` (también incluidos al final de `bar_pos.sql`)

---

## Cómo importarlos

```bash
# Opción A — archivo independiente (recomendado para re-ejecución)
mysql -u root bar_pos < stored_procedures.sql

# Opción B — desde phpMyAdmin
#   Importar > seleccionar stored_procedures.sql > Ejecutar
```

> Ambos archivos incluyen `DROP PROCEDURE IF EXISTS` antes de cada `CREATE`,
> por lo que son seguros de re-ejecutar en cualquier momento.

---

## `sp_resumen_dashboard`

### Propósito

Devuelve los **cinco KPIs escalares** del dashboard en una única fila,
reemplazando cuatro queries independientes que antes se ejecutaban por separado
en `DashboardController::index()`.

### Firma

```sql
CALL sp_resumen_dashboard(IN p_fecha DATE)
```

| Parámetro | Tipo   | Descripción                            |
|-----------|--------|----------------------------------------|
| `p_fecha` | `DATE` | Fecha a consultar (normalmente `NOW()`) |

### Resultado (una fila)

| Columna         | Tipo          | Descripción                                      |
|-----------------|---------------|--------------------------------------------------|
| `ventas_hoy`    | `DECIMAL`     | Suma de `pagos.monto` registrados en `p_fecha`   |
| `pedidos_hoy`   | `INT`         | Pedidos cuya `fecha_apertura` cae en `p_fecha`   |
| `alertas_stock` | `INT`         | Productos con `cantidad_disponible < cantidad_minima` |
| `mesas_ocupadas`| `INT`         | Mesas en estado `'ocupada'` en este momento      |
| `mesas_total`   | `INT`         | Total de mesas registradas                       |

### Tablas que consulta (solo lectura)

`pagos` · `pedidos` · `stock` · `mesas`

### Cómo se llama desde PHP (CodeIgniter 4)

```php
// DashboardController::index()
$kpis = $db->query('CALL sp_resumen_dashboard(?)', [date('Y-m-d')])->getRowArray();

$stats = [
    'ventas_hoy'     => (float) $kpis['ventas_hoy'],
    'pedidos_hoy'    => (int)   $kpis['pedidos_hoy'],
    'mesas_ocupadas' => (int)   $kpis['mesas_ocupadas'],
    'mesas_total'    => (int)   $kpis['mesas_total'],
    'alertas_stock'  => (int)   $kpis['alertas_stock'],
];
```

### Ejemplo de uso directo en MariaDB

```sql
CALL sp_resumen_dashboard(CURDATE());

-- ventas_hoy | pedidos_hoy | alertas_stock | mesas_ocupadas | mesas_total
-- -----------|-------------|---------------|----------------|------------
--    4250.00 |          12 |             3 |              5 |          8
```

---

## `sp_cerrar_pedido`

### Propósito

Cierra un pedido en una **única transacción atómica**, ejecutando los seis pasos
que antes estaban repartidos entre `PedidoController::cerrarPedido()` y código
PHP suelto. Además incorpora el **descuento de stock y el registro de movimiento**,
que el código PHP anterior omitía completamente.

### Firma

```sql
CALL sp_cerrar_pedido(IN p_id_pedido INT)
```

| Parámetro     | Tipo  | Descripción          |
|---------------|-------|----------------------|
| `p_id_pedido` | `INT` | ID del pedido a cerrar |

### Resultado (una fila)

| Columna      | Tipo      | Descripción                                          |
|--------------|-----------|------------------------------------------------------|
| `resultado`  | `VARCHAR` | `'OK'` o código de error (ver tabla de errores)     |
| `total`      | `DECIMAL` | Total calculado del pedido (`0.00` si hubo error)   |

#### Códigos de error

| Código                | Causa                                                 |
|-----------------------|-------------------------------------------------------|
| `ERROR:no_existe`     | No existe ningún pedido con `p_id_pedido`             |
| `ERROR:ya_cerrado`    | El pedido ya tiene `fecha_cierre` registrada          |
| `ERROR:transaccion`   | Fallo interno durante la transacción (ROLLBACK hecho) |

### Pasos internos (en orden)

1. **Verifica** que el pedido existe y está abierto (`fecha_cierre IS NULL`).
2. **Calcula** el total sumando `detalle_pedidos.subtotal`.
3. **Obtiene** el `id_estado_pedido` correspondiente al estado `'cerrado'`.
4. **Inicia transacción** → los pasos 4–7 son atómicos.
5. **Actualiza `pedidos`**: registra `fecha_cierre = NOW()` y cambia estado.
6. **Libera la mesa**: si el pedido tiene `id_mesa`, la pone en estado `'libre'`.
7. **Descuenta stock**: `UPDATE stock ... INNER JOIN detalle_pedidos` reduce
   `cantidad_disponible` para cada ítem (mínimo 0 con `GREATEST`).
8. **Registra movimiento**: inserta una fila `'salida'` por cada ítem en
   `movimientos_stock`, vinculada al `id_pedido` y al `id_usuario` del pedido.
9. **COMMIT** si no hubo errores; **ROLLBACK** si `SQLEXCEPTION` fue capturada.

### Tablas que modifica

| Tabla               | Operación  | Condición               |
|---------------------|------------|-------------------------|
| `pedidos`           | `UPDATE`   | Siempre                 |
| `mesas`             | `UPDATE`   | Solo si `id_mesa != NULL` |
| `stock`             | `UPDATE`   | Una fila por ítem       |
| `movimientos_stock` | `INSERT`   | Una fila por ítem       |

### Cómo se llama desde PHP (CodeIgniter 4)

```php
// PedidoController::cerrarPedido()
$db     = \Config\Database::connect();
$result = $db->query('CALL sp_cerrar_pedido(?)', [$idPedido])->getRowArray();

if ($result['resultado'] !== 'OK') {
    $msg = match($result['resultado']) {
        'ERROR:no_existe'   => 'El pedido no existe.',
        'ERROR:ya_cerrado'  => 'El pedido ya ha sido cerrado.',
        'ERROR:transaccion' => 'Error al cerrar el pedido. Intentá nuevamente.',
        default             => 'Error al cerrar el pedido.',
    };
    return redirect()->back()->with('error', $msg);
}

// $result['total'] disponible si se necesita mostrar el monto al cobrar
return redirect()->to('/pedidos/detalles/' . $idPedido)
    ->with('success', 'Pedido cerrado correctamente.');
```

### Ejemplo de uso directo en MariaDB

```sql
CALL sp_cerrar_pedido(42);

-- resultado | total
-- ----------|-------
-- OK        | 875.50
```

---

## Comparativa antes / después

### `DashboardController::index()`

| | Antes | Después |
|---|---|---|
| Queries para KPIs | 4 queries independientes | 1 llamada `CALL sp_resumen_dashboard(?)` |
| `ventas_hoy` | `$db->table('pagos')->selectSum(...)` | `$kpis['ventas_hoy']` |
| `pedidos_hoy` | `$db->table('pedidos')->countAllResults()` | `$kpis['pedidos_hoy']` |
| `alertas_stock` | `$db->table('stock')->countAllResults()` | `$kpis['alertas_stock']` |
| `mesas_ocupadas` | Contador en loop PHP | `$kpis['mesas_ocupadas']` |

### `PedidoController::cerrarPedido()`

| | Antes | Después |
|---|---|---|
| Pasos | 2 UPDATE en PHP | 1 `CALL sp_cerrar_pedido(?)` |
| Transacción | No | Sí (atómica) |
| Descuento de stock | **No** (bug) | Sí (paso 7 del SP) |
| Movimiento registrado | **No** (bug) | Sí (paso 8 del SP) |
| Manejo de errores | `if (!$model->update(...))` | Código de resultado del SP |
