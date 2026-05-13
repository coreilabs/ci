<?php

namespace App\Database\Migrations;

use App\Libraries\CurrentContractTemplate;
use CodeIgniter\Database\Migration;

class AddCurrentAdmissionContract extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('nationality', 'guardians')) {
            $this->forge->addColumn('guardians', [
                'nationality' => [
                    'type' => 'VARCHAR',
                    'constraint' => 80,
                    'null' => true,
                    'after' => 'cpf',
                ],
            ]);
        }

        if (! $this->db->fieldExists('nationality', 'patients')) {
            $this->forge->addColumn('patients', [
                'nationality' => [
                    'type' => 'VARCHAR',
                    'constraint' => 80,
                    'null' => true,
                    'after' => 'cpf',
                ],
            ]);
        }

        $exists = $this->db->table('document_templates')
            ->where('name', 'Contrato Terapêutico Amor Fraterno Atualizado')
            ->where('category', 'contrato')
            ->countAllResults();

        if ($exists === 0) {
            $row = $this->db->table('document_templates')
                ->selectMax('version')
                ->where('category', 'contrato')
                ->get()
                ->getRowArray();

            $this->db->table('document_templates')->insert([
                'name' => 'Contrato Terapêutico Amor Fraterno Atualizado',
                'category' => 'contrato',
                'body' => CurrentContractTemplate::html(),
                'version' => ((int) ($row['version'] ?? 0)) + 1,
                'is_required_admission' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $this->db->table('document_templates')
            ->where('name', 'Contrato Terapêutico Amor Fraterno Atualizado')
            ->where('category', 'contrato')
            ->delete();

        if ($this->db->fieldExists('nationality', 'guardians')) {
            $this->forge->dropColumn('guardians', 'nationality');
        }

        if ($this->db->fieldExists('nationality', 'patients')) {
            $this->forge->dropColumn('patients', 'nationality');
        }
    }
}
