<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCareCoordinationFeatures extends Migration
{
    public function up()
    {
        $this->ensureAuditColumns();
        $this->ensureFinancialColumns();
        $this->incidentTables();
        $this->notificationTables();
        $this->settingsTables();
        $this->familyPortalTables();
        $this->seedCoordinatorRoleAndPermissions();
        $this->seedEditableSettings();
        $this->seedPsychologyTemplates();
    }

    public function down()
    {
        foreach ([
            'family_portal_files',
            'family_portal_accesses',
            'app_settings',
            'user_notifications',
            'notifications',
            'incidents',
            'incident_types',
        ] as $table) {
            $this->forge->dropTable($table, true);
        }
    }

    private function ensureAuditColumns(): void
    {
        foreach (['clinical_records', 'financial_entries', 'documents', 'contracts', 'calendar_events', 'treatments'] as $table) {
            if (! $this->db->fieldExists('created_by', $table)) {
                $this->forge->addColumn($table, [
                    'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                    'updated_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                ]);
            }
        }
    }

    private function ensureFinancialColumns(): void
    {
        if (! $this->db->fieldExists('paid_amount', 'financial_entries')) {
            $this->forge->addColumn('financial_entries', [
                'paid_amount' => [
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'null' => true,
                    'after' => 'paid_at',
                ],
            ]);
        }
    }

    private function incidentTables(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'description' => ['type' => 'TEXT', 'null' => true],
            'severity' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'media'],
            'active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('incident_types', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'treatment_id' => ['type' => 'INT', 'unsigned' => true],
            'incident_type_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 180],
            'description' => ['type' => 'TEXT'],
            'occurred_at' => ['type' => 'DATETIME'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'open'],
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['treatment_id', 'occurred_at']);
        $this->forge->createTable('incidents', true);
    }

    private function notificationTables(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'type' => ['type' => 'VARCHAR', 'constraint' => 60],
            'title' => ['type' => 'VARCHAR', 'constraint' => 180],
            'body' => ['type' => 'TEXT', 'null' => true],
            'treatment_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('notifications', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'notification_id' => ['type' => 'INT', 'unsigned' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
            'read_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['notification_id', 'user_id']);
        $this->forge->createTable('user_notifications', true);
    }

    private function settingsTables(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'key' => ['type' => 'VARCHAR', 'constraint' => 120],
            'value' => ['type' => 'TEXT', 'null' => true],
            'group' => ['type' => 'VARCHAR', 'constraint' => 60, 'default' => 'geral'],
            'label' => ['type' => 'VARCHAR', 'constraint' => 180],
            'type' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'text'],
            'updated_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('key');
        $this->forge->createTable('app_settings', true);
    }

    private function familyPortalTables(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'treatment_id' => ['type' => 'INT', 'unsigned' => true],
            'guardian_id' => ['type' => 'INT', 'unsigned' => true],
            'token' => ['type' => 'VARCHAR', 'constraint' => 80],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('token');
        $this->forge->createTable('family_portal_accesses', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'portal_access_id' => ['type' => 'INT', 'unsigned' => true],
            'file_type' => ['type' => 'VARCHAR', 'constraint' => 30],
            'file_id' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('portal_access_id');
        $this->forge->createTable('family_portal_files', true);
    }

    private function seedCoordinatorRoleAndPermissions(): void
    {
        $roles = $this->db->table('roles');
        if ($roles->where('slug', 'coordenador')->countAllResults() === 0) {
            $roles->insert(['name' => 'Coordenador', 'slug' => 'coordenador']);
        }

        $permissions = [
            ['name' => 'Painel do coordenador', 'slug' => 'coordinator.view'],
            ['name' => 'Gerenciar ocorrencias', 'slug' => 'incidents.manage'],
            ['name' => 'Gerenciar configuracoes', 'slug' => 'settings.manage'],
            ['name' => 'Gerenciar permissoes', 'slug' => 'permissions.manage'],
            ['name' => 'Editar qualquer registro', 'slug' => 'records.edit_all'],
        ];

        foreach ($permissions as $permission) {
            if ($this->db->table('permissions')->where('slug', $permission['slug'])->countAllResults() === 0) {
                $this->db->table('permissions')->insert($permission);
            }
        }

        $admin = $this->db->table('roles')->where('slug', 'admin')->get()->getRowArray();
        $coordinator = $this->db->table('roles')->where('slug', 'coordenador')->get()->getRowArray();
        $coordinatorSlugs = ['dashboard.view', 'coordinator.view', 'incidents.manage', 'treatments.view', 'calendar.manage'];

        foreach ($this->db->table('permissions')->get()->getResultArray() as $permission) {
            if ($admin) {
                $this->attachPermission((int) $admin['id'], (int) $permission['id']);
            }
            if ($coordinator && in_array($permission['slug'], $coordinatorSlugs, true)) {
                $this->attachPermission((int) $coordinator['id'], (int) $permission['id']);
            }
        }
    }

    private function attachPermission(int $roleId, int $permissionId): void
    {
        $exists = $this->db->table('role_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->countAllResults();

        if ($exists === 0) {
            $this->db->table('role_permissions')->insert([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ]);
        }
    }

    private function seedEditableSettings(): void
    {
        $settings = [
            ['psychology_sla_days', '15', 'atendimento', 'Prazo maximo para atendimento psicologico', 'number'],
            ['family_portal_enabled', '1', 'familia', 'Habilitar area da familia', 'boolean'],
            ['family_portal_password_length', '8', 'familia', 'Tamanho da senha unica da familia', 'number'],
            ['whatsapp_daily_confirmation_enabled', '0', 'notificacoes', 'Confirmacao diaria por WhatsApp', 'boolean'],
            ['whatsapp_billing_template', 'Ola, lembramos que existe uma mensalidade em aberto de {{acolhido}} com vencimento em {{vencimento}}.', 'notificacoes', 'Template WhatsApp cobranca', 'textarea'],
            ['pdf_header_image', '', 'documentos', 'Imagem de cabecalho dos PDFs', 'text'],
            ['pdf_footer_image', '', 'documentos', 'Imagem de rodape dos PDFs', 'text'],
            ['push_provider', 'browser', 'notificacoes', 'Provedor de alerta push', 'text'],
            ['pushalert_site_id', '', 'notificacoes', 'PushAlert Site ID', 'text'],
        ];

        foreach ($settings as [$key, $value, $group, $label, $type]) {
            if ($this->db->table('app_settings')->where('key', $key)->countAllResults() === 0) {
                $this->db->table('app_settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'group' => $group,
                    'label' => $label,
                    'type' => $type,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        foreach (['Desrespeito', 'Agressao verbal', 'Descumprimento de rotina', 'Elogio / evolucao positiva'] as $name) {
            if ($this->db->table('incident_types')->where('name', $name)->countAllResults() === 0) {
                $this->db->table('incident_types')->insert([
                    'name' => $name,
                    'severity' => $name === 'Elogio / evolucao positiva' ? 'baixa' : 'media',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    private function seedPsychologyTemplates(): void
    {
        $templates = [
            ['Termo psicologico', 'psicologico', '<h1>Termo Psicologico</h1><p>Acolhido {{paciente}} e responsavel {{responsavel}} declaram ciencia do acompanhamento psicologico e das regras de sigilo e registro.</p>', 1],
            ['Orientacoes e-Psi', 'psicologico', '<h1>Orientacoes e-Psi</h1><p>Modelo editavel para orientacoes, consentimento e registros relacionados a atendimentos remotos quando aplicavel.</p>', 0],
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
