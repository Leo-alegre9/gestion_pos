<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PagoModel;
use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;

class FacturacionController extends BaseController
{
    protected $pagoModel;
    protected $pedidoModel;
    protected $detallePedidoModel;

    public function __construct()
    {
        $this->pagoModel          = new PagoModel();
        $this->pedidoModel        = new PedidoModel();
        $this->detallePedidoModel = new DetallePedidoModel();
    }

    /**
     * Lista todos los pagos del día seleccionado con KPIs y resumen por método.
     */
    public function index()
    {
        $fecha = $this->request->getGet('fecha') ?? date('Y-m-d');
        if (!strtotime($fecha)) {
            $fecha = date('Y-m-d');
        }

        $db = \Config\Database::connect();

        // Pagos del día con toda la información necesaria
        $pagos = $db->table('pagos p')
            ->select('
                p.id_pago,
                p.id_pedido,
                p.monto,
                p.fecha_pago,
                mp.nombre as metodo_nombre,
                pe.tipo_pedido,
                COALESCE(m.numero, NULL) as numero_mesa,
                u.nombre as usuario_nombre,
                COALESCE(SUM(dp.subtotal), 0) as total_pedido,
                COUNT(dp.id_detalle_pedido) as items_count
            ', false)
            ->join('metodos_pago mp', 'mp.id_metodo_pago = p.id_metodo_pago')
            ->join('pedidos pe', 'pe.id_pedido = p.id_pedido')
            ->join('mesas m', 'm.id_mesa = pe.id_mesa', 'left')
            ->join('usuarios u', 'u.id_usuario = pe.id_usuario', 'left')
            ->join('detalle_pedidos dp', 'dp.id_pedido = pe.id_pedido', 'left')
            ->where("DATE(p.fecha_pago) = '{$fecha}'", null, false)
            ->groupBy('p.id_pago')
            ->orderBy('p.fecha_pago', 'DESC')
            ->get()->getResultArray();

        // KPIs del día
        $totalDia       = array_sum(array_column($pagos, 'monto'));
        $countPagos     = count($pagos);
        $ticketPromedio = $countPagos > 0 ? $totalDia / $countPagos : 0;

        // Agrupado por método de pago
        $porMetodoMap = [];
        foreach ($pagos as $p) {
            $m = $p['metodo_nombre'];
            if (!isset($porMetodoMap[$m])) {
                $porMetodoMap[$m] = ['metodo' => $m, 'count' => 0, 'total' => 0.0];
            }
            $porMetodoMap[$m]['count']++;
            $porMetodoMap[$m]['total'] += (float)$p['monto'];
        }
        usort($porMetodoMap, fn($a, $b) => $b['total'] <=> $a['total']);
        $porMetodo     = array_values($porMetodoMap);
        $metodoTop     = !empty($porMetodo) ? $porMetodo[0]['metodo'] : '—';
        $maxMetodoTotal = !empty($porMetodo) ? $porMetodo[0]['total'] : 1;

        // Comparativa con ayer
        $fechaAyer = date('Y-m-d', strtotime($fecha . ' -1 day'));
        $ayerRow   = $db->table('pagos')
            ->selectSum('monto')
            ->where("DATE(fecha_pago) = '{$fechaAyer}'", null, false)
            ->get()->getRowArray();
        $totalAyer = (float)($ayerRow['monto'] ?? 0);

        // Variación porcentual vs ayer
        $variacion = null;
        if ($totalAyer > 0) {
            $variacion = round((($totalDia - $totalAyer) / $totalAyer) * 100, 1);
        }

        return view('facturacion/index', [
            'titulo'          => 'Facturación',
            'fecha'           => $fecha,
            'pagos'           => $pagos,
            'total_dia'       => $totalDia,
            'count_pagos'     => $countPagos,
            'ticket_promedio' => $ticketPromedio,
            'metodo_top'      => $metodoTop,
            'por_metodo'      => $porMetodo,
            'max_metodo_total'=> $maxMetodoTotal,
            'total_ayer'      => $totalAyer,
            'variacion'       => $variacion,
            'user'            => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ],
        ]);
    }

    /**
     * Muestra el detalle de factura de un pago específico.
     */
    public function detalle(int $idPago)
    {
        $pago = $this->pagoModel->getPagoConMetodo($idPago);
        if (!$pago) {
            return redirect()->to('/facturacion')->with('error', 'Factura no encontrada.');
        }

        $pedido = $this->pedidoModel->getPedidoConDetalles($pago['id_pedido']);

        $items = $this->detallePedidoModel
            ->select('detalle_pedidos.*, productos.nombre', false)
            ->join('productos', 'productos.id_producto = detalle_pedidos.id_producto')
            ->where('detalle_pedidos.id_pedido', $pago['id_pedido'])
            ->findAll();

        $total  = array_sum(array_column($items, 'subtotal'));
        $vuelto = (float)$pago['monto'] - $total;

        return view('facturacion/detalle', [
            'titulo' => 'Factura FAC-' . str_pad($idPago, 6, '0', STR_PAD_LEFT),
            'pago'   => $pago,
            'pedido' => $pedido,
            'items'  => $items,
            'total'  => $total,
            'vuelto' => $vuelto > 0.005 ? $vuelto : 0,
            'user'   => [
                'name' => session('nombre') ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ],
        ]);
    }
}
