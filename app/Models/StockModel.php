<?php

namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table            = 'stock';
    protected $primaryKey       = 'id_stock';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_producto', 'cantidad_disponible', 'cantidad_minima', 'ultima_actualizacion'];

    protected $useTimestamps    = false;

    /**
     * Reglas de validación para operaciones CRUD de stock.
     */
    protected $validationRules = [
        'id_producto'           => 'required|integer|greater_than[0]',
        'cantidad_disponible'   => 'required|decimal|greater_than_equal_to[0]',
        'cantidad_minima'       => 'required|decimal|greater_than_equal_to[0]',
    ];

    /**
     * Mensajes de validación personalizados.
     */
    protected $validationMessages = [
        'id_producto' => [
            'required' => 'El producto es requerido.',
            'integer' => 'Producto inválido.',
        ],
        'cantidad_disponible' => [
            'required' => 'La cantidad actual es requerida.',
            'decimal' => 'La cantidad debe ser un número válido.',
            'greater_than_equal_to' => 'La cantidad no puede ser negativa.',
        ],
        'cantidad_minima' => [
            'required' => 'La cantidad mínima es requerida.',
            'greater_than_equal_to' => 'La cantidad mínima no puede ser negativa.',
        ],
    ];

    /**
     * Obtiene el stock completo con información del producto.
     * Realiza un JOIN con la tabla productos para mostrar datos relevantes.
     * 
     * @return array Stock con detalles de productos.
     */
    public function getStockConProductos(): array
    {
        return $this->select('
                stock.*,
                productos.nombre as producto_nombre,
                productos.descripcion,
                categorias_productos.nombre as categoria_nombre
            ', false)
            ->join('productos', 'productos.id_producto = stock.id_producto', 'left')
            ->join('categorias_productos', 'categorias_productos.id_categoria = productos.id_categoria', 'left')
            ->orderBy('stock.cantidad_disponible', 'ASC')
            ->findAll();
    }

    /**
     * Obtiene productos con stock bajo (por debajo del mínimo).
     * Utilizado para alertas de reabastecimiento.
     * 
     * @return array Productos con stock crítico.
     */
    public function getStockBajo(): array
    {
        return $this->select('
                stock.*,
                productos.nombre as producto_nombre,
                categorias_productos.nombre as categoria_nombre,
                CASE
                    WHEN stock.cantidad_disponible = 0 THEN \'critico\'
                    WHEN stock.cantidad_disponible < stock.cantidad_minima THEN \'bajo\'
                    ELSE \'normal\'
                END as nivel_alerta
            ', false)
            ->join('productos', 'productos.id_producto = stock.id_producto', 'left')
            ->join('categorias_productos', 'categorias_productos.id_categoria = productos.id_categoria', 'left')
            ->where('stock.cantidad_disponible <= stock.cantidad_minima', null, false)
            ->orderBy('stock.cantidad_disponible', 'ASC')
            ->findAll();
    }

    /**
     * Obtiene el stock de un producto específico.
     * 
     * @param int $idProducto ID del producto.
     * @return array|null Información de stock o null si no existe.
     */
    public function getStockPorProducto(int $idProducto): ?array
    {
        return $this->select('
                stock.*,
                productos.nombre as producto_nombre,
                productos.precio_venta
            ', false)
            ->join('productos', 'productos.id_producto = stock.id_producto', 'left')
            ->where('stock.id_producto', $idProducto)
            ->first();
    }

    /**
     * Actualiza la cantidad de stock de un producto.
     * Valida que no se vuelva negativo.
     * 
     * @param int $idProducto ID del producto.
     * @param int $diferencia Cantidad a sumar (positiva) o restar (negativa).
     * @return bool True si se actualizó exitosamente.
     */
    public function actualizarCantidad(int $idProducto, int $diferencia): bool
    {
        $stock = $this->where('id_producto', $idProducto)->first();
        
        if (!$stock) {
            return false;
        }

        $nuevaCantidad = (float) $stock['cantidad_disponible'] + $diferencia;
        
        if ($nuevaCantidad < 0) {
            return false;
        }

        $this->update($stock['id_stock'], [
            'cantidad_disponible' => $nuevaCantidad,
            'ultima_actualizacion' => date('Y-m-d H:i:s')
        ]);

        return true;
    }

    /**
     * Verifica si hay suficiente stock de un producto.
     * 
     * @param int $idProducto ID del producto.
     * @param int $cantidad Cantidad requerida.
     * @return bool True si hay suficiente stock.
     */
    public function haySuficienteStock(int $idProducto, int $cantidad): bool
    {
        $stock = $this->where('id_producto', $idProducto)->first();
        
        if (!$stock) {
            return false;
        }

        return (float) $stock['cantidad_disponible'] >= $cantidad;
    }
}
