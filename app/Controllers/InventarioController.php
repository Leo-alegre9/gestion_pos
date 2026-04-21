<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductoModel;
use App\Models\StockModel;

class InventarioController extends BaseController
{
    /**
     * @var StockModel Modelo para operaciones de inventario y stock
     */
    protected $stockModel;
    protected $productoModel;

    public function __construct()
    {
        $this->stockModel = new StockModel();
        $this->productoModel = new ProductoModel();
    }

    /**
     * Muestra el resumen completo del inventario.
     * Incluye stock actual, mínimos y máximos de todos los productos.
     * 
     * @return string Genera el HTML para la vista de inventario.
     */
    public function index()
    {
        // 1. Obtener stock completo con información de productos
        $inventario = $this->stockModel->getStockConProductos();

        // 2. Obtener alertas de stock bajo
        $alertas = $this->stockModel->getStockBajo();

        return view('inventario/index', [
            'titulo' => 'Inventario',
            'inventario' => $inventario,
            'alertas' => $alertas,
            'user' => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo registro de stock para un producto.
     * 
     * @return string Genera el HTML para el formulario.
     */
    public function create()
    {
        $productos = $this->productoModel->getProductosConControlStock();
        
        return view('inventario/crear', [
            'productos' => $productos,
            'user' => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }

    /**
     * Almacena un nuevo registro de stock en la base de datos.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige al inventario con estado de la operación.
     */
    public function store()
    {
        // 1. Recopilar datos del formulario con sanitización
        $dataStock = [
            'id_producto'       => (int) $this->request->getPost('id_producto'),
            'cantidad_disponible' => (float) $this->request->getPost('cantidad_disponible'),
            'cantidad_minima'   => (float) $this->request->getPost('cantidad_minima'),
            'ultima_actualizacion' => date('Y-m-d H:i:s'),
        ];

        // 2. Validar usando las reglas definidas en el modelo
        if (!$this->stockModel->validate($dataStock)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->stockModel->errors());
        }

        // 3. Intentar guardar el registro de stock
        if (!$this->stockModel->insert($dataStock)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el registro de stock.');
        }

        return redirect()->to('/inventario')
            ->with('success', 'Registro de stock creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un registro de stock.
     * 
     * @param int $idStock ID del registro de stock a editar.
     * @return string Genera el HTML para el formulario de edición.
     */
    public function edit(int $idStock)
    {
        // 1. Verificar que el stock existe
        $stock = $this->stockModel->select('stock.*, productos.nombre as producto_nombre', false)
            ->join('productos', 'productos.id_producto = stock.id_producto', 'left')
            ->where('stock.id_stock', $idStock)
            ->first();
        if (!$stock) {
            return redirect()->back()->with('error', 'El registro de stock no existe.');
        }

        return view('inventario/editar', [
            'stock' => $stock,
            'user' => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }

    /**
     * Actualiza un registro de stock existente.
     * 
     * @param int $idStock ID del registro a actualizar.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige al inventario con estado de la operación.
     */
    public function update(int $idStock)
    {
        // 1. Verificar que el stock existe
        $stock = $this->stockModel->find($idStock);
        if (!$stock) {
            return redirect()->back()->with('error', 'El registro de stock no existe.');
        }

        // 2. Recopilar datos del formulario con sanitización
        $dataStock = [
            'cantidad_disponible' => (float) $this->request->getPost('cantidad_disponible'),
            'cantidad_minima'   => (float) $this->request->getPost('cantidad_minima'),
            'ultima_actualizacion' => date('Y-m-d H:i:s'),
        ];

        // 3. Validar usando las reglas definidas en el modelo
        if (!$this->stockModel->validate($dataStock)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->stockModel->errors());
        }

        // 4. Intentar actualizar el registro de stock
        if (!$this->stockModel->update($idStock, $dataStock)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el registro de stock.');
        }

        return redirect()->to('/inventario')
            ->with('success', 'Registro de stock actualizado exitosamente.');
    }

    /**
     * Muestra las alertas de stock bajo.
     * Productos con cantidad por debajo del mínimo establecido.
     * 
     * @return string Genera el HTML para la vista de alertas.
     */
    public function alertas()
    {
        // Obtener productos con stock bajo
        $alertas = $this->stockModel->getStockBajo();

        return view('inventario/alertas', [
            'titulo' => 'Alertas de Stock',
            'alertas' => $alertas,
            'user' => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }
}
