<?php

namespace App\Controllers;

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
     * Procesar login (hardcodeado por ahora)
     */
    public function authenticate()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validación hardcodeada
        if ($email === 'admin@barpos.com' && $password === '123456') {
            // Aquí iría la lógica de sesión real
            return redirect()->to('/dashboard')->with('success', 'Sesión iniciada correctamente');
        }

        return redirect()->back()
            ->with('error', 'Email o contraseña incorrectos')
            ->withInput();
    }

    /**
     * Mostrar formulario de registro
     */
    public function register()
    {
        return view('auth/register');
    }

    /**
     * Procesar registro (hardcodeado por ahora)
     */
    public function storeRegister()
    {
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $password_confirm = $this->request->getPost('password_confirm');

        // Validación hardcodeada simple
        if (empty($name) || empty($email) || empty($password)) {
            return redirect()->back()
                ->with('error', 'Todos los campos son requeridos')
                ->withInput();
        }

        if ($password !== $password_confirm) {
            return redirect()->back()
                ->with('error', 'Las contraseñas no coinciden')
                ->withInput();
        }

        // Aquí iría la lógica real de registro
        return redirect()->to('/login')->with('success', 'Registro exitoso. Inicia sesión con tus credenciales');
    }

    /**
     * Logout
     */
    public function logout()
    {
        // Aquí iría la lógica real de logout
        return redirect()->to('/')->with('success', 'Sesión cerrada correctamente');
    }
}
