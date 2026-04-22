<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MesaModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $mesaModel = new MesaModel();
        $db        = \Config\Database::connect();
        $today     = date('Y-m-d');

        // ── Usuario ──────────────────────────────────────────────────────
        $user = [
            'name' => session()->get('nombre') ?? 'Administrador',
            'role' => session()->get('rol_nombre') ?? 'Admin',
        ];

        // ── Mesas ────────────────────────────────────────────────────────
        $mesas         = $mesaModel->orderBy('numero', 'ASC')->findAll();
        $mesasOcupadas = 0;

        // Totales de pedidos abiertos por mesa
        $mesaAmountsRaw = $db->table('pedidos p')
            ->select('p.id_mesa, COALESCE(SUM(dp.subtotal), 0) as total', false)
            ->join('detalle_pedidos dp', 'dp.id_pedido = p.id_pedido', 'left')
            ->where('p.fecha_cierre IS NULL', null, false)
            ->where('p.id_mesa IS NOT NULL', null, false)
            ->groupBy('p.id_mesa')
            ->get()->getResultArray();

        $mesaAmounts = [];
        foreach ($mesaAmountsRaw as $row) {
            $mesaAmounts[(int)$row['id_mesa']] = (float)$row['total'];
        }

        $tables = [];
        foreach ($mesas as $mesa) {
            if ($mesa['estado'] === 'ocupada') {
                $mesasOcupadas++;
            }
            $tables[] = [
                'id_mesa' => $mesa['id_mesa'],
                'number'  => $mesa['numero'],
                'status'  => $mesa['estado'],
                'amount'  => ($mesaAmounts[$mesa['id_mesa']] ?? 0) > 0 ? $mesaAmounts[$mesa['id_mesa']] : null,
            ];
        }

        // ── Ventas hoy (suma de pagos registrados hoy) ───────────────────
        $ventasHoyRow = $db->table('pagos')
            ->selectSum('monto')
            ->where("DATE(fecha_pago) = '$today'", null, false)
            ->get()->getRowArray();
        $ventasHoy = (float)($ventasHoyRow['monto'] ?? 0);

        // ── Pedidos abiertos hoy ─────────────────────────────────────────
        $pedidosHoy = (int)$db->table('pedidos')
            ->where("DATE(fecha_apertura) = '$today'", null, false)
            ->countAllResults();

        // ── Alertas de stock ─────────────────────────────────────────────
        $alertasStock = (int)$db->table('stock')
            ->where('cantidad_disponible < cantidad_minima', null, false)
            ->countAllResults();

        // ── Top 5 productos más vendidos hoy ────────────────────────────
        $topProductsRaw = $db->table('detalle_pedidos dp')
            ->select('prod.nombre, SUM(dp.cantidad) as qty', false)
            ->join('pedidos pe', 'pe.id_pedido = dp.id_pedido')
            ->join('productos prod', 'prod.id_producto = dp.id_producto')
            ->where("DATE(pe.fecha_apertura) = '$today'", null, false)
            ->groupBy('dp.id_producto')
            ->orderBy('qty', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        $top_products = array_map(fn($r) => [
            'nombre' => $r['nombre'],
            'qty'    => (float)$r['qty'],
        ], $topProductsRaw);

        // ── Pedidos recientes del día ────────────────────────────────────
        $recentRaw = $db->table('pedidos p')
            ->select('
                p.id_pedido,
                p.tipo_pedido,
                p.fecha_apertura,
                p.fecha_cierre,
                COALESCE(m.numero, NULL) as numero_mesa,
                ep.nombre as estado_nombre,
                pa.id_pago,
                COALESCE(SUM(dp.subtotal), 0) as total,
                COUNT(dp.id_detalle_pedido) as items_count
            ', false)
            ->join('mesas m', 'm.id_mesa = p.id_mesa', 'left')
            ->join('estados_pedido ep', 'ep.id_estado_pedido = p.id_estado_pedido', 'left')
            ->join('pagos pa', 'pa.id_pedido = p.id_pedido', 'left')
            ->join('detalle_pedidos dp', 'dp.id_pedido = p.id_pedido', 'left')
            ->where("DATE(p.fecha_apertura) = '$today'", null, false)
            ->groupBy('p.id_pedido')
            ->orderBy('p.fecha_apertura', 'DESC')
            ->limit(8)
            ->get()->getResultArray();

        $recent_orders = [];
        foreach ($recentRaw as $r) {
            $pagado = !empty($r['id_pago']);
            $cerrado = !empty($r['fecha_cierre']);

            if (!$cerrado) {
                $statusClass = 'open';
                $statusLabel = 'Abierto';
            } elseif ($pagado) {
                $statusClass = 'paid';
                $statusLabel = 'Pagado';
            } else {
                $statusClass = 'pending';
                $statusLabel = 'Sin cobrar';
            }

            $mesaLabel = match($r['tipo_pedido']) {
                'barra'    => 'Barra',
                'take_away'=> 'Para llevar',
                default    => $r['numero_mesa'] ? 'Mesa ' . $r['numero_mesa'] : 'Mesa',
            };

            $recent_orders[] = [
                'id_pedido'    => $r['id_pedido'],
                'mesa_label'   => $mesaLabel,
                'items_count'  => (int)$r['items_count'],
                'status_class' => $statusClass,
                'status_label' => $statusLabel,
                'total'        => (float)$r['total'],
            ];
        }

        // ── Alertas de inventario (detalle) ─────────────────────────────
        $stockAlertsRaw = $db->table('stock s')
            ->select('prod.nombre, s.cantidad_disponible, s.cantidad_minima', false)
            ->join('productos prod', 'prod.id_producto = s.id_producto')
            ->where('s.cantidad_disponible < s.cantidad_minima', null, false)
            ->where('prod.activo', 1)
            ->orderBy('(s.cantidad_disponible / NULLIF(s.cantidad_minima, 0))', 'ASC', false)
            ->limit(6)
            ->get()->getResultArray();

        $stock_alerts = [];
        foreach ($stockAlertsRaw as $row) {
            $min   = (float)$row['cantidad_minima'];
            $disp  = (float)$row['cantidad_disponible'];
            $ratio = $min > 0 ? $disp / $min : 0;

            $stock_alerts[] = [
                'nombre'              => $row['nombre'],
                'cantidad_disponible' => $disp,
                'cantidad_minima'     => $min,
                'level'               => $ratio <= 0.5 ? 'critical' : 'low',
            ];
        }

        // ── Stats consolidados ───────────────────────────────────────────
        $stats = [
            'ventas_hoy'     => $ventasHoy,
            'pedidos_hoy'    => $pedidosHoy,
            'mesas_ocupadas' => $mesasOcupadas,
            'mesas_total'    => count($mesas),
            'alertas_stock'  => $alertasStock,
        ];

        return view('dashboard', [
            'user'          => $user,
            'stats'         => $stats,
            'tables'        => $tables,
            'top_products'  => $top_products,
            'recent_orders' => $recent_orders,
            'stock_alerts'  => $stock_alerts,
        ]);
    }
}
