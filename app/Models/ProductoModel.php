<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoModel extends Model
{
    protected $table            = 'productos';
    protected $primaryKey       = 'id_producto';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_categoria', 'nombre', 'descripcion', 'precio_venta', 'se_vende_en_barra', 'controla_stock', 'activo'];

    protected $useTimestamps    = false;

    /**
     * Reglas de validación para operaciones CRUD de productos.
     * Incluye validaciones de unicidad de nombre y restricciones de precio.
     */
    protected $validationRules = [
        'id_producto'        => 'permit_empty|integer',
        'id_categoria'       => 'required|integer|greater_than[0]',
        'nombre'             => 'required|string|max_length[120]|is_unique[productos.nombre,id_producto,{id_producto}]',
        'descripcion'        => 'permit_empty|string|max_length[255]',
        'precio_venta'       => 'required|decimal|greater_than[0]',
        'se_vende_en_barra'  => 'integer|in_list[0,1]',
        'controla_stock'     => 'integer|in_list[0,1]',
        'activo'             => 'integer|in_list[0,1]',
    ];

    /**
     * Mensajes de validación personalizados para mejorar UX.
     */
    protected $validationMessages = [
        'id_categoria' => [
            'required' => 'La categoría es requerida.',
            'integer' => 'La categoría debe ser un número válido.',
            'greater_than' => 'Selecciona una categoría válida.',
        ],
        'nombre' => [
            'required' => 'El nombre del producto es requerido.',
            'max_length' => 'El nombre no puede exceder 120 caracteres.',
            'is_unique' => 'Ya existe un producto con este nombre.',
        ],
        'descripcion' => [
            'max_length' => 'La descripción no puede exceder 255 caracteres.',
        ],
        'precio_venta' => [
            'required' => 'El precio de venta es requerido.',
            'decimal' => 'El precio debe ser un número válido (ej: 10.50).',
            'greater_than' => 'El precio debe ser mayor a 0.',
        ],
    ];

    /**
     * Obtiene todos los productos (activos e inactivos) con su categoría.
     * Usado en el panel de administración para gestión completa.
     */
    public function getTodos(): array
    {
        return $this->select('
                productos.id_producto,
                productos.nombre,
                productos.descripcion,
                productos.precio_venta,
                productos.se_vende_en_barra,
                productos.controla_stock,
                productos.activo,
                categorias_productos.nombre as categoria_nombre,
                categorias_productos.id_categoria
            ')
            ->join('categorias_productos', 'categorias_productos.id_categoria = productos.id_categoria', 'left')
            ->orderBy('productos.activo', 'DESC')
            ->orderBy('categorias_productos.nombre', 'ASC')
            ->orderBy('productos.nombre', 'ASC')
            ->findAll();
    }

    /**
     * Obtiene productos activos con su categoría.
     * Usado en pedidos y otros contextos donde solo interesan productos disponibles.
     */
    public function getProductosActivos(): array
    {
        return $this->select('
                productos.id_producto,
                productos.nombre,
                productos.descripcion,
                productos.precio_venta,
                productos.se_vende_en_barra,
                productos.controla_stock,
                productos.activo,
                categorias_productos.nombre as categoria_nombre,
                categorias_productos.id_categoria
            ')
            ->join('categorias_productos', 'categorias_productos.id_categoria = productos.id_categoria', 'left')
            ->where('productos.activo', 1)
            ->orderBy('categorias_productos.nombre', 'ASC')
            ->orderBy('productos.nombre', 'ASC')
            ->findAll();
    }

    /**
     * Obtiene productos filtrados por categoría.
     * Utilizado en la barra para mostrar solo los productos que se venden en ella.
     * 
     * @param int $idCategoria ID de la categoría a filtrar.
     * @param bool $soloVentaBarra Si true, solo retorna productos marcados para venta en barra.
     * @return array Lista de productos filtrados.
     */
    public function getProductosPorCategoria(int $idCategoria, bool $soloVentaBarra = false): array
    {
        $query = $this->where('id_categoria', $idCategoria)
            ->where('activo', 1);

        if ($soloVentaBarra) {
            $query->where('se_vende_en_barra', 1);
        }

        return $query->orderBy('nombre', 'ASC')->findAll();
    }

    /**
     * Obtiene productos que requieren control de stock.
     * Utilizado en el inventario para mostrar solo los artículos que necesitan seguimiento.
     * 
     * @return array Lista de productos con control de stock habilitado.
     */
    public function getProductosConControlStock(): array
    {
        return $this->where('controla_stock', 1)
            ->where('activo', 1)
            ->orderBy('nombre', 'ASC')
            ->findAll();
    }

    /**
     * Obtiene el producto con información completa incluyendo categoría.
     * Usado antes de mostrar detalles o editar.
     * 
     * @param int $idProducto ID del producto.
     * @return array|null Datos del producto con categoría o null si no existe.
     */
    public function getProductoConCategoria(int $idProducto): ?array
    {
        return $this->select('
                productos.*,
                categorias_productos.nombre as categoria_nombre
            ')
            ->join('categorias_productos', 'categorias_productos.id_categoria = productos.id_categoria', 'left')
            ->where('productos.id_producto', $idProducto)
            ->first();
    }
}
