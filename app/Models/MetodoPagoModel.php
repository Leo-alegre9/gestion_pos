<?php

namespace App\Models;

use CodeIgniter\Model;

class MetodoPagoModel extends Model
{
    protected $table      = 'metodos_pago';
    protected $primaryKey = 'id_metodo_pago';
    protected $returnType = 'array';
    protected $allowedFields = ['nombre', 'activo'];
    protected $useTimestamps = false;

    public function getActivos(): array
    {
        return $this->where('activo', 1)->orderBy('nombre', 'ASC')->findAll();
    }
}
