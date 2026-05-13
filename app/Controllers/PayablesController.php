<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\PayableEntryModel;

class PayablesController extends BaseController
{
    public function index()
    {
        $model = new PayableEntryModel();
        $filters = $this->request->getGet();

        if (! empty($filters['status'])) {
            $model->where('status', $filters['status']);
        }
        if (! empty($filters['category'])) {
            $model->where('category', $filters['category']);
        }
        if (! empty($filters['overdue'])) {
            $model->where('status', 'open')->where('due_date <', date('Y-m-d'));
        }

        return view('payables/index', [
            'entries' => $model->orderBy('due_date', 'ASC')->findAll(),
            'filters' => $filters,
        ]);
    }

    public function store()
    {
        helper('format');

        (new PayableEntryModel())->insert([
            'category' => $this->request->getPost('category') ?: 'Despesa',
            'payee_name' => $this->request->getPost('payee_name'),
            'description' => $this->request->getPost('description'),
            'amount' => money_to_float($this->request->getPost('amount')),
            'due_date' => $this->request->getPost('due_date') ?: null,
            'competence' => $this->request->getPost('competence') ?: null,
            'status' => 'open',
            'created_by' => session('user.id'),
        ]);

        return redirect()->to('contas-a-pagar')->with('success', 'Conta a pagar criada.');
    }

    public function pay(int $id)
    {
        $entry = (new PayableEntryModel())->find($id);
        if (! $entry) {
            return redirect()->to('contas-a-pagar')->with('error', 'Conta nao encontrada.');
        }

        (new PayableEntryModel())->update($id, [
            'status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s'),
            'updated_by' => session('user.id'),
        ]);

        (new AuditLogModel())->write($entry['treatment_id'] ? (int) $entry['treatment_id'] : null, 'payable.paid', ['payable_id' => $id]);

        return redirect()->back()->with('success', 'Pagamento registrado.');
    }
}
