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

        return view('users/index');
    }

    /**
     * DATATABLES AJAX
     */
    public function ajaxList()
    {
        if (!$this->request->isAJAX()) {
            return;
        }

        $db = db_connect();

        $builder = $db->table('users')
            ->select('users.id, users.name, users.email, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left');

        // SEARCH
        $search = $this->request->getPost('search')['value'] ?? '';

        if (!empty($search)) {

            $builder->groupStart()
                ->like('users.name', $search)
                ->orLike('users.email', $search)
                ->orLike('roles.name', $search)
                ->groupEnd();

        }

        // TOTAL
        $recordsFiltered = $builder->countAllResults(false);

        $totalBuilder = $db->table('users');

        $recordsTotal = $totalBuilder->countAllResults();

        // PAGINAÇÃO
        $start  = (int) ($this->request->getPost('start') ?? 0);

        $length = (int) ($this->request->getPost('length') ?? 10);

        // ORDENAÇÃO
        $columns = [
            1 => 'users.name',
            2 => 'users.email',
            3 => 'roles.name'
        ];

        $orderColumnIndex = $this->request->getPost('order')[0]['column'] ?? 1;

        $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'asc';

        $orderColumn = $columns[$orderColumnIndex] ?? 'users.name';

        $builder->orderBy($orderColumn, $orderDir);

        // LIMIT
        $builder->limit($length, $start);

        $users = $builder->get()->getResultArray();

        $data = [];

        foreach ($users as $u) {

            $buttons = '';

            if (hasPermission('users.manage')) {

                $buttons = '
                    <div class="btn-group btn-group-sm">

                        <a href="'.base_url('usuarios/edit/'.$u['id']).'"
                           class="btn btn-warning"
                           title="Editar">

                            <i class="fas fa-edit"></i>

                        </a>

                        <a href="'.base_url('usuarios/delete/'.$u['id']).'"
                           class="btn btn-danger"
                           title="Excluir"
                           onclick="return confirm(\'Tem certeza que deseja excluir este usuário?\')">

                            <i class="fas fa-trash"></i>

                        </a>

                    </div>
                ';
            }

          $data[] = [

    '<i class="fas fa-plus expand-row-icon text-center expand-row"></i>',

                esc($u['name']),

                esc($u['email']),

                esc($u['role_name']),

                $buttons

            ];

        }

        return $this->response->setJSON([

            'draw' => intval($this->request->getPost('draw')),

            'recordsTotal' => $recordsTotal,

            'recordsFiltered' => $recordsFiltered,

            'data' => $data

        ]);
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
     * SALVAR
     */
    public function store()
    {
        if (!hasPermission('users.manage')) {
            return redirect()->to('/painel');
        }

        $model = new UserModel();

        $existing = $model->where('email', $this->request->getPost('email'))->first();

        if ($existing) {

            return redirect()->back()
                ->withInput()
                ->with('error', 'Este email já está em uso');

        }

        $data = [

            'name'     => trim($this->request->getPost('name')),

            'email'    => trim($this->request->getPost('email')),

            'password' => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),

            'role_id'  => $this->request->getPost('role_id'),

        ];

        $model->save($data);

        return redirect()->to('/usuarios')
            ->with('success', 'Usuário criado com sucesso');
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
     * UPDATE
     */
    public function update($id)
    {
        if (!hasPermission('users.manage')) {
            return redirect()->to('/painel');
        }

        $model = new UserModel();

        $email = trim($this->request->getPost('email'));

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

            $data['password'] = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

        }

        $model->update($id, $data);

        return redirect()->to('/usuarios')
            ->with('success', 'Usuário atualizado com sucesso');
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

        return redirect()->to('/usuarios')
            ->with('success', 'Usuário removido com sucesso');
    }
}