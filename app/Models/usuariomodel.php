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
        'nombre'      => 'required|string|max_length[100]',
        'apellido'    => 'string|max_length[100]|permit_empty',
        'dni'         => 'required|numeric|is_unique[usuarios.dni]',
        'username'    => 'required|string|min_length[3]|max_length[50]|is_unique[usuarios.username]',
        'email'       => 'required|valid_email|max_length[120]|is_unique[usuarios.email]',
        'password'    => 'required|string|min_length[6]|max_length[255]',
        'id_rol'      => 'required|integer',
        'f_nacimiento' => 'valid_date|permit_empty'
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre es obligatorio.',
            'max_length' => 'El nombre no puede exceder 100 caracteres.'
        ],
        'dni' => [
            'required' => 'El DNI es obligatorio.',
            'integer' => 'El DNI debe ser un número.',
            'is_unique' => 'Este DNI ya está registrado en el sistema.'
        ],
        'username' => [
            'required' => 'El nombre de usuario es obligatorio.',
            'min_length' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'max_length' => 'El nombre de usuario no puede exceder 50 caracteres.',
            'is_unique' => 'Este nombre de usuario ya está en uso.'
        ],
        'email' => [
            'required' => 'El email es obligatorio.',
            'valid_email' => 'Ingresa un email válido.',
            'is_unique' => 'Este email ya está registrado en el sistema.'
        ],
        'password' => [
            'required' => 'La contraseña es obligatoria.',
            'min_length' => 'La contraseña debe tener al menos 6 caracteres.'
        ],
        'id_rol' => [
            'required' => 'El rol es obligatorio.'
        ]
    ];

    // ========================================
    // MÉTODOS DE AUTENTICACIÓN
    // ========================================

    /**
     * Validar credenciales de login
     * 
     * @param string $login Email o username
     * @param string $password Contraseña en texto plano
     * @return array|null Datos del usuario si la autenticación es exitosa, null si falla
     */
    public function validarLogin(string $login, string $password): ?array
    {
        $usuario = $this->getUsuarioPorLogin($login);

        if (!$usuario) {
            return null;
        }

        // Verificar que el usuario esté activo
        if (!$usuario['activo']) {
            return null;
        }

        // Verificar la contraseña usando password_verify
        if (!password_verify($password, $usuario['password_hash'])) {
            return null;
        }

        // Retornar datos del usuario sin la contraseña
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
     * @param array $datos Datos del usuario
     * @return int|false ID del usuario creado o false si falla
     */
    public function registrarUsuario(array $datos)
    {
        // Validar que la contraseña esté presente
        if (!isset($datos['password']) || empty($datos['password'])) {
            $this->errors['password'] = 'La contraseña es obligatoria.';
            return false;
        }

        // VALIDAR DATOS ANTES DE PROCESAR
        if (!$this->validate($datos)) {
            return false;
        }

        // Extraer password y hacer trim de campos string
        $password = trim($datos['password']);
        
        // Limpiar campos de texto
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

        // Insertar usuario
        try {
            $resultado = $this->insert($datosFinales, false);
            
            if ($resultado === false) {
                $error = $this->db->error();
                $mensajeError = $error['message'] ?? 'Error desconocido en la BD';
                $this->errors['database'] = $mensajeError;
                log_message('error', "Error al insertar usuario: {$mensajeError}");
                return false;
            }
            
            $idUsuario = $this->insertID();
            log_message('error', "Usuario registrado: ID={$idUsuario}");
            return $idUsuario;
            
        } catch (\Exception $e) {
            $this->errors['database'] = 'Error en la base de datos: ' . $e->getMessage();
            log_message('error', 'Excepción en registro: ' . $e->getMessage());
            return false;
        }
    }

    // ========================================
    // MÉTODOS DE VALIDACIÓN
    // ========================================

    /**
     * Validar si el email existe
     * 
     * @param string $email
     * @return bool
     */
    public function emailExiste(string $email): bool
    {
        return $this->where('email', $email)->first() !== null;
    }

    /**
     * Validar si el username existe
     * 
     * @param string $username
     * @return bool
     */
    public function usernameExiste(string $username): bool
    {
        return $this->where('username', $username)->first() !== null;
    }

    /**
     * Validar si el DNI existe
     * 
     * @param int $dni
     * @return bool
     */
    public function dniExiste(int $dni): bool
    {
        return $this->where('dni', $dni)->first() !== null;
    }

    /**
     * Verificar si el email existe (excluyendo un usuario específico)
     * Útil para validación en actualizaciones
     * 
     * @param string $email
     * @param int $excludeId ID del usuario a excluir
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
     * @param int $idUsuario
     * @param string $passwordActual
     * @param string $passwordNueva
     * @return bool
     */
    public function actualizarPassword(int $idUsuario, string $passwordActual, string $passwordNueva): bool
    {
        $usuario = $this->find($idUsuario);
        
        if (!$usuario) {
            return false;
        }

        // Verificar contraseña actual
        if (!password_verify($passwordActual, $usuario['password_hash'])) {
            $this->errors['password_actual'] = 'La contraseña actual es incorrecta.';
            return false;
        }

        // Actualizar con nueva contraseña
        return $this->update($idUsuario, [
            'password_hash' => password_hash($passwordNueva, PASSWORD_BCRYPT)
        ]);
    }

    /**
     * Activar o desactivar usuario
     * 
     * @param int $idUsuario
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
     * Obtener errores de validación
     * 
     * @return array
     */
    public function getErrores(): array
    {
        return $this->errors ?? [];
    }

    /**
     * Obtener primer error de validación
     * 
     * @return string|null
     */
    public function getPrimerError(): ?string
    {
        $errores = $this->getErrores();
        return !empty($errores) ? reset($errores) : null;
    }
}