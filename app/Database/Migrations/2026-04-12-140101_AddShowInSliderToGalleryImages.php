<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddShowInSliderToGalleryImages extends Migration
{
    public function up()
    {
        try {
            $this->forge->addColumn('gallery_images', [
                'show_in_slider' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                    'after'      => 'is_active',
                ],
            ]);
        } catch (\Throwable $e) {
            // Ignore when the column already exists in environments that were manually updated.
        }
    }

    public function down()
    {
        try {
            $this->forge->dropColumn('gallery_images', 'show_in_slider');
        } catch (\Throwable $e) {
            // Ignore when the column was already removed.
        }
    }
}
