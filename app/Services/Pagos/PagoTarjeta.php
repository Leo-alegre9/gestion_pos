<?php

namespace App\Services\Pagos;

class PagoTarjeta implements EstrategiaPagoInterface
{
    // Monto mínimo aceptado por lectores de tarjeta
    public const MONTO_MINIMO = 100.0;

    /**
     * Rechaza montos por debajo del mínimo operativo del lector de tarjeta.
     */
    public function validar(float $monto): bool
    {
        return $monto >= self::MONTO_MINIMO;
    }

    /**
     * Procesa el pago con tarjeta.
     * Genera un código de autorización único que debe anotarse en el voucher.
     */
    public function procesar(float $monto): array
    {
        $codigo = $this->generarCodigoAutorizacion();

        return [
            'ok'                  => true,
            'detalle'             => "Pago con tarjeta autorizado. Código: {$codigo}.",
            'codigo_autorizacion' => $codigo,
            'error'               => null,
        ];
    }

    /**
     * Genera un código de autorización alfanumérico de 6 caracteres.
     * En un sistema real este código vendría de la pasarela de pago.
     */
    public function generarCodigoAutorizacion(): string
    {
        return strtoupper(bin2hex(random_bytes(3)));
    }
}
