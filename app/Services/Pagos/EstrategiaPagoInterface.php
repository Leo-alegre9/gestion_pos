<?php

namespace App\Services\Pagos;

interface EstrategiaPagoInterface
{
    /**
     * Verifica si el monto es válido para este método de pago.
     */
    public function validar(float $monto): bool;

    /**
     * Ejecuta el procesamiento específico del método de pago.
     * Retorna como mínimo ['ok' => bool, 'detalle' => string, 'error' => string|null].
     * Las implementaciones concretas pueden incluir claves adicionales:
     *   - PagoEfectivo:     'redondeo' (float)
     *   - PagoTarjeta:      'codigo_autorizacion' (string)
     *   - PagoTransferencia:'referencia' (string)
     */
    public function procesar(float $monto): array;
}
