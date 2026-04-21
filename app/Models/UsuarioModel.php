<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id_usuario';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'id_rol',
        'nombre',
        'apellido',
        'dni',
        'f_nacimiento',
        'username',
        'password_hash',
        'email',
        'activo',
        'fecha_creacion'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'nombre'       => 'required|string|max_length[100]',
        'apellido'     => 'string|max_length[100]|permit_empty',
        'dni'          => 'required|integer|is_unique[usuarios.dni]',
        'username'     => 'required|string|min_length[3]|max_length[50]|is_unique[usuarios.username]',
        'email'        => 'required|valid_email|max_length[120]|is_unique[usuarios.email]',
        'password'     => 'required|string|min_length[6]|max_length[255]',
        'id_rol'       => 'required|integer',
        'f_nacimiento' => 'valid_date|permit_empty'
    ];

    protected $validationMessages = [
        'nombre' => [
            'required'   => 'El nombre es obligatorio.',
            'max_length' => 'El nombre no puede exceder 100 caracteres.'
        ],
        'dni' => [
            'required'  => 'El DNI es obligatorio.',
            'integer'   => 'El DNI debe ser un número.',
            'is_unique' => 'Este DNI ya está registrado en el sistema.'
        ],
        'username' => [
            'required'   => 'El nombre de usuario es obligatorio.',
            'min_length' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'max_length' => 'El nombre de usuario no puede exceder 50 caracteres.',
            'is_unique'  => 'Este nombre de usuario ya está en uso.'
        ],
        'email' => [
            'required'    => 'El email es obligatorio.',
            'valid_email' => 'Ingresa un email válido.',
            'is_unique'   => 'Este email ya está registrado en el sistema.'
        ],
        'password' => [
            'required'   => 'La contraseña es obligatoria.',
            'min_length' => 'La contraseña debe tener al menos 6 caracteres.'
        ],
        'id_rol' => [
            'required' => 'El rol es obligatorio.'
        ]
    ];

    // Almacena errores manuales (no del framework) sin pisar $this->errors() de CI4
    protected array $customErrors = [];

    // ========================================
    // MÉTODOS DE AUTENTICACIÓN
    // ========================================

    /**
     * Validar credenciales de login
     *
     * @param string $login    Email o username
     * @param string $password Contraseña en texto plano
     * @return array|null
     */
    public function validarLogin(string $login, string $password): ?array
    {
        $usuario = $this->getUsuarioPorLogin($login);

        if (!$usuario) {
            return null;
        }

        if (!$usuario['activo']) {
            return null;
        }

        if (!password_verify($password, $usuario['password_hash'])) {
            return null;
        }

        unset($usuario['password_hash']);
        return $usuario;
    }

    /**
     * Buscar usuario por username o email con información de rol
     *
     * @param string $login Email o username
     * @return array|null
     */
    public function getUsuarioPorLogin(string $login): ?array
    {
        return $this->select('usuarios.*, roles.nombre as rol_nombre')
                    ->join('roles', 'roles.id_rol = usuarios.id_rol', 'left')
                    ->groupStart()
                        ->where('usuarios.username', $login)
                        ->orWhere('usuarios.email', $login)
                    ->groupEnd()
                    ->first();
    }

    // ========================================
    // MÉTODOS DE REGISTRO
    // ========================================

    /**
     * Registrar un nuevo usuario
     *
     * @param array $datos Datos del usuario (debe incluir 'password' en texto plano)
     * @return int|false  ID del usuario creado o false si falla
     */
    public function registrarUsuario(array $datos)
    {
        $this->customErrors = [];

        // Validar que la contraseña esté presente antes de todo
        if (empty($datos['password'])) {
            $this->customErrors['password'] = 'La contraseña es obligatoria.';
            return false;
        }

        // Validar con las reglas definidas (mientras 'password' aún existe en $datos)
        if (!$this->validate($datos)) {
            // Los errores quedan accesibles vía $this->errors() del framework
            return false;
        }

        // Construir el array final solo con campos permitidos en la BD
        $datosFinales = [
            'id_rol'         => (int) $datos['id_rol'],
            'nombre'         => trim($datos['nombre']),
            'apellido'       => trim($datos['apellido'] ?? ''),
            'dni'            => (int) $datos['dni'],
            'f_nacimiento'   => !empty($datos['f_nacimiento']) ? $datos['f_nacimiento'] : null,
            'username'       => trim($datos['username']),
            'email'          => strtolower(trim($datos['email'])),
            'password_hash'  => password_hash(trim($datos['password']), PASSWORD_BCRYPT),
            'activo'         => 1,
            'fecha_creacion' => date('Y-m-d H:i:s'),
        ];

        if ($this->insert($datosFinales)) {
            return $this->insertID();
        }

        return false;
    }

    // ========================================
    // MÉTODOS DE VALIDACIÓN
    // ========================================

    /**
     * @param string $email
     * @return bool
     */
    public function emailExiste(string $email): bool
    {
        return $this->where('email', $email)->first() !== null;
    }

    /**
     * @param string $username
     * @return bool
     */
    public function usernameExiste(string $username): bool
    {
        return $this->where('username', $username)->first() !== null;
    }

    /**
     * @param int $dni
     * @return bool
     */
    public function dniExiste(int $dni): bool
    {
        return $this->where('dni', $dni)->first() !== null;
    }

    /**
     * Verificar si el email existe excluyendo un usuario específico (útil en actualizaciones)
     *
     * @param string $email
     * @param int    $excludeId
     * @return bool
     */
    public function emailExisteExcluir(string $email, int $excludeId): bool
    {
        return $this->where('email', $email)
                    ->where('id_usuario !=', $excludeId)
                    ->first() !== null;
    }

    // ========================================
    // MÉTODOS DE CONSULTA
    // ========================================

    /**
     * Obtener usuario por ID con datos del rol
     *
     * @param int $id
     * @return array|null
     */
    public function getUsuarioConRol(int $id): ?array
    {
        return $this->select('usuarios.*, roles.nombre as rol_nombre, roles.descripcion as rol_descripcion')
                    ->join('roles', 'roles.id_rol = usuarios.id_rol', 'left')
                    ->where('usuarios.id_usuario', $id)
                    ->first();
    }

    /**
     * Obtener todos los usuarios activos con datos del rol
     *
     * @return array
     */
    public function getUsuariosActivos(): array
    {
        return $this->select('usuarios.*, roles.nombre as rol_nombre')
                    ->join('roles', 'roles.id_rol = usuarios.id_rol', 'left')
                    ->where('usuarios.activo', 1)
                    ->orderBy('usuarios.nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Obtener usuarios por rol
     *
     * @param int $idRol
     * @return array
     */
    public function getUsuariosPorRol(int $idRol): array
    {
        return $this->where('id_rol', $idRol)
                    ->where('activo', 1)
                    ->orderBy('nombre', 'ASC')
                    ->findAll();
    }

    // ========================================
    // MÉTODOS DE ACTUALIZACIÓN
    // ========================================

    /**
     * Actualizar contraseña de un usuario
     *
     * @param int    $idUsuario
     * @param string $passwordActual
     * @param string $passwordNueva
     * @return bool
     */
    public function actualizarPassword(int $idUsuario, string $passwordActual, string $passwordNueva): bool
    {
        $this->customErrors = [];

        $usuario = $this->find($idUsuario);

        if (!$usuario) {
            return false;
        }

        if (!password_verify($passwordActual, $usuario['password_hash'])) {
            // FIX: usar customErrors en lugar de $this->errors para no pisar el método del framework
            $this->customErrors['password_actual'] = 'La contraseña actual es incorrecta.';
            return false;
        }

        return $this->update($idUsuario, [
            'password_hash' => password_hash($passwordNueva, PASSWORD_BCRYPT)
        ]);
    }

    /**
     * Activar o desactivar usuario
     *
     * @param int  $idUsuario
     * @param bool $activo
     * @return bool
     */
    public function setActivo(int $idUsuario, bool $activo): bool
    {
        return $this->update($idUsuario, ['activo' => $activo ? 1 : 0]);
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Obtener TODOS los errores: los del framework CI4 + los errores manuales
     *
     * FIX: antes se usaba $this->errors (propiedad) que pisaba el método errors() de CI4
     * y devolvía siempre un array vacío. Ahora se combinan correctamente.
     *
     * @return array
     */
    public function getErrores(): array
    {
        return array_merge($this->errors(), $this->customErrors);
    }

    /**
     * Obtener el primer error disponible
     *
     * @return string|null
     */
    public function getPrimerError(): ?string
    {
        $errores = $this->getErrores();
        return !empty($errores) ? reset($errores) : null;
    }
}
