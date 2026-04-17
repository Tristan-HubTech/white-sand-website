<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRequestTypeToInquiriesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('inquiries', [
            'request_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'inquiry',
                'after'      => 'email',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('inquiries', 'request_type');
    }
}
