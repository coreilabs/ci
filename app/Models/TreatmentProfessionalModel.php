<?php

namespace App\Models;

use CodeIgniter\Model;

class TreatmentProfessionalModel extends Model
{
    protected $table = 'treatment_professionals';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'treatment_id', 'user_id', 'specialty', 'next_attendance_at', 'created_by',
    ];

    public function psychologicalAssignments()
    {
        return $this->select('treatment_professionals.*, users.name AS professional_name')
            ->join('users', 'users.id = treatment_professionals.user_id')
            ->where('treatment_professionals.specialty', 'psicologia');
    }
}
