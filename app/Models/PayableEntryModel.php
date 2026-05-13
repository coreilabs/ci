<?php

namespace App\Models;

use CodeIgniter\Model;

class PayableEntryModel extends Model
{
    protected $table = 'payable_entries';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'treatment_id', 'competence', 'category', 'payee_name', 'description',
        'amount', 'due_date', 'paid_at', 'status', 'created_by', 'updated_by',
    ];
}
