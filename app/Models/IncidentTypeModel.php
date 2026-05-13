<?php

namespace App\Models;

use CodeIgniter\Model;

class IncidentTypeModel extends Model
{
    protected $table = 'incident_types';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'description', 'severity', 'active'];
}
