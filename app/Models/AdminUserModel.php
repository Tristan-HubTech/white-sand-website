<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminUserModel extends Model
{
    protected $table            = 'admin_users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'full_name',
        'email',
        'password_hash',
        'role',
        'is_active',
        'last_login_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'full_name'     => 'required|min_length[2]|max_length[120]',
        'email'         => 'required|valid_email|max_length[160]',
        'password_hash' => 'required|min_length[10]',
        'role'          => 'required|in_list[admin,staff,superadmin]',
        'is_active'     => 'required|in_list[0,1]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function findByEmail(string $email): ?array
    {
        $user = $this->where('email', strtolower(trim($email)))->first();

        return $user ?: null;
    }

    protected function hashPassword(array $data): array
    {
        if (! isset($data['data']['password_hash'])) {
            return $data;
        }

        $current = (string) $data['data']['password_hash'];
        $info = password_get_info($current);

        if ($info['algo'] === null) {
            $data['data']['password_hash'] = password_hash($current, PASSWORD_DEFAULT);
        }

        return $data;
    }
}
