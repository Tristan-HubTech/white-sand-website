<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResortRatingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
            ],
            'rating' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
            ],
            'comment' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_public' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('rating');
        $this->forge->addKey('is_public');
        $this->forge->addKey('created_at');
        $this->forge->createTable('resort_ratings', true);
    }

    public function down()
    {
        $this->forge->dropTable('resort_ratings', true);
    }
}
