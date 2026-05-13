<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleItemModel extends Model
{
    protected $table = 'schedule_items';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'day_label',
        'starts_at',
        'ends_at',
        'activity',
        'audience',
        'notes',
        'active',
        'sort_order',
        'created_by',
        'updated_by',
    ];
}
