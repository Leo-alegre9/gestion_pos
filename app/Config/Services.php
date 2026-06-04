<?php

namespace Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Registra las factories de los servicios de aplicación del módulo de pagos.
 * Acceso desde controladores: service('prepararPago'), service('registrarPago'), service('comprobante').
 */
class Services extends BaseService
{
    /**
     * Servicio de preparación del formulario de pago.
     * Valida el pedido, detecta duplicados y recopila ítems y métodos de pago.
     *
     * @see App\Services\PrepararPagoService
     */
    public static function prepararPago(bool $getShared = true): \App\Services\PrepararPagoService
    {
        if ($getShared) {
            return static::getSharedInstance('prepararPago');
        }

        return new \App\Services\PrepararPagoService(
            new \App\Models\PedidoModel(),
            new \App\Models\PagoModel(),
            new \App\Models\MetodoPagoModel(),
            new \App\Models\DetallePedidoModel(),
        );
    }

    /**
     * Servicio de registro del pago.
     * Calcula el total, persiste el pago y actualiza el estado del pedido
     * dentro de una transacción.
     *
     * @see App\Services\RegistrarPagoService
     */
    public static function registrarPago(bool $getShared = true): \App\Services\RegistrarPagoService
    {
        if ($getShared) {
            return static::getSharedInstance('registrarPago');
        }

        return new \App\Services\RegistrarPagoService(
            new \App\Models\PedidoModel(),
            new \App\Models\PagoModel(),
            new \App\Models\MetodoPagoModel(),
            new \App\Models\DetallePedidoModel(),
        );
    }

    /**
     * Servicio de comprobante de pago.
     * Recopila pago, pedido e ítems para renderizar el recibo.
     *
     * @see App\Services\ComprobanteService
     */
    public static function comprobante(bool $getShared = true): \App\Services\ComprobanteService
    {
        if ($getShared) {
            return static::getSharedInstance('comprobante');
        }

        return new \App\Services\ComprobanteService(
            new \App\Models\PagoModel(),
            new \App\Models\PedidoModel(),
            new \App\Models\DetallePedidoModel(),
        );
    }
}
