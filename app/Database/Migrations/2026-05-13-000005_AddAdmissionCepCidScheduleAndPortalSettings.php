<?php

namespace App\Database\Migrations;

use App\Libraries\CurrentContractTemplate;
use CodeIgniter\Database\Migration;

class AddAdmissionCepCidScheduleAndPortalSettings extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('zip_code', 'guardians')) {
            $this->forge->addColumn('guardians', [
                'zip_code' => ['type' => 'VARCHAR', 'constraint' => 12, 'null' => true, 'after' => 'address'],
            ]);
        }

        if (! $this->db->fieldExists('zip_code', 'patients')) {
            $this->forge->addColumn('patients', [
                'zip_code' => ['type' => 'VARCHAR', 'constraint' => 12, 'null' => true, 'after' => 'address'],
            ]);
        }

        if (! $this->db->fieldExists('cid_code', 'treatments')) {
            $this->forge->addColumn('treatments', [
                'cid_code' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'billing_day'],
            ]);
        }

        if (! $this->db->tableExists('schedule_items')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'day_label' => ['type' => 'VARCHAR', 'constraint' => 80],
                'starts_at' => ['type' => 'TIME', 'null' => true],
                'ends_at' => ['type' => 'TIME', 'null' => true],
                'activity' => ['type' => 'VARCHAR', 'constraint' => 180],
                'audience' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'notes' => ['type' => 'TEXT', 'null' => true],
                'active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'sort_order' => ['type' => 'INT', 'default' => 0],
                'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'updated_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['active', 'sort_order']);
            $this->forge->createTable('schedule_items');
        }

        $this->seedSettings();
        $this->seedSchedulePermission();
        $this->seedAdendoTemplate();
        $this->updateCurrentContract();
    }

    public function down()
    {
        $this->forge->dropTable('schedule_items', true);
        $this->db->table('document_templates')->where('name', 'Adendo Contratual')->delete();

        foreach ([
            'admission_max_contract_months',
            'family_portal_show_contracts',
            'family_portal_show_schedule',
            'family_portal_show_questions',
            'family_portal_whatsapp_group_link',
            'family_portal_questions_text',
        ] as $key) {
            $this->db->table('app_settings')->where('key', $key)->delete();
        }

        if ($this->db->fieldExists('zip_code', 'guardians')) {
            $this->forge->dropColumn('guardians', 'zip_code');
        }
        if ($this->db->fieldExists('zip_code', 'patients')) {
            $this->forge->dropColumn('patients', 'zip_code');
        }
        if ($this->db->fieldExists('cid_code', 'treatments')) {
            $this->forge->dropColumn('treatments', 'cid_code');
        }
    }

    private function seedSettings(): void
    {
        $settings = [
            ['admission_max_contract_months', '3', 'admissao', 'Máximo de meses do contrato', 'number'],
            ['family_portal_show_contracts', '1', 'familia', 'Exibir contratos na área da família', 'boolean'],
            ['family_portal_show_schedule', '1', 'familia', 'Exibir cronograma na área da família', 'boolean'],
            ['family_portal_show_questions', '1', 'familia', 'Exibir perguntas para administração', 'boolean'],
            ['family_portal_whatsapp_group_link', '', 'familia', 'Link do grupo de WhatsApp da família', 'text'],
            ['family_portal_questions_text', 'Envie suas perguntas para a administração pelo contato oficial da comunidade.', 'familia', 'Texto da seção de perguntas da família', 'textarea'],
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
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    private function seedSchedulePermission(): void
    {
        if ($this->db->table('permissions')->where('slug', 'schedule.manage')->countAllResults() === 0) {
            $this->db->table('permissions')->insert([
                'name' => 'Gerenciar cronograma',
                'slug' => 'schedule.manage',
            ]);
        }

        $permission = $this->db->table('permissions')->where('slug', 'schedule.manage')->get()->getRowArray();
        foreach (['admin', 'coordenador'] as $roleSlug) {
            $role = $this->db->table('roles')->where('slug', $roleSlug)->get()->getRowArray();
            if (! $role || ! $permission) {
                continue;
            }

            $exists = $this->db->table('role_permissions')
                ->where('role_id', $role['id'])
                ->where('permission_id', $permission['id'])
                ->countAllResults();

            if ($exists === 0) {
                $this->db->table('role_permissions')->insert([
                    'role_id' => $role['id'],
                    'permission_id' => $permission['id'],
                ]);
            }
        }
    }

    private function seedAdendoTemplate(): void
    {
        if ($this->db->table('document_templates')->where('name', 'Adendo Contratual')->countAllResults() > 0) {
            return;
        }

        $body = '<h1>Adendo Contratual</h1><p>Contratante: {{responsavel}}, CPF {{responsavel_cpf}}.</p><p>Acolhido: {{acolhido}}, CPF {{acolhido_cpf}}, CID informado: {{cid}}.</p><p>Este adendo complementa o contrato de prestação de serviços terapêuticos iniciado em {{admissao}}, mantendo-se válidas as demais cláusulas não alteradas expressamente.</p>';

        $this->db->table('document_templates')->insert([
            'name' => 'Adendo Contratual',
            'category' => 'adendo',
            'body' => $body,
            'version' => 1,
            'is_required_admission' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function updateCurrentContract(): void
    {
        $this->db->table('document_templates')
            ->where('name', 'Contrato Terapêutico Amor Fraterno Atualizado')
            ->where('category', 'contrato')
            ->update([
                'body' => CurrentContractTemplate::html(),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }
}
