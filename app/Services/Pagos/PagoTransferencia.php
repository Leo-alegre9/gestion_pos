<?php

namespace App\Services\Pagos;

class PagoTransferencia implements EstrategiaPagoInterface
{
    public function validar(float $monto): bool
    {
        return $monto > 0;
    }

    public function procesar(float $monto): array
    {
        return [
            'ok'      => true,
            'detalle' => 'Transferencia bancaria confirmada correctamente.',
            'error'   => null,
        ];
    }
}
