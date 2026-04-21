<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaProductoModel extends Model
{
    protected $table      = 'categorias_productos';
    protected $primaryKey = 'id_categoria';
    protected $returnType = 'array';
    protected $allowedFields = [
        'nombre',
        'descripcion',
        'activa',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'nombre'      => 'required|string|max_length[100]|is_unique[categorias_productos.nombre,id_categoria,{id_categoria}]',
        'descripcion' => 'permit_empty|string|max_length[255]',
        'activa'      => 'permit_empty|integer|in_list[0,1]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required'   => 'El nombre de la categoría es obligatorio.',
            'max_length' => 'El nombre no puede exceder 100 caracteres.',
            'is_unique'  => 'Ya existe una categoría con ese nombre.',
        ],
        'descripcion' => [
            'max_length' => 'La descripción no puede exceder 255 caracteres.',
        ],
    ];

    /**
     * Retorna todas las categorías (activas e inactivas) para vistas admin.
     */
    public function getAll(): array
    {
        return $this->orderBy('nombre', 'ASC')->findAll();
    }

    /**
     * Retorna categorías activas para uso en formularios de productos.
     */
    public function getCategoriasActivas(): array
    {
        return $this->where('activa', 1)
            ->orderBy('nombre', 'ASC')
            ->findAll();
    }
}
