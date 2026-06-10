# Plan de Pruebas

---

## Información General

| Campo                    | Detalle                          |
|--------------------------|----------------------------------|
| **Sistema**              | gestor_pos — Sistema POS para Bar/Cafetería |
| **Versión del caso de prueba** | 1.0                        |
| **Autor**                | Leonel Alegre                    |
| **Nombre del probador**  | Leonel Alegre                    |
| **Fecha de creación**    | 2026-05-21                       |
| **Fecha de ejecución**   | 2026-05-21                       |

---

## Alcance

El presente plan de pruebas cubre la verificación funcional y unitaria del sistema **gestor_pos**, una aplicación de punto de venta destinada a la gestión de pedidos, cobros, productos, mesas y métodos de pago en entornos de bar y cafetería.

Las pruebas están organizadas en dos grandes bloques:

1. **Plan de pruebas funcionales** — verifica el comportamiento del sistema desde la perspectiva del usuario final, evaluando flujos completos de negocio.
2. **Plan de pruebas unitarias** — verifica el comportamiento aislado de los métodos críticos del sistema, independientemente de la interfaz de usuario.

---

# 1. Plan de Pruebas Funcionales

---

## 1.1. Gestión de Pedidos

La siguiente sección contempla los casos de prueba asociados al ciclo de vida completo de un pedido dentro del sistema: desde su creación hasta su cierre, incluyendo la manipulación de sus productos y la consulta del historial.

| Nro. | Objetivo | Datos de entrada | Resultado esperado | Obtenido |
|------|----------|------------------|--------------------|----------|
| PF-PED-01 | Crear un nuevo pedido válido con tipo de pedido `mesa` y mesa disponible | Tipo: `mesa`, Mesa Nro.: 3 (estado: libre), Usuario autenticado | El sistema registra el pedido con estado `abierto`, asigna la fecha de apertura y cambia el estado de la mesa a `ocupada`. Redirige al detalle del pedido. | El sistema registra correctamente el pedido. |
| PF-PED-02 | Crear un pedido de tipo `barra` sin seleccionar mesa | Tipo: `barra`, Sin mesa seleccionada, Usuario autenticado | El sistema registra el pedido con estado `abierto` sin mesa asociada. Redirige al detalle del pedido. | El sistema registra correctamente el pedido. |
| PF-PED-03 | Intentar crear un pedido de tipo `mesa` sin seleccionar una mesa | Tipo: `mesa`, Sin mesa seleccionada | El sistema muestra un mensaje de error indicando que se debe seleccionar una mesa para pedidos de tipo `mesa`. No se crea el pedido. | El sistema muestra mensaje de error correspondiente. |
| PF-PED-04 | Agregar un producto válido y activo a un pedido abierto | ID pedido: existente y abierto, ID producto: existente y activo, Cantidad: 2 | El sistema registra el ítem en el detalle del pedido con el precio unitario y subtotal correctos. Muestra mensaje de confirmación. | El sistema registra correctamente el producto en el pedido. |
| PF-PED-05 | Intentar agregar un producto inexistente a un pedido abierto | ID pedido: existente y abierto, ID producto: 9999 (no existe) | El sistema muestra un mensaje de error indicando que el producto no fue encontrado. El detalle del pedido no se modifica. | El sistema muestra mensaje de error correspondiente. |
| PF-PED-06 | Eliminar un producto existente del detalle de un pedido abierto | ID pedido: existente y abierto, ID detalle: perteneciente al pedido | El sistema elimina el ítem del detalle del pedido y muestra un mensaje de confirmación. | El sistema registra correctamente la eliminación del producto. |
| PF-PED-07 | Intentar eliminar un ítem de un pedido ya cerrado | ID pedido: cerrado, ID detalle: perteneciente al pedido | El sistema muestra un mensaje de error indicando que el pedido ya está cerrado. El detalle no se modifica. | El sistema impide realizar la operación inválida. |
| PF-PED-08 | Consultar el detalle de un pedido existente | ID pedido: existente | El sistema presenta el encabezado del pedido, la lista de ítems con cantidades, precios unitarios y subtotales, el total acumulado y el estado de pago. | El sistema muestra correctamente el detalle del pedido. |
| PF-PED-09 | Cerrar un pedido abierto con ítems registrados | ID pedido: abierto con al menos un ítem | El sistema registra la fecha y hora de cierre, actualiza el estado a `cerrado` y, si tiene mesa asociada, la cambia a `libre`. | El sistema registra correctamente el cierre del pedido. |
| PF-PED-10 | Intentar cerrar un pedido que ya fue cerrado previamente | ID pedido: ya cerrado | El sistema muestra un mensaje de error indicando que el pedido ya fue cerrado. No se realizan modificaciones. | El sistema impide realizar la operación inválida. |
| PF-PED-11 | Consultar el historial de pedidos cerrados para la fecha actual | Fecha: fecha del día en curso | El sistema lista todos los pedidos con `fecha_cierre` correspondiente a la fecha indicada, con información del usuario, mesa, estado y pago asociado (si existe). | El sistema muestra correctamente el historial de pedidos. |
| PF-PED-12 | Intentar crear un pedido de tipo `mesa` con una mesa que está en estado `ocupada` | Tipo: `mesa`, Mesa: ocupada | El sistema muestra un mensaje de error indicando que la mesa seleccionada no está disponible. No se crea el pedido. | El sistema impide realizar la operación inválida. |

---

## 1.2. Gestión de Cobros

La siguiente sección contempla los casos de prueba asociados al proceso de cobro de un pedido cerrado, incluyendo el registro del pago, la selección del método de pago, la generación del comprobante y las validaciones de integridad.

| Nro. | Objetivo | Datos de entrada | Resultado esperado | Obtenido |
|------|----------|------------------|--------------------|----------|
| PF-COB-01 | Cobrar un pedido cerrado y sin pago registrado | ID pedido: cerrado sin pago, Método de pago: `Efectivo` | El sistema registra el pago con el monto calculado server-side, actualiza el estado del pedido a `pagado` dentro de una transacción y redirige al comprobante. | El sistema registra correctamente el pago. |
| PF-COB-02 | Verificar que el total calculado sea correcto para un pedido con múltiples ítems | ID pedido: con 3 ítems de distintos precios y cantidades | El total presentado en el formulario de cobro coincide con la suma de los subtotales de cada ítem calculada desde la base de datos. | El sistema calcula y muestra el total correctamente. |
| PF-COB-03 | Registrar un pago con método `Efectivo` | ID pedido: cerrado, Método: `Efectivo` | El sistema registra el pago con el método seleccionado, almacena la fecha y hora del pago y redirige al comprobante de la operación. | El sistema registra correctamente el pago en efectivo. |
| PF-COB-04 | Registrar un pago con método `Tarjeta de crédito` | ID pedido: cerrado, Método: `Tarjeta de crédito` | El sistema registra el pago con el método seleccionado, almacena la fecha y hora del pago y redirige al comprobante de la operación. | El sistema registra correctamente el pago con tarjeta. |
| PF-COB-05 | Confirmar que el comprobante de cobro se genera correctamente | ID pago: existente | El sistema presenta el comprobante con número de factura formateado, datos del pedido, lista de ítems, total del pedido, monto abonado y vuelto (si corresponde). | El sistema genera y muestra correctamente el comprobante. |
| PF-COB-06 | Intentar cobrar un pedido que no existe | ID pedido: 9999 (inexistente) | El sistema muestra un mensaje de error indicando que el pedido no existe o no está disponible para cobro. No se registra ningún pago. | El sistema muestra mensaje de error correspondiente. |
| PF-COB-07 | Intentar cobrar un pedido que ya tiene un pago registrado | ID pedido: ya cobrado | El sistema detecta el pago existente y redirige automáticamente al comprobante ya generado, sin registrar un nuevo pago. | El sistema impide realizar la operación inválida. |
| PF-COB-08 | Intentar acceder al formulario de cobro de un pedido que aún está abierto | ID pedido: abierto (sin fecha de cierre) | El sistema muestra un mensaje de error indicando que el pedido debe cerrarse antes de proceder al cobro. | El sistema impide realizar la operación inválida. |
| PF-COB-09 | Intentar registrar un pago con un método de pago inexistente o inactivo | ID pedido: cerrado, ID método de pago: 9999 (no existe) | El sistema muestra un mensaje de error de validación indicando que el método de pago seleccionado no es válido. No se registra el pago. | El sistema muestra mensaje de error correspondiente. |
| PF-COB-10 | Verificar que el comprobante refleja el vuelto cuando el monto abonado supera el total | ID pago: con monto mayor al total del pedido | El comprobante muestra el campo `Vuelto` con el importe positivo correspondiente a la diferencia entre el monto abonado y el total del pedido. | El sistema calcula y muestra el vuelto correctamente. |
| PF-COB-11 | Verificar que el sistema no permite registrar un pago con monto igual a cero o negativo | ID pedido: cerrado, Monto enviado: `0` | El sistema rechaza el registro del pago por fallo en las reglas de validación del modelo y muestra los errores correspondientes. | El sistema impide realizar la operación inválida. |
| PF-COB-12 | Verificar la integridad transaccional del registro de pago | ID pedido: cerrado, Método: `Efectivo`, Simulación de fallo en segunda operación de la transacción | Ante un error durante la transacción, el sistema revierte todas las operaciones pendientes y no persiste ningún cambio parcial en la base de datos. | El sistema garantiza la integridad transaccional correctamente. |

---

# 2. Plan de Pruebas Unitarias

---

## Descripción General

Las pruebas unitarias documentadas en esta sección evalúan el comportamiento aislado de los tres métodos más críticos del sistema en relación a la gestión de pedidos y cobros. Cada método se prueba con distintos conjuntos de parámetros de entrada, incluyendo casos válidos, casos límite y casos de error, con el objetivo de verificar que la lógica de negocio encapsulada en cada método responde de manera correcta y predecible ante cualquier escenario de uso.

Los métodos seleccionados son:

- `calcularTotalPedido()` — cálculo del total de un pedido a partir de sus ítems.
- `agregarProductoAlPedido()` — incorporación de un ítem al detalle de un pedido abierto.
- `registrarCobro()` — registro de un pago asociado a un pedido cerrado.

---

## 2.1. Método: `calcularTotalPedido()`

**Descripción:** Calcula el total de un pedido sumando los subtotales de cada uno de sus ítems. Recibe el identificador del pedido y retorna un valor de tipo `float` con el monto acumulado. Si el pedido no tiene ítems, debe retornar `0.0`. No debe admitir subtotales negativos como entradas válidas.

| ID Prueba | ID pedido | Ítems del pedido | Descripción | Resultado esperado |
|-----------|-----------|------------------|-------------|--------------------|
| PU-CAL-01 | 1 | Producto A × 2 @ $500, Producto B × 1 @ $300 | Pedido con múltiples productos de distintos precios y cantidades | Retorna `1300.0` |
| PU-CAL-02 | 2 | Sin ítems | Pedido vacío sin ningún detalle registrado | Retorna `0.0` |
| PU-CAL-03 | 3 | Producto C × 1 @ `-200` | Ítem con precio unitario negativo | Retorna error o excepción; el sistema no admite subtotales negativos |
| PU-CAL-04 | 4 | Producto D × `-1` @ $100 | Ítem con cantidad negativa | Retorna error o excepción; la cantidad debe ser mayor a cero |
| PU-CAL-05 | 5 | Producto A × 3 @ $400, Producto A × 2 @ $400 | Mismo producto registrado en dos líneas de detalle separadas | Retorna `2000.0` (suma ambas líneas sin consolidar) |
| PU-CAL-06 | 9999 | — | ID de pedido inexistente en la base de datos | Retorna `0.0` o lanza excepción según la implementación |

---

## 2.2. Método: `agregarProductoAlPedido()`

**Descripción:** Incorpora un ítem (producto + cantidad) al detalle de un pedido activo. Verifica que el pedido exista y esté en estado abierto, que el producto exista y esté activo, y que la cantidad sea un valor entero positivo. En sistemas con control de stock, también verifica que haya suficiente disponibilidad antes de registrar el ítem.

| ID Prueba | ID pedido | ID producto | Cantidad | Descripción | Resultado esperado |
|-----------|-----------|-------------|----------|-------------|--------------------|
| PU-AGR-01 | 1 (abierto) | 10 (activo) | 2 | Producto válido y activo con cantidad positiva en pedido abierto | El ítem se inserta en `detalle_pedidos` con subtotal calculado correctamente |
| PU-AGR-02 | 1 (abierto) | 9999 (inexistente) | 1 | Producto que no existe en la base de datos | Retorna error indicando que el producto no fue encontrado; no se modifica el pedido |
| PU-AGR-03 | 1 (abierto) | 10 (activo) | 0 | Cantidad igual a cero | Retorna error de validación; la cantidad debe ser mayor a cero |
| PU-AGR-04 | 1 (abierto) | 10 (activo) | -3 | Cantidad negativa | Retorna error de validación; la cantidad no puede ser un valor negativo |
| PU-AGR-05 | 2 (cerrado) | 10 (activo) | 1 | Intento de agregar un ítem a un pedido que ya fue cerrado | Retorna error indicando que el pedido no está abierto; no se registra el ítem |
| PU-AGR-06 | 1 (abierto) | 15 (activo, stock insuficiente) | 100 | Cantidad solicitada superior al stock disponible del producto | Retorna error indicando stock insuficiente; no se registra el ítem |

---

## 2.3. Método: `registrarCobro()`

**Descripción:** Registra el pago de un pedido cerrado. Verifica que el pedido exista y tenga fecha de cierre, que no exista un pago previo registrado para ese pedido, que el método de pago sea válido y esté activo, y que el monto sea mayor a cero. Persiste el pago y actualiza el estado del pedido dentro de una transacción atómica.

| ID Prueba | ID pedido | ID método de pago | Descripción | Resultado esperado |
|-----------|-----------|-------------------|-------------|--------------------|
| PU-REG-01 | 1 (cerrado, sin pago) | 1 (Efectivo, activo) | Cobro válido sobre pedido cerrado con método de pago activo | Inserta un registro en `pagos`, actualiza el estado del pedido a `pagado` y retorna el ID del pago generado |
| PU-REG-02 | 9999 (inexistente) | 1 (Efectivo) | Pedido que no existe en la base de datos | Retorna error indicando que el pedido no fue encontrado; no se registra ningún pago |
| PU-REG-03 | 2 (cerrado, ya cobrado) | 1 (Efectivo) | Pedido que ya tiene un pago registrado | Retorna el ID del pago existente sin insertar un duplicado |
| PU-REG-04 | 1 (cerrado, sin pago) | 9999 (inexistente) | Método de pago que no existe en la tabla `metodos_pago` | Retorna error de validación; no se registra el pago |
| PU-REG-05 | 3 (abierto, sin fecha de cierre) | 1 (Efectivo) | Intento de cobrar un pedido que aún no fue cerrado | Retorna error indicando que el pedido debe cerrarse antes de registrar el pago |
| PU-REG-06 | 1 (cerrado, sin pago) | 1 (Efectivo) | Pedido con total calculado server-side igual al subtotal exacto de sus ítems | Registra el pago con el monto calculado internamente; no acepta el monto enviado por el cliente |
| PU-REG-07 | 1 (cerrado, sin pago) | 2 (Tarjeta, activo) | Monto abonado mayor al total del pedido | Registra el pago con el monto calculado del pedido; el vuelto se calcula a nivel de comprobante, no en la persistencia |
