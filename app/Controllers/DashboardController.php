<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $today = date('Y-m-d');
        $weekStart = date('Y-m-d 00:00:00', strtotime('monday this week'));
        $weekEnd = date('Y-m-d 23:59:59', strtotime('sunday this week'));

        $data = [
            'activeTreatments' => $db->table('treatments')->where('status', 'active')->countAllResults(),
            'totalPatients' => $db->table('patients')->countAllResults(),
            'openCharges' => $db->table('financial_entries')->where('status', 'open')->countAllResults(),
            'lateCharges' => $db->table('financial_entries')
                ->where('status', 'open')
                ->where('due_date <', $today)
                ->countAllResults(),
            'monthRevenue' => (float) ($db->table('financial_entries')
                ->selectSum('amount')
                ->where('status', 'paid')
                ->where('competence', date('Y-m'))
                ->get()
                ->getRow('amount') ?? 0),
            'openAmount' => (float) ($db->table('financial_entries')
                ->selectSum('amount')
                ->where('status', 'open')
                ->get()
                ->getRow('amount') ?? 0),
            'pendingDocuments' => $db->table('documents')
                ->where('signed_file_path', null)
                ->countAllResults(),
            'unassignedPsychology' => $db->table('treatments')
                ->join('treatment_professionals tp', 'tp.treatment_id = treatments.id AND tp.specialty = "psicologia"', 'left')
                ->where('treatments.status', 'active')
                ->where('tp.id', null)
                ->countAllResults(),
            'todayEvents' => $this->eventsBetween($today . ' 00:00:00', $today . ' 23:59:59'),
            'weeklyPsychology' => $this->psychologyEventsBetween($weekStart, $weekEnd),
            'psychologyWorkload' => $this->psychologyWorkload(),
            'recentRecords' => $this->recentRecords(),
            'upcomingCharges' => $this->upcomingCharges(),
        ];

        return view('dashboard/index', $data);
    }

    private function eventsBetween(string $start, string $end): array
    {
        return db_connect()->table('calendar_events')
            ->select('calendar_events.*, patients.name AS patient_name, users.name AS professional_name')
            ->join('treatments', 'treatments.id = calendar_events.treatment_id', 'left')
            ->join('patients', 'patients.id = treatments.patient_id', 'left')
            ->join('users', 'users.id = calendar_events.professional_user_id', 'left')
            ->where('calendar_events.starts_at >=', $start)
            ->where('calendar_events.starts_at <=', $end)
            ->orderBy('calendar_events.starts_at', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function psychologyEventsBetween(string $start, string $end): array
    {
        return db_connect()->table('calendar_events')
            ->select('calendar_events.*, patients.name AS patient_name, users.name AS professional_name')
            ->join('treatments', 'treatments.id = calendar_events.treatment_id', 'left')
            ->join('patients', 'patients.id = treatments.patient_id', 'left')
            ->join('users', 'users.id = calendar_events.professional_user_id', 'left')
            ->where('calendar_events.category', 'psicologico')
            ->where('calendar_events.starts_at >=', $start)
            ->where('calendar_events.starts_at <=', $end)
            ->orderBy('calendar_events.starts_at', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function psychologyWorkload(): array
    {
        return db_connect()->table('treatment_professionals tp')
            ->select('users.name AS professional_name, COUNT(tp.id) AS total')
            ->join('users', 'users.id = tp.user_id')
            ->where('tp.specialty', 'psicologia')
            ->groupBy('users.id, users.name')
            ->orderBy('users.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function recentRecords(): array
    {
        return db_connect()->table('clinical_records')
            ->select('clinical_records.*, patients.name AS patient_name, users.name AS professional_name')
            ->join('treatments', 'treatments.id = clinical_records.treatment_id')
            ->join('patients', 'patients.id = treatments.patient_id')
            ->join('users', 'users.id = clinical_records.user_id', 'left')
            ->orderBy('clinical_records.recorded_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
    }

    private function upcomingCharges(): array
    {
        return db_connect()->table('financial_entries')
            ->select('financial_entries.*, patients.name AS patient_name')
            ->join('treatments', 'treatments.id = financial_entries.treatment_id')
            ->join('patients', 'patients.id = treatments.patient_id')
            ->where('financial_entries.status', 'open')
            ->orderBy('financial_entries.due_date', 'ASC')
            ->limit(5)
            ->get()
            ->getResultArray();
    }
}
