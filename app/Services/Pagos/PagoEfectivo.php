<?php

namespace App\Services\Pagos;

class PagoEfectivo implements EstrategiaPagoInterface
{
    public function validar(float $monto): bool
    {
        return $monto > 0;
    }

    /**
     * Procesa el pago en efectivo.
     * Incluye 'redondeo' con el múltiplo de $50 superior más cercano,
     * útil para que el cajero sepa cuánto pedir al cliente.
     */
    public function procesar(float $monto): array
    {
        return [
            'ok'       => true,
            'detalle'  => 'Pago en efectivo procesado correctamente.',
            'redondeo' => $this->calcularRedondeo($monto),
            'error'    => null,
        ];
    }

    /**
     * Calcula el vuelto a entregar al cliente.
     * Retorna 0 si el monto entregado es insuficiente.
     */
    public function calcularVuelto(float $montoEntregado, float $total): float
    {
        if ($montoEntregado < $total) {
            return 0.0;
        }

        return round($montoEntregado - $total, 2);
    }

    /**
     * Redondea el monto al múltiplo de $50 superior más cercano.
     * Permite al cajero pedir un billete redondo al cliente.
     */
    public function calcularRedondeo(float $monto): float
    {
        return ceil($monto / 50) * 50;
    }
}
