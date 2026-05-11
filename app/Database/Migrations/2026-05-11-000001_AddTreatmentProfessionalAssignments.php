<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTreatmentProfessionalAssignments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'treatment_id' => ['type' => 'INT', 'unsigned' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
            'specialty' => ['type' => 'VARCHAR', 'constraint' => 40],
            'next_attendance_at' => ['type' => 'DATETIME', 'null' => true],
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['treatment_id', 'specialty']);
        $this->forge->addKey(['user_id', 'specialty']);
        $this->forge->createTable('treatment_professionals', true);
    }

    public function down()
    {
        $this->forge->dropTable('treatment_professionals', true);
    }
}
