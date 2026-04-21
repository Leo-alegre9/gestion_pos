<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoModel extends Model
{
    protected $table         = 'pedidos';
    protected $primaryKey    = 'id_pedido';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'id_mesa',
        'id_usuario',
        'id_estado_pedido',
        'tipo_pedido',
        'fecha_apertura',
        'fecha_cierre',
        'observaciones'
    ];

    // Deshabilitar timestamps integrados, CodeIgniter intentaría actualizarlos sino
    protected $useTimestamps = false;

    /**
     * Reglas de validación para operaciones CRUD de pedidos.
     */
    protected $validationRules = [
        'id_mesa'           => 'permit_empty|integer|greater_than[0]',
        'id_usuario'        => 'required|integer|greater_than[0]',
        'id_estado_pedido'  => 'required|integer|greater_than[0]',
        'tipo_pedido'       => 'required|in_list[mesa,barra,take_away]',
        'observaciones'     => 'permit_empty|string|max_length[255]',
    ];

    /**
     * Mensajes de validación personalizados.
     */
    protected $validationMessages = [
        'id_mesa' => [
            'integer' => 'Mesa inválida.',
            'greater_than' => 'Selecciona una mesa válida.',
        ],
        'id_usuario' => [
            'required' => 'El usuario es requerido.',
            'integer' => 'Usuario inválido.',
        ],
        'id_estado_pedido' => [
            'required' => 'El estado del pedido es requerido.',
            'integer' => 'Estado inválido.',
        ],
        'tipo_pedido' => [
            'required' => 'El tipo de pedido es requerido.',
            'in_list' => 'Tipo de pedido no válido. Debe ser: mesa, barra o take_away.',
        ],
    ];

    /**
     * Devuelve el pedido que se encuentra abierto actualmente para una mesa específica.
     * Un pedido abierto no tiene 'fecha_cierre'.
     * 
     * @param int $idMesa ID de la mesa a buscar.
     * @return array|null El primer elemento de matriz (pedido) sin fecha_cierre o nulo si no hay ninguno.
     */
    public function getPedidoActivoPorMesa(int $idMesa): ?array
    {
        return $this->where('id_mesa', $idMesa)
            ->where('fecha_cierre', null)
            ->first();
    }

    /**
     * Obtiene todos los pedidos activos (abiertos) con información completa.
     * Incluye datos de mesa, usuario y estado del pedido.
     * 
     * @return array Lista de pedidos abiertos con detalles.
     */
    public function getPedidosActivos(): array
    {
        return $this->select('
                pedidos.id_pedido,
                pedidos.id_mesa,
                pedidos.tipo_pedido,
                pedidos.fecha_apertura,
                pedidos.fecha_cierre,
                pedidos.observaciones,
                COALESCE(mesas.numero, \'N/A\') as numero_mesa,
                usuarios.nombre as usuario_nombre,
                estados_pedido.nombre as estado_nombre
            ', false)
            ->join('mesas', 'mesas.id_mesa = pedidos.id_mesa', 'left')
            ->join('usuarios', 'usuarios.id_usuario = pedidos.id_usuario', 'left')
            ->join('estados_pedido', 'estados_pedido.id_estado_pedido = pedidos.id_estado_pedido', 'left')
            ->where('pedidos.fecha_cierre', null)
            ->orderBy('pedidos.fecha_apertura', 'DESC')
            ->findAll();
    }

    /**
     * Obtiene todos los pedidos cerrados en una fecha específica.
     * Utilizado para reportes y estadísticas diarias.
     * 
     * @param string $fecha Fecha en formato Y-m-d.
     * @return array Pedidos cerrados en la fecha especificada.
     */
    public function getPedidosCerradosPorFecha(string $fecha): array
    {
        return $this->select('
                pedidos.*,
                COALESCE(mesas.numero, \'N/A\') as numero_mesa,
                usuarios.nombre as usuario_nombre,
                estados_pedido.nombre as estado_nombre
            ', false)
            ->join('mesas', 'mesas.id_mesa = pedidos.id_mesa', 'left')
            ->join('usuarios', 'usuarios.id_usuario = pedidos.id_usuario', 'left')
            ->join('estados_pedido', 'estados_pedido.id_estado_pedido = pedidos.id_estado_pedido', 'left')
            ->where('DATE(pedidos.fecha_cierre) =', $fecha, false)
            ->where('pedidos.fecha_cierre IS NOT NULL', null, false)
            ->orderBy('pedidos.fecha_cierre', 'DESC')
            ->findAll();
    }

    /**
     * Obtiene un pedido específico con toda su información relacionada.
     * 
     * @param int $idPedido ID del pedido.
     * @return array|null Datos completos del pedido o null si no existe.
     */
    public function getPedidoConDetalles(int $idPedido): ?array
    {
        return $this->select('
                pedidos.*,
                COALESCE(mesas.numero, \'N/A\') as numero_mesa,
                mesas.capacidad as capacidad_mesa,
                usuarios.nombre as usuario_nombre,
                roles.nombre as usuario_rol,
                estados_pedido.nombre as estado_nombre
            ', false)
            ->join('mesas', 'mesas.id_mesa = pedidos.id_mesa', 'left')
            ->join('usuarios', 'usuarios.id_usuario = pedidos.id_usuario', 'left')
            ->join('roles', 'roles.id_rol = usuarios.id_rol', 'left')
            ->join('estados_pedido', 'estados_pedido.id_estado_pedido = pedidos.id_estado_pedido', 'left')
            ->where('pedidos.id_pedido', $idPedido)
            ->first();
    }

    /**
     * Cierra un pedido registrando la fecha y hora de cierre.
     * 
     * @param int $idPedido ID del pedido a cerrar.
     * @return bool True si se cerró exitosamente.
     */
    public function cerrarPedido(int $idPedido): bool
    {
        return $this->update($idPedido, [
            'fecha_cierre' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Cuenta la cantidad de pedidos abiertos por tipo.
     * Utilizado para estadísticas en el dashboard.
     * 
     * @return array Conteo de pedidos por tipo.
     */
    public function contarPedidosPorTipo(): array
    {
        return $this->select('tipo_pedido, COUNT(*) as total')
            ->where('fecha_cierre', null)
            ->groupBy('tipo_pedido')
            ->orderBy('tipo_pedido', 'ASC')
            ->findAll();
    }
}
