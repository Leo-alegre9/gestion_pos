# Contratos de Operación — Funcionalidad "Cobrar"

---

## Contrato 1: `mostrarFormularioDePago(idPedido)`

### 1. Nombre
`mostrarFormularioDePago(idPedido: int)`

### 2. Referencia Cruzada
- Caso de Uso: *Registrar Pago de Pedido*
- Ruta: `GET /pagos/formulario/{idPedido}`
- Controlador: `PagoController::mostrarFormularioDePago()`
- Servicios: `PrepararPagoService::preparar()`
- Modelos: `PedidoModel::getPedidoConDetalles()`, `PagoModel::getPagoPorPedido()`, `DetallePedidoModel::getDetallesPorPedido()`, `MetodoPagoModel::getActivos()`
- Vista: `app/Views/pagos/pagar.php`

### 3. Responsabilidades
Recuperar los datos del pedido identificado por `idPedido`, calcular el total a cobrar sumando los subtotales de todos los ítems (`detalle_pedidos`), y presentar al operador el formulario de cobro con los métodos de pago activos disponibles.

### 4. Excepciones
- El pedido no existe en la base de datos → se establece mensaje de error en sesión y se redirige a `/pedidos`.
- El pedido no está en estado *cerrado* (sin `fecha_cierre`) → se establece mensaje de error y se redirige a `/pedidos`.
- Ya existe un pago registrado para ese pedido → se redirige directamente a `/pagos/comprobante/{pagoExistenteId}` sin mostrar el formulario.

### 5. Pre-condiciones
- El pedido `idPedido` existe en la tabla `pedidos`.
- El pedido tiene `fecha_cierre IS NOT NULL` (fue cerrado previamente por `PedidoController::cerrar()`).
- No existe ningún registro en `pagos` con ese `id_pedido`.
- Existe al menos un método de pago activo (`activo = 1`) en `metodos_pago`.

### 6. Post-condiciones
- No se modifica ningún dato en la base de datos (operación de sólo lectura).
- Se presenta la vista `pagos/pagar.php` con los datos del pedido, el listado de ítems, el total calculado y los métodos de pago disponibles.
- El total mostrado es calculado del lado del servidor a partir de `detalle_pedidos.subtotal`, sin confiar en ningún valor del cliente.

---

## Contrato 2: `procesarRegistroDePago(idPedido, idMetodoPago)`

### 1. Nombre
`procesarRegistroDePago(idPedido: int, idMetodoPago: int)`

### 2. Referencia Cruzada
- Caso de Uso: *Registrar Pago de Pedido*
- Ruta: `POST /pagos/procesar/{idPedido}`
- Controlador: `PagoController::procesarRegistroDePago()`
- Servicio: `RegistrarPagoService::registrar()`
- Métodos internos: `RegistrarPagoService::obtenerOCrearEstado('pagado')`
- Modelos: `PedidoModel::find()`, `PagoModel::getPagoPorPedido()`, `PagoModel::validate()`, `PagoModel::insert()`, `PedidoModel::update()`, `DetallePedidoModel::getDetallesPorPedido()`
- Vista: redirección a `mostrarComprobanteDePago()`

### 3. Responsabilidades
Validar que el pedido existe y está en condiciones de ser cobrado, recalcular el monto total del lado del servidor a partir de los ítems registrados, ejecutar en forma atómica la inserción del registro de pago en la tabla `pagos` y la actualización del estado del pedido a *pagado* en `pedidos`, y redirigir al comprobante del pago generado.

### 4. Excepciones
- El pedido no existe → mensaje de error en sesión, redirección a `/pedidos`.
- El pedido no está cerrado (sin `fecha_cierre`) → mensaje de error, redirección a `/pedidos`.
- Ya existe un pago para ese pedido → redirección a `/pagos/comprobante/{pagoExistenteId}` (idempotencia: no duplica el pago).
- `id_metodo_pago` no supera las reglas de validación de `PagoModel` (requerido, entero > 0) → mensaje de error con detalles de validación, redirección al formulario.
- El monto calculado es ≤ 0 (pedido sin ítems) → validación de `PagoModel` falla, redirección al formulario.
- Error durante la transacción de base de datos → se hace rollback, mensaje de error, redirección a `/pedidos`.

### 5. Pre-condiciones
- El pedido `idPedido` existe y tiene `fecha_cierre IS NOT NULL`.
- No existe ningún registro previo en `pagos` para `id_pedido`.
- `idMetodoPago` corresponde a un método de pago existente en `metodos_pago`.
- La tabla `detalle_pedidos` tiene al menos un ítem asociado al pedido con `subtotal > 0`.
- El estado *pagado* existe o puede crearse en `estados_pedido` (lo garantiza `obtenerOCrearEstado()`).

### 6. Post-condiciones
- Se crea un nuevo registro en `pagos` con: `id_pedido`, `id_metodo_pago`, `monto` (calculado del servidor), `fecha_pago = NOW()`.
- El registro en `pedidos` para `idPedido` tiene su `id_estado_pedido` actualizado al id del estado *pagado*.
- Ambas operaciones se ejecutan dentro de una transacción atómica (si una falla, la otra se revierte).
- El usuario es redirigido a `GET /pagos/comprobante/{idPago}` con el id del pago recién creado.
- El pedido no puede volver a ser cobrado (la pre-condición de unicidad de pago queda satisfecha).

---

## Contrato 3: `mostrarComprobanteDePago(idPago)`

### 1. Nombre
`mostrarComprobanteDePago(idPago: int)`

### 2. Referencia Cruzada
- Caso de Uso: *Registrar Pago de Pedido* (paso final) / *Consultar Comprobante*
- Ruta: `GET /pagos/comprobante/{idPago}`
- Controlador: `PagoController::mostrarComprobanteDePago()`
- Servicio: `ComprobanteService::obtenerDatos()`
- Modelos: `PagoModel::getPagoConMetodo()`, `PedidoModel::getPedidoConDetalles()`, `DetallePedidoModel::getDetallesPorPedido()`
- Vista: `app/Views/pagos/recibo.php`

### 3. Responsabilidades
Recuperar el registro de pago junto con su método de pago, los datos completos del pedido asociado y el detalle de sus ítems, calcular el total y presentar al operador el comprobante imprimible del cobro realizado.

### 4. Excepciones
- El pago `idPago` no existe en la tabla `pagos` → `ComprobanteService` retorna `ok = false`, se establece mensaje de error y se redirige a `/pedidos`.
- El pedido asociado al pago no puede ser recuperado → mensaje de error, redirección a `/pedidos`.

### 5. Pre-condiciones
- Existe un registro en `pagos` con `id_pago = idPago`.
- El pago tiene asociado un `id_pedido` válido en `pedidos`.
- El pago tiene asociado un `id_metodo_pago` válido en `metodos_pago`.

### 6. Post-condiciones
- No se modifica ningún dato en la base de datos (operación de sólo lectura).
- Se presenta la vista `pagos/recibo.php` con: número de comprobante, fecha y hora del pago, tipo y datos del pedido, detalle de ítems, total cobrado y método de pago utilizado.
- La vista incluye funcionalidad de impresión (botón que invoca `window.print()`) ocultando elementos de navegación vía media query CSS.

---

## Resumen del flujo entre contratos

```
cerrar(idPedido)                               [PedidoController — prerrequisito]
        ↓
mostrarFormularioDePago(idPedido)              → Contrato 1
        ↓
procesarRegistroDePago(idPedido, idMetodoPago) → Contrato 2
        ↓
mostrarComprobanteDePago(idPago)               → Contrato 3
```
