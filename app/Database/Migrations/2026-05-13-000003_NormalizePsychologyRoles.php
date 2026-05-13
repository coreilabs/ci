<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizePsychologyRoles extends Migration
{
    public function up()
    {
        $role = $this->db->table('roles')->where('slug', 'psicologia')->get()->getRowArray();

        if (! $role) {
            $this->db->table('roles')->insert([
                'name' => 'Psicologia',
                'slug' => 'psicologia',
            ]);
            $role = $this->db->table('roles')->where('slug', 'psicologia')->get()->getRowArray();
        }

        $targetId = (int) $role['id'];
        $duplicates = $this->db->table('roles')
            ->whereIn('slug', ['psicologo', 'psicologa'])
            ->get()
            ->getResultArray();

        foreach ($duplicates as $duplicate) {
            $duplicateId = (int) $duplicate['id'];
            if ($duplicateId === $targetId) {
                continue;
            }

            $this->db->table('users')->where('role_id', $duplicateId)->update(['role_id' => $targetId]);

            foreach ($this->db->table('role_permissions')->where('role_id', $duplicateId)->get()->getResultArray() as $permission) {
                $exists = $this->db->table('role_permissions')
                    ->where('role_id', $targetId)
                    ->where('permission_id', $permission['permission_id'])
                    ->countAllResults();

                if ($exists === 0) {
                    $this->db->table('role_permissions')->insert([
                        'role_id' => $targetId,
                        'permission_id' => $permission['permission_id'],
                    ]);
                }
            }

            $this->db->table('role_permissions')->where('role_id', $duplicateId)->delete();
            $this->db->table('roles')->where('id', $duplicateId)->delete();
        }
    }

    public function down()
    {
    }
}
