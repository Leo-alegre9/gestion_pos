<?php

namespace App\Services\Pagos;

/**
 * EstrategiaPagoFactory
 *
 * Mapea el nombre del método de pago (tal como está en `metodos_pago.nombre`)
 * a la estrategia concreta correspondiente.
 *
 * La comparación es case-insensitive y elimina espacios extra, por lo que
 * "Efectivo", "EFECTIVO" y " efectivo " resuelven al mismo resultado.
 *
 * Retorna null si no existe una estrategia registrada para ese nombre,
 * permitiendo que el llamador decida cómo manejarlo sin lanzar excepciones.
 */
class EstrategiaPagoFactory
{
    private static array $mapa = [
        'efectivo'      => PagoEfectivo::class,
        'tarjeta'       => PagoTarjeta::class,
        'transferencia' => PagoTransferencia::class,
    ];

    public static function crear(string $nombreMetodo): ?EstrategiaPagoInterface
    {
        $clave = strtolower(trim($nombreMetodo));

        if (!isset(self::$mapa[$clave])) {
            return null;
        }

        $clase = self::$mapa[$clave];
        return new $clase();
    }
}
