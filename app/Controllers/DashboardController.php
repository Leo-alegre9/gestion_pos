<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MesaModel;

class DashboardController extends BaseController
{
    /**
     * Muestra el Dashboard principal del sistema.
     * Carga información real de mesas y simula los datos de pedidos por ahora,
     * hasta que se implementen por completo los demás modelos (pedidos, productos).
     */
    public function index()
    {
        $mesaModel = new MesaModel();

        // 1. Obtener los datos del usuario logueado en la sesión
        $user = [
            'name' => session()->get('nombre') ?? 'Administrador',
            'role' => session()->get('rol_nombre') ?? 'Admin',
        ];

        // 2. Traer las mesas reales registradas en base de datos.
        // Se ordena por el número de mesa ascendente.
        $mesas = $mesaModel->orderBy('numero', 'ASC')->findAll();

        // 3. Transformar para el uso específico de la vista de dashboard
        $tables = [];
        $mesasOcupadas = 0;

        foreach ($mesas as $mesa) {
            if (($mesa['estado'] ?? '') === 'ocupada') {
                $mesasOcupadas++;
            }

            $tables[] = [
                'id_mesa' => $mesa['id_mesa'],
                'number'  => $mesa['numero'],
                'status'  => $mesa['estado'],
                // TODO: Conectar más adelante con la tabla 'pedidos' para traer el importe total de la mesa.
                'amount'  => null, 
            ];
        }

        // 4. Estadísticas del día (KPIs). 
        // Algunas se obtienen de las tablas (mesas) y otras por ahora son de prueba hasta su implementación.
        $stats = [
            'ventas_hoy'     => 124500, // Hardcoded: Reemplazar por SUM(pagos.monto) WHERE fecha = hoy
            'pedidos_hoy'    => 37,     // Hardcoded: Reemplazar por COUNT(id_pedido) WHERE fecha = hoy
            'mesas_ocupadas' => $mesasOcupadas, // Dato Real de la DB
            'mesas_total'    => count($mesas),  // Dato Real de la DB
            'alertas_stock'  => 3,      // Hardcoded: Reemplazar por COUNT de productos bajos en stock

            'week_sales' => [85000, 110000, 92000, 135000, 78000, 124500, 0], // Dejar como historial simulado

            'top_products' => [ // Produtos más vendidos
                ['name' => 'Cerveza Quilmes 1L', 'qty' => 42],
                ['name' => 'Fernet con Coca', 'qty' => 31],
                ['name' => 'Papas fritas', 'qty' => 28],
                ['name' => 'Hamburguesa clásica', 'qty' => 19],
                ['name' => 'Agua mineral 500ml', 'qty' => 15],
            ],
        ];

        // Pedidos recientes
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
        ];

        // Stock bajo
        $stock_alerts = [
            ['name' => 'Fernet Branca 750ml', 'qty' => 2, 'level' => 'critical'],
            ['name' => 'Gin Beefeater 700ml', 'qty' => 4, 'level' => 'critical'],
            ['name' => 'Coca-Cola 1.5L', 'qty' => 8, 'level' => 'low'],
            ['name' => 'Cerveza Stella 330ml', 'qty' => 10, 'level' => 'low'],
            ['name' => 'Agua Mineral 500ml', 'qty' => 12, 'level' => 'low'],
            ['name' => 'Hielo bolsa 3kg', 'qty' => 3, 'level' => 'critical'],
        ];

        return view('dashboard', [
            'user'          => $user,
            'stats'         => $stats,
            'tables'        => $tables,
            'recent_orders' => $recent_orders,
            'stock_alerts'  => $stock_alerts,
        ]);
    }
}