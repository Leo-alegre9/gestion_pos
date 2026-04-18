# Diccionario de datos — bar_pos

Sistema POS para bar, una sola sucursal. Motor: SQL Server.

---

## Índice de tablas

1. [roles](#1-roles)
2. [usuarios](#2-usuarios)
3. [mesas](#3-mesas)
4. [categorias_productos](#4-categorias_productos)
5. [productos](#5-productos)
6. [estados_pedido](#6-estados_pedido)
7. [pedidos](#7-pedidos)
8. [detalle_pedidos](#8-detalle_pedidos)
9. [metodos_pago](#9-metodos_pago)
10. [pagos](#10-pagos)
11. [stock](#11-stock)
12. [movimientos_stock](#12-movimientos_stock)

---

## 1. `roles`

Catálogo de roles de acceso al sistema. Define qué tipo de usuario es cada persona (administrador, cajero, mozo, bartender, etc.).

| Columna | Tipo | Nulo | Default | Descripción |
|---|---|---|---|---|
| `id_rol` | INT | NO | IDENTITY | Clave primaria autoincremental. |
| `nombre` | VARCHAR(50) | NO | — | Nombre único del rol. Ejemplos: `administrador`, `cajero`, `mozo`, `bartender`. |
| `descripcion` | VARCHAR(255) | SÍ | NULL | Descripción opcional del rol y sus responsabilidades. |

**Restricciones:** `uq_roles_nombre` — `nombre` único.

---

## 2. `usuarios`

Personas que operan el sistema. Cada usuario tiene un único rol que determina sus permisos. Se registran datos personales básicos y credenciales de acceso.

| Columna | Tipo | Nulo | Default | Descripción |
|---|---|---|---|---|
| `id_usuario` | INT | NO | IDENTITY | Clave primaria autoincremental. |
| `id_rol` | INT | NO | — | FK → `roles.id_rol`. Rol asignado al usuario. |
| `nombre` | VARCHAR(100) | NO | — | Nombre de pila del usuario. |
| `apellido` | VARCHAR(100) | SÍ | NULL | Apellido del usuario. |
| `dni` | INT | SÍ | NULL | Documento Nacional de Identidad (8 dígitos). |
| `f_nacimiento` | DATE | SÍ | NULL | Fecha de nacimiento. |
| `username` | VARCHAR(50) | NO | — | Nombre de usuario para iniciar sesión. Único en el sistema. |
| `password_hash` | VARCHAR(255) | NO | — | Hash de la contraseña (nunca se almacena en texto plano). |
| `email` | VARCHAR(120) | SÍ | NULL | Correo electrónico del usuario. Único si se informa. |
| `activo` | BIT | NO | 1 | Indica si el usuario puede operar el sistema. `1` = activo, `0` = dado de baja. |
| `fecha_creacion` | DATETIME | NO | GETDATE() | Fecha y hora en que se creó el registro. |

**Restricciones:** `uq_usuarios_username` — `username` único. `uq_usuarios_email` — `email` único. `fk_usuarios_rol` — FK a `roles`.

---

## 3. `mesas`

Mesas físicas del bar. Cada mesa tiene un número identificador visible para el personal y un estado operativo que refleja su situación en tiempo real.

| Columna | Tipo | Nulo | Default | Descripción |
|---|---|---|---|---|
| `id_mesa` | INT | NO | IDENTITY | Clave primaria autoincremental. |
| `numero` | INT | NO | — | Número de mesa visible en el salón. Único. |
| `capacidad` | INT | SÍ | NULL | Cantidad máxima de comensales. |
| `estado` | VARCHAR(20) | NO | `'libre'` | Estado actual de la mesa. Valores: `libre`, `ocupada`, `reservada`, `inactiva`. |

**Restricciones:** `uq_mesas_numero` — `numero` único. `ck_mesas_estado` — `estado` limitado a los valores permitidos.

---

## 4. `categorias_productos`

Categorías para agrupar productos del menú. Permiten organizar la carta y filtrar artículos en el punto de venta. Ejemplos: Bebidas, Comidas, Postres, Cócteles.

| Columna | Tipo | Nulo | Default | Descripción |
|---|---|---|---|---|
| `id_categoria` | INT | NO | IDENTITY | Clave primaria autoincremental. |
| `nombre` | VARCHAR(100) | NO | — | Nombre de la categoría. Único. |
| `descripcion` | VARCHAR(255) | SÍ | NULL | Descripción opcional de la categoría. |
| `activa` | BIT | NO | 1 | Indica si la categoría está disponible en el sistema. `1` = activa, `0` = oculta. |

**Restricciones:** `uq_categorias_nombre` — `nombre` único.

---

## 5. `productos`

Artículos del menú que pueden venderse. Incluye bebidas, comidas y cualquier ítem cobrable. El precio se registra aquí como precio de referencia; al generar un pedido se copia a `detalle_pedidos.precio_unitario` para preservar el valor histórico.

| Columna | Tipo | Nulo | Default | Descripción |
|---|---|---|---|---|
| `id_producto` | INT | NO | IDENTITY | Clave primaria autoincremental. |
| `id_categoria` | INT | NO | — | FK → `categorias_productos.id_categoria`. Categoría a la que pertenece. |
| `nombre` | VARCHAR(120) | NO | — | Nombre del producto tal como aparece en la carta. |
| `descripcion` | VARCHAR(255) | SÍ | NULL | Descripción del producto (ingredientes, presentación, etc.). |
| `precio_venta` | DECIMAL(10,2) | NO | — | Precio de venta actual en moneda local. |
| `se_vende_en_barra` | BIT | NO | 1 | Indica si el producto puede venderse directamente en barra sin mesa asignada. |
| `controla_stock` | BIT | NO | 1 | Indica si este producto descuenta stock al ser vendido. `0` para ítems sin inventario (ej.: servicio de mesa). |
| `activo` | BIT | NO | 1 | Indica si el producto está disponible para la venta. `0` = dado de baja del menú. |

**Restricciones:** `fk_productos_categoria` — FK a `categorias_productos`.

---

## 6. `estados_pedido`

Catálogo de estados por los que puede pasar un pedido durante su ciclo de vida. Permite rastrear el avance desde que se toma la comanda hasta el cobro o cancelación.

| Columna | Tipo | Nulo | Default | Descripción |
|---|---|---|---|---|
| `id_estado_pedido` | INT | NO | IDENTITY | Clave primaria autoincremental. |
| `nombre` | VARCHAR(30) | NO | — | Nombre del estado. Valores típicos: `pendiente`, `en_preparacion`, `entregado`, `cobrado`, `cancelado`. |

**Restricciones:** `uq_estados_pedido_nombre` — `nombre` único.

---

## 7. `pedidos`

Registro central de cada comanda generada en el bar. Un pedido puede originarse desde una mesa, desde la barra o como pedido para llevar. No almacena el total: este se calcula siempre a partir de `detalle_pedidos` para evitar inconsistencias.

| Columna | Tipo | Nulo | Default | Descripción |
|---|---|---|---|---|
| `id_pedido` | INT | NO | IDENTITY | Clave primaria autoincremental. |
| `id_mesa` | INT | SÍ | NULL | FK → `mesas.id_mesa`. Mesa asociada al pedido. `NULL` si es venta en barra o take away. |
| `id_usuario` | INT | NO | — | FK → `usuarios.id_usuario`. Usuario que registró el pedido. |
| `id_estado_pedido` | INT | NO | — | FK → `estados_pedido.id_estado_pedido`. Estado actual del pedido. |
| `tipo_pedido` | VARCHAR(20) | NO | — | Origen del pedido. Valores: `mesa`, `barra`, `take_away`. |
| `fecha_apertura` | DATETIME | NO | GETDATE() | Fecha y hora en que se abrió el pedido. |
| `fecha_cierre` | DATETIME | SÍ | NULL | Fecha y hora en que se cerró (cobrado o cancelado). `NULL` mientras está activo. |
| `observaciones` | VARCHAR(255) | SÍ | NULL | Notas generales sobre el pedido (alergias, preferencias del cliente, etc.). |

**Restricciones:** `ck_pedidos_tipo` — `tipo_pedido` limitado a valores permitidos. `fk_pedidos_mesa`, `fk_pedidos_usuario`, `fk_pedidos_estado` — FKs a sus respectivas tablas.

> **Nota de diseño:** el total del pedido no se persiste. Para obtenerlo usar: `SELECT SUM(subtotal) FROM detalle_pedidos WHERE id_pedido = @id`.

---

## 8. `detalle_pedidos`

Líneas de productos dentro de un pedido. Cada fila representa un ítem pedido con su cantidad y precio al momento de la venta. El `precio_unitario` se copia desde `productos.precio_venta` al crear el detalle, preservando así el precio histórico ante cambios futuros en el catálogo.

| Columna | Tipo | Nulo | Default | Descripción |
|---|---|---|---|---|
| `id_detalle_pedido` | INT | NO | IDENTITY | Clave primaria autoincremental. |
| `id_pedido` | INT | NO | — | FK → `pedidos.id_pedido`. Pedido al que pertenece esta línea. |
| `id_producto` | INT | NO | — | FK → `productos.id_producto`. Producto pedido. |
| `cantidad` | DECIMAL(10,2) | NO | — | Cantidad solicitada del producto (acepta decimales para productos a granel o fraccionados). |
| `precio_unitario` | DECIMAL(10,2) | NO | — | Precio del producto en el momento de la venta. Copia histórica de `productos.precio_venta`. |
| `subtotal` | DECIMAL(12,2) | NO | — | Resultado de `cantidad × precio_unitario`. Se persiste por performance. |
| `observaciones` | VARCHAR(255) | SÍ | NULL | Indicaciones específicas del ítem (ej.: "sin hielo", "término medio"). |

**Restricciones:** `fk_detalle_pedido` — FK a `pedidos`. `fk_detalle_producto` — FK a `productos`.

---

## 9. `metodos_pago`

Catálogo de formas de pago aceptadas en el bar. Al poder registrar múltiples pagos por pedido (tabla `pagos`), se admiten pagos combinados, por ejemplo efectivo + transferencia.

| Columna | Tipo | Nulo | Default | Descripción |
|---|---|---|---|---|
| `id_metodo_pago` | INT | NO | IDENTITY | Clave primaria autoincremental. |
| `nombre` | VARCHAR(50) | NO | — | Nombre del método. Ejemplos: `efectivo`, `debito`, `credito`, `transferencia`. |
| `activo` | BIT | NO | 1 | Indica si el método está habilitado para cobrar. `0` = deshabilitado. |

**Restricciones:** `uq_metodos_pago_nombre` — `nombre` único.

---

## 10. `pagos`

Registro de los cobros realizados sobre un pedido. Un mismo pedido puede tener múltiples registros de pago (pago dividido, pago parcial con saldo pendiente, distintos métodos). La suma de `monto` de todos los pagos del pedido debe igualar el total calculado desde `detalle_pedidos`.

| Columna | Tipo | Nulo | Default | Descripción |
|---|---|---|---|---|
| `id_pago` | INT | NO | IDENTITY | Clave primaria autoincremental. |
| `id_pedido` | INT | NO | — | FK → `pedidos.id_pedido`. Pedido que se está cobrando. |
| `id_metodo_pago` | INT | NO | — | FK → `metodos_pago.id_metodo_pago`. Forma de pago utilizada. |
| `monto` | DECIMAL(12,2) | NO | — | Monto abonado en esta transacción. |
| `fecha_pago` | DATETIME | NO | GETDATE() | Fecha y hora del cobro. |

**Restricciones:** `fk_pagos_pedido` — FK a `pedidos`. `fk_pagos_metodo` — FK a `metodos_pago`.

---

## 11. `stock`

Estado actual del inventario por producto. Mantiene una única fila por producto (relación 1-1), reflejando la cantidad disponible en tiempo real. Se actualiza mediante la tabla `movimientos_stock`.

| Columna | Tipo | Nulo | Default | Descripción |
|---|---|---|---|---|
| `id_stock` | INT | NO | IDENTITY | Clave primaria autoincremental. |
| `id_producto` | INT | NO | — | FK → `productos.id_producto`. Producto al que corresponde este stock. Único. |
| `cantidad_disponible` | DECIMAL(10,2) | NO | 0 | Cantidad actual en depósito o barra. |
| `cantidad_minima` | DECIMAL(10,2) | NO | 0 | Umbral de alerta de reposición. Cuando `cantidad_disponible ≤ cantidad_minima` se debe reponer. |
| `ultima_actualizacion` | DATETIME | NO | GETDATE() | Fecha y hora de la última modificación del stock. |

**Restricciones:** `uq_stock_producto` — `id_producto` único (garantiza relación 1-1 con `productos`). `fk_stock_producto` — FK a `productos`.

> **Nota:** solo se crean filas en `stock` para productos donde `productos.controla_stock = 1`.

---

## 12. `movimientos_stock`

Libro de movimientos del inventario. Cada fila representa una entrada, salida o ajuste de stock. Las salidas se generan automáticamente al confirmar un pedido; las entradas y ajustes los registra el personal con acceso administrativo.

| Columna | Tipo | Nulo | Default | Descripción |
|---|---|---|---|---|
| `id_movimiento` | INT | NO | IDENTITY | Clave primaria autoincremental. |
| `id_producto` | INT | NO | — | FK → `productos.id_producto`. Producto afectado por el movimiento. |
| `id_pedido` | INT | SÍ | NULL | FK → `pedidos.id_pedido`. Pedido que generó la salida. `NULL` para entradas manuales o ajustes. |
| `tipo_movimiento` | VARCHAR(20) | NO | — | Tipo de operación. Valores: `entrada`, `salida`, `ajuste`. |
| `cantidad` | DECIMAL(10,2) | NO | — | Cantidad movida. Siempre positiva; el tipo de movimiento determina si suma o resta al stock disponible. |
| `motivo` | VARCHAR(100) | SÍ | NULL | Descripción del motivo del movimiento. Ejemplos: `venta`, `compra`, `merma`, `ajuste inventario`. |
| `fecha` | DATETIME | NO | GETDATE() | Fecha y hora en que ocurrió el movimiento. |
| `id_usuario` | INT | NO | — | FK → `usuarios.id_usuario`. Usuario que registró el movimiento. |

**Restricciones:** `ck_mov_tipo` — `tipo_movimiento` limitado a `entrada`, `salida`, `ajuste`. `fk_mov_producto` — FK a `productos`. `fk_mov_pedido` — FK a `pedidos`. `fk_mov_usuario` — FK a `usuarios`.

---

## Resumen de relaciones

| Tabla hijo | Columna FK | Tabla padre | Descripción |
|---|---|---|---|
| `usuarios` | `id_rol` | `roles` | Un usuario tiene un rol. |
| `productos` | `id_categoria` | `categorias_productos` | Un producto pertenece a una categoría. |
| `pedidos` | `id_mesa` | `mesas` | Un pedido puede estar asociado a una mesa. |
| `pedidos` | `id_usuario` | `usuarios` | Un pedido es registrado por un usuario. |
| `pedidos` | `id_estado_pedido` | `estados_pedido` | Un pedido tiene un estado. |
| `detalle_pedidos` | `id_pedido` | `pedidos` | Un detalle pertenece a un pedido. |
| `detalle_pedidos` | `id_producto` | `productos` | Un detalle referencia un producto. |
| `pagos` | `id_pedido` | `pedidos` | Un pago corresponde a un pedido. |
| `pagos` | `id_metodo_pago` | `metodos_pago` | Un pago usa un método de pago. |
| `stock` | `id_producto` | `productos` | Relación 1-1: cada producto tiene un estado de stock. |
| `movimientos_stock` | `id_producto` | `productos` | Un movimiento afecta a un producto. |
| `movimientos_stock` | `id_pedido` | `pedidos` | Un movimiento puede originarse en un pedido (salidas). |
| `movimientos_stock` | `id_usuario` | `usuarios` | Un movimiento es registrado por un usuario. |