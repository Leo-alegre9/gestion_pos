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

    /**
     * Buscar usuario por username o email
     */
    public function getUsuarioPorLogin(string $login)
    {
        return $this->select('usuarios.*, roles.nombre as rol_nombre')
                    ->join('roles', 'roles.id_rol = usuarios.id_rol')
                    ->groupStart()
                        ->where('usuarios.username', $login)
                        ->orWhere('usuarios.email', $login)
                    ->groupEnd()
                    ->first();
    }
}