<?php

namespace App\Controllers;

use App\Models\ScheduleItemModel;

class ScheduleController extends BaseController
{
    public function index()
    {
        return view('schedule/index', [
            'items' => $this->model()->orderBy('sort_order', 'ASC')->orderBy('starts_at', 'ASC')->findAll(),
        ]);
    }

    public function store()
    {
        if (! $this->canManage()) {
            return redirect()->to('painel')->with('error', 'Sem permissão para alterar o cronograma.');
        }

        $this->model()->insert($this->payload() + [
            'created_by' => session('user.id'),
        ]);

        return redirect()->to('cronograma')->with('success', 'Atividade criada no cronograma.');
    }

    public function update(int $id)
    {
        if (! $this->canManage()) {
            return redirect()->to('painel')->with('error', 'Sem permissão para alterar o cronograma.');
        }

        $this->model()->update($id, $this->payload() + [
            'updated_by' => session('user.id'),
        ]);

        return redirect()->to('cronograma')->with('success', 'Atividade atualizada.');
    }

    public function delete(int $id)
    {
        if (! $this->canManage()) {
            return redirect()->to('painel')->with('error', 'Sem permissão para alterar o cronograma.');
        }

        $this->model()->delete($id);

        return redirect()->to('cronograma')->with('success', 'Atividade removida.');
    }

    private function payload(): array
    {
        return [
            'day_label' => trim((string) $this->request->getPost('day_label')),
            'starts_at' => $this->request->getPost('starts_at') ?: null,
            'ends_at' => $this->request->getPost('ends_at') ?: null,
            'activity' => trim((string) $this->request->getPost('activity')),
            'audience' => trim((string) $this->request->getPost('audience')) ?: null,
            'notes' => trim((string) $this->request->getPost('notes')) ?: null,
            'active' => $this->request->getPost('active') ? 1 : 0,
            'sort_order' => (int) ($this->request->getPost('sort_order') ?? 0),
        ];
    }

    private function model(): ScheduleItemModel
    {
        return new ScheduleItemModel();
    }

    private function canManage(): bool
    {
        return session('user.role') === 'admin'
            || hasPermission('settings.manage')
            || hasPermission('coordinator.view')
            || hasPermission('schedule.manage');
    }
}
