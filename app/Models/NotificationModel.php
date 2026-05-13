<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $returnType = 'array';
    protected $allowedFields = ['type', 'title', 'body', 'treatment_id', 'created_by', 'created_at'];

    public function createForRoleSlug(string $roleSlug, array $data): int
    {
        $data['created_by'] = $data['created_by'] ?? session('user.id');
        $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
        $notificationId = (int) $this->insert($data);

        $users = db_connect()->table('users')
            ->select('users.id')
            ->join('roles', 'roles.id = users.role_id')
            ->where('roles.slug', $roleSlug)
            ->get()
            ->getResultArray();

        foreach ($users as $user) {
            db_connect()->table('user_notifications')->insert([
                'notification_id' => $notificationId,
                'user_id' => $user['id'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $notificationId;
    }
}
