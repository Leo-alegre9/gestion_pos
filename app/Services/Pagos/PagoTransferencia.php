<?php

namespace App\Services\Pagos;

class PagoTransferencia implements EstrategiaPagoInterface
{
    public function validar(float $monto): bool
    {
        return $monto > 0;
    }

    /**
     * Procesa el pago por transferencia bancaria.
     * Genera una referencia única para cruzar con el comprobante del cliente.
     */
    public function procesar(float $monto): array
    {
        $referencia = $this->generarReferencia();

        return [
            'ok'         => true,
            'detalle'    => "Transferencia registrada. Referencia: {$referencia}.",
            'referencia' => $referencia,
            'error'      => null,
        ];
    }

    /**
     * Genera una referencia de transferencia con formato TRF-YYYYMMDD-XXXXXX.
     * Permite al cajero cruzar el comprobante del cliente contra este código.
     */
    public function generarReferencia(): string
    {
        return 'TRF-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}
