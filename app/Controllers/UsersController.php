<?php

namespace App\Controllers;

use App\Models\UserModel;

class UsersController extends BaseController
{
    public function index()
    {
        if (! hasPermission('users.manage') && ! hasPermission('users.view')) {
            return redirect()->to('/painel');
        }

        return view('users/index');
    }

    public function ajaxList()
    {
        if (! $this->request->isAJAX()) {
            return;
        }

        $db = db_connect();
        $builder = $db->table('users')
            ->select('users.id, users.name, users.email, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left');

        $search = $this->request->getPost('search')['value'] ?? '';
        if ($search !== '') {
            $builder->groupStart()
                ->like('users.name', $search)
                ->orLike('users.email', $search)
                ->orLike('roles.name', $search)
                ->groupEnd();
        }

        $recordsFiltered = $builder->countAllResults(false);
        $recordsTotal = $db->table('users')->countAllResults();
        $start = (int) ($this->request->getPost('start') ?? 0);
        $length = (int) ($this->request->getPost('length') ?? 10);

        $columns = [1 => 'users.name', 2 => 'users.email', 3 => 'roles.name'];
        $orderColumnIndex = $this->request->getPost('order')[0]['column'] ?? 1;
        $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'asc';
        $builder->orderBy($columns[$orderColumnIndex] ?? 'users.name', $orderDir);
        $users = $builder->limit($length, $start)->get()->getResultArray();

        $data = [];
        foreach ($users as $user) {
            $buttons = '';
            if (hasPermission('users.manage')) {
                $buttons = '
                    <div class="btn-group btn-group-sm">
                        <a href="' . base_url('usuarios/edit/' . $user['id']) . '" class="btn btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="post" action="' . base_url('usuarios/delete/' . $user['id']) . '" onsubmit="return confirm(\'Tem certeza que deseja excluir este usuario?\')">
                            ' . csrf_field() . '
                            <button class="btn btn-danger btn-sm" title="Excluir"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>';
            }

            $data[] = [
                '<i class="fas fa-plus expand-row-icon text-center expand-row"></i>',
                esc($user['name']),
                esc($user['email']),
                esc($user['role_name']),
                $buttons,
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($this->request->getPost('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function create()
    {
        if (! hasPermission('users.manage')) {
            return redirect()->to('/painel');
        }

        return view('users/create', [
            'roles' => db_connect()->table('roles')->orderBy('name')->get()->getResultArray(),
        ]);
    }

    public function store()
    {
        if (! hasPermission('users.manage')) {
            return redirect()->to('/painel');
        }

        $model = new UserModel();
        if ($model->where('email', $this->request->getPost('email'))->first()) {
            return redirect()->back()->withInput()->with('error', 'Este email ja esta em uso');
        }

        $model->save([
            'name' => trim($this->request->getPost('name')),
            'email' => trim($this->request->getPost('email')),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role_id' => $this->request->getPost('role_id'),
        ]);

        return redirect()->to('/usuarios')->with('success', 'Usuario criado com sucesso');
    }

    public function edit($id)
    {
        if (! hasPermission('users.manage')) {
            return redirect()->to('/painel');
        }

        $model = new UserModel();
        $user = $model->find($id);
        if (! $user) {
            return redirect()->to('/usuarios');
        }

        return view('users/edit', [
            'user' => $user,
            'roles' => db_connect()->table('roles')->orderBy('name')->get()->getResultArray(),
        ]);
    }

    public function update($id)
    {
        if (! hasPermission('users.manage')) {
            return redirect()->to('/painel');
        }

        $model = new UserModel();
        $email = trim($this->request->getPost('email'));
        if ($model->where('email', $email)->where('id !=', $id)->first()) {
            return redirect()->back()->withInput()->with('error', 'Este email ja esta em uso');
        }

        $data = [
            'name' => trim($this->request->getPost('name')),
            'email' => $email,
            'role_id' => $this->request->getPost('role_id'),
        ];

        $password = trim((string) $this->request->getPost('password'));
        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $model->update($id, $data);

        return redirect()->to('/usuarios')->with('success', 'Usuario atualizado com sucesso');
    }

    public function delete($id)
    {
        if (! hasPermission('users.manage')) {
            return redirect()->to('/painel');
        }

        (new UserModel())->delete($id);

        return redirect()->to('/usuarios')->with('success', 'Usuario removido com sucesso');
    }
}
