<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controlador de Autenticación
 * 
 * Maneja el login, registro y logout de usuarios
 * Ejemplo de implementación basado en UsuarioModel mejorado
 */
class Auth extends BaseController
{
    protected $usuarioModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->usuarioModel = model('UsuarioModel');
    }

    // ========================================
    // VISTAS
    // ========================================

    /**
     * Mostrar formulario de login
     */
    public function login()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (session()->get('usuario')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    /**
     * Mostrar formulario de registro
     */
    public function register()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (session()->get('usuario')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register');
    }

    // ========================================
    // PROCESAMIENTO DE LOGIN
    // ========================================

    /**
     * Procesar autenticación de login
     * POST /auth/authenticate
     */
    public function authenticate()
    {
        // Validar método POST
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/auth/login');
        }

        // Obtener datos del formulario
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');

        // Validaciones básicas
        if (!$email || !$password) {
            return redirect()->back()
                ->with('error', 'Email y contraseña son requeridos.');
        }

        // Validar credenciales usando el modelo
        $usuario = $this->usuarioModel->validarLogin($email, $password);

        if (!$usuario) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email/Usuario o contraseña incorrectos.');
        }

        // Credenciales válidas - Crear sesión
        $datosSession = [
            'id_usuario'  => $usuario['id_usuario'],
            'nombre'      => $usuario['nombre'],
            'apellido'    => $usuario['apellido'] ?? '',
            'email'       => $usuario['email'],
            'username'    => $usuario['username'],
            'id_rol'      => $usuario['id_rol'],
            'rol_nombre'  => $usuario['rol_nombre'] ?? '',
            'autenticado' => true
        ];

        session()->set($datosSession);

        // Recordar usuario si marcó la opción (opcional)
        if ($remember) {
            // Aquí podrías guardar un token de recuerdo en cookies
            // Por ahora solo prolongamos la sesión
            session()->setTempdata('_remember', 1, 7 * 24 * 60 * 60);
        }

        // Log de acceso (opcional)
        log_message('info', "Usuario {$usuario['username']} inició sesión");

        // Redirigir al dashboard
        return redirect()->to('/dashboard')
            ->with('success', "Bienvenido {$usuario['nombre']}");
    }

    // ========================================
    // PROCESAMIENTO DE REGISTRO
    // ========================================

    /**
     * Procesar registro de nuevo usuario
     * POST /auth/store
     */
    public function store()
    {
        // Validar método POST
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/auth/register');
        }

        // Obtener datos del formulario
        $datos = [
            'nombre'       => $this->request->getPost('nombre'),
            'apellido'     => $this->request->getPost('apellido'),
            'dni'          => $this->request->getPost('dni'),
            'f_nacimiento' => $this->request->getPost('f_nacimiento'),
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email'),
            'password'     => $this->request->getPost('password'),
            'password_confirm' => $this->request->getPost('password_confirm'),
            'id_rol'       => 2  // Rol de usuario regular por defecto
        ];

        // Validación adicional: confirmar contraseña
        if ($datos['password'] !== $datos['password_confirm']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Las contraseñas no coinciden.');
        }

        // Remover campo de confirmación antes de pasar al modelo
        unset($datos['password_confirm']);

        // Registrar usuario usando el modelo
        $idUsuario = $this->usuarioModel->registrarUsuario($datos);

        if ($idUsuario) {
            // Registro exitoso
            log_message('info', "Nuevo usuario registrado: {$datos['username']}");

            return redirect()->to('/auth/login')
                ->with('success', 'Registro exitoso. Por favor inicia sesión con tus credenciales.');
        }

        // Registro fallido - obtener errores
        $errores = $this->usuarioModel->getErrores();

        return redirect()->back()
            ->withInput()
            ->with('errores', $errores)
            ->with('error', 'Ocurrió un error durante el registro. Revisa los campos.');
    }

    // ========================================
    // LOGOUT
    // ========================================

    /**
     * Cerrar sesión
     * GET /auth/logout
     */
    public function logout()
    {
        $nombreUsuario = session()->get('username');
        
        // Limpiar sesión
        session()->destroy();

        // Log de logout
        if ($nombreUsuario) {
            log_message('info', "Usuario {$nombreUsuario} cerró sesión");
        }

        return redirect()->to('/')
            ->with('success', 'Sesión cerrada correctamente.');
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Verificar si el usuario está autenticado
     * 
     * @return bool
     */
    public function isAutenticado(): bool
    {
        return (bool) session()->get('autenticado');
    }

    /**
     * Obtener usuario actual de la sesión
     * 
     * @return array|null
     */
    public function getUsuarioActual(): ?array
    {
        if (!$this->isAutenticado()) {
            return null;
        }

        return [
            'id_usuario' => session()->get('id_usuario'),
            'nombre'     => session()->get('nombre'),
            'email'      => session()->get('email'),
            'username'   => session()->get('username'),
            'id_rol'     => session()->get('id_rol'),
            'rol_nombre' => session()->get('rol_nombre')
        ];
    }

    /**
     * Verificar si el usuario tiene un rol específico
     * 
     * @param string|int $rol Nombre o ID del rol
     * @return bool
     */
    public function tieneRol($rol): bool
    {
        if (!$this->isAutenticado()) {
            return false;
        }

        // Si es un número, comparar IDs
        if (is_numeric($rol)) {
            return session()->get('id_rol') == $rol;
        }

        // Si es string, comparar nombres
        return session()->get('rol_nombre') === $rol;
    }
}
