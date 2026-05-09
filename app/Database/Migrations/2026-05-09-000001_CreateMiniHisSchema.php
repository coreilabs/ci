<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMiniHisSchema extends Migration
{
    public function up()
    {
        $this->guardians();
        $this->patients();
        $this->treatments();
        $this->admissionDrafts();
        $this->clinicalRecords();
        $this->financialEntries();
        $this->documentTemplates();
        $this->documents();
        $this->contracts();
        $this->calendarEvents();
        $this->discharges();
        $this->administrativeRecords();
        $this->auditLogs();
        $this->seedPermissions();
        $this->seedTemplates();
    }

    public function down()
    {
        foreach ([
            'audit_logs',
            'administrative_records',
            'discharges',
            'calendar_events',
            'contracts',
            'documents',
            'document_templates',
            'financial_entries',
            'clinical_records',
            'admission_drafts',
            'treatments',
            'patients',
            'guardians',
        ] as $table) {
            $this->forge->dropTable($table, true);
        }
    }

    private function guardians(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 160],
            'cpf' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            'relationship' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'address' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('guardians');
    }

    private function patients(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'guardian_id' => ['type' => 'INT', 'unsigned' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 160],
            'cpf' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'birth_date' => ['type' => 'DATE', 'null' => true],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'address' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('guardian_id');
        $this->forge->createTable('patients');
    }

    private function treatments(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'patient_id' => ['type' => 'INT', 'unsigned' => true],
            'guardian_id' => ['type' => 'INT', 'unsigned' => true],
            'admission_date' => ['type' => 'DATE'],
            'monthly_amount' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'registration_amount' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'captor_name' => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'active'],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['patient_id', 'status']);
        $this->forge->createTable('treatments');
    }

    private function admissionDrafts(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'step' => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'responsavel'],
            'payload' => ['type' => 'TEXT', 'null' => true],
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'finished_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('admission_drafts');
    }

    private function clinicalRecords(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'treatment_id' => ['type' => 'INT', 'unsigned' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'type' => ['type' => 'VARCHAR', 'constraint' => 40],
            'title' => ['type' => 'VARCHAR', 'constraint' => 180],
            'content' => ['type' => 'TEXT'],
            'sae_collection' => ['type' => 'TEXT', 'null' => true],
            'sae_diagnosis' => ['type' => 'TEXT', 'null' => true],
            'sae_planning' => ['type' => 'TEXT', 'null' => true],
            'sae_execution' => ['type' => 'TEXT', 'null' => true],
            'sae_evaluation' => ['type' => 'TEXT', 'null' => true],
            'recorded_at' => ['type' => 'DATETIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['treatment_id', 'recorded_at']);
        $this->forge->createTable('clinical_records');
    }

    private function financialEntries(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'treatment_id' => ['type' => 'INT', 'unsigned' => true],
            'competence' => ['type' => 'CHAR', 'constraint' => 7],
            'type' => ['type' => 'VARCHAR', 'constraint' => 40],
            'description' => ['type' => 'VARCHAR', 'constraint' => 180],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'due_date' => ['type' => 'DATE', 'null' => true],
            'paid_at' => ['type' => 'DATETIME', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'open'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['treatment_id', 'competence', 'type']);
        $this->forge->createTable('financial_entries');
    }

    private function documentTemplates(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 180],
            'category' => ['type' => 'VARCHAR', 'constraint' => 60],
            'body' => ['type' => 'TEXT'],
            'version' => ['type' => 'INT', 'default' => 1],
            'is_required_admission' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('document_templates');
    }

    private function documents(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'treatment_id' => ['type' => 'INT', 'unsigned' => true],
            'template_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'category' => ['type' => 'VARCHAR', 'constraint' => 60],
            'name' => ['type' => 'VARCHAR', 'constraint' => 180],
            'body_snapshot' => ['type' => 'TEXT', 'null' => true],
            'file_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'signed_file_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'version' => ['type' => 'INT', 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('treatment_id');
        $this->forge->createTable('documents');
    }

    private function contracts(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'treatment_id' => ['type' => 'INT', 'unsigned' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 180],
            'body_snapshot' => ['type' => 'TEXT'],
            'pdf_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'signed_file_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('treatment_id');
        $this->forge->createTable('contracts');
    }

    private function calendarEvents(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'treatment_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'source_type' => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'manual'],
            'source_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 180],
            'category' => ['type' => 'VARCHAR', 'constraint' => 40],
            'starts_at' => ['type' => 'DATETIME'],
            'ends_at' => ['type' => 'DATETIME', 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['treatment_id', 'starts_at']);
        $this->forge->createTable('calendar_events');
    }

    private function discharges(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'treatment_id' => ['type' => 'INT', 'unsigned' => true],
            'type' => ['type' => 'VARCHAR', 'constraint' => 40],
            'summary' => ['type' => 'TEXT'],
            'discharged_at' => ['type' => 'DATE'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('treatment_id');
        $this->forge->createTable('discharges');
    }

    private function administrativeRecords(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'category' => ['type' => 'VARCHAR', 'constraint' => 80],
            'name' => ['type' => 'VARCHAR', 'constraint' => 180],
            'due_date' => ['type' => 'DATE', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'active'],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('administrative_records');
    }

    private function auditLogs(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'treatment_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'action' => ['type' => 'VARCHAR', 'constraint' => 120],
            'payload' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('treatment_id');
        $this->forge->createTable('audit_logs');
    }

    private function seedPermissions(): void
    {
        $rows = [
            ['name' => 'Gerenciar admissoes', 'slug' => 'admissions.manage'],
            ['name' => 'Visualizar tratamentos', 'slug' => 'treatments.view'],
            ['name' => 'Gerenciar prontuario', 'slug' => 'records.manage'],
            ['name' => 'Gerenciar financeiro', 'slug' => 'finance.manage'],
            ['name' => 'Gerenciar documentos', 'slug' => 'documents.manage'],
            ['name' => 'Gerenciar agenda', 'slug' => 'calendar.manage'],
            ['name' => 'Gerenciar administrativo clinico', 'slug' => 'clinical_admin.manage'],
        ];

        foreach ($rows as $row) {
            if ($this->db->table('permissions')->where('slug', $row['slug'])->countAllResults() === 0) {
                $this->db->table('permissions')->insert($row);
            }
        }

        $admin = $this->db->table('roles')->where('slug', 'admin')->get()->getRowArray();
        if (! $admin) {
            return;
        }

        foreach ($this->db->table('permissions')->get()->getResultArray() as $permission) {
            $exists = $this->db->table('role_permissions')
                ->where('role_id', $admin['id'])
                ->where('permission_id', $permission['id'])
                ->countAllResults();

            if ($exists === 0) {
                $this->db->table('role_permissions')->insert([
                    'role_id' => $admin['id'],
                    'permission_id' => $permission['id'],
                ]);
            }
        }
    }

    private function seedTemplates(): void
    {
        $contract = '<h1>Contrato de Prestacao de Servicos Terapeuticos</h1><p><b>Paciente:</b> {{paciente}}</p><p><b>Responsavel:</b> {{responsavel}}</p><p><b>Data de admissao:</b> {{admissao}}</p><p><b>Mensalidade:</b> {{mensalidade}}</p><p>A clinica prestara acompanhamento multidisciplinar em regime de longa permanencia, observando o plano terapeutico e as normas internas.</p><p>As partes declaram ciencia das responsabilidades financeiras, regras de convivencia, autorizacoes e documentos complementares emitidos na admissao.</p>';

        $templates = [
            ['Contrato padrao de admissao', 'contrato', $contract, 0],
            ['Termo voluntario', 'juridico', '<h1>Termo Voluntario</h1><p>Eu, {{paciente}}, declaro ciencia e voluntariedade no processo terapeutico.</p>', 1],
            ['Termo de laborterapia individual e familiar', 'juridico', '<h1>Termo de Laborterapia</h1><p>Paciente {{paciente}} e responsavel {{responsavel}} declaram ciencia das atividades terapeuticas.</p>', 1],
            ['Autorizacao de imagem e regimento interno', 'juridico', '<h1>Autorizacao de Imagem e Regimento</h1><p>Paciente {{paciente}} e responsavel {{responsavel}} declaram ciencia do regimento interno.</p>', 1],
            ['Termo de responsabilidade de saida', 'juridico', '<h1>Termo de Responsabilidade de Saida</h1><p>Responsavel {{responsavel}} declara ciencia das regras de saida.</p>', 1],
            ['Termo de saida terapeutica', 'juridico', '<h1>Termo de Saida Terapeutica</h1><p>Paciente {{paciente}} sai para finalidade autorizada, com retorno previsto conforme registro.</p>', 1],
            ['Laudo sanitario padrao', 'sanitario', '<h1>Documento Sanitario</h1><p>Modelo editavel para registros de vigilancia sanitaria.</p>', 0],
        ];

        foreach ($templates as [$name, $category, $body, $required]) {
            if ($this->db->table('document_templates')->where('name', $name)->countAllResults() === 0) {
                $this->db->table('document_templates')->insert([
                    'name' => $name,
                    'category' => $category,
                    'body' => $body,
                    'version' => 1,
                    'is_required_admission' => $required,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
