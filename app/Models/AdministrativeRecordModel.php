<?php

namespace App\Models;

use CodeIgniter\Model;

class AdministrativeRecordModel extends Model
{
    protected $table = 'administrative_records';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['category', 'name', 'due_date', 'status', 'notes'];
}
