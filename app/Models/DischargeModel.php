<?php

namespace App\Models;

use CodeIgniter\Model;

class DischargeModel extends Model
{
    protected $table = 'discharges';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['treatment_id', 'type', 'summary', 'discharged_at'];
}
