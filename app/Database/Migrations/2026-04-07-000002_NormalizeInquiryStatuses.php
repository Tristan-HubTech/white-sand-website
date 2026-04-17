<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeInquiryStatuses extends Migration
{
    public function up()
    {
        $this->db->query("UPDATE inquiries SET status = 'replied' WHERE status IN ('responded')");
        $this->db->query("UPDATE inquiries SET status = 'pending' WHERE status IN ('new','read','archived') OR status IS NULL OR status = ''");

        $this->forge->modifyColumn('inquiries', [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pending',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('inquiries', [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'new',
            ],
        ]);

        $this->db->query("UPDATE inquiries SET status = 'responded' WHERE status = 'replied'");
        $this->db->query("UPDATE inquiries SET status = 'new' WHERE status = 'pending'");
    }
}
