<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useSoftDeletes = false;

    /**
     * Campos permitidos (limpo e seguro)
     */
    protected $allowedFields = [
        'name',
        'email',
        'password',
        'role_id'
    ];

    /**
     * Timestamps automáticos
     */
    protected $useTimestamps = true;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Validação básica (sem is_unique aqui)
     */
    protected $validationRules = [
        'name'  => 'required|min_length[3]',
        'email' => 'required|valid_email',
    ];

    protected $validationMessages = [
        'email' => [
            'valid_email' => 'Email inválido'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Login RBAC (user + role)
     */
    public function getUserWithRole($email)
    {
        return $this->select('users.*, roles.slug as role, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.email', $email)
            ->first();
    }

    /**
     * Listagem com roles
     */
    public function getUsersWithRoles()
    {
        return $this->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id')
            ->findAll();
    }
}