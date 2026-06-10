# Guía de Implementación Completa - Login y Registro

## 📋 Contenido de esta Guía

Esta guía te lleva paso a paso para implementar un sistema completo de login y registro en Gestion_POS usando el modelo mejorado.

---

## 🎯 Archivos Incluidos

1. **UsuarioModel.php** (actualizado)
   - Modelo completo con métodos de login, registro y validación
   - Ubicación: `app/Models/usuariomodel.php`

2. **MODELO_USUARIO_DOCUMENTACION.md**
   - Documentación completa del modelo
   - Métodos disponibles y ejemplos

3. **EJEMPLO_AUTH_CONTROLLER.php**
   - Controlador Auth listo para usar
   - Métodos: login, register, authenticate, store, logout

4. **EJEMPLO_VISTA_REGISTRO.php**
   - Vista de registro con validaciones en cliente

5. **EJEMPLO_ROUTES.php**
   - Configuración de rutas

6. **EJEMPLO_FILTROS.php**
   - Filtros de autenticación y autorización

---

## 🚀 Pasos de Implementación

### PASO 1: Verificar la Base de Datos

Asegúrate de que las tablas estén creadas:

```sql
-- Crear tabla de roles
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255),
    UNIQUE KEY uq_roles_nombre (nombre)
) ENGINE=InnoDB;

-- Crear tabla de usuarios
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_rol INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    dni INT NOT NULL,
    f_nacimiento DATE,
    username VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(120),
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY uq_usuarios_username (username),
    UNIQUE KEY uq_usuarios_email (email),
    
    CONSTRAINT fk_usuarios_rol 
        FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
) ENGINE=InnoDB;

-- Insertar rol de administrador
INSERT INTO roles (nombre, descripcion) VALUES ('Administrador', 'Usuario con acceso total');

-- Insertar rol de usuario regular
INSERT INTO roles (nombre, descripcion) VALUES ('Usuario', 'Usuario regular del sistema');
```

### PASO 2: Actualizar el Modelo

El archivo `app/Models/usuariomodel.php` ya ha sido actualizado con:
- ✅ Métodos de autenticación
- ✅ Métodos de registro
- ✅ Validaciones completas
- ✅ Hasheo de contraseña con bcrypt

**No necesitas hacer nada - el modelo está listo.**

### PASO 3: Crear el Controlador Auth

Copia el contenido de **EJEMPLO_AUTH_CONTROLLER.php** a:
```
app/Controllers/Auth.php
```

**Cambios requeridos en el archivo:**
```php
<?php
// Línea 1 debe tener:
namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    // ... resto del código igual
}
```

### PASO 4: Crear la Vista de Registro

Copia el contenido de **EJEMPLO_VISTA_REGISTRO.php** a:
```
app/Views/auth/register.php
```

**Nota:** Ya tienes `app/Views/auth/login.php`, solo necesitas crear el register.php

### PASO 5: Configurar las Rutas

Abre `app/Config/Routes.php` y agrega esto ANTES de `return $routes;`:

```php
// Rutas de autenticación
$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('authenticate', 'Auth::authenticate');
    $routes->get('register', 'Auth::register');
    $routes->post('store', 'Auth::store');
    $routes->get('logout', 'Auth::logout');
});
```

### PASO 6: Crear Filtros (Opcional pero Recomendado)

Si deseas proteger rutas, crea estos archivos:

**app/Filters/AuthFilter.php**
```php
<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('autenticado')) {
            session()->set('redirect_url', current_url());
            return redirect()->to('/auth/login')
                ->with('info', 'Necesitas iniciar sesión.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
```

Luego registra el filtro en `app/Config/Filters.php`:

```php
public array $aliases = [
    'csrf'     => CSRF::class,
    'toolbar'  => DebugToolbar::class,
    'honeypot' => Honeypot::class,
    'invalidate' => InvalidatePage::class,
    'auth'     => AuthFilter::class,  // <- Agregar esta línea
];
```

### PASO 7: Proteger Rutas (Opcional)

En `app/Config/Routes.php`, agrupa rutas protegidas:

```php
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('perfil', 'User::perfil');
});
```

---

## ✅ Verificación de Implementación

### Prueba 1: Acceso a Login
1. Ve a `/auth/login`
2. Deberías ver el formulario de login

### Prueba 2: Acceso a Registro
1. Ve a `/auth/register`
2. Deberías ver el formulario de registro

### Prueba 3: Crear Usuario
1. Rellena el formulario de registro
2. Haz clic en "Crear Cuenta"
3. Deberías ser redirigido a login con un mensaje de éxito

### Prueba 4: Iniciar Sesión
1. Ingresa el email/username y contraseña del usuario creado
2. Deberías ser redirigido al dashboard (o al home si no existe)

### Prueba 5: Datos de Sesión
Dentro de cualquier vista o controlador, verifica:
```php
<?php
// En una vista:
<?php if (session()->get('autenticado')): ?>
    Bienvenido, <?= session()->get('nombre') ?>
<?php endif; ?>
```

---

## 🔒 Seguridad Implementada

✅ **Contraseñas hasheadas** con bcrypt (PASSWORD_BCRYPT)
✅ **Campos únicos** en BD: username, email, dni
✅ **Validaciones** en servidor y cliente
✅ **CSRF Protection** con `csrf_field()`
✅ **Sesiones** para almacenar datos de usuario
✅ **Filtros** para proteger rutas
✅ **Verificación de activo** - solo usuarios activos pueden login

---

## 🎨 Personalización Recomendada

### 1. Customizar Mensajes de Error
En `app/Models/usuariomodel.php`, edita `$validationMessages`:

```php
protected $validationMessages = [
    'nombre' => [
        'required' => 'Tu mensaje personalizado aquí',
    ],
    // ... más campos
];
```

### 2. Agregar Campos Adicionales
Si necesitas más campos:
1. Agrega columna en BD
2. Actualiza `$allowedFields` en el modelo
3. Agrega validación en `$validationRules`
4. Agrega input en la vista

### 3. Roles y Permisos
Actualmente se soportan roles básicos. Para permisos más complejos:

```php
// En el controlador
public function adminPanel()
{
    $auth = new Auth();
    
    if (!$auth->tieneRol('Administrador')) {
        return redirect()->to('/dashboard')
            ->with('error', 'Acceso denegado');
    }
    
    // ... contenido del panel
}
```

---

## 🐛 Solución de Problemas Comunes

### Error: "Class not found UsuarioModel"
- Verifica que el archivo esté en `app/Models/usuariomodel.php`
- Verifica el namespace: `namespace App\Models;`

### Error: "Column not found" en validación
- Asegúrate de que las columnas existan en la BD
- Verifica los nombres en `$allowedFields` y `$validationRules`

### Las contraseñas no se guardan correctamente
- Verifica que `password_hash` es VARCHAR(255)
- El modelo hashea automáticamente con bcrypt

### No puedo iniciar sesión
- Verifica que el usuario esté marcado como `activo = 1`
- Verifica la contraseña coincida (case-sensitive)

### Las validaciones no funcionan
- Verifica que `$validationRules` tenga los campos
- Revisa que `registrarUsuario()` sea llamado correctamente

---

## 📚 Documentación Adicional

Para más detalles sobre cada método del modelo, consulta:
**MODELO_USUARIO_DOCUMENTACION.md**

---

## 🎓 Próximos Pasos

Una vez que el sistema de login está funcionando:

1. **Crear Dashboard** - Controlador Dashboard::index
2. **Gestionar Usuarios** - CRUD de usuarios (solo admin)
3. **Cambiar Contraseña** - Método en Auth::changePassword
4. **Recuperar Contraseña** - Con tokens y email
5. **2FA (Autenticación de Dos Factores)** - Seguridad adicional

---

## 🤝 Soporte

Si tienes problemas:
1. Revisa los logs en `writable/logs/`
2. Verifica los errores con `$model->getErrores()`
3. Usa `log_message()` para debug

---

**¡Tu sistema de login está listo! 🚀**
