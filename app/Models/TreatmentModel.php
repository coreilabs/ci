<?php

namespace App\Models;

use CodeIgniter\Model;

class TreatmentModel extends Model
{
    protected $table = 'treatments';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'patient_id', 'guardian_id', 'admission_date', 'monthly_amount',
        'registration_amount', 'captor_name', 'status', 'notes',
    ];

    public function listWithPeople()
    {
        return $this->select('treatments.*, patients.name AS patient_name, guardians.name AS guardian_name')
            ->join('patients', 'patients.id = treatments.patient_id')
            ->join('guardians', 'guardians.id = treatments.guardian_id');
    }
}
