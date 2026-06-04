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
     * Retorna ['ok' => bool, 'detalle' => string, 'error' => string|null].
     */
    public function procesar(float $monto): array;
}
