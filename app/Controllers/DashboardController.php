<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    /**
     * Muestra el dashboard principal.
     * Todos los datos están hardcodeados por ahora.
     */
    public function index()
    {
        // ── Usuario en sesión ──────────────────────────────────────────────
        $user = [
            'name' => session()->get('user_name') ?? 'Administrador',
            'role' => session()->get('user_role') ?? 'Admin',
        ];

        // ── KPIs y stats generales ─────────────────────────────────────────
        $stats = [
            'ventas_hoy'     => 124500,
            'pedidos_hoy'    => 37,
            'mesas_ocupadas' => 4,
            'mesas_total'    => 8,
            'alertas_stock'  => 3,

            // Ventas por día (L-D). El último valor = hoy (en curso)
            'week_sales' => [85000, 110000, 92000, 135000, 78000, 124500, 0],

            // Top 5 productos más vendidos hoy
            'top_products' => [
                ['name' => 'Cerveza Quilmes 1L',   'qty' => 42],
                ['name' => 'Fernet con Coca',       'qty' => 31],
                ['name' => 'Papas fritas',           'qty' => 28],
                ['name' => 'Hamburguesa clásica',   'qty' => 19],
                ['name' => 'Agua mineral 500ml',    'qty' => 15],
            ],
        ];

        // ── Estado de mesas ────────────────────────────────────────────────
        // status: libre | ocupada | reservada | inactiva
        $tables = [
            ['number' => 1, 'status' => 'ocupada',   'amount' => 8500],
            ['number' => 2, 'status' => 'libre',      'amount' => null],
            ['number' => 3, 'status' => 'reservada',  'amount' => null],
            ['number' => 4, 'status' => 'ocupada',    'amount' => 12300],
            ['number' => 5, 'status' => 'libre',      'amount' => null],
            ['number' => 6, 'status' => 'ocupada',    'amount' => 4200],
            ['number' => 7, 'status' => 'inactiva',   'amount' => null],
            ['number' => 8, 'status' => 'ocupada',    'amount' => 6800],
        ];

        // ── Pedidos recientes ──────────────────────────────────────────────
        // status_class: open | paid | pending | canceled
        $recent_orders = [
            [
                'id'           => 1047,
                'mesa_label'   => 'Mesa 04',
                'items_count'  => 5,
                'status_class' => 'open',
                'status_label' => 'Abierto',
                'total'        => 12300,
            ],
            [
                'id'           => 1046,
                'mesa_label'   => 'Mesa 01',
                'items_count'  => 3,
                'status_class' => 'open',
                'status_label' => 'Abierto',
                'total'        => 8500,
            ],
            [
                'id'           => 1045,
                'mesa_label'   => 'Take Away',
                'items_count'  => 2,
                'status_class' => 'paid',
                'status_label' => 'Pagado',
                'total'        => 3200,
            ],
            [
                'id'           => 1044,
                'mesa_label'   => 'Mesa 08',
                'items_count'  => 4,
                'status_class' => 'open',
                'status_label' => 'Abierto',
                'total'        => 6800,
            ],
            [
                'id'           => 1043,
                'mesa_label'   => 'Barra',
                'items_count'  => 1,
                'status_class' => 'paid',
                'status_label' => 'Pagado',
                'total'        => 1500,
            ],
            [
                'id'           => 1042,
                'mesa_label'   => 'Mesa 06',
                'items_count'  => 6,
                'status_class' => 'paid',
                'status_label' => 'Pagado',
                'total'        => 9800,
            ],
            [
                'id'           => 1041,
                'mesa_label'   => 'Mesa 02',
                'items_count'  => 2,
                'status_class' => 'canceled',
                'status_label' => 'Cancelado',
                'total'        => 2400,
            ],
        ];

        // ── Alertas de stock bajo ──────────────────────────────────────────
        // level: critical (≤ 5 u.) | low (≤ 15 u.)
        $stock_alerts = [
            ['name' => 'Fernet Branca 750ml',  'qty' => 2,  'level' => 'critical'],
            ['name' => 'Gin Beefeater 700ml',  'qty' => 4,  'level' => 'critical'],
            ['name' => 'Coca-Cola 1.5L',        'qty' => 8,  'level' => 'low'],
            ['name' => 'Cerveza Stella 330ml', 'qty' => 10, 'level' => 'low'],
            ['name' => 'Agua Mineral 500ml',   'qty' => 12, 'level' => 'low'],
            ['name' => 'Hielo bolsa 3kg',      'qty' => 3,  'level' => 'critical'],
        ];

        // ── Render ─────────────────────────────────────────────────────────
        return view('dashboard', [
            'user'          => $user,
            'stats'         => $stats,
            'tables'        => $tables,
            'recent_orders' => $recent_orders,
            'stock_alerts'  => $stock_alerts,
        ]);
    }
}