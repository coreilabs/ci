<?php

namespace App\Models;

use CodeIgniter\Model;

class GuardianModel extends Model
{
    protected $table = 'guardians';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'cpf', 'phone', 'email', 'relationship', 'address'];
    protected $validationRules = ['name' => 'required|min_length[3]'];
}
