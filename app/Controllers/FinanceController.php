<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\CalendarEventModel;
use App\Models\FinancialEntryModel;
use App\Models\TreatmentModel;
use App\Libraries\PdfService;

class FinanceController extends BaseController
{
    public function index()
    {
        $model = (new FinancialEntryModel())
            ->select('financial_entries.*, patients.name AS patient_name')
            ->join('treatments', 'treatments.id = financial_entries.treatment_id')
            ->join('patients', 'patients.id = treatments.patient_id');

        $filters = $this->request->getGet();
        $this->applyFilters($model, $filters);

        return view('finance/index', [
            'entries' => $model->orderBy('due_date', 'ASC')->findAll(),
            'filters' => $filters,
            'acolhidos' => db_connect()->table('treatments')
                ->select('treatments.id, patients.name')
                ->join('patients', 'patients.id = treatments.patient_id')
                ->orderBy('patients.name', 'ASC')
                ->get()
                ->getResultArray(),
        ]);
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
                'due_date' => $this->dueDateForCompetence($competence, (int) ($treatment['billing_day'] ?? 10)),
                'status' => 'open',
                'created_by' => session('user.id'),
            ]);

            (new CalendarEventModel())->insert([
                'treatment_id' => $treatment['id'],
                'source_type' => 'finance',
                'source_id' => $id,
                'title' => 'Vencimento mensalidade',
                'category' => 'financeiro',
                'starts_at' => $this->dueDateForCompetence($competence, (int) ($treatment['billing_day'] ?? 10)) . ' 09:00:00',
                'created_by' => session('user.id'),
            ]);

            $created++;
        }

        return redirect()->to('financeiro')->with('success', $created . ' mensalidade(s) gerada(s).');
    }

    private function dueDateForCompetence(string $competence, int $billingDay): string
    {
        $billingDay = min(28, max(1, $billingDay));
        $lastDay = (int) date('t', strtotime($competence . '-01'));
        $day = min($billingDay, $lastDay);

        return $competence . '-' . str_pad((string) $day, 2, '0', STR_PAD_LEFT);
    }

    public function pay(int $id)
    {
        helper('format');

        $entry = (new FinancialEntryModel())->find($id);
        if (! $entry) {
            return redirect()->to('financeiro')->with('error', 'Lancamento nao encontrado.');
        }

        $paidAmount = money_to_float($this->request->getPost('paid_amount') ?: (string) $entry['amount']);

        (new FinancialEntryModel())->update($id, [
            'status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s'),
            'paid_amount' => $paidAmount,
            'updated_by' => session('user.id'),
        ]);

        (new AuditLogModel())->write((int) $entry['treatment_id'], 'finance.paid', ['entry_id' => $id]);

        return redirect()->back()->with('success', 'Pagamento registrado.');
    }

    public function pdf()
    {
        $model = (new FinancialEntryModel())
            ->select('financial_entries.*, patients.name AS patient_name')
            ->join('treatments', 'treatments.id = financial_entries.treatment_id')
            ->join('patients', 'patients.id = treatments.patient_id');

        $this->applyFilters($model, $this->request->getGet());
        $entries = $model->orderBy('patients.name', 'ASC')->orderBy('due_date', 'ASC')->findAll();

        $body = '<table><thead><tr><th>Acolhido</th><th>Competência</th><th>Descrição</th><th>Vencimento</th><th>Valor</th><th>Status</th></tr></thead><tbody>';
        foreach ($entries as $entry) {
            $body .= '<tr><td>' . esc($entry['patient_name']) . '</td><td>' . esc(human_month($entry['competence'])) . '</td><td>' . esc($this->statusLabel($entry['description'])) . '</td><td>' . esc(human_date($entry['due_date'])) . '</td><td>R$ ' . number_format((float) $entry['amount'], 2, ',', '.') . '</td><td>' . esc($this->statusLabel($entry['status'])) . '</td></tr>';
        }
        $body .= '</tbody></table>';

        $pdf = (new PdfService())->stream(view('pdf/financial_list', [
            'title' => 'Mensalidades',
            'body' => $body,
        ]), 'mensalidades.pdf');

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="mensalidades.pdf"')
            ->setBody($pdf);
    }

    private function applyFilters(FinancialEntryModel $model, array $filters): void
    {
        if (! empty($filters['status'])) {
            $model->where('financial_entries.status', $filters['status']);
        }

        if (! empty($filters['competence'])) {
            $model->where('financial_entries.competence', $filters['competence']);
        }

        if (! empty($filters['treatment_id'])) {
            $model->where('financial_entries.treatment_id', $filters['treatment_id']);
        }

        if (! empty($filters['type'])) {
            $model->where('financial_entries.type', $filters['type']);
        }

        if (! empty($filters['overdue'])) {
            $model->where('financial_entries.status', 'open')
                ->where('financial_entries.due_date <', date('Y-m-d'));
        }
    }

    private function statusLabel(?string $value): string
    {
        return [
            'open' => 'Aberto',
            'paid' => 'Pago',
        ][$value] ?? (string) $value;
    }
}
