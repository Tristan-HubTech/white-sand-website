<?php

namespace App\Models;

use CodeIgniter\Model;

class ResortRoomModel extends Model
{
    protected $table            = 'resort_rooms';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'description',
        'price_per_night',
        'amenities',
        'is_active',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'            => 'required|min_length[2]|max_length[120]',
        'description'     => 'permit_empty|max_length[2000]',
        'price_per_night' => 'required|decimal|greater_than_equal_to[0]',
        'amenities'       => 'permit_empty|max_length[3000]',
        'is_active'       => 'required|in_list[0,1]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
