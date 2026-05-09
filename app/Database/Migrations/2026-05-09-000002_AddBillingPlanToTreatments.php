<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBillingPlanToTreatments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('treatments', [
            'stay_months' => [
                'type' => 'INT',
                'default' => 1,
                'after' => 'registration_amount',
            ],
            'billing_day' => [
                'type' => 'TINYINT',
                'default' => 10,
                'after' => 'stay_months',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('treatments', ['stay_months', 'billing_day']);
    }
}
