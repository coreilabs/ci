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
        $model = (new TreatmentModel())->listWithPeople();
        $filters = $this->request->getGet();

        if (! empty($filters['status'])) {
            $model->where('treatments.status', $filters['status']);
        }

        if (! empty($filters['acolhido'])) {
            $model->like('patients.name', $filters['acolhido']);
        }

        if (! empty($filters['contract_end_month'])) {
            $month = db_connect()->escape($filters['contract_end_month']);
            $model->where('DATE_FORMAT(DATE_ADD(treatments.admission_date, INTERVAL treatments.stay_months MONTH), "%Y-%m") = ' . $month, null, false);
        }

        return view('treatments/index', [
            'treatments' => $model->orderBy('treatments.id', 'DESC')->findAll(),
            'filters' => $filters,
        ]);
    }

    public function show(int $id)
    {
        $treatment = (new TreatmentModel())->listWithPeople()->where('treatments.id', $id)->first();
        if (! $treatment) {
            return redirect()->to('tratamentos')->with('error', 'Tratamento nao encontrado.');
        }

        $guardian = db_connect()->table('guardians')->where('id', $treatment['guardian_id'])->get()->getRowArray();

        $records = (new ClinicalRecordModel())
            ->select('clinical_records.*, users.name AS professional_name')
            ->join('users', 'users.id = clinical_records.user_id', 'left')
            ->where('clinical_records.treatment_id', $id);

        if (! $this->isAdmin()) {
            $records->where('clinical_records.user_id', session('user.id'));
        }

        return view('treatments/show', [
            'treatment' => $treatment,
            'guardian' => $guardian,
            'records' => $records->orderBy('recorded_at', 'DESC')->findAll(),
            'financial' => (new FinancialEntryModel())->where('treatment_id', $id)->orderBy('competence', 'DESC')->findAll(),
            'documents' => (new DocumentModel())->where('treatment_id', $id)->orderBy('id', 'DESC')->findAll(),
            'contracts' => (new ContractModel())->where('treatment_id', $id)->orderBy('id', 'DESC')->findAll(),
            'familyAccess' => (new \App\Models\FamilyPortalAccessModel())->where('treatment_id', $id)->orderBy('id', 'DESC')->first(),
            'familyFiles' => db_connect()->table('family_portal_files fpf')
                ->join('family_portal_accesses fpa', 'fpa.id = fpf.portal_access_id')
                ->where('fpa.treatment_id', $id)
                ->get()
                ->getResultArray(),
            'incidents' => db_connect()->table('incidents')
                ->select('incidents.*, users.name AS created_by_name')
                ->join('users', 'users.id = incidents.created_by', 'left')
                ->where('incidents.treatment_id', $id)
                ->orderBy('incidents.occurred_at', 'DESC')
                ->get()
                ->getResultArray(),
            'canSeeAllRecords' => $this->isAdmin(),
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
            ->with('success', 'Dia de cobrança atualizado nas mensalidades futuras em aberto.');
    }

    public function reactivate(int $id)
    {
        if (! $this->isAdmin()) {
            return redirect()->back()->with('error', 'Apenas administradores podem reativar internações.');
        }

        $treatment = (new TreatmentModel())->find($id);
        if (! $treatment || $treatment['status'] !== 'discharged') {
            return redirect()->back()->with('error', 'Internação não encontrada ou ainda ativa.');
        }

        $newId = (new TreatmentModel())->insert([
            'patient_id' => $treatment['patient_id'],
            'guardian_id' => $treatment['guardian_id'],
            'admission_date' => date('Y-m-d'),
            'monthly_amount' => $treatment['monthly_amount'],
            'registration_amount' => $treatment['registration_amount'],
            'stay_months' => $treatment['stay_months'] ?? 1,
            'billing_day' => $treatment['billing_day'] ?? 10,
            'captor_name' => $treatment['captor_name'],
            'status' => 'active',
            'notes' => trim(($treatment['notes'] ?? '') . "\nReativada a partir da internação #" . $id),
            'created_by' => session('user.id'),
        ]);

        (new \App\Models\AuditLogModel())->write($newId, 'treatment.reactivated', ['previous_treatment_id' => $id]);

        return redirect()->to('tratamentos/' . $newId)->with('success', 'Nova internação ativa criada com dados populados da internação anterior.');
    }

    private function dueDateForCompetence(string $competence, int $billingDay): string
    {
        $month = $competence . '-01';
        $lastDay = (int) date('t', strtotime($month));
        $day = min($billingDay, $lastDay);

        return $competence . '-' . str_pad((string) $day, 2, '0', STR_PAD_LEFT);
    }

    private function isAdmin(): bool
    {
        return session('user.role') === 'admin' || hasPermission('users.manage');
    }
}
