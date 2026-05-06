# Historia de Usuario - Registrar Pago
## Sistema POS para Bar - BarPOS

---

## HU-PAG-004: Registrar Pago de Pedido

| Aspecto | Descripción |
|---------|-------------|
| **ID** | HU-PAG-004 |
| **Título** | Registrar Pago de Pedido |
| **Como** | Camarero o personal administrativo |
| **Quiero** | Registrar el pago de un pedido en el sistema |
| **Para** | Finalizar la operación de venta y cerrar la transacción |
| **Prioridad** | Alta |

### Criterios de Aceptación

| CA-# | Criterio |
|---|----------|
| CA-1  | Dado que he completado el formulario de pago con un método válido, cuando hago clic en "Confirmar pago", entonces el sistema debe enviar los datos mediante POST a `/pagos/procesar/{idPedido}` |
| CA-2  | El sistema debe validar la integridad de los datos antes de registrar |
| CA-3  | El sistema debe calcular el total desde backend (no del formulario HTML) |
| CA-4  | El sistema debe registrar el pago con: ID pedido, ID método pago, monto total, fecha/hora, usuario responsable |
| CA-5  | El sistema debe cambiar automáticamente el estado del pedido a "pagado" |
| CA-6  | El sistema debe liberar la mesa asociada (cambiar a estado "libre") |
| CA-7  | El sistema debe redirigir al comprobante de pago exitoso |

### Validaciones

| # | Validación |
|---|------------|
| V-1 | El monto debe calcularse sumando subtotales, no del formulario |
| V-2 | El método de pago debe existir y estar activo |
| V-3 | Debe verificarse que el pedido no tenga pago previo (validación de duplicado) |
| V-4 | El usuario debe estar autenticado y tener permisos suficientes |
| V-5 | El pedido debe existir en la base de datos |
| V-6 | El pedido debe estar en estado "cerrado" |

### Casos Alternativos

| # | Caso | Acción del Sistema |
|---|------|-------------------|
| CA-ALT-1 | Pago duplicado | Si ya existe un pago registrado, redirigir a `/pagos/comprobante/{idPago}` |
| CA-ALT-2 | Validación fallida | Redirigir al formulario con los errores mostrando qué campos son inválidos |
| CA-ALT-3 | Error en la transacción | Si falla el registro, mostrar mensaje de error y permitir reintentar |
| CA-ALT-4 | Error de conexión | Revertir cambios y mostrar mensaje "Error al registrar el pago" |

### Datos de Entrada

| Campo | Tipo | Validación | Requerido |
|-------|------|-----------|-----------|
| id_pedido | Integer | Debe existir en BD | Sí |
| id_metodo_pago | Integer | Debe estar activo | Sí |
| monto | Float | Calculado del servidor | No (automático) |
| fecha_pago | DateTime | Timestamp actual | No (automático) |
| id_usuario | Integer | De la sesión | No (automático) |

### Datos de Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| ok | Boolean | true si el pago se registró exitosamente |
| idPago | Integer | ID del pago registrado (si ok=true) |
| error | String | Mensaje de error (si ok=false) |
| pagoExistenteId | Integer | ID del pago duplicado (si aplica) |

### Rutas y Endpoints

| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | `/pagos/procesar/{idPedido}` | Endpoint principal para registrar el pago |

### Controlador/Servicio

| Componente | Responsabilidad |
|-----------|-----------------|
| `PagoController::procesarRegistroDePago()` | Leer POST, delegar en servicio, redirigir |
| `RegistrarPagoService::registrar()` | Lógica de negocio: validar, calcular, registrar |

### Notas Técnicas

| Nota |
|------|
| Debe ejecutarse dentro de una transacción de base de datos para garantizar atomicidad |
| El ID del usuario debe obtenerse de la sesión mediante `session('id_usuario')` |
| Se debe registrar la auditoría de quién realizó el cobro |
| El total calculado en el servidor debe coincidir con el del pedido |
| Utilizar `$db->transStart()` y `$db->transComplete()` para transacciones |
| Validar con `$db->transStatus()` antes de redirigir |

### Flujo de Ejecución

| Paso | Acción |
|------|--------|
| 1 | Usuario hace clic en "Confirmar pago" del formulario |
| 2 | Se envía POST a `/pagos/procesar/{idPedido}` con `id_metodo_pago` |
| 3 | `PagoController` lee el método de pago: `$this->request->getPost('id_metodo_pago')` |
| 4 | `PagoController` invoca: `service('registrarPago')->registrar(idPedido, idMetodoPago)` |
| 5 | `RegistrarPagoService` valida que el pedido exista y esté cerrado |
| 6 | `RegistrarPagoService` verifica que no exista pago previo |
| 7 | `RegistrarPagoService` calcula el total desde detalles del pedido |
| 8 | `RegistrarPagoService` construye array de registro con datos del pago |
| 9 | `RegistrarPagoService` valida el registro mediante `PagoModel::validate()` |
| 10 | `RegistrarPagoService` obtiene el ID del estado "pagado" |
| 11 | `RegistrarPagoService` inicia transacción: `$db->transStart()` |
| 12 | Inserta el pago: `PagoModel::insert($registro)` |
| 13 | Actualiza estado del pedido: `PedidoModel::update(id_pedido, estado='pagado')` |
| 14 | Libera la mesa: `MesaModel::update(id_mesa, estado='libre')` |
| 15 | Completa transacción: `$db->transComplete()` |
| 16 | Verifica estatus: `if ($db->transStatus())` |
| 17 | Si OK: redirige a `/pagos/comprobante/{id_pago}` con mensaje de éxito |
| 18 | Si ERROR: devuelve error y permite reintentar |

### Reglas de Negocio Aplicables

| Regla | Descripción |
|-------|-------------|
| RN#06 | El total del pedido será la suma de todos los subtotales de productos cargados |
| RN#05 | Al cerrar un pedido (pagar), la mesa asociada deberá pasar a estado libre |
| RN#07 | Solo usuarios con permisos suficientes podrán acceder a funciones críticas |
| RN#08 | Toda operación de cobro deberá conservarse para consultas posteriores |

### Requisitos Relacionados

| Tipo | ID | Descripción |
|------|----|----|
| RF | RF#04 | Registrar cobro |
| RF | RF#06 | Cerrar pedido abonado |
| RF | RF#07 | Liberar mesa |
| RF | RF#10 | Control de usuario cobrador |
| RNF | RNF#06 | Confiabilidad en datos de pagos |
| SEG | SEG#05 | Validación de datos |
| SEG | SEG#06 | Auditoría de operaciones |

### Criterios de Aceptación - Escenarios

#### Escenario 1: Pago Exitoso (Curso Normal)

| Paso | Actor | Sistema | Resultado |
|------|-------|--------|-----------|
| 1 | Usuario | Completa formulario y hace clic en "Confirmar" | Envía POST |
| 2 | Sistema | Recibe datos POST | Valida método de pago |
| 3 | Sistema | Verifica pedido | Pedido existe y está cerrado |
| 4 | Sistema | Calcula total | Total = Σ subtotales |
| 5 | Sistema | Valida datos | Todos los datos son válidos |
| 6 | Sistema | Inicia transacción | transStart() |
| 7 | Sistema | Registra pago | INSERT en tabla pagos |
| 8 | Sistema | Actualiza estado | UPDATE estado pedido a "pagado" |
| 9 | Sistema | Libera mesa | UPDATE estado mesa a "libre" |
| 10 | Sistema | Confirma transacción | transComplete() |
| 11 | Sistema | Redirige | Envía a `/pagos/comprobante/{idPago}` |
| 12 | Usuario | Ve comprobante | Éxito |

#### Escenario 2: Pago Duplicado

| Paso | Actor | Sistema | Resultado |
|------|-------|--------|-----------|
| 1 | Usuario | Intenta pagar | Envía POST |
| 2 | Sistema | Verifica pago previo | Encuentra pago existente |
| 3 | Sistema | Retorna resultado | `{ok: false, pagoExistenteId: 5}` |
| 4 | Sistema | Redirige | Envía a `/pagos/comprobante/5` |
| 5 | Usuario | Ve comprobante previo | Usuario informado |

#### Escenario 3: Error de Validación

| Paso | Actor | Sistema | Resultado |
|------|-------|--------|-----------|
| 1 | Usuario | Intenta pagar | Envía POST |
| 2 | Sistema | Valida datos | Encuentra error |
| 3 | Sistema | Retorna resultado | `{ok: false, errors: [...]}` |
| 4 | Sistema | Redirige | Vuelve al formulario con errores |
| 5 | Usuario | Ve errores | Usuario puede corregir |

---

## Generado

Documento: Historia de Usuario en Formato Tabla  
Módulo: Gestión de Pagos  
Sistema: BarPOS - Sistema POS para Bar  
Año: 2026
