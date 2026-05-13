<?php

namespace App\Models;

use CodeIgniter\Model;

class CalendarEventModel extends Model
{
    protected $table = 'calendar_events';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'treatment_id', 'professional_user_id', 'source_type', 'source_id', 'title', 'category',
        'starts_at', 'ends_at', 'notes', 'created_by', 'updated_by',
    ];
}
