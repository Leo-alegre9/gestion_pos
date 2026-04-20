# 🏗️ Arquitectura del Sistema de Autenticación

## Diagrama General del Flujo

```
┌─────────────────────────────────────────────────────────────┐
│                    USUARIO                                   │
│            (Navegador Web - Cliente)                         │
└────────────────────┬────────────────────────────────────────┘
                     │
        ┌────────────┴────────────┐
        │                         │
        ▼                         ▼
   ┌─────────────┐          ┌──────────────┐
   │  Formulario │          │  Formulario  │
   │   Login     │          │  Registro    │
   │ /auth/login │          │ /auth/register
   └─────────────┘          └──────────────┘
        │                         │
        │ POST email              │ POST datos
        │ POST password           │
        │                         │
        └────────────┬────────────┘
                     │
                     ▼
        ┌────────────────────────────┐
        │   CONTROLADOR: Auth        │
        │                            │
        │ • authenticate()           │
        │ • store()                  │
        │ • login()                  │
        │ • logout()                 │
        └────────────┬───────────────┘
                     │
                     ▼
        ┌────────────────────────────┐
        │   MODELO: UsuarioModel     │
        │                            │
        │ • validarLogin()           │
        │ • registrarUsuario()       │
        │ • emailExiste()            │
        │ • validaciones             │
        └────────────┬───────────────┘
                     │
                     ▼
        ┌────────────────────────────┐
        │   BASE DE DATOS            │
        │                            │
        │ Tabla: usuarios            │
        │ Tabla: roles               │
        └────────────────────────────┘
```

---

## Flujo Detallado de LOGIN

```
1. USUARIO ACCEDE A /auth/login
   │
   └─→ Controlador Auth::login()
       │
       └─→ Retorna vista login.php
           │
           └─→ Usuario ingresa email y contraseña
               │
               └─→ FORM POST a /auth/authenticate

2. PROCESAMIENTO EN authenticate()
   │
   ├─→ Obtiene datos del POST
   ├─→ Valida que haya email y password
   │
   └─→ Llamar: $usuarioModel->validarLogin($email, $password)
       │
       ├─→ MODELO: Busca usuario por email/username
       ├─→ Verifica que esté ACTIVO
       ├─→ Verifica contraseña con password_verify()
       │
       └─→ Si TODO ES CORRECTO:
           ├─→ Retorna datos del usuario
           │
           └─→ CONTROLADOR crea sesión
               ├─→ session()->set('id_usuario', ...)
               ├─→ session()->set('nombre', ...)
               ├─→ session()->set('email', ...)
               ├─→ session()->set('autenticado', true)
               │
               └─→ Redirige a /dashboard
                   └─→ "Bienvenido Juan"

3. SI FALLA LA VALIDACIÓN
   │
   └─→ Retorna a /auth/login
       └─→ Con error: "Email/Usuario o contraseña incorrectos"
```

---

## Flujo Detallado de REGISTRO

```
1. USUARIO ACCEDE A /auth/register
   │
   └─→ Controlador Auth::register()
       │
       └─→ Retorna vista register.php
           │
           └─→ Usuario rellena formulario
               │
               └─→ FORM POST a /auth/store

2. VALIDACIONES EN CLIENTE (JavaScript)
   │
   └─→ ¿Las dos contraseñas coinciden?
       ├─→ NO → Mostrar error
       └─→ SÍ → Enviar formulario

3. PROCESAMIENTO EN store()
   │
   ├─→ Obtiene datos del POST
   │
   └─→ Validación: password === password_confirm
       ├─→ NO → Retorna con error
       │
       └─→ SÍ → Llamar: $usuarioModel->registrarUsuario($datos)

4. EN MODELO: registrarUsuario()
   │
   ├─→ Quita password y password_confirm
   │
   ├─→ HASHEA password con bcrypt
   │   └─→ $password_hash = password_hash($password, PASSWORD_BCRYPT)
   │
   ├─→ Establece fecha_creacion = NOW()
   │
   ├─→ VALIDA TODOS LOS CAMPOS
   │   ├─→ Nombre: obligatorio, máx 100 caracteres
   │   ├─→ DNI: obligatorio, único
   │   ├─→ Username: 3-50 caracteres, único
   │   ├─→ Email: formato válido, único
   │   ├─→ ID Rol: obligatorio
   │   └─→ ...más validaciones
   │
   └─→ Si TODO es válido:
       │
       ├─→ INSERT en tabla usuarios
       │
       └─→ Retorna ID del usuario

5. DE VUELTA EN store()
   │
   └─→ Si registrarUsuario() retorna ID:
       │
       ├─→ Log de nuevo usuario registrado
       │
       └─→ Redirige a /auth/login
           └─→ Con éxito: "Registro exitoso. Por favor inicia sesión."

6. SI FALLA LA VALIDACIÓN
   │
   └─→ Retorna a /auth/register
       ├─→ WithInput (conserva datos ingresados)
       │
       └─→ Con errores del modelo
           ├─→ "Este email ya está registrado"
           ├─→ "Este username ya está en uso"
           ├─→ "Las contraseñas no coinciden"
           └─→ ...más errores según sea
```

---

## Tabla de Seguridad: Hasheo de Contraseña

```
ENTRADA: "123456"
│
└─→ Función: password_hash("123456", PASSWORD_BCRYPT)
    │
    └─→ SALIDA: "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi"
        │
        │ Se almacena en BD
        │
        └─→ Cuando usuario ingresa contraseña:
            │
            └─→ password_verify("123456", "$2y$10$...")
                │
                ├─→ Correcto: TRUE → Login exitoso
                └─→ Incorrecto: FALSE → Login fallido
```

---

## Estructura de Datos en Sesión

```php
SESSION:
┌─────────────────────────────────────┐
│ Después de login exitoso:           │
│                                     │
│ $_SESSION['autenticado'] = true    │
│ $_SESSION['id_usuario'] = 1        │
│ $_SESSION['nombre'] = "Juan"       │
│ $_SESSION['apellido'] = "Admin"    │
│ $_SESSION['email'] = "admin@..." │
│ $_SESSION['username'] = "admin"    │
│ $_SESSION['id_rol'] = 1            │
│ $_SESSION['rol_nombre'] = "Admin"  │
│                                     │
└─────────────────────────────────────┘

Acceso en vistas/controladores:
session()->get('autenticado')  → true
session()->get('nombre')       → "Juan"
session()->get('id_rol')       → 1
```

---

## Estructura de BD: Relaciones

```
TABLA: roles
┌──────────────────────────────────────┐
│ id_rol (INT, PK, AI)                │
│ nombre (VARCHAR, UNIQUE)             │
│ descripcion (VARCHAR)                │
└──────────────────────────────────────┘
         ▲
         │ FOREIGN KEY
         │
TABLA: usuarios
┌──────────────────────────────────────┐
│ id_usuario (INT, PK, AI)            │
│ id_rol (INT, FK)     ─────────→      │
│ nombre (VARCHAR)                     │
│ apellido (VARCHAR)                   │
│ dni (INT, UNIQUE)                    │
│ f_nacimiento (DATE)                  │
│ username (VARCHAR, UNIQUE)           │
│ password_hash (VARCHAR(255))         │ ← Almacena hash bcrypt
│ email (VARCHAR, UNIQUE)              │
│ activo (TINYINT)                     │
│ fecha_creacion (DATETIME)            │
└──────────────────────────────────────┘
```

---

## Validaciones en Capas

```
CLIENTE (JavaScript)
│
├─→ Tipo de datos
├─→ Longitud mínima/máxima
├─→ Campos requeridos
├─→ Contraseñas coinciden
│
└─→ Previene solicitudes inválidas

                ▼

SERVIDOR - CONTROLADOR (PHP)
│
├─→ Método POST
├─→ CSRF Token válido
├─→ Email y password presentes
│
└─→ Validaciones básicas

                ▼

SERVIDOR - MODELO (PHP)
│
├─→ Email válido
├─→ DNI único
├─→ Username único
├─→ Contraseña mínimo 6 caracteres
├─→ Campos requeridos
├─→ Longitudes máximas
│
└─→ Validaciones completas

                ▼

BASE DE DATOS (SQL)
│
├─→ UNIQUE constraints
├─→ NOT NULL constraints
├─→ FOREIGN KEY constraints
│
└─→ Garantiza integridad de datos
```

---

## Archivo de Flujo: Actualizar Contraseña (Futuro)

```
Usuario en perfil → Click "Cambiar contraseña"
│
└─→ Formulario con:
    ├─→ Contraseña actual
    ├─→ Contraseña nueva
    └─→ Confirmar nueva

        │
        └─→ POST a /user/updatePassword
            │
            └─→ Controlador valida
                │
                └─→ Modelo: actualizarPassword()
                    │
                    ├─→ Verifica password actual con password_verify()
                    ├─→ Hashea nueva contraseña
                    │
                    └─→ Si todo OK: UPDATE en BD
                        │
                        └─→ Redirige con "Contraseña actualizada"
```

---

## Matriz de Permisos (Roles)

```
                    │ Admin │ User │ Manager │
────────────────────┼───────┼──────┼─────────┤
Ver Dashboard       │  ✓    │  ✓   │   ✓     │
Crear Usuario       │  ✓    │  ✗   │   ✗     │
Editar Usuario      │  ✓    │  ✗   │   ✗     │
Eliminar Usuario    │  ✓    │  ✗   │   ✗     │
Ver Reportes        │  ✓    │  ✓   │   ✓     │
Exportar Reportes   │  ✓    │  ✗   │   ✓     │
Gestionar Roles     │  ✓    │  ✗   │   ✗     │
Cambiar Pass Propio │  ✓    │  ✓   │   ✓     │

Implementación con filtro:
$routes->group('', ['filter' => 'role:1'], ...);
// 1 = Admin only
```

---

## Ciclo de Vida de una Sesión

```
1. INICIO
   └─→ Usuario no autenticado

2. LOGIN EXITOSO
   └─→ session()->set('autenticado', true)
   └─→ Session ID guardado en cookie
   └─→ Datos de usuario en $_SESSION

3. NAVEGACIÓN
   └─→ Usuario puede acceder a rutas protegidas
   └─→ Filtro verifica: session()->get('autenticado')
   └─→ Si es true → Permitir acceso
   └─→ Si es false → Redirigir a login

4. INACTIVIDAD (TIMEOUT)
   └─→ Sesión expira (tiempo configurable)
   └─→ Datos se borran
   └─→ Próxima solicitud → Redirige a login

5. LOGOUT MANUAL
   └─→ Usuario hace click en "Cerrar sesión"
   └─→ session()->destroy()
   └─→ Redirige a home
   └─→ 'autenticado' = false
```

---

## Checklist de Seguridad ✅

```
CONTRASEÑA
├── ✓ Hasheada con bcrypt
├── ✓ Mínimo 6 caracteres
├── ✓ Nunca se almacena en texto plano
└── ✓ Verificada con password_verify()

CAMPOS ÚNICOS
├── ✓ Email único en BD
├── ✓ Username único en BD
├── ✓ DNI único en BD
└── ✓ Validado antes de INSERT

FORMULARIOS
├── ✓ CSRF Token incluido
├── ✓ Método POST (no GET)
├── ✓ Validación servidor
└── ✓ Validación cliente

SESIÓN
├── ✓ Usuario activo verificado
├── ✓ Sesión limpia en logout
├── ✓ Filtros protegen rutas
└── ✓ Datos sensibles no en cookie

AUTENTICACIÓN
├── ✓ Solo email O username
├── ✓ Contraseña verificada
├── ✓ Mensajes genéricos (sin revelar si existe)
└── ✓ Log de accesos
```

---

## Diagrama de Componentes

```
┌─────────────────────────────────────────────────┐
│              APLICACIÓN CODEIGNITER 4           │
├─────────────────────────────────────────────────┤
│                                                 │
│  ┌──────────────┐         ┌──────────────┐     │
│  │   ROUTES     │         │   CONFIG     │     │
│  │              │         │              │     │
│  │ /auth/login  │◄────────│ Filtros      │     │
│  │ /auth/auth.  │         │ Sessions     │     │
│  │ /auth/regist.│         │ DB Conn      │     │
│  │ /auth/store  │         └──────────────┘     │
│  │ /auth/logout │                              │
│  └──────┬───────┘                              │
│         │                                       │
│         ▼                                       │
│  ┌──────────────────────────────────────┐      │
│  │     CONTROLADOR: Auth                │      │
│  ├──────────────────────────────────────┤      │
│  │ • login()          • register()       │      │
│  │ • authenticate()   • store()          │      │
│  │ • logout()         • helpers          │      │
│  └──────────────┬─────────────────────────┘    │
│                 │                              │
│                 ▼                              │
│  ┌──────────────────────────────────────┐      │
│  │      MODELO: UsuarioModel            │      │
│  ├──────────────────────────────────────┤      │
│  │ • validarLogin()                     │      │
│  │ • registrarUsuario()                 │      │
│  │ • validaciones                       │      │
│  │ • consultas                          │      │
│  └──────────────┬─────────────────────────┘    │
│                 │                              │
│                 ▼                              │
│  ┌──────────────────────────────────────┐      │
│  │      VISTAS: Auth                    │      │
│  ├──────────────────────────────────────┤      │
│  │ • login.php                          │      │
│  │ • register.php                       │      │
│  │ • Formularios HTML + CSS             │      │
│  └──────────────────────────────────────┘      │
│                                                 │
└────────────────────┬─────────────────────────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │    BASE DE DATOS       │
        ├────────────────────────┤
        │ • Tabla roles          │
        │ • Tabla usuarios       │
        │ • Índices UNIQUE       │
        │ • Foreign Keys         │
        └────────────────────────┘
```

---

Esta arquitectura garantiza:
✅ **Seguridad** - Validaciones en múltiples capas
✅ **Mantenibilidad** - Código organizado
✅ **Escalabilidad** - Fácil de extender
✅ **Integridad** - Datos consistentes
