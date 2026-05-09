<?php

namespace App\Models;

use CodeIgniter\Model;

class AdmissionDraftModel extends Model
{
    protected $table = 'admission_drafts';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['step', 'payload', 'created_by', 'finished_at'];
}
