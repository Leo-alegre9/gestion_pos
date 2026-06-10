# Modelo de Usuario - Documentación Completa

## Descripción General

El modelo `UsuarioModel` es la capa de acceso a datos para la gestión de usuarios en el sistema Gestion_POS. Proporciona métodos para autenticación, registro, validación y consulta de usuarios.

## Estructura de Base de Datos

### Tabla `roles`
```sql
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255),
    UNIQUE KEY uq_roles_nombre (nombre)
) ENGINE=InnoDB;
```

### Tabla `usuarios`
```sql
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
```

## Métodos Disponibles

### Métodos de Autenticación

#### `validarLogin(string $login, string $password): ?array`
Valida las credenciales de un usuario.

**Parámetros:**
- `$login` (string): Email o username
- `$password` (string): Contraseña en texto plano

**Retorna:**
- Array con datos del usuario (sin contraseña) si es exitoso
- null si falla la autenticación

**Ejemplo de uso en controlador:**
```php
$usuarioModel = model('UsuarioModel');
$usuario = $usuarioModel->validarLogin($_POST['email'], $_POST['password']);

if ($usuario) {
    session()->set('usuario', $usuario);
    return redirect()->to('/dashboard');
} else {
    return redirect()->back()->with('error', 'Email/Usuario o contraseña incorrectos');
}
```

---

#### `getUsuarioPorLogin(string $login): ?array`
Busca un usuario por username o email con información del rol.

**Parámetros:**
- `$login` (string): Email o username

**Retorna:**
- Array con datos del usuario + nombre del rol
- null si no existe

---

### Métodos de Registro

#### `registrarUsuario(array $datos): int|false`
Registra un nuevo usuario en el sistema.

**Parámetros:**
- `$datos` (array): Datos del usuario
  - Requerido: `nombre`, `apellido`, `dni`, `username`, `email`, `password`, `id_rol`
  - Opcional: `f_nacimiento`

**Retorna:**
- ID del usuario registrado
- false si la validación o inserción falla

**Validaciones aplicadas:**
- Nombre: requerido, máx. 100 caracteres
- DNI: requerido, número único
- Username: requerido, 3-50 caracteres, único
- Email: requerido, formato válido, único
- Contraseña: requerido, mínimo 6 caracteres
- ID Rol: requerido

**Ejemplo de uso en controlador:**
```php
$usuarioModel = model('UsuarioModel');

$datos = [
    'nombre'       => $_POST['nombre'],
    'apellido'     => $_POST['apellido'],
    'dni'          => $_POST['dni'],
    'f_nacimiento' => $_POST['f_nacimiento'] ?? null,
    'username'     => $_POST['username'],
    'email'        => $_POST['email'],
    'password'     => $_POST['password'],
    'id_rol'       => 2  // Rol de usuario regular
];

if ($usuarioModel->registrarUsuario($datos)) {
    return redirect()->to('/auth/login')->with('success', 'Registro exitoso. Por favor inicia sesión.');
} else {
    $errores = $usuarioModel->getErrores();
    return redirect()->back()->withInput()->with('errores', $errores);
}
```

---

### Métodos de Validación

#### `emailExiste(string $email): bool`
Verifica si un email ya está registrado.

#### `usernameExiste(string $username): bool`
Verifica si un username ya está registrado.

#### `dniExiste(int $dni): bool`
Verifica si un DNI ya está registrado.

#### `emailExisteExcluir(string $email, int $excludeId): bool`
Verifica si un email existe, excluyendo un usuario específico (útil para actualizaciones).

**Ejemplo:**
```php
$usuarioModel = model('UsuarioModel');

if ($usuarioModel->emailExiste('admin@gestion-pos.com')) {
    echo "Email ya registrado";
}

if ($usuarioModel->usernameExiste('admin123')) {
    echo "Username no disponible";
}
```

---

### Métodos de Consulta

#### `getUsuarioConRol(int $id): ?array`
Obtiene un usuario específico con sus datos de rol.

**Retorna:**
- Array con datos del usuario y rol
- null si no existe

#### `getUsuariosActivos(): array`
Obtiene todos los usuarios activos con datos de rol.

#### `getUsuariosPorRol(int $idRol): array`
Obtiene todos los usuarios de un rol específico.

---

### Métodos de Actualización

#### `actualizarPassword(int $idUsuario, string $passwordActual, string $passwordNueva): bool`
Actualiza la contraseña de un usuario.

**Verifica:**
- Que la contraseña actual sea correcta
- Luego hashea y guarda la nueva contraseña

**Ejemplo:**
```php
$usuarioModel = model('UsuarioModel');

if ($usuarioModel->actualizarPassword($idUsuario, $passwordActual, $passwordNueva)) {
    return redirect()->with('success', 'Contraseña actualizada');
} else {
    $error = $usuarioModel->getPrimerError();
    return redirect()->back()->with('error', $error);
}
```

---

#### `setActivo(int $idUsuario, bool $activo): bool`
Activa o desactiva un usuario.

---

### Métodos Auxiliares

#### `getErrores(): array`
Retorna array con todos los errores de validación.

#### `getPrimerError(): ?string`
Retorna el primer error de validación.

---

## Características de Seguridad

### Hasheo de Contraseña
- Utiliza `PASSWORD_BCRYPT` (bcrypt)
- Se aplica automáticamente en `registrarUsuario()`
- Se valida con `password_verify()`

### Validaciones
- Campos únicos: `username`, `email`, `dni`
- Validación de formato de email
- Longitudes máximas definidas
- Mensajes de error personalizados en español

### Protección contra Inyección SQL
- Utiliza prepared statements de CodeIgniter
- Validación de tipos de datos

---

## Ejemplo de Flujo Completo

### 1. Registro de Usuario
```php
// En app/Controllers/Auth.php
public function register()
{
    $usuarioModel = model('UsuarioModel');
    
    if ($this->request->getMethod() === 'post') {
        $datos = [
            'nombre'       => $this->request->getPost('nombre'),
            'apellido'     => $this->request->getPost('apellido'),
            'dni'          => $this->request->getPost('dni'),
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email'),
            'password'     => $this->request->getPost('password'),
            'id_rol'       => 2  // Usuario regular
        ];

        if ($usuarioModel->registrarUsuario($datos)) {
            return redirect()->to('/auth/login')
                ->with('success', 'Registro exitoso. Inicia sesión.');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('errores', $usuarioModel->getErrores());
        }
    }

    return view('auth/register');
}
```

### 2. Login de Usuario
```php
public function authenticate()
{
    $usuarioModel = model('UsuarioModel');
    
    $login = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    $usuario = $usuarioModel->validarLogin($login, $password);

    if ($usuario) {
        session()->set('usuario', $usuario);
        return redirect()->to('/dashboard');
    }

    return redirect()->back()
        ->with('error', 'Credenciales inválidas');
}
```

### 3. Logout
```php
public function logout()
{
    session()->destroy();
    return redirect()->to('/');
}
```

---

## Mensajes de Error Personalizados

El modelo incluye mensajes de error en español para todas las validaciones:

| Campo | Error |
|-------|-------|
| nombre | El nombre es obligatorio |
| dni | El DNI ya está registrado en el sistema |
| username | Este nombre de usuario ya está en uso |
| email | Este email ya está registrado en el sistema |
| password | La contraseña debe tener al menos 6 caracteres |
| id_rol | El rol es obligatorio |

---

## Notas Importantes

1. **Contraseña:** Nunca se almacena en texto plano. Siempre se hashea con bcrypt.
2. **Email y Username:** Son únicos en la base de datos y validados automáticamente.
3. **Usuario Activo:** Solo usuarios con `activo = 1` pueden iniciar sesión.
4. **Rol:** Cada usuario debe tener un rol existente en la tabla `roles`.
5. **Timestamp:** La fecha de creación se establece automáticamente en el servidor.

---

## Próximos Pasos

Para completar la implementación, necesitas:

1. **Crear un Controlador Auth** con métodos:
   - `login()` - Vista de login
   - `register()` - Vista de registro  
   - `authenticate()` - Validación de login
   - `createUser()` - Crear nuevo usuario
   - `logout()` - Cerrar sesión

2. **Crear Filtros de Seguridad**:
   - Filtro para verificar autenticación
   - Filtro para verificar autorización por rol

3. **Configurar Rutas** en `app/Config/Routes.php`

4. **Crear Vista de Registro** similar a la que ya tienes de login
