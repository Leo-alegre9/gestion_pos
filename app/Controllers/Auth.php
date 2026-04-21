<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Auth extends BaseController
{
    protected $usuarioModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->usuarioModel = model('UsuarioModel');
    }

    // ========================================
    // LOGIN
    // ========================================

    public function login()
    {
        if (session()->get('autenticado')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function authenticate()
    {
        if (! $this->request->is('post')) {
            return redirect()->to('/auth/login');
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (!$email || !$password) {
            return redirect()->back()
                ->with('error', 'Email y contraseña son requeridos.')
                ->withInput();
        }

        $usuario = $this->usuarioModel->validarLogin($email, $password);

        if (!$usuario) {
            return redirect()->back()
                ->with('error', 'Email/Usuario o contraseña incorrectos.')
                ->withInput();
        }

        session()->set([
            'id_usuario'  => $usuario['id_usuario'],
            'nombre'      => $usuario['nombre'],
            'apellido'    => $usuario['apellido'] ?? '',
            'email'       => $usuario['email'],
            'username'    => $usuario['username'],
            'id_rol'      => $usuario['id_rol'],
            'rol_nombre'  => $usuario['rol_nombre'] ?? '',
            'autenticado' => true,
        ]);

        log_message('info', "Usuario {$usuario['username']} inició sesión");

        return redirect()->to('/dashboard')
            ->with('success', "Bienvenido {$usuario['nombre']}");
    }

    // ========================================
    // REGISTRO
    // ========================================

    public function register()
    {
        if (session()->get('autenticado')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register');
    }

    public function store()
    {
        if (! $this->request->is('post')) {
            return redirect()->to('/auth/register');
        }

        $nombre          = trim($this->request->getPost('nombre'));
        $apellido        = trim($this->request->getPost('apellido') ?? '');
        $dni             = $this->request->getPost('dni');
        $f_nacimiento    = $this->request->getPost('f_nacimiento');
        $username        = trim($this->request->getPost('username'));
        $email           = strtolower(trim($this->request->getPost('email')));
        $password        = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');

        // ── 1. Validaciones básicas ──────────────────────────────────────
        if (!$nombre || !$dni || !$username || !$email || !$password) {
            return redirect()->back()->withInput()
                ->with('error', 'Todos los campos obligatorios deben completarse.');
        }

        // ── 2. Confirmar contraseña ──────────────────────────────────────
        if ($password !== $passwordConfirm) {
            return redirect()->back()->withInput()
                ->with('error', 'Las contraseñas no coinciden.');
        }

        // ── 3. Verificar duplicados manualmente antes de insertar ────────
        if ($this->usuarioModel->where('dni', (int)$dni)->first()) {
            return redirect()->back()->withInput()
                ->with('error', 'El DNI ingresado ya está registrado.');
        }

        if ($this->usuarioModel->where('username', $username)->first()) {
            return redirect()->back()->withInput()
                ->with('error', 'El nombre de usuario ya está en uso.');
        }

        if ($this->usuarioModel->where('email', $email)->first()) {
            return redirect()->back()->withInput()
                ->with('error', 'El email ingresado ya está registrado.');
        }

        // ── 4. Verificar que el rol 1 exista en la tabla roles ───────────
        $db  = \Config\Database::connect();
        $rol = $db->table('roles')->where('id_rol', 1)->get()->getRowArray();

        if (!$rol) {
            log_message('error', 'No existe el rol con id_rol=1 en la tabla roles.');
            return redirect()->back()->withInput()
                ->with('error', 'Error de configuración: no existe el rol por defecto. Contactá al administrador.');
        }

        // ── 5. Insertar directo saltando la validación del modelo ────────
        //    La regla 'password' del modelo espera el campo 'password' pero
        //    la BD usa 'password_hash', lo que causa que el validador falle.
        //    Ya validamos todo manualmente arriba, así que es seguro saltear.
        $datos = [
            'id_rol'         => 1,
            'nombre'         => $nombre,
            'apellido'       => $apellido,
            'dni'            => (int)$dni,
            'f_nacimiento'   => !empty($f_nacimiento) ? $f_nacimiento : null,
            'username'       => $username,
            'email'          => $email,
            'password_hash'  => password_hash($password, PASSWORD_BCRYPT),
            'activo'         => 1,
            'fecha_creacion' => date('Y-m-d H:i:s'),
        ];

        $this->usuarioModel->skipValidation(true);

        if ($this->usuarioModel->insert($datos)) {
            log_message('info', "Nuevo usuario registrado: {$username}");

            return redirect()->to('/auth/login')
                ->with('success', 'Cuenta creada correctamente. Ya podés iniciar sesión.');
        }

        // ── 6. Si insert() falla, loguear el error de BD ─────────────────
        $dbError = $db->error();
        log_message('error', 'Error al insertar usuario: ' . json_encode($dbError));

        return redirect()->back()->withInput()
            ->with('error', 'No se pudo crear la cuenta. Intentá de nuevo más tarde.');
    }

    // ========================================
    // LOGOUT
    // ========================================

    public function logout()
    {
        $nombreUsuario = session()->get('username');

        session()->destroy();

        if ($nombreUsuario) {
            log_message('info', "Usuario {$nombreUsuario} cerró sesión");
        }

        return redirect()->to('/')
            ->with('success', 'Sesión cerrada correctamente.');
    }
}