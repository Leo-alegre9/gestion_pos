<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Auth extends BaseController
{
    /**
     * Mostrar formulario de login
     */
    public function login()
    {
        return view('auth/login');
    }

    /**
     * Procesar login real con base de datos
     */
    public function authenticate()
    {
        $login    = trim($this->request->getPost('login'));
        $password = $this->request->getPost('password');

        // Validación básica
        if (empty($login) || empty($password)) {
            return redirect()->back()
                ->with('error', 'Usuario/email y contraseña son obligatorios')
                ->withInput();
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->getUsuarioPorLogin($login);

        // Verificar existencia
        if (!$usuario) {
            return redirect()->back()
                ->with('error', 'Usuario o contraseña incorrectos')
                ->withInput();
        }

        // Verificar si está activo
        if ((int)$usuario['activo'] !== 1) {
            return redirect()->back()
                ->with('error', 'Tu usuario está inactivo')
                ->withInput();
        }

        // Verificar contraseña
        if (!password_verify($password, $usuario['password_hash'])) {
            return redirect()->back()
                ->with('error', 'Usuario o contraseña incorrectos')
                ->withInput();
        }

        // Guardar sesión
        $sessionData = [
            'id_usuario'   => $usuario['id_usuario'],
            'nombre'       => $usuario['nombre'],
            'apellido'     => $usuario['apellido'],
            'username'     => $usuario['username'],
            'email'        => $usuario['email'],
            'id_rol'       => $usuario['id_rol'],
            'rol_nombre'   => $usuario['rol_nombre'],
            'isLoggedIn'   => true
        ];

        session()->set($sessionData);

        return redirect()->to('/dashboard')
            ->with('success', 'Sesión iniciada correctamente');
    }

    /**
     * Mostrar formulario de registro
     */
    public function register()
    {
        return view('auth/register');
    }

    /**
     * Procesar registro real en base de datos
     */
    public function storeRegister()
    {
        $nombre           = trim($this->request->getPost('nombre'));
        $apellido         = trim($this->request->getPost('apellido'));
        $dni              = trim($this->request->getPost('dni'));
        $f_nacimiento     = $this->request->getPost('f_nacimiento');
        $username         = trim($this->request->getPost('username'));
        $email            = trim($this->request->getPost('email'));
        $password         = $this->request->getPost('password');
        $password_confirm = $this->request->getPost('password_confirm');

        // Validaciones
        if (
            empty($nombre) ||
            empty($dni) ||
            empty($username) ||
            empty($email) ||
            empty($password) ||
            empty($password_confirm)
        ) {
            return redirect()->back()
                ->with('error', 'Todos los campos obligatorios deben completarse')
                ->withInput();
        }

        if ($password !== $password_confirm) {
            return redirect()->back()
                ->with('error', 'Las contraseñas no coinciden')
                ->withInput();
        }

        $usuarioModel = new UsuarioModel();

        // Verificar username existente
        $existeUsername = $usuarioModel->where('username', $username)->first();
        if ($existeUsername) {
            return redirect()->back()
                ->with('error', 'El nombre de usuario ya está en uso')
                ->withInput();
        }

        // Verificar email existente
        $existeEmail = $usuarioModel->where('email', $email)->first();
        if ($existeEmail) {
            return redirect()->back()
                ->with('error', 'El email ya está registrado')
                ->withInput();
        }

        // Insertar usuario
        $data = [
            'id_rol'         => 2, // por ejemplo: 2 = empleado/usuario
            'nombre'         => $nombre,
            'apellido'       => $apellido,
            'dni'            => $dni,
            'f_nacimiento'   => !empty($f_nacimiento) ? $f_nacimiento : null,
            'username'       => $username,
            'password_hash'  => password_hash($password, PASSWORD_DEFAULT),
            'email'          => $email,
            'activo'         => 1
        ];

        if (!$usuarioModel->insert($data)) {
            return redirect()->back()
                ->with('error', 'No se pudo registrar el usuario')
                ->withInput();
        }

        return redirect()->to('/login')
            ->with('success', 'Registro exitoso. Ahora puedes iniciar sesión');
    }

    /**
     * Logout
     */
    public function logout()
    {
        session()->destroy();

        return redirect()->to('/')
            ->with('success', 'Sesión cerrada correctamente');
    }
}