<?php

namespace App\Models;

use CodeIgniter\Model;

class FinancialEntryModel extends Model
{
    protected $table = 'financial_entries';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'treatment_id', 'competence', 'type', 'description', 'amount',
        'due_date', 'paid_at', 'status',
    ];
}
