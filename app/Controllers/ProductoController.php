<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoriaProductoModel;
use App\Models\ProductoModel;

class ProductoController extends BaseController
{
    /**
     * @var ProductoModel Modelo para operaciones CRUD de productos
     */
    protected $productoModel;
    protected $categoriaModel;

    public function __construct()
    {
        $this->productoModel = new ProductoModel();
        $this->categoriaModel = new CategoriaProductoModel();
    }

    /**
     * Muestra el listado de todos los productos activos.
     * Incluye información de categoría y disponibilidad.
     * 
     * @return string Genera el HTML para la vista de productos.
     */
    public function index()
    {
        $productos = $this->productoModel->getTodos();

        return view('productos/index', [
            'titulo' => 'Productos',
            'productos' => $productos,
            'user' => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo producto.
     * 
     * @return string Genera el HTML para el formulario de creación.
     */
    public function create()
    {
        $categorias = $this->categoriaModel->getCategoriasActivas();

        return view('productos/crear', [
            'categorias' => $categorias,
            'user' => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }

    /**
     * Almacena un nuevo producto en la base de datos.
     * Valida los datos ingresados y maneja errores de validación.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige a la lista de productos con estado de la operación.
     */
    public function store()
    {
        $descripcion = trim((string) $this->request->getPost('descripcion'));

        // 1. Recopilar datos del formulario con sanitización
        $dataProducto = [
            'id_categoria'      => (int) $this->request->getPost('id_categoria'),
            'nombre'            => trim((string) $this->request->getPost('nombre')),
            'descripcion'       => $descripcion === '' ? null : $descripcion,
            'precio_venta'      => (float) $this->request->getPost('precio_venta'),
            'se_vende_en_barra' => (int) ($this->request->getPost('se_vende_en_barra') ?? 0),
            'controla_stock'    => (int) ($this->request->getPost('controla_stock') ?? 0),
            'activo'            => 1, // Nuevos productos inician como activos
        ];

        // 2. Validar usando las reglas definidas en el modelo
        if (!$this->productoModel->validate($dataProducto)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->productoModel->errors());
        }

        // 3. Intentar guardar el producto en la BD
        if (!$this->productoModel->insert($dataProducto)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al guardar el producto. Por favor, intente nuevamente.');
        }

        return redirect()->to('/productos')
            ->with('success', "Producto '{$dataProducto['nombre']}' creado exitosamente.");
    }

    /**
     * Muestra el formulario para editar un producto existente.
     * 
     * @param int $idProducto ID del producto a editar.
     * @return string Genera el HTML para el formulario de edición.
     */
    public function edit(int $idProducto)
    {
        // 1. Verificar que el producto existe
        $producto = $this->productoModel->getProductoConCategoria($idProducto);
        if (!$producto) {
            return redirect()->back()->with('error', 'El producto no existe.');
        }

        $categorias = $this->categoriaModel->getCategoriasActivas();

        return view('productos/editar', [
            'producto' => $producto,
            'categorias' => $categorias,
            'user' => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }

    /**
     * Actualiza un producto existente en la base de datos.
     * 
     * @param int $idProducto ID del producto a actualizar.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige a la lista de productos con estado de la operación.
     */
    public function update(int $idProducto)
    {
        // 1. Verificar que el producto existe
        $producto = $this->productoModel->find($idProducto);
        if (!$producto) {
            return redirect()->back()->with('error', 'El producto no existe.');
        }

        $descripcion = trim((string) $this->request->getPost('descripcion'));

        // 2. Recopilar datos del formulario con sanitización
        $dataProducto = [
            'id_categoria'      => (int) $this->request->getPost('id_categoria'),
            'nombre'            => trim((string) $this->request->getPost('nombre')),
            'descripcion'       => $descripcion === '' ? null : $descripcion,
            'precio_venta'      => (float) $this->request->getPost('precio_venta'),
            'se_vende_en_barra' => (int) ($this->request->getPost('se_vende_en_barra') ?? 0),
            'controla_stock'    => (int) ($this->request->getPost('controla_stock') ?? 0),
            'activo'            => (int) ($this->request->getPost('activo') ?? 0),
        ];

        // 3. Validar — se pasa id_producto para que is_unique excluya el registro actual
        if (!$this->productoModel->validate($dataProducto + ['id_producto' => $idProducto])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->productoModel->errors());
        }

        // 4. Intentar actualizar el producto
        if (!$this->productoModel->update($idProducto, $dataProducto)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto. Por favor, intente nuevamente.');
        }

        return redirect()->to('/productos')
            ->with('success', "Producto actualizado exitosamente.");
    }

    /**
     * Desactiva un producto (no lo elimina físicamente).
     * Los productos desactivados no aparecen en vistas normales.
     * 
     * @param int $idProducto ID del producto a desactivar.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige a la lista de productos.
     */
    public function deactivate(int $idProducto)
    {
        // 1. Verificar que el producto existe
        $producto = $this->productoModel->find($idProducto);
        if (!$producto) {
            return redirect()->back()->with('error', 'El producto no existe.');
        }

        // 2. Desactivar el producto
        $nombreProducto = $producto['nombre'];
        if (!$this->productoModel->update($idProducto, ['activo' => 0])) {
            return redirect()->back()->with('error', 'Error al desactivar el producto.');
        }

        return redirect()->to('/productos')
            ->with('success', "Producto '{$nombreProducto}' desactivado exitosamente.");
    }
}
