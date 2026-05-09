<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientModel extends Model
{
    protected $table = 'patients';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['guardian_id', 'name', 'cpf', 'birth_date', 'phone', 'address'];
    protected $validationRules = ['guardian_id' => 'required|integer', 'name' => 'required|min_length[3]'];
}
