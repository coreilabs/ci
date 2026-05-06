<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('user')) {
            return redirect()->to('/painel');
        }

        return view('auth/login');
    }

    public function attempt()
    {
        // Validação básica
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Dados inválidos');
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $model = new UserModel();

        $user = $model
            ->select('users.*, roles.slug as role')
            ->join('roles', 'roles.id = users.role_id')
            ->where('email', $email)
            ->first();

        // Evita enumeração de usuário
        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email ou senha inválidos');
        }

        // 🔐 Carregar permissões (RBAC real)
        $db = db_connect();

        $permissions = $db->table('role_permissions rp')
            ->select('p.slug')
            ->join('permissions p', 'p.id = rp.permission_id')
            ->where('rp.role_id', $user['role_id'])
            ->get()
            ->getResultArray();

        // 🔄 Regenerar sessão (segurança)
        session()->regenerate();

        session()->set('user', [
            'id'          => $user['id'],
            'name'        => $user['name'],
            'email'       => $user['email'],
            'role'        => $user['role'],
            'permissions' => array_column($permissions, 'slug')
        ]);

        return redirect()->to('/painel');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }


}