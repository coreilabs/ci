<?php

namespace App\Controllers;

use App\Models\AppSettingModel;
use App\Models\IncidentTypeModel;

class AdminPanelController extends BaseController
{
    public function index()
    {
        $db = db_connect();

        return view('admin_panel/index', [
            'settings' => (new AppSettingModel())->orderBy('group')->orderBy('label')->findAll(),
            'roles' => $db->table('roles')->orderBy('name')->get()->getResultArray(),
            'permissions' => $db->table('permissions')->orderBy('name')->get()->getResultArray(),
            'rolePermissions' => $db->table('role_permissions')->get()->getResultArray(),
            'incidentTypes' => (new IncidentTypeModel())->orderBy('name')->findAll(),
        ]);
    }

    public function updateSettings()
    {
        if (! hasPermission('settings.manage')) {
            return redirect()->to('painel')->with('error', 'Sem permissao para alterar configuracoes.');
        }

        $model = new AppSettingModel();
        foreach ($model->findAll() as $setting) {
            $value = $this->request->getPost($setting['key']);
            if ($setting['type'] === 'boolean') {
                $value = $value ? '1' : '0';
            }
            $model->update($setting['id'], [
                'value' => $value,
                'updated_by' => session('user.id'),
            ]);
        }

        return redirect()->to('painel-administrativo')->with('success', 'Configurações atualizadas.');
    }

    public function updatePermissions()
    {
        if (! hasPermission('permissions.manage') && ! hasPermission('users.manage')) {
            return redirect()->to('painel')->with('error', 'Sem permissão para alterar permissões.');
        }

        $db = db_connect();
        $roleId = (int) $this->request->getPost('role_id');
        $permissions = array_map('intval', (array) $this->request->getPost('permissions'));

        $db->table('role_permissions')->where('role_id', $roleId)->delete();
        foreach ($permissions as $permissionId) {
            $db->table('role_permissions')->insert([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ]);
        }

        return redirect()->to('painel-administrativo')->with('success', 'Permissões do cargo atualizadas.');
    }

    public function storeIncidentType()
    {
        if (! hasPermission('settings.manage') && ! hasPermission('incidents.manage')) {
            return redirect()->to('painel')->with('error', 'Sem permissão para criar tipos.');
        }

        (new IncidentTypeModel())->insert([
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'severity' => $this->request->getPost('severity') ?: 'media',
            'active' => $this->request->getPost('active') ? 1 : 0,
        ]);

        return redirect()->to('painel-administrativo')->with('success', 'Tipo de ocorrência criado.');
    }

    public function updateIncidentType(int $id)
    {
        if (! hasPermission('settings.manage') && ! hasPermission('incidents.manage')) {
            return redirect()->to('painel')->with('error', 'Sem permissão para editar tipos.');
        }

        (new IncidentTypeModel())->update($id, [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'severity' => $this->request->getPost('severity') ?: 'media',
            'active' => $this->request->getPost('active') ? 1 : 0,
        ]);

        return redirect()->to('painel-administrativo')->with('success', 'Tipo de ocorrência atualizado.');
    }

    public function deleteIncidentType(int $id)
    {
        if (! hasPermission('settings.manage') && ! hasPermission('incidents.manage')) {
            return redirect()->to('painel')->with('error', 'Sem permissão para excluir tipos.');
        }

        (new IncidentTypeModel())->delete($id);

        return redirect()->to('painel-administrativo')->with('success', 'Tipo de ocorrência excluído.');
    }
}
