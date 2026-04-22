<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\PagoModel;
use App\Models\MetodoPagoModel;
use App\Models\DetallePedidoModel;

class PagoController extends BaseController
{
    protected $pedidoModel;
    protected $pagoModel;
    protected $metodoPagoModel;
    protected $detallePedidoModel;

    public function __construct()
    {
        $this->pedidoModel        = new PedidoModel();
        $this->pagoModel          = new PagoModel();
        $this->metodoPagoModel    = new MetodoPagoModel();
        $this->detallePedidoModel = new DetallePedidoModel();
    }

    private function calcularTotal(int $idPedido): float
    {
        $items = $this->detallePedidoModel->where('id_pedido', $idPedido)->findAll();
        return array_sum(array_column($items, 'subtotal'));
    }

    private function getOrCreateEstado(string $nombre): int
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
     * Muestra el formulario de pago para un pedido cerrado.
     */
    public function pagar(int $idPedido)
    {
        $pedido = $this->pedidoModel->getPedidoConDetalles($idPedido);
        if (!$pedido) {
            return redirect()->to('/pedidos')->with('error', 'El pedido no existe.');
        }
        if (!$pedido['fecha_cierre']) {
            return redirect()->to('/pedidos/detalles/' . $idPedido)
                ->with('error', 'El pedido debe cerrarse antes de registrar el pago.');
        }

        $pagoExistente = $this->pagoModel->getPagoPorPedido($idPedido);
        if ($pagoExistente) {
            return redirect()->to('/pagos/recibo/' . $pagoExistente['id_pago']);
        }

        $items = $this->detallePedidoModel
            ->select('detalle_pedidos.*, productos.nombre', false)
            ->join('productos', 'productos.id_producto = detalle_pedidos.id_producto')
            ->where('detalle_pedidos.id_pedido', $idPedido)
            ->findAll();

        $total   = array_sum(array_column($items, 'subtotal'));
        $metodos = $this->metodoPagoModel->getActivos();

        return view('pagos/pagar', [
            'titulo'  => 'Registrar Pago',
            'pedido'  => $pedido,
            'items'   => $items,
            'total'   => $total,
            'metodos' => $metodos,
            'user'    => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ],
        ]);
    }

    /**
     * Procesa y guarda el pago de un pedido.
     */
    public function store(int $idPedido)
    {
        $pedido = $this->pedidoModel->find($idPedido);
        if (!$pedido || !$pedido['fecha_cierre']) {
            return redirect()->to('/pedidos')->with('error', 'Pedido no válido para registrar pago.');
        }

        $pagoExistente = $this->pagoModel->getPagoPorPedido($idPedido);
        if ($pagoExistente) {
            return redirect()->to('/pagos/recibo/' . $pagoExistente['id_pago']);
        }

        $idMetodo = (int) $this->request->getPost('id_metodo_pago');
        $monto    = str_replace(',', '.', (string) $this->request->getPost('monto'));
        $monto    = (float) $monto;

        $data = [
            'id_pedido'      => $idPedido,
            'id_metodo_pago' => $idMetodo,
            'monto'          => $monto,
            'fecha_pago'     => date('Y-m-d H:i:s'),
        ];

        if (!$this->pagoModel->validate($data)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->pagoModel->errors());
        }

        $this->pagoModel->insert($data);
        $idPago = $this->pagoModel->getInsertID();

        $idEstadoPagado = $this->getOrCreateEstado('pagado');
        $this->pedidoModel->update($idPedido, ['id_estado_pedido' => $idEstadoPagado]);

        return redirect()->to('/pagos/recibo/' . $idPago)
            ->with('success', 'Pago registrado correctamente.');
    }

    /**
     * Muestra el recibo/comprobante de un pago.
     */
    public function recibo(int $idPago)
    {
        $pago = $this->pagoModel->getPagoConMetodo($idPago);
        if (!$pago) {
            return redirect()->to('/pedidos')->with('error', 'Comprobante no encontrado.');
        }

        $pedido = $this->pedidoModel->getPedidoConDetalles($pago['id_pedido']);
        $items  = $this->detallePedidoModel
            ->select('detalle_pedidos.*, productos.nombre', false)
            ->join('productos', 'productos.id_producto = detalle_pedidos.id_producto')
            ->where('detalle_pedidos.id_pedido', $pago['id_pedido'])
            ->findAll();
        $total = array_sum(array_column($items, 'subtotal'));

        return view('pagos/recibo', [
            'titulo' => 'Comprobante de Pago',
            'pago'   => $pago,
            'pedido' => $pedido,
            'items'  => $items,
            'total'  => $total,
            'user'   => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ],
        ]);
    }
}
