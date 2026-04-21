<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoriaProductoModel;

class CategoriaController extends BaseController
{
    /**
     * @var CategoriaProductoModel Modelo para operaciones CRUD de categorías
     */
    protected $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaProductoModel();
    }

    /**
     * Muestra el listado de todas las categorías activas.
     * Incluye información de productos asociados.
     *
     * @return string Genera el HTML para la vista de categorías.
     */
    public function index()
    {
        // Obtener todas las categorías activas
        $categorias = $this->categoriaModel->getAll();

        return view('categorias/index', [
            'titulo' => 'Categorías de Productos',
            'categorias' => $categorias,
            'user' => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva categoría.
     *
     * @return string Genera el HTML para el formulario de creación.
     */
    public function create()
    {
        return view('categorias/crear', [
            'user' => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }

    /**
     * Almacena una nueva categoría en la base de datos.
     * Valida los datos ingresados y maneja errores de validación.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige a la lista de categorías con estado de la operación.
     */
    public function store()
    {
        // 1. Recopilar datos del formulario con sanitización
        $dataCategoria = [
            'nombre'      => trim((string) $this->request->getPost('nombre')),
            'descripcion' => trim((string) $this->request->getPost('descripcion')) ?: null,
            'activa'      => 1, // Nuevas categorías inician como activas
        ];

        // 2. Validar usando las reglas definidas en el modelo
        if (!$this->categoriaModel->validate($dataCategoria)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->categoriaModel->errors());
        }

        // 3. Intentar guardar la categoría en la BD
        if (!$this->categoriaModel->insert($dataCategoria)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al guardar la categoría. Por favor, intente nuevamente.');
        }

        return redirect()->to('/categorias')
            ->with('success', "Categoría '{$dataCategoria['nombre']}' creada exitosamente.");
    }

    /**
     * Muestra el formulario para editar una categoría existente.
     *
     * @param int $idCategoria ID de la categoría a editar.
     * @return string Genera el HTML para el formulario de edición.
     */
    public function edit(int $idCategoria)
    {
        // 1. Verificar que la categoría existe
        $categoria = $this->categoriaModel->find($idCategoria);
        if (!$categoria) {
            return redirect()->back()->with('error', 'La categoría no existe.');
        }

        return view('categorias/editar', [
            'categoria' => $categoria,
            'user' => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }

    /**
     * Actualiza una categoría existente en la base de datos.
     *
     * @param int $idCategoria ID de la categoría a actualizar.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige a la lista de categorías con estado de la operación.
     */
    public function update(int $idCategoria)
    {
        // 1. Verificar que la categoría existe
        $categoria = $this->categoriaModel->find($idCategoria);
        if (!$categoria) {
            return redirect()->back()->with('error', 'La categoría no existe.');
        }

        // 2. Recopilar datos del formulario con sanitización
        $dataCategoria = [
            'nombre'      => trim((string) $this->request->getPost('nombre')),
            'descripcion' => trim((string) $this->request->getPost('descripcion')) ?: null,
            'activa'      => (int) ($this->request->getPost('activa') ?? 0),
        ];

        // 3. Validar usando las reglas definidas en el modelo
        if (!$this->categoriaModel->validate($dataCategoria)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->categoriaModel->errors());
        }

        // 4. Intentar actualizar la categoría
        if (!$this->categoriaModel->update($idCategoria, $dataCategoria)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la categoría. Por favor, intente nuevamente.');
        }

        return redirect()->to('/categorias')
            ->with('success', "Categoría actualizada exitosamente.");
    }

    /**
     * Desactiva una categoría (no la elimina físicamente).
     * Las categorías desactivadas no aparecen en vistas normales.
     *
     * @param int $idCategoria ID de la categoría a desactivar.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige a la lista de categorías.
     */
    public function deactivate(int $idCategoria)
    {
        // 1. Verificar que la categoría existe
        $categoria = $this->categoriaModel->find($idCategoria);
        if (!$categoria) {
            return redirect()->back()->with('error', 'La categoría no existe.');
        }

        // 2. Desactivar la categoría
        $nombreCategoria = $categoria['nombre'];
        if (!$this->categoriaModel->update($idCategoria, ['activa' => 0])) {
            return redirect()->back()->with('error', 'Error al desactivar la categoría.');
        }

        return redirect()->to('/categorias')
            ->with('success', "Categoría '{$nombreCategoria}' desactivada exitosamente.");
    }
}