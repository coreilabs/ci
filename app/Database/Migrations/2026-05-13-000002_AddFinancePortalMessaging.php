<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFinancePortalMessaging extends Migration
{
    public function up()
    {
        $this->familyPortalColumns();
        $this->payables();
        $this->messageLogs();
        $this->seedSettings();
        $this->seedPermissions();
    }

    public function down()
    {
        $this->forge->dropTable('whatsapp_logs', true);
        $this->forge->dropTable('payable_entries', true);

        foreach (['access_url', 'initial_password', 'last_sent_at', 'last_sent_to'] as $column) {
            if ($this->db->fieldExists($column, 'family_portal_accesses')) {
                $this->forge->dropColumn('family_portal_accesses', $column);
            }
        }
    }

    private function familyPortalColumns(): void
    {
        $columns = [];
        if (! $this->db->fieldExists('access_url', 'family_portal_accesses')) {
            $columns['access_url'] = ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true];
        }
        if (! $this->db->fieldExists('initial_password', 'family_portal_accesses')) {
            $columns['initial_password'] = ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true];
        }
        if (! $this->db->fieldExists('last_sent_at', 'family_portal_accesses')) {
            $columns['last_sent_at'] = ['type' => 'DATETIME', 'null' => true];
        }
        if (! $this->db->fieldExists('last_sent_to', 'family_portal_accesses')) {
            $columns['last_sent_to'] = ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true];
        }

        if ($columns) {
            $this->forge->addColumn('family_portal_accesses', $columns);
        }
    }

    private function payables(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'treatment_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'competence' => ['type' => 'CHAR', 'constraint' => 7, 'null' => true],
            'category' => ['type' => 'VARCHAR', 'constraint' => 60],
            'payee_name' => ['type' => 'VARCHAR', 'constraint' => 160],
            'description' => ['type' => 'VARCHAR', 'constraint' => 180],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'due_date' => ['type' => 'DATE', 'null' => true],
            'paid_at' => ['type' => 'DATETIME', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'open'],
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['status', 'due_date']);
        $this->forge->createTable('payable_entries', true);
    }

    private function messageLogs(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'channel' => ['type' => 'VARCHAR', 'constraint' => 30],
            'recipient' => ['type' => 'VARCHAR', 'constraint' => 80],
            'message' => ['type' => 'TEXT'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30],
            'provider_response' => ['type' => 'TEXT', 'null' => true],
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('whatsapp_logs', true);
    }

    private function seedSettings(): void
    {
        $settings = [
            ['whatsapp_enabled', '0', 'notificacoes', 'Habilitar WhatsApp oficial', 'boolean'],
            ['whatsapp_graph_version', 'v20.0', 'notificacoes', 'Versao da Graph API', 'text'],
            ['whatsapp_phone_number_id', '', 'notificacoes', 'Phone Number ID WhatsApp', 'text'],
            ['whatsapp_access_token', '', 'notificacoes', 'Token permanente WhatsApp', 'textarea'],
            ['whatsapp_family_portal_template', 'Ola, segue o acesso da Area da Familia do acolhido {{acolhido}}: {{link}} Senha: {{senha}}', 'notificacoes', 'Mensagem do portal familiar', 'textarea'],
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
    }

    private function seedPermissions(): void
    {
        $rows = [
            ['name' => 'Gerenciar contas a pagar', 'slug' => 'payables.manage'],
            ['name' => 'Enviar WhatsApp', 'slug' => 'whatsapp.send'],
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
}
