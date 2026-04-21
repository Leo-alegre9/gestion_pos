<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\MesaModel;
use App\Models\ProductoModel;
use App\Models\DetallePedidoModel;

class PedidoController extends BaseController
{
    /**
     * @var PedidoModel Modelo para operaciones CRUD de pedidos
     */
    protected $pedidoModel;

    /**
     * @var MesaModel Modelo para obtener datos de mesas
     */
    protected $mesaModel;

    /**
     * @var ProductoModel Modelo para obtener datos de productos
     */
    protected $productoModel;

    /**
     * @var DetallePedidoModel Modelo para obtener items del pedido
     */
    protected $detallePedidoModel;

    public function __construct()
    {
        $this->pedidoModel        = new PedidoModel();
        $this->mesaModel          = new MesaModel();
        $this->productoModel      = new ProductoModel();
        $this->detallePedidoModel = new DetallePedidoModel();
    }

    /**
     * Devuelve el id_estado_pedido para un nombre dado.
     * Si el estado no existe lo inserta, garantizando que la FK siempre se cumpla
     * aunque la tabla estados_pedido no haya sido sembrada manualmente.
     */
    private function getEstadoId(string $nombre): int
    {
        $db  = \Config\Database::connect();
        $row = $db->table('estados_pedido')->where('nombre', $nombre)->get()->getRowArray();
        if ($row) {
            return (int) $row['id_estado_pedido'];
        }
        $db->table('estados_pedido')->insert(['nombre' => $nombre]);
        return (int) $db->insertID();
    }

    /**
     * Muestra el listado de todos los pedidos activos.
     * Filtra por tipo y estado del pedido.
     * 
     * @return string Genera el HTML para la vista de pedidos.
     */
    public function index()
    {
        // 1. Obtener todos los pedidos activos
        $pedidos = $this->pedidoModel->getPedidosActivos();

        // 2. Obtener conteo por tipo de pedido
        $resumen = $this->pedidoModel->contarPedidosPorTipo();

        return view('pedidos/index', [
            'titulo' => 'Pedidos Activos',
            'pedidos' => $pedidos,
            'resumen' => $resumen,
            'user' => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo pedido.
     * Permite seleccionar tipo de pedido y mesa (si aplica).
     * 
     * @return string Genera el HTML para el formulario de creación.
     */
    public function create()
    {
        $mesas     = $this->mesaModel->where('estado', 'libre')->orderBy('numero', 'ASC')->findAll();
        $productos = $this->productoModel->getProductosActivos();

        return view('pedidos/crear', [
            'mesas'     => $mesas,
            'productos' => $productos,
            'user' => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }

    /**
     * Almacena un nuevo pedido en la base de datos.
     * Valida que la mesa esté disponible si es un pedido de mesa.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige a pedidos con estado de la operación.
     */
    public function store()
    {
        $idUsuario = (int) (session('id_usuario') ?? 0);
        if ($idUsuario <= 0) {
            return redirect()->to('/auth/login')
                ->with('error', 'Debes iniciar sesión para crear pedidos.');
        }

        $idMesa        = !empty($this->request->getPost('id_mesa')) ? (int)$this->request->getPost('id_mesa') : null;
        $tipoPedido    = trim((string)$this->request->getPost('tipo_pedido'));
        $observaciones = trim((string)$this->request->getPost('observaciones'));

        // Validaciones previas al insert
        if ($tipoPedido === 'mesa' && !$idMesa) {
            return redirect()->back()->withInput()
                ->with('error', 'Debés seleccionar una mesa para un pedido de mesa.');
        }

        if ($idMesa) {
            $mesa = $this->mesaModel->find($idMesa);
            if (!$mesa || $mesa['estado'] !== 'libre') {
                return redirect()->back()->withInput()
                    ->with('error', 'La mesa seleccionada no existe o no está libre.');
            }
        }

        $dataPedido = [
            'id_mesa'          => $idMesa,
            'id_usuario'       => $idUsuario,
            'id_estado_pedido' => $this->getEstadoId('abierto'),
            'tipo_pedido'      => $tipoPedido,
            'observaciones'    => $observaciones === '' ? null : $observaciones,
        ];

        if (!$this->pedidoModel->validate($dataPedido)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->pedidoModel->errors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Insertar pedido
        $this->pedidoModel->insert($dataPedido);
        $idPedido = $this->pedidoModel->getInsertID();

        // Insertar items del carrito enviados desde el formulario
        $items = $this->request->getPost('items') ?? [];
        foreach ($items as $item) {
            $idProducto = (int)($item['id_producto'] ?? 0);
            $cantidad   = (float)($item['cantidad']   ?? 0);
            if ($idProducto <= 0 || $cantidad <= 0) continue;

            $producto = $this->productoModel->find($idProducto);
            if (!$producto || !$producto['activo']) continue;

            $precioUnit = (float)$producto['precio_venta'];
            $this->detallePedidoModel->insert([
                'id_pedido'       => $idPedido,
                'id_producto'     => $idProducto,
                'cantidad'        => $cantidad,
                'precio_unitario' => $precioUnit,
                'subtotal'        => round($precioUnit * $cantidad, 2),
                'observaciones'   => null,
            ]);
        }

        // Marcar mesa como ocupada
        if ($idMesa) {
            $this->mesaModel->update($idMesa, ['estado' => 'ocupada']);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->withInput()
                ->with('error', 'Error al registrar el pedido. Intentá nuevamente.');
        }

        return redirect()->to('/pedidos/detalles/' . $idPedido)
            ->with('success', 'Pedido creado. Podés seguir agregando productos desde aquí.');
    }

    /**
     * Muestra los detalles de un pedido específico.
     * Incluye items agregados al pedido.
     * 
     * @param int $idPedido ID del pedido a ver.
     * @return string Genera el HTML para la vista de detalles.
     */
    public function show(int $idPedido)
    {
        // 1. Obtener detalles del pedido
        $pedido = $this->pedidoModel->getPedidoConDetalles($idPedido);
        if (!$pedido) {
            return redirect()->back()->with('error', 'El pedido no existe.');
        }

        // Obtener items del pedido
        $items = $this->detallePedidoModel
            ->select('detalle_pedidos.*, productos.nombre', false)
            ->join('productos', 'productos.id_producto = detalle_pedidos.id_producto')
            ->where('detalle_pedidos.id_pedido', $idPedido)
            ->findAll();

        $productosDisponibles = $this->productoModel->getProductosActivos();

        return view('pedidos/detalles', [
            'pedido' => $pedido,
            'items' => $items,
            'productosDisponibles' => $productosDisponibles,
            'user' => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }

    public function agregarDetalle(int $idPedido)
    {
        $pedido = $this->pedidoModel->find($idPedido);
        if (!$pedido || $pedido['fecha_cierre'] !== null) {
            return redirect()->back()->with('error', 'Pedido no válido o ya está cerrado.');
        }

        $idProducto = $this->request->getPost('id_producto');
        $cantidad = $this->request->getPost('cantidad');

        $producto = $this->productoModel->find($idProducto);
        if (!$producto) {
            return redirect()->back()->with('error', 'Producto no encontrado.');
        }

        $subtotal = $producto['precio_venta'] * $cantidad;

        $data = [
            'id_pedido' => $idPedido,
            'id_producto' => $idProducto,
            'cantidad' => $cantidad,
            'precio_unitario' => $producto['precio_venta'],
            'subtotal' => $subtotal,
            'observaciones' => $this->request->getPost('observaciones')
        ];

        if ($this->detallePedidoModel->insert($data)) {
            return redirect()->back()->with('success', 'Producto agregado al pedido.');
        }

        return redirect()->back()->with('error', 'Error al agregar el producto al pedido.');
    }

    public function eliminarDetalle(int $idPedido, int $idDetalle)
    {
        $pedido = $this->pedidoModel->find($idPedido);
        if (!$pedido || $pedido['fecha_cierre'] !== null) {
            return redirect()->back()->with('error', 'Pedido no válido o ya está cerrado.');
        }

        $detalle = $this->detallePedidoModel->find($idDetalle);
        if (!$detalle || $detalle['id_pedido'] != $idPedido) {
            return redirect()->back()->with('error', 'Detalle no válido.');
        }

        if ($this->detallePedidoModel->delete($idDetalle)) {
            return redirect()->back()->with('success', 'Producto eliminado del pedido.');
        }

        return redirect()->back()->with('error', 'Error al eliminar el producto del pedido.');
    }

    /**
     * Cierra un pedido existente.
     * Marca la mesa como libre y registra fecha de cierre.
     * 
     * @param int $idPedido ID del pedido a cerrar.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige a pedidos con estado de la operación.
     */
    public function cerrar(int $idPedido)
    {
        // 1. Verificar que el pedido existe
        $pedido = $this->pedidoModel->find($idPedido);
        if (!$pedido) {
            return redirect()->back()->with('error', 'El pedido no existe.');
        }

        // 2. Verificar que no esté ya cerrado
        if ($pedido['fecha_cierre'] !== null) {
            return redirect()->back()->with('error', 'El pedido ya ha sido cerrado.');
        }

        // 3. Cerrar el pedido y actualizar su estado
        $idEstadoCerrado = $this->getEstadoId('cerrado');
        if (!$this->pedidoModel->update($idPedido, [
            'fecha_cierre'     => date('Y-m-d H:i:s'),
            'id_estado_pedido' => $idEstadoCerrado,
        ])) {
            return redirect()->back()->with('error', 'Error al cerrar el pedido.');
        }

        // 4. Si tiene mesa asociada, marcarla como libre
        if ($pedido['id_mesa']) {
            $this->mesaModel->update($pedido['id_mesa'], ['estado' => 'libre']);
        }

        return redirect()->to('/pedidos')
            ->with('success', 'Pedido cerrado exitosamente. Listo para cobrar.');
    }

    /**
     * Muestra el historial de pedidos cerrados en una fecha específica.
     * Utilizado para reportes y auditoría.
     * 
     * @return string Genera el HTML para el historial.
     */
    public function historial()
    {
        // Obtener fecha de consulta (por defecto hoy)
        $fecha = $this->request->getGet('fecha') ?? date('Y-m-d');

        // Validar formato de fecha
        if (!strtotime($fecha)) {
            return redirect()->back()->with('error', 'Fecha inválida.');
        }

        // Obtener pedidos cerrados en esa fecha
        $pedidos = $this->pedidoModel->getPedidosCerradosPorFecha($fecha);

        return view('pedidos/historial', [
            'titulo' => 'Historial de Pedidos',
            'fecha' => $fecha,
            'pedidos' => $pedidos,
            'user' => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ]
        ]);
    }
}
