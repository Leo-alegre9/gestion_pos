# 🐛 CORRECCIÓN - Usuario No Se Registraba

## Problema Identificado

**Síntoma:** El formulario se enviaba correctamente pero el usuario NO se guardaba en la BD.

**Causa Raíz:** 
En el método `registrarUsuario()` del modelo, la **validación ocurría DESPUÉS** de procesar los datos:

```php
// ❌ ORDEN INCORRECTO
$password = $datos['password'];
unset($datos['password']);                    // Se removió password
$datos['password_hash'] = ...;                // Se agregó password_hash
$datos['activo'] = ...;                       // Se agregó activo
$datos['fecha_creacion'] = ...;               // Se agregó fecha_creacion

// Ahora validar (pero los campos originales ya no existen)
if (!$this->validate($datos)) {
    return false;  // ❌ VALIDACIÓN FALLA
}
```

**Por qué fallaba:**
- Las `$validationRules` esperaban campos: `nombre`, `apellido`, `dni`, `username`, `email`, `password`, `id_rol`, `f_nacimiento`
- Pero al momento de validar, tenía: `nombre`, `apellido`, `dni`, `username`, `email`, `password_hash`, `activo`, `fecha_creacion`
- Los campos `password_hash`, `activo` y `fecha_creacion` **no están en las reglas de validación**
- Esto causaba que la validación falle silenciosamente y el registro abortara

---

## Solución Aplicada

### Cambio Principal: Validar ANTES de Procesar

**Archivo:** `app/Models/usuariomodel.php`

**ANTES:**
```php
public function registrarUsuario(array $datos)
{
    // Validar password
    if (!isset($datos['password']) || empty($datos['password'])) {
        $this->errors['password'] = 'La contraseña es obligatoria.';
        return false;
    }

    // ❌ PROCESAR PRIMERO
    $password = $datos['password'];
    unset($datos['password']);
    $datos['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
    $datos['activo'] = $datos['activo'] ?? 1;
    $datos['fecha_creacion'] = date('Y-m-d H:i:s');

    // ❌ VALIDAR DESPUÉS (con datos modificados)
    if (!$this->validate($datos)) {
        return false;
    }

    return $this->insert($datos);
}
```

**DESPUÉS:**
```php
public function registrarUsuario(array $datos)
{
    // Validar que la contraseña esté presente
    if (!isset($datos['password']) || empty($datos['password'])) {
        $this->errors['password'] = 'La contraseña es obligatoria.';
        return false;
    }

    // ✅ VALIDAR PRIMERO (con todos los campos originales)
    if (!$this->validate($datos)) {
        return false;
    }

    // ✅ PROCESAR DESPUÉS (con datos ya validados)
    $password = trim($datos['password']);
    
    $datosFinales = [
        'id_rol'         => (int)$datos['id_rol'],
        'nombre'         => trim($datos['nombre']),
        'apellido'       => trim($datos['apellido'] ?? ''),
        'dni'            => (int)$datos['dni'],
        'f_nacimiento'   => !empty($datos['f_nacimiento']) ? $datos['f_nacimiento'] : null,
        'username'       => trim($datos['username']),
        'email'          => trim($datos['email']),
        'password_hash'  => password_hash($password, PASSWORD_BCRYPT),
        'activo'         => 1,
        'fecha_creacion' => date('Y-m-d H:i:s')
    ];

    // ✅ INSERTAR
    if ($this->insert($datosFinales)) {
        return $this->insertID();  // Retorna el ID del usuario creado
    }

    return false;
}
```

---

## Mejoras Incluidas

| Mejora | Beneficio |
|--------|----------|
| ✅ Validación ANTES de procesar | Las reglas de validación se aplican sobre los datos correctos |
| ✅ Trim en campos string | Elimina espacios en blanco que podrían causar problemas |
| ✅ Cast de tipos correctos | `(int)$dni`, `(int)$id_rol` para evitar errores de tipo |
| ✅ Manejo correcto de null | `f_nacimiento` puede ser null si no se proporciona |
| ✅ Retorna insertID() | Confirma que el usuario se creó y devuelve su ID |

---

## Flujo Correcto Ahora

```
Auth::store() recibe datos
        ↓
Valida password === password_confirm ✅
        ↓
Llama: $usuarioModel->registrarUsuario($datos)
        ↓
MODELO: Valida TODOS los campos con $validationRules ✅
        ├─→ nombre: required, string, max_length[100]
        ├─→ dni: required, integer, is_unique
        ├─→ username: required, min_length[3], max_length[50], is_unique
        ├─→ email: required, valid_email, is_unique
        ├─→ password: required, min_length[6], max_length[255]
        ├─→ id_rol: required, integer
        └─→ f_nacimiento: valid_date (opcional)
        ↓
Si validación OK:
        ├─→ Extrae password
        ├─→ Hashea con bcrypt ✅
        ├─→ Trim todos los strings ✅
        ├─→ Crea array datosFinales ✅
        ├─→ INSERT en BD ✅
        └─→ Retorna insertID() (> 0) ✅
        ↓
Auth::store() recibe ID > 0
        ↓
Redirige a /auth/login con "Registro exitoso"
```

---

## Verificación

### Prueba 1: Registro Exitoso
1. Ir a `/auth/register`
2. Completar con datos válidos:
   - Nombre: Juan
   - Apellido: Pérez
   - DNI: 45123456 (único)
   - Username: juan.perez (único, 3+ caracteres)
   - Email: juan.perez@example.com (único, válido)
   - Password: 123456 (6+ caracteres)
   - Confirmar: 123456 (igual)
3. ✅ Debería redirigir a `/auth/login` con "Registro exitoso"
4. Verificar BD: `SELECT * FROM usuarios WHERE username = 'juan.perez'`

### Prueba 2: Validaciones Funcionan
1. Intentar con email duplicado: "Este email ya está registrado"
2. Intentar con username corto: "Al menos 3 caracteres"
3. Intentar con contraseña corta: "Al menos 6 caracteres"
4. Intentar con DNI duplicado: "Este DNI ya está registrado"

---

## Archivos Modificados

✅ **app/Models/usuariomodel.php**
- Cambio del orden de operaciones en `registrarUsuario()`
- Validación ANTES de procesar datos
- Agregado trim() a campos string
- Agregado cast de tipos numéricos
- Retorna `insertID()` en lugar de solo `insert()`

---

## Resumen

El problema era que el modelo **validaba datos modificados** en lugar de los datos originales. Ahora:

✅ Valida primero (con password original)
✅ Procesa después (crea password_hash, aplica trim)
✅ Inserta los datos limpios y validados
✅ Retorna el ID del usuario creado

**El registro de usuarios ahora funciona correctamente** 🚀
