<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClinicalDemoSeeder extends Seeder
{
    private string $now;

    public function run()
    {
        $this->now = date('Y-m-d H:i:s');

        $this->clearTables();

        $roles = $this->seedRoles();
        $permissions = $this->seedPermissions();
        $this->seedRolePermissions($roles, $permissions);

        $users = $this->seedUsers($roles);
        $this->seedClinicalData($users);
    }

    private function clearTables(): void
    {
        $tables = [
            'audit_logs',
            'administrative_records',
            'discharges',
            'calendar_events',
            'treatment_professionals',
            'contracts',
            'documents',
            'document_templates',
            'financial_entries',
            'clinical_records',
            'admission_drafts',
            'treatments',
            'patients',
            'guardians',
            'role_permissions',
            'users',
            'permissions',
            'roles',
        ];

        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $table) {
            if ($this->db->tableExists($table)) {
                $this->db->table($table)->truncate();
            }
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    private function seedRoles(): array
    {
        $rows = [
            ['name' => 'Administração', 'slug' => 'admin'],
            ['name' => 'Psicólogo', 'slug' => 'psicologo'],
            ['name' => 'Psicóloga', 'slug' => 'psicologa'],
            ['name' => 'Terapeuta', 'slug' => 'terapeuta'],
            ['name' => 'Enfermeira', 'slug' => 'enfermeira'],
            ['name' => 'Médico', 'slug' => 'medico'],
        ];

        $this->db->table('roles')->insertBatch($rows);

        return $this->keyedRows('roles', 'slug');
    }

    private function seedPermissions(): array
    {
        $rows = [
            ['name' => 'Acessar dashboard', 'slug' => 'dashboard.view'],
            ['name' => 'Gerenciar usuários', 'slug' => 'users.manage'],
            ['name' => 'Visualizar usuários', 'slug' => 'users.view'],
            ['name' => 'Gerenciar admissões', 'slug' => 'admissions.manage'],
            ['name' => 'Visualizar tratamentos', 'slug' => 'treatments.view'],
            ['name' => 'Gerenciar prontuário', 'slug' => 'records.manage'],
            ['name' => 'Gerenciar financeiro', 'slug' => 'finance.manage'],
            ['name' => 'Gerenciar documentos', 'slug' => 'documents.manage'],
            ['name' => 'Gerenciar agenda', 'slug' => 'calendar.manage'],
            ['name' => 'Gerenciar administrativo clínico', 'slug' => 'clinical_admin.manage'],
            ['name' => 'Gerenciar admissão legado', 'slug' => 'admission.manage'],
            ['name' => 'Gerenciar agenda legado', 'slug' => 'schedule.manage'],
            ['name' => 'Gerenciar administrativo legado', 'slug' => 'administrative.manage'],
        ];

        $this->db->table('permissions')->insertBatch($rows);

        return $this->keyedRows('permissions', 'slug');
    }

    private function seedRolePermissions(array $roles, array $permissions): void
    {
        $rows = [];

        foreach ($permissions as $permission) {
            $rows[] = ['role_id' => $roles['admin']['id'], 'permission_id' => $permission['id']];
        }

        $clinicalPermissions = [
            'dashboard.view',
            'treatments.view',
            'records.manage',
            'calendar.manage',
        ];

        foreach (['psicologo', 'psicologa', 'terapeuta', 'enfermeira', 'medico'] as $roleSlug) {
            foreach ($clinicalPermissions as $permissionSlug) {
                $rows[] = [
                    'role_id' => $roles[$roleSlug]['id'],
                    'permission_id' => $permissions[$permissionSlug]['id'],
                ];
            }
        }

        $this->db->table('role_permissions')->insertBatch($rows);
    }

    private function seedUsers(array $roles): array
    {
        $password = password_hash('Dedomarco27###', PASSWORD_DEFAULT);
        $demoPassword = password_hash('Demo123###', PASSWORD_DEFAULT);

        $rows = [
            [
                'name' => 'Admin Master',
                'email' => 'coreilabs@protonmail.com',
                'password' => $password,
                'role_id' => $roles['admin']['id'],
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ],
            [
                'name' => 'Dra. Helena Duarte',
                'email' => 'psicologa1@demo.local',
                'password' => $demoPassword,
                'role_id' => $roles['psicologa']['id'],
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ],
            [
                'name' => 'Dr. Rafael Pires',
                'email' => 'psicologo2@demo.local',
                'password' => $demoPassword,
                'role_id' => $roles['psicologo']['id'],
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ],
            [
                'name' => 'Terapeuta Marcos Lima',
                'email' => 'terapeuta@demo.local',
                'password' => $demoPassword,
                'role_id' => $roles['terapeuta']['id'],
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ],
            [
                'name' => 'Enf. Camila Reis',
                'email' => 'enfermagem@demo.local',
                'password' => $demoPassword,
                'role_id' => $roles['enfermeira']['id'],
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ],
            [
                'name' => 'Dr. André Monteiro',
                'email' => 'medico@demo.local',
                'password' => $demoPassword,
                'role_id' => $roles['medico']['id'],
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ],
        ];

        $this->db->table('users')->insertBatch($rows);

        return $this->keyedRows('users', 'email');
    }

    private function seedClinicalData(array $users): void
    {
        $this->seedTemplates();

        $patients = [
            [
                'guardian' => ['name' => 'Marina Alves', 'cpf' => '12345678901', 'phone' => '(11) 98888-1001', 'email' => 'marina.demo@email.com', 'relationship' => 'Mãe'],
                'patient' => ['name' => 'Lucas Alves', 'cpf' => '11122233344', 'birth_date' => '1998-03-14', 'phone' => '(11) 97777-1001', 'address' => 'Rua das Acácias, 120'],
                'treatment' => ['admission_date' => date('Y-m-d', strtotime('-18 days')), 'monthly_amount' => 2800, 'registration_amount' => 700, 'stay_months' => 6, 'billing_day' => 10, 'captor_name' => 'Indicação familiar'],
                'psychologist' => 'psicologa1@demo.local',
            ],
            [
                'guardian' => ['name' => 'Paulo Mendes', 'cpf' => '22345678901', 'phone' => '(21) 98888-2002', 'email' => 'paulo.demo@email.com', 'relationship' => 'Pai'],
                'patient' => ['name' => 'Bruno Mendes', 'cpf' => '22233344455', 'birth_date' => '1994-08-09', 'phone' => '(21) 97777-2002', 'address' => 'Av. Central, 440'],
                'treatment' => ['admission_date' => date('Y-m-d', strtotime('-41 days')), 'monthly_amount' => 3100, 'registration_amount' => 900, 'stay_months' => 4, 'billing_day' => 5, 'captor_name' => 'Busca orgânica'],
                'psychologist' => 'psicologo2@demo.local',
            ],
            [
                'guardian' => ['name' => 'Sônia Carvalho', 'cpf' => '32345678901', 'phone' => '(31) 98888-3003', 'email' => 'sonia.demo@email.com', 'relationship' => 'Irmã'],
                'patient' => ['name' => 'Mateus Carvalho', 'cpf' => '33344455566', 'birth_date' => '1989-12-22', 'phone' => '(31) 97777-3003', 'address' => 'Rua Horizonte, 89'],
                'treatment' => ['admission_date' => date('Y-m-d', strtotime('-9 days')), 'monthly_amount' => 2950, 'registration_amount' => 650, 'stay_months' => 3, 'billing_day' => 12, 'captor_name' => 'Rede social'],
                'psychologist' => 'psicologa1@demo.local',
            ],
            [
                'guardian' => ['name' => 'Renata Rocha', 'cpf' => '42345678901', 'phone' => '(41) 98888-4004', 'email' => 'renata.demo@email.com', 'relationship' => 'Esposa'],
                'patient' => ['name' => 'Diego Rocha', 'cpf' => '44455566677', 'birth_date' => '1991-06-03', 'phone' => '(41) 97777-4004', 'address' => 'Alameda Norte, 55'],
                'treatment' => ['admission_date' => date('Y-m-d', strtotime('-3 days')), 'monthly_amount' => 3000, 'registration_amount' => 800, 'stay_months' => 5, 'billing_day' => 15, 'captor_name' => 'Encaminhamento médico'],
                'psychologist' => null,
            ],
        ];

        foreach ($patients as $index => $row) {
            $guardianId = $this->insertGuardian($row['guardian']);
            $patientId = $this->insertPatient($guardianId, $row['patient']);
            $treatmentId = $this->insertTreatment($patientId, $guardianId, $row['treatment']);

            $this->seedFinancialEntries($treatmentId, $row['treatment'], $index);
            $this->seedDocumentsAndContract($treatmentId, $row['patient']['name'], $row['guardian']['name'], $row['treatment']);
            $this->seedRecords($treatmentId, $users, $row['patient']['name']);

            if ($row['psychologist']) {
                $psychologistId = $users[$row['psychologist']]['id'];
                $nextAttendance = date('Y-m-d H:i:s', strtotime('+' . ($index + 1) . ' days 10:00'));
                $this->db->table('treatment_professionals')->insert([
                    'treatment_id' => $treatmentId,
                    'user_id' => $psychologistId,
                    'specialty' => 'psicologia',
                    'next_attendance_at' => $nextAttendance,
                    'created_by' => $users['coreilabs@protonmail.com']['id'],
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ]);
                $this->db->table('calendar_events')->insert([
                    'treatment_id' => $treatmentId,
                    'professional_user_id' => $psychologistId,
                    'source_type' => 'psychology_assignment',
                    'title' => 'Atendimento psicológico',
                    'category' => 'psicologico',
                    'starts_at' => $nextAttendance,
                    'notes' => 'Atendimento semanal definido pela divisão de pacientes.',
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ]);
            }
        }
    }

    private function insertGuardian(array $data): int
    {
        $this->db->table('guardians')->insert($data + [
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        return (int) $this->db->insertID();
    }

    private function insertPatient(int $guardianId, array $data): int
    {
        $this->db->table('patients')->insert($data + [
            'guardian_id' => $guardianId,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        return (int) $this->db->insertID();
    }

    private function insertTreatment(int $patientId, int $guardianId, array $data): int
    {
        $this->db->table('treatments')->insert($data + [
            'patient_id' => $patientId,
            'guardian_id' => $guardianId,
            'status' => 'active',
            'notes' => 'Paciente em acompanhamento multidisciplinar.',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        return (int) $this->db->insertID();
    }

    private function seedFinancialEntries(int $treatmentId, array $treatment, int $index): void
    {
        $entries = [[
            'treatment_id' => $treatmentId,
            'competence' => date('Y-m'),
            'type' => 'matricula',
            'description' => 'Matrícula inicial',
            'amount' => $treatment['registration_amount'],
            'due_date' => $treatment['admission_date'],
            'status' => $index === 0 ? 'paid' : 'open',
            'paid_at' => $index === 0 ? date('Y-m-d H:i:s', strtotime('-10 days')) : null,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]];

        for ($month = 0; $month < 3; $month++) {
            $competence = date('Y-m', strtotime('+' . $month . ' months'));
            $dueDate = $this->dueDateForCompetence($competence, (int) $treatment['billing_day']);
            $status = $month === 0 && $index < 2 ? 'paid' : 'open';

            $entries[] = [
                'treatment_id' => $treatmentId,
                'competence' => $competence,
                'type' => 'mensalidade',
                'description' => 'Mensalidade ' . ($month + 1) . '/3',
                'amount' => $treatment['monthly_amount'],
                'due_date' => $dueDate,
                'status' => $status,
                'paid_at' => $status === 'paid' ? date('Y-m-d H:i:s', strtotime($dueDate . ' 14:00')) : null,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        }

        $this->db->table('financial_entries')->insertBatch($entries);

        $financeRows = $this->db->table('financial_entries')
            ->where('treatment_id', $treatmentId)
            ->where('status', 'open')
            ->get()
            ->getResultArray();

        foreach ($financeRows as $entry) {
            $this->db->table('calendar_events')->insert([
                'treatment_id' => $treatmentId,
                'source_type' => 'finance',
                'source_id' => $entry['id'],
                'title' => 'Receber ' . $entry['description'],
                'category' => 'financeiro',
                'starts_at' => $entry['due_date'] . ' 09:00:00',
                'notes' => $entry['description'],
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ]);
        }
    }

    private function seedDocumentsAndContract(int $treatmentId, string $patientName, string $guardianName, array $treatment): void
    {
        $templates = $this->db->table('document_templates')->get()->getResultArray();
        foreach ($templates as $template) {
            if ((int) $template['is_required_admission'] !== 1) {
                continue;
            }

            $this->db->table('documents')->insert([
                'treatment_id' => $treatmentId,
                'template_id' => $template['id'],
                'category' => $template['category'],
                'name' => $template['name'],
                'body_snapshot' => $this->replaceDocumentTokens($template['body'], $patientName, $guardianName, $treatment),
                'version' => $template['version'],
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ]);
        }

        $this->db->table('contracts')->insert([
            'treatment_id' => $treatmentId,
            'title' => 'Contrato de prestação de serviços',
            'body_snapshot' => '<h1>Contrato</h1><p>Paciente ' . esc($patientName) . ' acompanhado pelo responsável ' . esc($guardianName) . '.</p>',
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);
    }

    private function seedRecords(int $treatmentId, array $users, string $patientName): void
    {
        $rows = [
            [
                'user_id' => $users['terapeuta@demo.local']['id'],
                'type' => 'terapeutico',
                'title' => 'Atendimento terapêutico individual',
                'content' => $patientName . ' participou de atendimento individual com boa adesão ao plano terapêutico.',
                'recorded_at' => date('Y-m-d H:i:s', strtotime('-2 days 09:30')),
            ],
            [
                'user_id' => $users['enfermagem@demo.local']['id'],
                'type' => 'enfermagem',
                'title' => 'Avaliação de enfermagem',
                'content' => 'Paciente orientado, sinais vitais dentro dos parâmetros observados.',
                'sae_collection' => 'Coleta inicial sem intercorrências.',
                'sae_diagnosis' => 'Risco de ansiedade relacionado ao processo de adaptação.',
                'sae_planning' => 'Monitorar rotina, sono e hidratação.',
                'sae_execution' => 'Acompanhamento diário e orientação de autocuidado.',
                'sae_evaluation' => 'Evolução estável.',
                'recorded_at' => date('Y-m-d H:i:s', strtotime('-1 day 08:00')),
            ],
            [
                'user_id' => $users['medico@demo.local']['id'],
                'type' => 'medico',
                'title' => 'Consulta médica',
                'content' => 'Revisão clínica realizada, sem sinais de abstinência grave no momento.',
                'recorded_at' => date('Y-m-d H:i:s', strtotime('-1 day 15:00')),
            ],
        ];

        foreach ($rows as $row) {
            $this->db->table('clinical_records')->insert($row + [
                'treatment_id' => $treatmentId,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ]);
        }
    }

    private function seedTemplates(): void
    {
        $templates = [
            ['Contrato padrão de admissão', 'contrato', '<h1>Contrato de Prestação de Serviços Terapêuticos</h1><p><b>Paciente:</b> {{paciente}}</p><p><b>Responsável:</b> {{responsavel}}</p><p><b>Data de admissão:</b> {{admissao}}</p><p><b>Mensalidade:</b> {{mensalidade}}</p>', 0],
            ['Termo voluntário', 'juridico', '<h1>Termo Voluntário</h1><p>Eu, {{paciente}}, declaro ciência e voluntariedade no processo terapêutico.</p>', 1],
            ['Termo de laborterapia individual e familiar', 'juridico', '<h1>Termo de Laborterapia</h1><p>Paciente {{paciente}} e responsável {{responsavel}} declaram ciência das atividades terapêuticas.</p>', 1],
            ['Autorização de imagem e regimento interno', 'juridico', '<h1>Autorização de Imagem e Regimento</h1><p>Paciente {{paciente}} e responsável {{responsavel}} declaram ciência do regimento interno.</p>', 1],
            ['Termo de responsabilidade de saída', 'juridico', '<h1>Termo de Responsabilidade de Saída</h1><p>Responsável {{responsavel}} declara ciência das regras de saída.</p>', 1],
            ['Termo de saída terapêutica', 'juridico', '<h1>Termo de Saída Terapêutica</h1><p>Paciente {{paciente}} sai para finalidade autorizada, com retorno previsto conforme registro.</p>', 1],
            ['Laudo sanitário padrão', 'sanitario', '<h1>Documento Sanitário</h1><p>Modelo editável para registros de vigilância sanitária.</p>', 0],
        ];

        foreach ($templates as [$name, $category, $body, $required]) {
            $this->db->table('document_templates')->insert([
                'name' => $name,
                'category' => $category,
                'body' => $body,
                'version' => 1,
                'is_required_admission' => $required,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ]);
        }
    }

    private function replaceDocumentTokens(string $body, string $patientName, string $guardianName, array $treatment): string
    {
        return str_replace(
            ['{{paciente}}', '{{responsavel}}', '{{admissao}}', '{{mensalidade}}'],
            [$patientName, $guardianName, $treatment['admission_date'], number_format((float) $treatment['monthly_amount'], 2, ',', '.')],
            $body
        );
    }

    private function dueDateForCompetence(string $competence, int $billingDay): string
    {
        $lastDay = (int) date('t', strtotime($competence . '-01'));
        $day = min($billingDay, $lastDay);

        return $competence . '-' . str_pad((string) $day, 2, '0', STR_PAD_LEFT);
    }

    private function keyedRows(string $table, string $key): array
    {
        $rows = $this->db->table($table)->get()->getResultArray();
        $keyed = [];

        foreach ($rows as $row) {
            $keyed[$row[$key]] = $row;
        }

        return $keyed;
    }
}
