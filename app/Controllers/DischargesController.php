<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\DischargeModel;
use App\Models\TreatmentModel;

class DischargesController extends BaseController
{
    public function create(int $treatmentId)
    {
        $treatment = (new TreatmentModel())->listWithPeople()->where('treatments.id', $treatmentId)->first();
        if (! $treatment || $treatment['status'] !== 'active') {
            return redirect()->to('tratamentos/' . $treatmentId)->with('error', 'Alta indisponivel.');
        }

        return view('discharges/create', ['treatment' => $treatment]);
    }

    public function store(int $treatmentId)
    {
        $db = db_connect();
        $db->transStart();

        (new DischargeModel())->insert([
            'treatment_id' => $treatmentId,
            'type' => $this->request->getPost('type'),
            'summary' => $this->request->getPost('summary'),
            'discharged_at' => $this->request->getPost('discharged_at') ?: date('Y-m-d'),
        ]);

        (new TreatmentModel())->update($treatmentId, ['status' => 'discharged']);
        (new AuditLogModel())->write($treatmentId, 'treatment.discharged');

        $db->transComplete();

        return redirect()->to('tratamentos/' . $treatmentId)->with('success', 'Alta registrada e tratamento bloqueado.');
    }
}
