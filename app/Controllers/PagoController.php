<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/**
 * PagoController
 *
 * Adaptador HTTP del módulo de pagos.
 * Responsabilidad única: leer la entrada HTTP, delegar en los servicios
 * y convertir el resultado en redirect o view.
 *
 * Expone tres rutas HTTP:
 *   GET  pagos/formulario/{idPedido}  →  mostrarFormularioDePago()
 *   POST pagos/procesar/{idPedido}    →  procesarRegistroDePago()
 *   GET  pagos/comprobante/{idPago}   →  mostrarComprobanteDePago()
 *
 * La lógica de negocio vive en App\Services\PrepararPagoService,
 * App\Services\RegistrarPagoService y App\Services\ComprobanteService.
 */
class PagoController extends BaseController
{
    /**
     * [GET] pagos/formulario/{idPedido}
     *
     * Delega en PrepararPagoService::preparar() la validación y los datos.
     *
     * Cursos alternativos:
     *   — Si el pedido ya fue pagado, redirige al comprobante existente.
     *   — Si el pedido no es válido, redirige a /pedidos con mensaje de error.
     *
     * Curso normal:
     *   — Renderiza el formulario de pago con pedido, ítems, total y métodos.
     *
     * @param int $idPedido ID del pedido cerrado que se desea pagar.
     */
    public function mostrarFormularioDePago(int $idPedido)
    {
        //1. Preparacion del pago -- CURSO NORMAL
        $resultado = service('prepararPago')->preparar($idPedido);

        //1.1. Manejo de cursos alternativos -- CURSO ALTERNATIVO
        if ($resultado['pagoExistenteId']) {
            return redirect()->to('/pagos/comprobante/' . $resultado['pagoExistenteId']);
        }

        //1.2. Manejo de errores -- CURSO ALTERNATIVO
        if (!$resultado['ok']) {
            return redirect()->to('/pedidos')->with('error', $resultado['error']);
        }

        //2. Renderizado de formulario -- CURSO NORMAL
        $d = $resultado['data'];
        return view('pagos/pagar', [
            'titulo'  => 'Registrar Pago',
            'pedido'  => $d['pedido'],
            'items'   => $d['items'],
            'total'   => $d['total'],
            'metodos' => $d['metodos'],
            'user'    => [
                'name' => session('nombre')     ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ],
        ]);
    }

    /**
     * [POST] pagos/procesar/{idPedido}
     *
     * Lee el método de pago del request y delega en RegistrarPagoService::registrar().
     *
     * Cursos alternativos:
     *   — Si el pedido ya fue pagado, redirige al comprobante existente.
     *   — Si la validación falla, regresa al formulario con los errores.
     *   — Si el pedido no es válido, redirige a /pedidos con mensaje de error.
     *
     * Curso normal:
     *   — Redirige al comprobante del pago recién registrado.
     *
     * @param int $idPedido ID del pedido que se está pagando.
     */
    public function procesarRegistroDePago(int $idPedido)
    {
        $idMetodoPago = (int) $this->request->getPost('id_metodo_pago');
        $resultado    = service('registrarPago')->registrar($idPedido, $idMetodoPago);

        // Manejo de cursos alternativos -- CURSO ALTERNATIVO
        if ($resultado['pagoExistenteId']) {
            return redirect()->to('/pagos/comprobante/' . $resultado['pagoExistenteId']);
        }

        // Manejo de validación fallida -- CURSO ALTERNATIVO
        if (!$resultado['ok'] && $resultado['errors']) {
            return redirect()->back()->withInput()->with('errors', $resultado['errors']);
        }

        // Manejo de pedido no válido -- CURSO ALTERNATIVO
        if (!$resultado['ok']) {
            return redirect()->to('/pedidos')->with('error', $resultado['error']);
        }

        // Redirección al comprobante del pago registrado -- CURSO NORMAL
        return redirect()->to('/pagos/comprobante/' . $resultado['idPago'])
            ->with('success', 'Pago registrado correctamente.');
    }

    /**
     * [GET] pagos/comprobante/{idPago}
     *
     * Delega en ComprobanteService::obtenerDatos() y renderiza el recibo.
     *
     * @param int $idPago ID del pago del que se quiere mostrar el comprobante.
     */
    public function mostrarComprobanteDePago(int $idPago)
    {
        $resultado = service('comprobante')->obtenerDatos($idPago);

        if (!$resultado['ok']) {
            return redirect()->to('/pedidos')->with('error', 'Comprobante no encontrado.');
        }

        return view('pagos/recibo', [
            'titulo' => 'Comprobante de Pago',
            'pago'   => $resultado['pago'],
            'pedido' => $resultado['pedido'],
            'items'  => $resultado['items'],
            'total'  => $resultado['total'],
            'user'   => [
                'name' => session('nombre')     ?? 'Administrador',
                'role' => session('rol_nombre') ?? 'Admin',
            ],
        ]);
    }
}
