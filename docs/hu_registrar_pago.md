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
