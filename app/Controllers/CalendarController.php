<?php

namespace App\Controllers;

use App\Models\CalendarEventModel;
use App\Models\FinancialEntryModel;
use App\Models\TreatmentProfessionalModel;
use App\Models\TreatmentModel;

class CalendarController extends BaseController
{
    public function index()
    {
        return view('calendar/index', [
            'treatments' => (new TreatmentModel())->listWithPeople()->where('treatments.status', 'active')->findAll(),
            'professionals' => $this->professionals(),
            'weeklyPsychologicalEvents' => $this->weeklyPsychologicalEvents(),
        ]);
    }

    public function events()
    {
        $events = [];
        $rows = (new CalendarEventModel())
            ->select('calendar_events.*, financial_entries.amount, financial_entries.status AS financial_status, financial_entries.due_date, patients.name AS patient_name, users.name AS professional_name')
            ->join('financial_entries', 'financial_entries.id = calendar_events.source_id AND calendar_events.source_type = "finance"', 'left')
            ->join('treatments', 'treatments.id = calendar_events.treatment_id', 'left')
            ->join('patients', 'patients.id = treatments.patient_id', 'left')
            ->join('users', 'users.id = calendar_events.professional_user_id', 'left')
            ->findAll();

        foreach ($rows as $event) {
            $events[] = [
                'id' => $event['id'],
                'title' => $this->eventTitle($event),
                'start' => $event['starts_at'],
                'end' => $event['ends_at'],
                'className' => 'event-' . $event['category'],
                'extendedProps' => [
                    'category' => $event['category'],
                    'source_type' => $event['source_type'],
                    'source_id' => $event['source_id'],
                    'treatment_id' => $event['treatment_id'],
                    'patient_name' => $event['patient_name'],
                    'professional_name' => $event['professional_name'],
                    'amount' => $event['amount'],
                    'financial_status' => $event['financial_status'],
                    'due_date' => $event['due_date'],
                    'formatted_due_date' => human_date($event['due_date']),
                    'notes' => $event['notes'],
                ],
            ];
        }

        return $this->response->setJSON($events);
    }

    private function eventTitle(array $event): string
    {
        if ($event['source_type'] !== 'finance') {
            $patient = $event['patient_name'] ? ' - ' . $event['patient_name'] : '';
            $professional = $event['professional_name'] ? ' (' . $event['professional_name'] . ')' : '';

            return $event['title'] . $patient . $professional;
        }

        $status = $event['financial_status'] === 'paid' ? 'Pago' : 'Aberto';
        $patient = $event['patient_name'] ? ' - ' . $event['patient_name'] : '';

        return $event['title'] . $patient . ' (' . $status . ')';
    }

    public function store()
    {
        helper('format');

        (new CalendarEventModel())->insert([
            'treatment_id' => $this->request->getPost('treatment_id') ?: null,
            'professional_user_id' => $this->request->getPost('professional_user_id') ?: null,
            'source_type' => 'manual',
            'title' => $this->request->getPost('title'),
            'category' => $this->request->getPost('category'),
            'starts_at' => datetime_local_to_sql($this->request->getPost('starts_at')),
            'ends_at' => datetime_local_to_sql($this->request->getPost('ends_at')),
            'notes' => $this->request->getPost('notes'),
        ]);

        return redirect()->to('agenda')->with('success', 'Evento criado.');
    }

    public function move(int $id)
    {
        helper('format');

        $event = (new CalendarEventModel())->find($id);
        if (! $event) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Evento nao encontrado.']);
        }

        (new CalendarEventModel())->update($id, [
            'starts_at' => datetime_local_to_sql($this->request->getPost('starts_at')),
            'ends_at' => datetime_local_to_sql($this->request->getPost('ends_at')),
        ]);

        if ($event['source_type'] === 'finance' && $event['source_id']) {
            (new FinancialEntryModel())->update($event['source_id'], [
                'due_date' => date('Y-m-d', strtotime($this->request->getPost('starts_at'))),
            ]);
        }

        if ($event['source_type'] === 'psychology_assignment' && $event['professional_user_id']) {
            $assignment = (new TreatmentProfessionalModel())
                ->where('treatment_id', $event['treatment_id'])
                ->where('user_id', $event['professional_user_id'])
                ->where('specialty', 'psicologia')
                ->first();

            if ($assignment) {
                (new TreatmentProfessionalModel())->update($assignment['id'], [
                    'next_attendance_at' => datetime_local_to_sql($this->request->getPost('starts_at')),
                ]);
            }
        }

        return $this->response->setJSON(['ok' => true]);
    }

    public function payFinancialEvent(int $id)
    {
        $event = (new CalendarEventModel())->find($id);
        if (! $event || $event['source_type'] !== 'finance' || ! $event['source_id']) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Cobranca nao encontrada.']);
        }

        (new FinancialEntryModel())->update($event['source_id'], [
            'status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['ok' => true]);
    }

    public function rescheduleFinancialEvent(int $id)
    {
        helper('format');

        $event = (new CalendarEventModel())->find($id);
        if (! $event || $event['source_type'] !== 'finance' || ! $event['source_id']) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Cobranca nao encontrada.']);
        }

        $dueDate = $this->request->getPost('due_date');
        if (! $dueDate) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Informe a nova data.']);
        }

        (new FinancialEntryModel())->update($event['source_id'], [
            'due_date' => $dueDate,
        ]);

        (new CalendarEventModel())->update($id, [
            'starts_at' => $dueDate . ' 09:00:00',
        ]);

        return $this->response->setJSON(['ok' => true]);
    }

    private function professionals(): array
    {
        return db_connect()->table('users')
            ->select('users.id, users.name, roles.name AS role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->orderBy('users.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function weeklyPsychologicalEvents(): array
    {
        $weekStart = date('Y-m-d 00:00:00', strtotime('monday this week'));
        $weekEnd = date('Y-m-d 23:59:59', strtotime('sunday this week'));

        $builder = db_connect()->table('calendar_events')
            ->select('calendar_events.*, patients.name AS patient_name, users.name AS professional_name')
            ->join('treatments', 'treatments.id = calendar_events.treatment_id', 'left')
            ->join('patients', 'patients.id = treatments.patient_id', 'left')
            ->join('users', 'users.id = calendar_events.professional_user_id', 'left')
            ->where('calendar_events.category', 'psicologico')
            ->where('calendar_events.starts_at >=', $weekStart)
            ->where('calendar_events.starts_at <=', $weekEnd)
            ->orderBy('calendar_events.starts_at', 'ASC');

        if (! $this->isAdmin() && $this->isPsychologist()) {
            $builder->where('calendar_events.professional_user_id', session('user.id'));
        }

        return $builder->get()->getResultArray();
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
