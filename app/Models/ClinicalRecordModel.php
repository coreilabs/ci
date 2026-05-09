<?php

namespace App\Models;

use CodeIgniter\Model;

class ClinicalRecordModel extends Model
{
    protected $table = 'clinical_records';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'treatment_id', 'user_id', 'type', 'title', 'content',
        'sae_collection', 'sae_diagnosis', 'sae_planning', 'sae_execution',
        'sae_evaluation', 'recorded_at',
    ];
}
