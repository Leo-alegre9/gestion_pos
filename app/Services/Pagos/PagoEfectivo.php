<?php

namespace App\Services\Pagos;

class PagoEfectivo implements EstrategiaPagoInterface
{
    public function validar(float $monto): bool
    {
        return $monto > 0;
    }

    public function procesar(float $monto): array
    {
        return [
            'ok'      => true,
            'detalle' => 'Pago en efectivo procesado correctamente.',
            'error'   => null,
        ];
    }
}
