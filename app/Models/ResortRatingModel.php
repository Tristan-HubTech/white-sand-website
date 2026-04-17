<?php

namespace App\Models;

use CodeIgniter\Model;

class ResortRatingModel extends Model
{
    protected $table            = 'resort_ratings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'rating',
        'comment',
        'is_public',
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
        'name'      => 'required|min_length[2]|max_length[120]',
        'rating'    => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
        'comment'   => 'permit_empty|max_length[500]',
        'is_public' => 'required|in_list[0,1]',
    ];
}
