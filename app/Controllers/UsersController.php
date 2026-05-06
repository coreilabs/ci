<?php

namespace App\Controllers;

use App\Models\UserModel;

class UsersController extends BaseController
{
    /**
     * LISTAGEM
     */
    public function index()
    {
        if (!hasPermission('users.manage') && !hasPermission('users.view')) {
            return redirect()->to('/painel');
        }

        $model = new UserModel();

        $data['users'] = $model->getUsersWithRoles();

        return view('users/index', $data);
    }

    /**
     * FORM CRIAR
     */
    public function create()
    {
        if (!hasPermission('users.manage')) {
            return redirect()->to('/painel');
        }

        $db = db_connect();
        $data['roles'] = $db->table('roles')->get()->getResultArray();

        return view('users/create', $data);
    }

    /**
     * SALVAR USUÁRIO
     */
    public function store()
    {
        if (!hasPermission('users.manage')) {
            return redirect()->to('/painel');
        }

        $model = new UserModel();

        // valida email duplicado
        $existing = $model->where('email', $this->request->getPost('email'))->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Este email já está em uso');
        }

        $data = [
            'name'     => trim($this->request->getPost('name')),
            'email'    => trim($this->request->getPost('email')),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role_id'  => $this->request->getPost('role_id'),
        ];

        $model->save($data);

        return redirect()->to('/usuarios')->with('success', 'Usuário criado com sucesso');
    }

    /**
     * FORM EDITAR
     */
    public function edit($id)
    {
        if (!hasPermission('users.manage')) {
            return redirect()->to('/painel');
        }

        $model = new UserModel();
        $db = db_connect();

        $data['user'] = $model->find($id);
        $data['roles'] = $db->table('roles')->get()->getResultArray();

        if (!$data['user']) {
            return redirect()->to('/usuarios');
        }

        return view('users/edit', $data);
    }

    /**
     * ATUALIZAR USUÁRIO
     */
    public function update($id)
    {
        if (!hasPermission('users.manage')) {
            return redirect()->to('/painel');
        }

        $model = new UserModel();

        $email = trim($this->request->getPost('email'));

        // valida email duplicado (ignorando o próprio usuário)
        $existing = $model->where('email', $email)
                          ->where('id !=', $id)
                          ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Este email já está em uso');
        }

        $data = [
            'name'    => trim($this->request->getPost('name')),
            'email'   => $email,
            'role_id' => $this->request->getPost('role_id'),
        ];

        $password = trim($this->request->getPost('password'));

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $model->update($id, $data);

        return redirect()->to('/usuarios')->with('success', 'Usuário atualizado com sucesso');
    }

    /**
     * DELETE
     */
    public function delete($id)
    {
        if (!hasPermission('users.manage')) {
            return redirect()->to('/painel');
        }

        $model = new UserModel();

        $model->delete($id);

        return redirect()->to('/usuarios')->with('success', 'Usuário removido com sucesso');
    }
}