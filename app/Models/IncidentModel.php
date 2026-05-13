<?php

namespace App\Models;

use CodeIgniter\Model;

class IncidentModel extends Model
{
    protected $table = 'incidents';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'treatment_id', 'incident_type_id', 'title', 'description', 'occurred_at',
        'status', 'created_by', 'updated_by',
    ];
}
