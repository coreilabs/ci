<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\CalendarEventModel;
use App\Models\FinancialEntryModel;
use App\Models\TreatmentModel;

class FinanceController extends BaseController
{
    public function index()
    {
        $entries = (new FinancialEntryModel())
            ->select('financial_entries.*, patients.name AS patient_name')
            ->join('treatments', 'treatments.id = financial_entries.treatment_id')
            ->join('patients', 'patients.id = treatments.patient_id')
            ->orderBy('competence', 'DESC')
            ->findAll();

        return view('finance/index', ['entries' => $entries]);
    }

    public function generateMonthly()
    {
        $competence = $this->request->getPost('competence') ?: date('Y-m');
        $created = 0;
        $finance = new FinancialEntryModel();

        foreach ((new TreatmentModel())->where('status', 'active')->findAll() as $treatment) {
            if ((float) $treatment['monthly_amount'] <= 0) {
                continue;
            }

            $exists = $finance->where('treatment_id', $treatment['id'])
                ->where('competence', $competence)
                ->where('type', 'mensalidade')
                ->first();

            if ($exists) {
                continue;
            }

            $id = $finance->insert([
                'treatment_id' => $treatment['id'],
                'competence' => $competence,
                'type' => 'mensalidade',
                'description' => 'Mensalidade',
                'amount' => $treatment['monthly_amount'],
                'due_date' => $competence . '-10',
                'status' => 'open',
            ]);

            (new CalendarEventModel())->insert([
                'treatment_id' => $treatment['id'],
                'source_type' => 'finance',
                'source_id' => $id,
                'title' => 'Vencimento mensalidade',
                'category' => 'financeiro',
                'starts_at' => $competence . '-10 09:00:00',
            ]);

            $created++;
        }

        return redirect()->to('financeiro')->with('success', $created . ' mensalidade(s) gerada(s).');
    }

    public function pay(int $id)
    {
        $entry = (new FinancialEntryModel())->find($id);
        if (! $entry) {
            return redirect()->to('financeiro')->with('error', 'Lancamento nao encontrado.');
        }

        (new FinancialEntryModel())->update($id, [
            'status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s'),
        ]);

        (new AuditLogModel())->write((int) $entry['treatment_id'], 'finance.paid', ['entry_id' => $id]);

        return redirect()->back()->with('success', 'Pagamento registrado.');
    }
}
