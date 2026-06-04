<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MesaModel;
use App\Models\PedidoModel;

class MesaController extends BaseController
{
    /**
     * @var MesaModel Modelo para operaciones CRUD y listado de mesas
     */
    protected $mesaModel;

    /**
     * @var PedidoModel Modelo para la lógica de pedidos, especialmente los activos de una mesa
     */
    protected $pedidoModel;

    public function __construct()
    {
        $this->mesaModel   = new MesaModel();
        $this->pedidoModel = new PedidoModel();
    }

    /**
     * Muestra el resumen y estado en tiempo real de todas las mesas.
     * Carga el estado actual (ocupada, libre) e indica mediante un LEFT JOIN si tienen pedido asociado.
     *
     * @return string Genera el HTML para la vista 'mesas'.
     */
    public function mostrarResumen()
    {
        // 1. Validar la existencia de pedidos para evitar cambiar a "Libre" una mesa facturando.
        $mesas = $this->mesaModel->getMesasConPedidoActivo();

        // 2. Extraer los contadores de mesas por estado desde la DB.
        $resumen = $this->mesaModel->contarPorEstado();

        return view('mesas', [
            'titulo'  => 'Mesas - Estado Actual',
            'mesas'   => $mesas,
            'resumen' => $resumen,
            'user'    => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }

    /**
     * Endpoint para actualizar explícitamente el estado de una mesa.
     * Incluye validaciones básicas de estado seguro (no desocupar mesas facturadas).
     * 
     * @param int $idMesa Identificador único de la mesa.
     * @return \CodeIgniter\HTTP\RedirectResponse Regresa la vista con Flashdata del resultado.
     */
    public function cambiarEstado($idMesa)
    {
        $nuevoEstado = $this->request->getPost('estado');

        $mesa = $this->mesaModel->find($idMesa);
        if (!$mesa) {
            return redirect()->back()->with('error', 'La mesa no existe.');
        }

        $estadosValidos = ['libre', 'ocupada', 'reservada', 'inactiva'];
        if (!in_array($nuevoEstado, $estadosValidos, true)) {
            return redirect()->back()->with('error', 'Estado inválido.');
        }

        // Regla: si la mesa tiene pedido activo, no dejar pasar a libre/inactiva directamente
        $pedidoActivo = $this->pedidoModel->getPedidoActivoPorMesa((int)$idMesa);
        if ($pedidoActivo && in_array($nuevoEstado, ['libre', 'inactiva'], true)) {
            return redirect()->back()->with('error', 'No podés cambiar la mesa a ese estado porque tiene un pedido activo.');
        }

        $this->mesaModel->update($idMesa, ['estado' => $nuevoEstado]);

        return redirect()->to('/mesas')->with('success', 'Estado actualizado correctamente.');
    }

    /**
     * Muestra el formulario para crear una nueva mesa.
     * Pre-llena el próximo número disponible (máximo actual + 1).
     *
     * @return string Genera el HTML para el formulario de creación.
     */
    public function crearMesa()
    {
        // Obtener el siguiente número de mesa disponible
        $mesaAnterior = $this->mesaModel->selectMax('numero')->first();
        $proximoNumero = ($mesaAnterior['numero'] ?? 0) + 1;

        return view('mesas_crear', [
            'proximoNumero' => $proximoNumero,
            'user' => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }

    /**
     * Valida los datos del formulario y almacena la nueva mesa en la base de datos.
     * El estado inicial siempre es 'libre'. Usa las reglas de validación definidas en MesaModel.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige a la lista de mesas con estado de la operación.
     */
    public function validarYalmacenar()
    {
        // 1. Recopilar datos del formulario
        $dataMesa = [
            'numero'    => $this->request->getPost('numero'),
            'capacidad' => $this->request->getPost('capacidad') ?? null,
            'estado'    => 'libre', // Toda nueva mesa inicia en estado "libre"
        ];

        // 2. Validar usando las reglas definidas en el modelo
        if (!$this->mesaModel->validate($dataMesa)) {
            // Si hay errores, redirigir de vuelta al formulario con los errores
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->mesaModel->errors());
        }

        // 3. Intentar guardar la mesa en la BD
        if (!$this->mesaModel->insert($dataMesa)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al guardar la mesa. Por favor, intente nuevamente.');
        }

        return redirect()->to('/mesas')
            ->with('success', "Mesa #{$dataMesa['numero']} creada exitosamente.");
    }

    /**
     * Elimina físicamente una mesa de la base de datos.
     * Bloquea la eliminación si la mesa tiene un pedido activo para evitar pérdida de datos.
     *
     * @param int $idMesa Identificador único de la mesa a eliminar.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige a la lista de mesas.
     */
    public function eliminarMesa($idMesa)
    {
        // 1. Verificar que la mesa existe
        $mesa = $this->mesaModel->find($idMesa);
        if (!$mesa) {
            return redirect()->back()->with('error', 'La mesa no existe.');
        }

        // 2. Prevención de pérdida de datos: verificar si tiene pedidos activos
        $pedidoActivo = $this->pedidoModel->getPedidoActivoPorMesa((int)$idMesa);
        if ($pedidoActivo) {
            return redirect()->back()->with('error', 
                'No se puede eliminar una mesa que tiene un pedido activo. Cierre o cancele el pedido primero.');
        }

        // 3. Eliminar la mesa
        $numeroMesa = $mesa['numero'];
        if (!$this->mesaModel->delete($idMesa)) {
            return redirect()->back()->with('error', 'Error al eliminar la mesa. Por favor, intente nuevamente.');
        }

        return redirect()->to('/mesas')
            ->with('success', "Mesa #{$numeroMesa} eliminada exitosamente.");
    }
}