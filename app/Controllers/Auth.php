<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    protected $usuarioModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->usuarioModel = model('UsuarioModel');
    }

    /**
     * Mostrar formulario de login
     */
    public function login()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (session()->get('autenticado')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    /**
     * Procesar autenticación de login
     * Usa el método validarLogin() del modelo
     */
    public function authenticate()
    {
        // Validar método POST
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/auth/login');
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validaciones básicas
        if (!$email || !$password) {
            return redirect()->back()
                ->with('error', 'Email y contraseña son requeridos.')
                ->withInput();
        }

        // Usar método del modelo para validar credenciales
        $usuario = $this->usuarioModel->validarLogin($email, $password);

        if (!$usuario) {
            return redirect()->back()
                ->with('error', 'Email/Usuario o contraseña incorrectos.')
                ->withInput();
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

        // Log de acceso (opcional)
        log_message('info', "Usuario {$usuario['username']} inició sesión");

        return redirect()->to('/dashboard')
            ->with('success', "Bienvenido {$usuario['nombre']}");
    }

    /**
     * Mostrar formulario de registro
     */
    public function register()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (session()->get('autenticado')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register');
    }

    /**
     * Procesar registro de nuevo usuario
     * Usa el método registrarUsuario() del modelo
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
            'id_rol'       => 1  // Rol 1 (Admin) por defecto
        ];

        // Validación adicional: confirmar contraseña
        if ($datos['password'] !== $datos['password_confirm']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Las contraseñas no coinciden.');
        }

        // Remover campo de confirmación antes de pasar al modelo
        unset($datos['password_confirm']);

        // Registrar usuario usando el método del modelo
        $idUsuario = $this->usuarioModel->registrarUsuario($datos);

        if ($idUsuario && $idUsuario > 0) {
            // Registro exitoso
            log_message('info', "Nuevo usuario registrado: {$datos['username']} con ID: {$idUsuario}");

            return redirect()->to('/auth/login')
                ->with('success', 'Registro exitoso. Por favor inicia sesión con tus credenciales.');
        }

        // Registro fallido - obtener errores del modelo
        $errores = $this->usuarioModel->getErrores();
        log_message('error', "Fallo en registro de {$datos['username']}: " . json_encode($errores));

        return redirect()->back()
            ->withInput()
            ->with('errores', $errores)
            ->with('error', 'Ocurrió un error durante el registro. Revisa los campos.');
    }

    /**
     * Cerrar sesión
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
}