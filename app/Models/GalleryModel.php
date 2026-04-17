<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class GalleryModel extends Model
{
    protected $table            = 'gallery_images';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'description',
        'image_path',
        'sort_order',
        'is_active',
        'show_in_slider',
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
        'title'       => 'required|min_length[3]|max_length[160]',
        'description' => 'permit_empty|max_length[1000]',
        'image_path'  => 'required|max_length[255]',
        'sort_order'  => 'permit_empty|integer',
        'is_active'   => 'required|in_list[0,1]',
        'show_in_slider' => 'permit_empty|in_list[0,1]',
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

    public function getPublicGallery(): array
    {
        $dbItems = [];
        try {
            $dbItems = $this->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->orderBy('created_at', 'DESC')
                ->findAll();
        } catch (DatabaseException $e) {
            // Graceful fallback: allow site to render images from filesystem when DB is unavailable.
            log_message('error', 'Gallery DB query failed, using filesystem fallback only: {message}', ['message' => $e->getMessage()]);
        }

        // Include manually copied files from public/uploads/gallery that are not in DB yet.
        $knownPaths = [];
        foreach ($dbItems as $item) {
            $knownPaths[(string) ($item['image_path'] ?? '')] = true;
        }

        $galleryDir = FCPATH . 'uploads/gallery';
        if (! is_dir($galleryDir)) {
            return $dbItems;
        }

        $files = scandir($galleryDir);
        if ($files === false) {
            return $dbItems;
        }

        $extras = [];
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === '.gitkeep') {
                continue;
            }

            $fullPath = $galleryDir . DIRECTORY_SEPARATOR . $file;
            if (! is_file($fullPath)) {
                continue;
            }

            $ext = strtolower((string) pathinfo($file, PATHINFO_EXTENSION));
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }

            $relativePath = 'uploads/gallery/' . $file;
            if (isset($knownPaths[$relativePath])) {
                continue;
            }

            $extras[] = [
                'id'          => 0,
                'title'       => 'Gallery Image',
                'description' => '',
                'image_path'  => $relativePath,
                'sort_order'  => 9999,
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s', (int) @filemtime($fullPath)),
            ];
        }

        usort(
            $extras,
            static fn (array $a, array $b): int => strcmp((string) $b['created_at'], (string) $a['created_at'])
        );

        return array_merge($dbItems, $extras);
    }

    public function getHeroSlides(int $limit = 5): array
    {
        $selectedItems = [];
        $dbItems = [];

        try {
            $selectedItems = $this->where('is_active', 1)
                ->where('show_in_slider', 1)
                ->orderBy('sort_order', 'ASC')
                ->orderBy('created_at', 'DESC')
                ->findAll($limit);

            $dbItems = $this->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->orderBy('created_at', 'DESC')
                ->findAll($limit);
        } catch (DatabaseException $e) {
            log_message('error', 'Hero slide query failed, using gallery fallback: {message}', ['message' => $e->getMessage()]);
        }

        if (count($selectedItems) >= $limit) {
            return $selectedItems;
        }

        $publicGallery = $this->getPublicGallery();
        $seen = [];
        $slides = [];

        foreach ($selectedItems as $item) {
            $path = (string) ($item['image_path'] ?? '');
            if ($path === '' || isset($seen[$path])) {
                continue;
            }

            $seen[$path] = true;
            $slides[] = $item;
        }

        foreach ($dbItems as $item) {
            $path = (string) ($item['image_path'] ?? '');
            if ($path === '' || isset($seen[$path])) {
                continue;
            }

            $seen[$path] = true;
            $slides[] = $item;
        }

        foreach ($publicGallery as $item) {
            $path = (string) ($item['image_path'] ?? '');
            if ($path === '' || isset($seen[$path])) {
                continue;
            }

            $seen[$path] = true;
            $slides[] = $item;

            if (count($slides) >= $limit) {
                break;
            }
        }

        return array_slice($slides, 0, $limit);
    }
}
