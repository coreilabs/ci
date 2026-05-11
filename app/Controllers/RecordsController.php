<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\CalendarEventModel;
use App\Models\ClinicalRecordModel;
use App\Models\TreatmentModel;
use App\Models\TreatmentProfessionalModel;

class RecordsController extends BaseController
{
    public function create(int $treatmentId)
    {
        $treatment = (new TreatmentModel())->listWithPeople()->where('treatments.id', $treatmentId)->first();
        if (! $treatment || $treatment['status'] !== 'active') {
            return redirect()->to('tratamentos/' . $treatmentId)->with('error', 'Tratamento fechado para novos registros.');
        }

        if (! $this->canRegisterForTreatment($treatmentId)) {
            return redirect()->to('tratamentos/' . $treatmentId)->with('error', 'Paciente não está na sua lista de atendimento.');
        }

        return view('records/create', ['treatment' => $treatment]);
    }

    public function store(int $treatmentId)
    {
        helper('format');

        $treatment = (new TreatmentModel())->find($treatmentId);
        if (! $treatment || $treatment['status'] !== 'active') {
            return redirect()->to('tratamentos/' . $treatmentId)->with('error', 'Tratamento fechado para novos registros.');
        }

        if (! $this->canRegisterForTreatment($treatmentId)) {
            return redirect()->to('tratamentos/' . $treatmentId)->with('error', 'Paciente não está na sua lista de atendimento.');
        }

        $recordId = (new ClinicalRecordModel())->insert([
            'treatment_id' => $treatmentId,
            'user_id' => session('user.id'),
            'type' => $this->request->getPost('type'),
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'sae_collection' => $this->request->getPost('sae_collection'),
            'sae_diagnosis' => $this->request->getPost('sae_diagnosis'),
            'sae_planning' => $this->request->getPost('sae_planning'),
            'sae_execution' => $this->request->getPost('sae_execution'),
            'sae_evaluation' => $this->request->getPost('sae_evaluation'),
            'recorded_at' => datetime_local_to_sql($this->request->getPost('recorded_at')) ?? date('Y-m-d H:i:s'),
        ]);

        if ($this->request->getPost('create_event')) {
            (new CalendarEventModel())->insert([
                'treatment_id' => $treatmentId,
                'professional_user_id' => session('user.id'),
                'source_type' => 'clinical_record',
                'source_id' => $recordId,
                'title' => $this->request->getPost('title'),
                'category' => $this->request->getPost('type'),
                'starts_at' => datetime_local_to_sql($this->request->getPost('recorded_at')) ?? date('Y-m-d H:i:s'),
            ]);
        }

        (new AuditLogModel())->write($treatmentId, 'record.created', ['record_id' => $recordId]);

        return redirect()->to('tratamentos/' . $treatmentId)->with('success', 'Registro incluido na timeline.');
    }

    private function canRegisterForTreatment(int $treatmentId): bool
    {
        if ($this->isAdmin() || ! $this->isPsychologist()) {
            return true;
        }

        $assignment = (new TreatmentProfessionalModel())
            ->where('treatment_id', $treatmentId)
            ->where('specialty', 'psicologia')
            ->first();

        return $assignment && (int) $assignment['user_id'] === (int) session('user.id');
    }

    private function isAdmin(): bool
    {
        return session('user.role') === 'admin' || hasPermission('users.manage');
    }

    private function isPsychologist(): bool
    {
        $role = strtolower((string) session('user.role'));

        return strpos($role, 'psic') !== false || strpos($role, 'psych') !== false;
    }
}
