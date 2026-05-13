<?php

namespace App\Models;

use CodeIgniter\Model;

class FamilyPortalAccessModel extends Model
{
    protected $table = 'family_portal_accesses';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'treatment_id', 'guardian_id', 'token', 'password_hash', 'initial_password',
        'access_url', 'last_sent_at', 'last_sent_to', 'active', 'created_by',
    ];
}
