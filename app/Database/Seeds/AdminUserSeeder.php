<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $owner = [
            'full_name'     => 'White Sand Admin',
            'email'         => 'admin@whitesandresort.local',
            'password_hash' => password_hash('Admin@12345', PASSWORD_DEFAULT),
            'role'          => 'admin',
            'is_active'     => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        $staff = [
            'full_name'     => 'White Sand Staff',
            'email'         => 'staff@whitesandresort.local',
            'password_hash' => password_hash('Staff@12345', PASSWORD_DEFAULT),
            'role'          => 'staff',
            'is_active'     => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        $builder = $this->db->table('admin_users');
        $ownerExisting = $builder->where('email', $owner['email'])->get()->getRowArray();
        if ($ownerExisting === null) {
            $builder->insert($owner);
        }

        $staffExisting = $builder->where('email', $staff['email'])->get()->getRowArray();
        if ($staffExisting === null) {
            $builder->insert($staff);
        }
    }
}
