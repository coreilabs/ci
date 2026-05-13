<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\CalendarEventModel;
use App\Models\IncidentModel;
use App\Models\IncidentTypeModel;
use App\Models\NotificationModel;
use App\Models\TreatmentModel;

class CoordinatorController extends BaseController
{
    public function index()
    {
        $db = db_connect();

        $acolhidos = (new TreatmentModel())->listWithPeople()
            ->where('treatments.status', 'active')
            ->orderBy('patients.name', 'ASC')
            ->findAll();

        $incidents = $db->table('incidents')
            ->select('incidents.*, patients.name AS acolhido_name, users.name AS created_by_name, incident_types.name AS type_name')
            ->join('treatments', 'treatments.id = incidents.treatment_id')
            ->join('patients', 'patients.id = treatments.patient_id')
            ->join('users', 'users.id = incidents.created_by', 'left')
            ->join('incident_types', 'incident_types.id = incidents.incident_type_id', 'left')
            ->orderBy('incidents.occurred_at', 'DESC')
            ->limit(30)
            ->get()
            ->getResultArray();

        return view('coordinator/index', [
            'acolhidos' => $acolhidos,
            'incidentTypes' => (new IncidentTypeModel())->where('active', 1)->orderBy('name')->findAll(),
            'incidents' => $incidents,
        ]);
    }

    public function storeIncident()
    {
        if (! hasPermission('incidents.manage') && ! hasPermission('coordinator.view')) {
            return redirect()->to('painel')->with('error', 'Sem permissão para registrar ocorrências.');
        }

        helper('format');

        $treatmentId = (int) $this->request->getPost('treatment_id');
        $treatment = (new TreatmentModel())->listWithPeople()->where('treatments.id', $treatmentId)->first();

        if (! $treatment) {
            return redirect()->to('coordenacao')->with('error', 'Acolhido nao encontrado.');
        }

        $occurredAt = datetime_local_to_sql($this->request->getPost('occurred_at')) ?? date('Y-m-d H:i:s');
        $title = trim((string) $this->request->getPost('title'));
        $description = trim((string) $this->request->getPost('description'));

        $incidentId = (new IncidentModel())->insert([
            'treatment_id' => $treatmentId,
            'incident_type_id' => $this->request->getPost('incident_type_id') ?: null,
            'title' => $title,
            'description' => $description,
            'occurred_at' => $occurredAt,
            'created_by' => session('user.id'),
        ]);

        (new CalendarEventModel())->insert([
            'treatment_id' => $treatmentId,
            'source_type' => 'incident',
            'source_id' => $incidentId,
            'title' => $title,
            'category' => 'ocorrencia',
            'starts_at' => $occurredAt,
            'notes' => $description,
            'created_by' => session('user.id'),
        ]);

        (new NotificationModel())->createForRoleSlug('admin', [
            'type' => 'incident',
            'title' => 'Ocorrencia registrada',
            'body' => 'Acolhido ' . $treatment['patient_name'] . ': ' . $title,
            'treatment_id' => $treatmentId,
        ]);

        (new AuditLogModel())->write($treatmentId, 'incident.created', ['incident_id' => $incidentId]);

        return redirect()->to('coordenacao')->with('success', 'Ocorrencia registrada no painel, agenda e prontuario.');
    }
}
