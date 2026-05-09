<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table = 'audit_logs';
    protected $returnType = 'array';
    protected $allowedFields = ['user_id', 'treatment_id', 'action', 'payload', 'created_at'];

    public function write(?int $treatmentId, string $action, array $payload = []): void
    {
        $this->insert([
            'user_id' => session('user.id'),
            'treatment_id' => $treatmentId,
            'action' => $action,
            'payload' => json_encode($payload),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
