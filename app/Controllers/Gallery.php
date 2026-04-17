<?php

namespace App\Controllers;

use App\Models\GalleryModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Gallery extends BaseController
{
    public function index(): string
    {
        $galleryModel = new GalleryModel();

        return view('gallery/index', [
            'title'  => 'Gallery',
            'images' => $galleryModel->getPublicGallery(),
        ]);
    }

    public function barcadaRoom(): string
    {
        return $this->renderRoomGallery(
            'Barcada Room',
            'Barcada Room Gallery',
            'uploads/Barcada-room',
            'Explore our group-friendly Barcada Room with bunk-bed setup and spacious shared accommodation.'
        );
    }

    public function standardRoom(): string
    {
        return $this->renderRoomGallery(
            'Standard Room',
            'Standard Room Gallery',
            'uploads/Standard Room',
            'Experience comfort in our air-conditioned Standard Rooms with modern amenities and island views.'
        );
    }

    public function bungalow(): string
    {
        return $this->renderRoomGallery(
            'Bungalow Sea View House',
            'Bungalow Gallery',
            'uploads/Bungalow Sea',
            'Enjoy our spacious 2-bedroom bungalow with full kitchen facilities and stunning sea views from your private balcony.'
        );
    }

    public function cave(): string
    {
        return $this->renderRoomGallery(
            'Poseidon Cave',
            'Poseidon Cave Gallery',
            'uploads/Cave',
            'Explore the mystical Poseidon Cave with its magnificent stalactites, underground pools, and natural beauty. A unique feature of our oceanfront property.'
        );
    }

    private function renderRoomGallery(string $title, string $heading, string $relativeFolder, string $lead): string
    {
        $images = $this->imagesFromFolder($relativeFolder, $heading);

        if ($images === []) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('gallery/room_gallery', [
            'title'      => $title,
            'heading'    => $heading,
            'lead'       => $lead,
            'images'     => $images,
            'backLink'   => base_url('/gallery'),
            'backLabel'  => 'Back to Gallery',
        ]);
    }

    private function imagesFromFolder(string $relativeFolder, string $fallbackTitle): array
    {
        $folder = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $relativeFolder);
        if (! is_dir($folder)) {
            return [];
        }

        $files = scandir($folder);
        if ($files === false) {
            return [];
        }

        $images = [];
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === '.gitkeep') {
                continue;
            }

            $fullPath = $folder . DIRECTORY_SEPARATOR . $file;
            if (! is_file($fullPath)) {
                continue;
            }

            $extension = strtolower((string) pathinfo($file, PATHINFO_EXTENSION));
            if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }

            $images[] = [
                'title'       => $fallbackTitle,
                'description' => '',
                'image_path'  => $relativeFolder . '/' . $file,
            ];
        }

        sort($images);

        return $images;
    }
}
