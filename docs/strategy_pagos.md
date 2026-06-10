# Patrón Strategy en el módulo de pagos

## Contexto

El módulo de pagos soporta tres métodos distintos: efectivo, tarjeta y transferencia bancaria. Cada uno tiene reglas de validación y comportamiento de procesamiento diferentes. En lugar de resolver esto con condicionales (`if/switch` sobre el tipo de pago), se aplica el patrón **Strategy**.

---

## Estructura

```
EstrategiaPagoInterface
        ├── PagoEfectivo
        ├── PagoTarjeta
        └── PagoTransferencia

EstrategiaPagoFactory   (crea la estrategia correcta por nombre)
```

### Interfaz

`App\Services\Pagos\EstrategiaPagoInterface` define el contrato que todas las estrategias deben cumplir:

```php
public function validar(float $monto): bool;
public function procesar(float $monto): array;
```

`procesar()` retorna como mínimo:

```php
['ok' => bool, 'detalle' => string, 'error' => string|null]
```

Las implementaciones concretas pueden incluir claves adicionales sin romper el contrato.

### Factory

`EstrategiaPagoFactory::crear(string $nombreMetodo)` mapea el nombre del método de pago (tal como está en `metodos_pago.nombre`) a la clase concreta. La comparación es *case-insensitive* y elimina espacios. Retorna `null` si no hay estrategia registrada para ese nombre, permitiendo que el llamador decida cómo manejarlo.

---

## Estrategias concretas

### `PagoEfectivo`

**Caso de uso:** el cliente paga con billetes y monedas; el cajero necesita saber cuánto cobrar y cuánto devolver.

| Método | Descripción |
|---|---|
| `validar(float $monto)` | Acepta cualquier monto mayor a cero. |
| `procesar(float $monto)` | Retorna el resultado estándar más `'redondeo'`: el múltiplo de $50 superior más cercano al total, para que el cajero pueda pedir un billete redondo al cliente. |
| `calcularVuelto(float $montoEntregado, float $total)` | Calcula el cambio a entregar. Retorna `0.0` si el monto entregado es insuficiente. |
| `calcularRedondeo(float $monto)` | Redondea al múltiplo de $50 superior (`ceil($monto / 50) * 50`). |

**Ejemplo de `procesar()`:**
```php
// monto = $730
[
    'ok'       => true,
    'detalle'  => 'Pago en efectivo procesado correctamente.',
    'redondeo' => 750.0,   // se le piden $750 al cliente → vuelto $20
    'error'    => null,
]
```

---

### `PagoTarjeta`

**Caso de uso:** el cliente paga con tarjeta de débito o crédito a través de un lector. Los lectores de tarjeta rechazan cobros por debajo de un monto mínimo operativo.

| Método | Descripción |
|---|---|
| `validar(float $monto)` | Exige `$monto >= MONTO_MINIMO` (constante: `100.0`). Rechaza montos menores. |
| `procesar(float $monto)` | Genera un código de autorización y lo incluye en el resultado bajo `'codigo_autorizacion'`. |
| `generarCodigoAutorizacion()` | Genera 6 caracteres hexadecimales en mayúsculas (`bin2hex(random_bytes(3))`). En producción este código vendría de la pasarela de pago real. |

**Ejemplo de `procesar()`:**
```php
[
    'ok'                  => true,
    'detalle'             => 'Pago con tarjeta autorizado. Código: A3F2B1.',
    'codigo_autorizacion' => 'A3F2B1',
    'error'               => null,
]
```

**Comportamiento de `validar()` cuando falla:**
```php
// monto = $50 → false → RegistrarPagoService retorna error al controlador
// "El monto no es válido para el método de pago seleccionado."
```

---

### `PagoTransferencia`

**Caso de uso:** el cliente transfiere desde su billetera o banco y presenta el comprobante. El cajero necesita una referencia interna para cruzar ese comprobante con el registro en el sistema.

| Método | Descripción |
|---|---|
| `validar(float $monto)` | Acepta cualquier monto mayor a cero. |
| `procesar(float $monto)` | Genera una referencia única y la incluye en el resultado bajo `'referencia'`. |
| `generarReferencia()` | Genera una cadena con formato `TRF-YYYYMMDD-XXXXXX` (fecha del día + 6 chars hex). |

**Ejemplo de `procesar()`:**
```php
[
    'ok'         => true,
    'detalle'    => 'Transferencia registrada. Referencia: TRF-20260610-C4A1F3.',
    'referencia' => 'TRF-20260610-C4A1F3',
    'error'      => null,
]
```

---

## Integración con `RegistrarPagoService`

El servicio obtiene la estrategia a través de la factory y la ejecuta en dos pasos:

```php
$estrategia = EstrategiaPagoFactory::crear($metodoPago['nombre']);

if ($estrategia !== null) {
    if (!$estrategia->validar($total)) {
        return $this->falla('El monto no es válido para el método de pago seleccionado.');
    }

    $resultado = $estrategia->procesar($total);

    if (!($resultado['ok'] ?? false)) {
        return $this->falla($resultado['error'] ?? 'Error al procesar el pago.');
    }
}
```

Si la factory retorna `null` (método de pago sin estrategia registrada), el servicio omite la validación específica y continúa con el flujo general. Esto permite agregar métodos de pago a la base de datos sin romper el sistema mientras no tengan una estrategia implementada.

---

## Métodos específicos vs. contrato de la interfaz

Los métodos `calcularVuelto()`, `generarCodigoAutorizacion()` y `generarReferencia()` son **capacidades propias de cada clase concreta**, no forman parte de `EstrategiaPagoInterface`. Esto es intencional:

- La interfaz define la operación genérica (validar y procesar un monto).
- Los métodos adicionales exponen comportamiento específico del medio de pago que solo tiene sentido para esa estrategia concreta.
- Un cliente que trabaja contra la interfaz (como `RegistrarPagoService`) no necesita ni puede llamar a esos métodos. Solo accede a ellos quien conoce el tipo concreto.

---

## Cómo agregar una nueva estrategia

1. Crear `app/Services/Pagos/PagoNuevoMetodo.php` implementando `EstrategiaPagoInterface`.
2. Registrar la clase en el mapa de `EstrategiaPagoFactory::$mapa`:
   ```php
   'nuevo_metodo' => PagoNuevoMetodo::class,
   ```
3. Insertar el método de pago en la tabla `metodos_pago` con el mismo nombre (case-insensitive).

No se requiere modificar `RegistrarPagoService` ni ningún otro componente.
