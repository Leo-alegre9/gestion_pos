# Patrón Service Layer en el módulo de pagos

## Contexto

El módulo de pagos de BarPOS gestiona un flujo que involucra múltiples modelos (`PedidoModel`, `PagoModel`, `MetodoPagoModel`, `DetallePedidoModel`), una transacción de base de datos, un patrón Strategy para los métodos de pago y varias reglas de negocio (detección de duplicados, cálculo server-side del total, validación de estado del pedido).

En lugar de concentrar esa lógica en el controlador o dispersarla en los modelos, se implementa una **capa de servicios de aplicación** compuesta por dos clases:

- `App\Services\PrepararPagoService`
- `App\Services\RegistrarPagoService`

---

## Qué es el patrón Service Layer

El patrón **Service Layer** (Fowler, *Patterns of Enterprise Application Architecture*, 2002) define un conjunto de operaciones disponibles para los clientes de la aplicación, coordinando la respuesta de la aplicación en cada operación.

Cada clase de servicio:

1. Representa **un caso de uso** del sistema.
2. Contiene la **lógica de aplicación**: orquesta modelos, aplica reglas de negocio y gestiona transacciones.
3. **No conoce HTTP**: no recibe `Request`, no devuelve vistas ni redirects.
4. Retorna estructuras de datos simples (arrays) que el controlador convierte en respuesta HTTP.

---

## Por qué estos servicios son Service Layer y no Fachada

El patrón **Fachada** proporciona una interfaz simplificada a un subsistema complejo, pero **delega sin agregar lógica propia**. Su rol es de intermediario transparente.

El patrón **Service Layer** también simplifica el acceso desde el cliente, pero **encapsula lógica de aplicación** que no pertenece ni al controlador ni al modelo.

| Criterio | Fachada | Service Layer | Estos servicios |
|---|---|---|---|
| Simplifica la interfaz al cliente | Sí | Sí | Sí |
| Delega sin lógica propia | Sí | No | No |
| Contiene reglas de negocio / coordinación | No | Sí | Sí |
| Gestiona transacciones | No | Sí | Sí |
| Granularidad | Un subsistema | Un caso de uso | Un caso de uso |

---

## Análisis de cada clase

### `PrepararPagoService::preparar(int $idPedido)`

Encapsula el caso de uso *"preparar el formulario de pago"*. Coordina:

1. **Validación de estado del pedido** — verifica que exista y tenga `fecha_cierre` no nula.
2. **Detección de pago duplicado** — consulta `PagoModel::getPagoPorPedido()` antes de presentar el formulario.
3. **Recopilación de datos para la vista** — obtiene ítems, calcula el total y recupera los métodos de pago activos.

Ninguna de estas tres responsabilidades pertenece al controlador (lógica HTTP) ni a un modelo individual (persistencia de una entidad). Son responsabilidades de **coordinación de aplicación**.

```php
// El controlador solo delega y maneja la respuesta HTTP
$resultado = service('prepararPago')->preparar($idPedido);
```

### `RegistrarPagoService::registrar(int $idPedido, int $idMetodoPago)`

Encapsula el caso de uso *"registrar el pago de un pedido"*. Coordina:

1. **Validación del pedido** — verifica existencia y estado cerrado.
2. **Detección de duplicados** — evita registrar un segundo pago sobre el mismo pedido.
3. **Cálculo server-side del total** — recalcula el monto desde `DetallePedidoModel` para no confiar en datos del cliente.
4. **Aplicación del patrón Strategy** — delega en `EstrategiaPagoFactory` para validar y procesar según el método de pago.
5. **Validación del modelo** — valida los datos contra las reglas de `PagoModel` antes de persistir.
6. **Transacción de base de datos** — inserta el pago y actualiza el estado del pedido de forma atómica.

```php
// Dentro del servicio: coordina 4 modelos + Strategy + transacción DB
$db->transStart();
$this->pagoModel->skipValidation(true)->insert($registro);
$this->pedidoModel->update($idPedido, ['id_estado_pedido' => $idEstadoPagado]);
$db->transComplete();
```

La responsabilidad de la transacción es clave: no pertenece a ningún modelo individual (cada uno gestiona su propia tabla) ni al controlador (no debe conocer detalles de persistencia). Es una responsabilidad de la capa de aplicación.

---

## Estructura de capas resultante

```
[ HTTP / Request ]
       ↓
[ PagoController ]          ← lee input HTTP, delega, convierte resultado en redirect/view
       ↓
[ PrepararPagoService ]     ← caso de uso: preparar formulario
[ RegistrarPagoService ]    ← caso de uso: registrar pago
[ ComprobanteService ]      ← caso de uso: obtener datos del comprobante
       ↓
[ PedidoModel / PagoModel / MetodoPagoModel / DetallePedidoModel ]
       ↓
[ Base de datos ]
```

`PagoController` no importa ningún modelo directamente. Si el subsistema de pagos cambia (nuevo modelo, nueva regla, nueva estrategia), el controlador no se modifica.

---

## Contrato de retorno

Ambos servicios retornan un array con forma fija, lo que define un **contrato explícito** entre la capa de servicio y sus clientes:

```php
// PrepararPagoService::preparar()
[
    'ok'              => bool,
    'error'           => string|null,
    'pagoExistenteId' => int|null,
    'data'            => array|null,   // {pedido, items, total, metodos}
]

// RegistrarPagoService::registrar()
[
    'ok'              => bool,
    'idPago'          => int|null,
    'error'           => string|null,
    'errors'          => array|null,   // errores de validación del modelo
    'pagoExistenteId' => int|null,
]
```

El controlador toma decisiones HTTP únicamente en función de estos valores, sin necesidad de conocer cómo se obtuvieron.

---

## Referencias

- Fowler, M. (2002). *Patterns of Enterprise Application Architecture*. Addison-Wesley. Cap. 9: Service Layer.
- Gamma et al. (1994). *Design Patterns*. Addison-Wesley. Facade (pág. 185) — para la comparación.
