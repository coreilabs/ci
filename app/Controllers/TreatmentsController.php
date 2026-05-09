<?php

namespace App\Controllers;

use App\Models\ClinicalRecordModel;
use App\Models\ContractModel;
use App\Models\DocumentModel;
use App\Models\FinancialEntryModel;
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
}
