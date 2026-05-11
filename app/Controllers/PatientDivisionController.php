<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\CalendarEventModel;
use App\Models\TreatmentProfessionalModel;

class PatientDivisionController extends BaseController
{
    private const SPECIALTY = 'psicologia';

    public function index()
    {
        $db = db_connect();

        $patients = $db->table('treatments')
            ->select('treatments.id, treatments.admission_date, patients.name AS patient_name, guardians.name AS guardian_name, tp.user_id AS psychologist_id, tp.next_attendance_at, users.name AS psychologist_name')
            ->join('patients', 'patients.id = treatments.patient_id')
            ->join('guardians', 'guardians.id = treatments.guardian_id')
            ->join('treatment_professionals tp', 'tp.treatment_id = treatments.id AND tp.specialty = "psicologia"', 'left')
            ->join('users', 'users.id = tp.user_id', 'left')
            ->where('treatments.status', 'active')
            ->orderBy('patients.name', 'ASC')
            ->get()
            ->getResultArray();

        return view('patient_division/index', [
            'patients' => $patients,
            'psychologists' => $this->psychologists(),
            'isAdmin' => $this->isAdmin(),
            'canManageDivision' => $this->isAdmin() || $this->isPsychologist(),
            'currentUserId' => (int) session('user.id'),
        ]);
    }

    public function assign(int $treatmentId)
    {
        $professionalId = $this->professionalIdFromRequest();
        if (! $professionalId) {
            return redirect()->to('divisao-pacientes')->with('error', 'Selecione um psicólogo ou acesse com um usuário de psicologia.');
        }

        $assignmentModel = new TreatmentProfessionalModel();
        $existing = $assignmentModel
            ->where('treatment_id', $treatmentId)
            ->where('specialty', self::SPECIALTY)
            ->first();

        if ($existing && (int) $existing['user_id'] !== $professionalId && ! $this->isAdmin()) {
            return redirect()->to('divisao-pacientes')->with('error', 'Paciente já vinculado a outro psicólogo.');
        }

        $nextAttendance = $this->nextAttendance();
        $data = [
            'treatment_id' => $treatmentId,
            'user_id' => $professionalId,
            'specialty' => self::SPECIALTY,
            'next_attendance_at' => $nextAttendance,
            'created_by' => session('user.id'),
        ];

        if ($existing) {
            if ((int) $existing['user_id'] !== $professionalId) {
                $this->clearPsychologicalCalendarEvent($treatmentId, (int) $existing['user_id']);
            }

            $assignmentModel->update($existing['id'], $data);
        } else {
            $assignmentModel->insert($data);
        }

        if ($nextAttendance) {
            $this->syncPsychologicalCalendarEvent($treatmentId, $professionalId, $nextAttendance);
        } else {
            $this->clearPsychologicalCalendarEvent($treatmentId, $professionalId);
        }

        (new AuditLogModel())->write($treatmentId, 'psychology.assigned', [
            'professional_user_id' => $professionalId,
            'next_attendance_at' => $nextAttendance,
        ]);

        return redirect()->to('divisao-pacientes')->with('success', 'Paciente incluído na lista de atendimento.');
    }

    public function release(int $treatmentId)
    {
        $assignmentModel = new TreatmentProfessionalModel();
        $assignment = $assignmentModel
            ->where('treatment_id', $treatmentId)
            ->where('specialty', self::SPECIALTY)
            ->first();

        if (! $assignment) {
            return redirect()->to('divisao-pacientes')->with('error', 'Vínculo não encontrado.');
        }

        if (! $this->isAdmin() && (int) $assignment['user_id'] !== (int) session('user.id')) {
            return redirect()->to('divisao-pacientes')->with('error', 'Apenas o responsável ou o administrador pode liberar este paciente.');
        }

        $assignmentModel->delete($assignment['id']);
        (new CalendarEventModel())
            ->where('treatment_id', $treatmentId)
            ->where('professional_user_id', $assignment['user_id'])
            ->where('source_type', 'psychology_assignment')
            ->delete();

        (new AuditLogModel())->write($treatmentId, 'psychology.released', [
            'professional_user_id' => (int) $assignment['user_id'],
        ]);

        return redirect()->to('divisao-pacientes')->with('success', 'Paciente liberado para nova divisão.');
    }

    private function psychologists(): array
    {
        return db_connect()->table('users')
            ->select('users.id, users.name, roles.name AS role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->groupStart()
                ->like('roles.slug', 'psic')
                ->orLike('roles.name', 'psic')
            ->groupEnd()
            ->orderBy('users.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function professionalIdFromRequest(): ?int
    {
        if (! $this->isAdmin()) {
            return $this->isPsychologist() ? (int) session('user.id') : null;
        }

        $professionalId = (int) $this->request->getPost('professional_user_id');
        return $professionalId > 0 ? $professionalId : null;
    }

    private function nextAttendance(): ?string
    {
        helper('format');

        return datetime_local_to_sql($this->request->getPost('next_attendance_at'));
    }

    private function syncPsychologicalCalendarEvent(int $treatmentId, int $professionalId, string $startsAt): void
    {
        $events = new CalendarEventModel();
        $event = $events
            ->where('treatment_id', $treatmentId)
            ->where('professional_user_id', $professionalId)
            ->where('source_type', 'psychology_assignment')
            ->first();

        $data = [
            'treatment_id' => $treatmentId,
            'professional_user_id' => $professionalId,
            'source_type' => 'psychology_assignment',
            'title' => 'Atendimento psicológico',
            'category' => 'psicologico',
            'starts_at' => $startsAt,
        ];

        if ($event) {
            $events->update($event['id'], $data);
            return;
        }

        $events->insert($data);
    }

    private function clearPsychologicalCalendarEvent(int $treatmentId, int $professionalId): void
    {
        (new CalendarEventModel())
            ->where('treatment_id', $treatmentId)
            ->where('professional_user_id', $professionalId)
            ->where('source_type', 'psychology_assignment')
            ->delete();
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
