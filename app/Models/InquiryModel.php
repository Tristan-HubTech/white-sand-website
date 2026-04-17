<?php

namespace App\Models;

use CodeIgniter\Model;

class InquiryModel extends Model
{
    protected $table            = 'inquiries';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'request_type',
        'name',
        'email',
        'phone',
        'check_in',
        'check_out',
        'guests',
        'message',
        'status',
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
        'request_type' => 'required|in_list[booking,inquiry,reservation]',
        'name'      => 'required|min_length[2]|max_length[120]',
        'email'     => 'required|valid_email|max_length[160]',
        'phone'     => 'permit_empty|max_length[30]',
        'check_in'  => 'permit_empty|valid_date[Y-m-d]',
        'check_out' => 'permit_empty|valid_date[Y-m-d]',
        'guests'    => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[20]',
        'message'   => 'required|min_length[10]|max_length[3000]',
        'status'    => 'required|in_list[pending,replied]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function statuses(): array
    {
        return ['pending', 'replied'];
    }

    public function getAdminList(int $limit = 20, int $offset = 0): array
    {
        return $this->orderBy('created_at', 'DESC')
            ->findAll($limit, $offset);
    }
}
