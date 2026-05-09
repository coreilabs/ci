<?php

namespace App\Controllers;

use App\Models\ClinicalRecordModel;
use App\Models\ContractModel;
use App\Models\DocumentModel;
use App\Models\FinancialEntryModel;
use App\Models\CalendarEventModel;
use App\Models\TreatmentModel;

class TreatmentsController extends BaseController
{
    public function index()
    {
        return view('treatments/index', [
            'treatments' => (new TreatmentModel())->listWithPeople()->orderBy('treatments.id', 'DESC')->findAll(),
        ]);
    }

    public function show(int $id)
    {
        $treatment = (new TreatmentModel())->listWithPeople()->where('treatments.id', $id)->first();
        if (! $treatment) {
            return redirect()->to('tratamentos')->with('error', 'Tratamento nao encontrado.');
        }

        return view('treatments/show', [
            'treatment' => $treatment,
            'records' => (new ClinicalRecordModel())->where('treatment_id', $id)->orderBy('recorded_at', 'DESC')->findAll(),
            'financial' => (new FinancialEntryModel())->where('treatment_id', $id)->orderBy('competence', 'DESC')->findAll(),
            'documents' => (new DocumentModel())->where('treatment_id', $id)->orderBy('id', 'DESC')->findAll(),
            'contracts' => (new ContractModel())->where('treatment_id', $id)->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function updateBillingDay(int $id)
    {
        $billingDay = min(28, max(1, (int) $this->request->getPost('billing_day')));
        $treatment = (new TreatmentModel())->find($id);

        if (! $treatment) {
            return redirect()->to('tratamentos')->with('error', 'Tratamento nao encontrado.');
        }

        (new TreatmentModel())->update($id, ['billing_day' => $billingDay]);

        $finance = new FinancialEntryModel();
        $events = new CalendarEventModel();

        $entries = $finance->where('treatment_id', $id)
            ->where('type', 'mensalidade')
            ->where('status', 'open')
            ->where('due_date >=', date('Y-m-d'))
            ->findAll();

        foreach ($entries as $entry) {
            $dueDate = $this->dueDateForCompetence($entry['competence'], $billingDay);
            $finance->update($entry['id'], ['due_date' => $dueDate]);

            $event = $events->where('source_type', 'finance')
                ->where('source_id', $entry['id'])
                ->first();

            if ($event) {
                $events->update($event['id'], ['starts_at' => $dueDate . ' 09:00:00']);
            }
        }

        return redirect()->to('tratamentos/' . $id)
            ->with('success', 'Dia de cobranca atualizado nas mensalidades futuras em aberto.');
    }

    private function dueDateForCompetence(string $competence, int $billingDay): string
    {
        $month = $competence . '-01';
        $lastDay = (int) date('t', strtotime($month));
        $day = min($billingDay, $lastDay);

        return $competence . '-' . str_pad((string) $day, 2, '0', STR_PAD_LEFT);
    }
}
