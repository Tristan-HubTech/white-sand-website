<?php

namespace App\Controllers;

use App\Models\AdminUserModel;
use App\Models\GalleryModel;
use App\Models\InquiryModel;
use App\Models\ResortRatingModel;
use App\Models\ResortRoomModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Database;

class Admin extends BaseController
{
    private AdminUserModel $adminUserModel;
    private GalleryModel $galleryModel;
    private InquiryModel $inquiryModel;
    private ResortRatingModel $ratingModel;
    private ResortRoomModel $roomModel;

    public function __construct()
    {
        $this->adminUserModel = new AdminUserModel();
        $this->galleryModel = new GalleryModel();
        $this->inquiryModel = new InquiryModel();
        $this->ratingModel = new ResortRatingModel();
        $this->roomModel = new ResortRoomModel();
    }

    public function login(): string|RedirectResponse
    {
        if ((bool) session()->get('admin_logged_in')) {
            return redirect()->to('/admin/dashboard');
        }

        return view('admin/login', [
            'title' => 'Admin Login',
        ]);
    }

    public function authenticate(): RedirectResponse
    {
        $rules = [
            'email'    => 'required|valid_email|max_length[160]',
            'password' => 'required|min_length[8]|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/login')->withInput()->with('error', 'Invalid login credentials format.');
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');
        $user = $this->adminUserModel->findByEmail($email);

        if ($user === null || (int) $user['is_active'] !== 1 || ! password_verify($password, (string) $user['password_hash'])) {
            return redirect()->to('/admin/login')->withInput()->with('error', 'Email or password is incorrect.');
        }

        session()->set([
            'admin_logged_in' => true,
            'admin_user_id'   => (int) $user['id'],
            'admin_name'      => $user['full_name'],
            'admin_role'      => $this->normalizeRole((string) $user['role']),
        ]);

        $this->adminUserModel->update((int) $user['id'], [
            'last_login_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/dashboard')->with('success', 'Welcome back, ' . $user['full_name'] . '.');
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();

        return redirect()->to('/admin/login')->with('success', 'You have been logged out.');
    }

    public function dashboard(): string
    {
        $ratingCount = 0;
        $ratingAverage = 0.0;

        if ($this->ratingsTableExists()) {
            $averageRow = $this->ratingModel
                ->select('AVG(rating) AS avg_rating')
                ->where('is_public', 1)
                ->first();

            $ratingCount = (int) $this->ratingModel->where('is_public', 1)->countAllResults();
            $ratingAverage = (float) ($averageRow['avg_rating'] ?? 0);
        }

        $metrics = [
            'gallery_count'   => $this->galleryModel->countAllResults(),
            'pending_inquiry' => $this->inquiryModel->where('status', 'pending')->countAllResults(),
            'replied_inquiry' => $this->inquiryModel->where('status', 'replied')->countAllResults(),
            'total_inquiry'   => $this->inquiryModel->countAllResults(),
            'rooms_count'     => $this->roomModel->countAllResults(),
            'recent_inquiry'  => $this->inquiryModel->orderBy('created_at', 'DESC')->findAll(5),
            'rating_count'    => $ratingCount,
            'rating_average'  => $ratingAverage,
        ];

        return $this->adminView('dashboard', [
            'title'   => 'Admin Dashboard',
            'metrics' => $metrics,
        ]);
    }

    public function gallery(): string|RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff can only manage inquiries.');
        }

        return $this->adminView('gallery_list', [
            'title'  => 'Manage Gallery',
            'images' => $this->galleryModel
                ->orderBy('sort_order', 'ASC')
                ->orderBy('created_at', 'DESC')
                ->findAll(),
        ]);
    }

    public function galleryCreate(): string|RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff cannot edit website content.');
        }

        return $this->adminView('gallery_form', [
            'title' => 'Add Gallery Image',
            'mode'  => 'create',
            'item'  => null,
        ]);
    }

    public function galleryStore(): RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff cannot edit website content.');
        }

        $rules = [
            'title'       => 'permit_empty|min_length[3]|max_length[160]',
            'description' => 'permit_empty|max_length[1000]',
            'sort_order'  => 'permit_empty|integer',
            'is_active'   => 'required|in_list[0,1]',
            'show_in_slider' => 'required|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/gallery/create')->withInput()->with('error', 'Please fix the highlighted errors.');
        }

        $uploadedFiles = $this->request->getFileMultiple('images');
        if (empty($uploadedFiles)) {
            $single = $this->request->getFile('image');
            if ($single !== null) {
                $uploadedFiles = [$single];
            }
        }

        if (empty($uploadedFiles)) {
            return redirect()->to('/admin/gallery/create')->withInput()->with('error', 'Please select at least one image.');
        }

        $description = trim((string) $this->request->getPost('description'));
        $providedTitle = trim((string) $this->request->getPost('title'));
        $baseSortOrder = (int) ($this->request->getPost('sort_order') ?: 0);
        $isActive = (int) $this->request->getPost('is_active');
        $showInSlider = (int) $this->request->getPost('show_in_slider');

        $allowedMime = ['image/jpg', 'image/jpeg', 'image/png', 'image/webp'];
        $insertedCount = 0;

        foreach ($uploadedFiles as $index => $uploaded) {
            if ($uploaded === null || ! $uploaded->isValid() || $uploaded->hasMoved()) {
                continue;
            }

            if (! str_starts_with((string) $uploaded->getMimeType(), 'image/')) {
                continue;
            }

            if (! in_array((string) $uploaded->getMimeType(), $allowedMime, true)) {
                continue;
            }

            if ($uploaded->getSizeByUnit('kb') > 5120) {
                continue;
            }

            $newName = $uploaded->getRandomName();
            $uploaded->move(FCPATH . 'uploads/gallery', $newName);

            $autoTitle = pathinfo((string) $uploaded->getClientName(), PATHINFO_FILENAME);
            $title = $providedTitle !== '' && count($uploadedFiles) === 1 ? $providedTitle : ucwords(str_replace(['-', '_'], ' ', $autoTitle));

            $this->galleryModel->insert([
                'title'       => $title !== '' ? $title : 'Gallery Image',
                'description' => $description,
                'sort_order'  => $baseSortOrder + $index,
                'is_active'   => $isActive,
                'show_in_slider' => $showInSlider,
                'image_path'  => 'uploads/gallery/' . $newName,
            ]);

            $insertedCount++;
        }

        if ($insertedCount === 0) {
            return redirect()->to('/admin/gallery/create')->withInput()->with('error', 'No valid image was uploaded.');
        }

        return redirect()->to('/admin/gallery')->with('success', $insertedCount . ' gallery image(s) added successfully.');
    }

    public function galleryEdit(int $id): string|RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff cannot edit website content.');
        }

        $item = $this->galleryModel->find($id);

        if ($item === null) {
            return redirect()->to('/admin/gallery')->with('error', 'Image record not found.');
        }

        return $this->adminView('gallery_form', [
            'title' => 'Edit Gallery Image',
            'mode'  => 'edit',
            'item'  => $item,
        ]);
    }

    public function galleryUpdate(int $id): RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff cannot edit website content.');
        }

        $item = $this->galleryModel->find($id);

        if ($item === null) {
            return redirect()->to('/admin/gallery')->with('error', 'Image record not found.');
        }

        $rules = [
            'title'       => 'required|min_length[3]|max_length[160]',
            'description' => 'permit_empty|max_length[1000]',
            'sort_order'  => 'permit_empty|integer',
            'is_active'   => 'required|in_list[0,1]',
            'show_in_slider' => 'required|in_list[0,1]',
        ];

        $uploaded = $this->request->getFile('image');
        if ($uploaded && $uploaded->isValid() && ! $uploaded->hasMoved()) {
            $rules['image'] = 'is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]|max_size[image,5120]';
        }

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/gallery/edit/' . $id)->withInput()->with('error', 'Please fix the highlighted errors.');
        }

        $updateData = [
            'title'       => trim((string) $this->request->getPost('title')),
            'description' => trim((string) $this->request->getPost('description')),
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active'   => (int) $this->request->getPost('is_active'),
            'show_in_slider' => (int) $this->request->getPost('show_in_slider'),
        ];

        if ($uploaded && $uploaded->isValid() && ! $uploaded->hasMoved()) {
            $newName = $uploaded->getRandomName();
            $uploaded->move(FCPATH . 'uploads/gallery', $newName);
            $updateData['image_path'] = 'uploads/gallery/' . $newName;
            $this->deleteImageIfExists((string) $item['image_path']);
        }

        $this->galleryModel->update($id, $updateData);

        return redirect()->to('/admin/gallery')->with('success', 'Gallery image updated successfully.');
    }

    public function galleryDelete(int $id): RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff cannot delete important data.');
        }

        $item = $this->galleryModel->find($id);

        if ($item === null) {
            return redirect()->to('/admin/gallery')->with('error', 'Image record not found.');
        }

        $this->galleryModel->delete($id);
        $this->deleteImageIfExists((string) $item['image_path']);

        return redirect()->to('/admin/gallery')->with('success', 'Gallery image deleted successfully.');
    }

    public function inquiries(): string
    {
        $status = trim((string) $this->request->getGet('status'));
        $type = trim((string) $this->request->getGet('type'));
        $q = trim((string) $this->request->getGet('q'));

        $builder = $this->inquiryModel->orderBy('created_at', 'DESC');

        if ($status !== '' && in_array($status, $this->inquiryModel->statuses(), true)) {
            $builder->where('status', $status);
        }

        if ($type !== '' && in_array($type, ['booking', 'inquiry', 'reservation'], true)) {
            $builder->where('request_type', $type);
        }

        if ($q !== '') {
            $builder->groupStart()
                ->like('name', $q)
                ->orLike('email', $q)
                ->orLike('message', $q)
                ->groupEnd();
        }

        return $this->adminView('inquiry_list', [
            'title'     => 'Inquiries',
            'inquiries' => $builder->paginate(15),
            'pager'     => $builder->pager,
            'statuses'  => $this->inquiryModel->statuses(),
            'filters'   => [
                'status' => $status,
                'type'   => $type,
                'q'      => $q,
            ],
        ]);
    }

    public function inquiryShow(int $id): string|RedirectResponse
    {
        $inquiry = $this->inquiryModel->find($id);

        if ($inquiry === null) {
            return redirect()->to('/admin/inquiries')->with('error', 'Inquiry not found.');
        }

        return $this->adminView('inquiry_show', [
            'title'    => 'Inquiry Details',
            'inquiry'  => $inquiry,
            'statuses' => $this->inquiryModel->statuses(),
        ]);
    }

    public function inquiryStatus(int $id): RedirectResponse
    {
        $inquiry = $this->inquiryModel->find($id);

        if ($inquiry === null) {
            return redirect()->to('/admin/inquiries')->with('error', 'Inquiry not found.');
        }

        $status = (string) $this->request->getPost('status');
        if (! in_array($status, $this->inquiryModel->statuses(), true)) {
            return redirect()->back()->with('error', 'Invalid status value.');
        }

        $previousStatus = (string) ($inquiry['status'] ?? 'pending');

        if ($previousStatus === $status) {
            return redirect()->back()->with('success', 'Inquiry is already marked as ' . ucfirst($status) . '.');
        }

        $this->inquiryModel->update($id, ['status' => $status]);

        // Auto email sending is disabled; status updates are admin-only.

        return redirect()->back()->with('success', 'Inquiry marked as ' . ucfirst($status) . '.');
    }

    public function ratings(): string|RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff cannot manage ratings.');
        }

        if (! $this->ratingsTableExists()) {
            return redirect()->to('/admin/dashboard')->with('error', 'Ratings table is missing. Run migrations first.');
        }

        $ratings = $this->ratingModel
            ->orderBy('created_at', 'DESC')
            ->paginate(20);

        return $this->adminView('ratings_list', [
            'title'   => 'Resort Ratings',
            'ratings' => $ratings,
            'pager'   => $this->ratingModel->pager,
        ]);
    }

    public function ratingDelete(int $id): RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff cannot delete ratings.');
        }

        if (! $this->ratingsTableExists()) {
            return redirect()->to('/admin/dashboard')->with('error', 'Ratings table is missing. Run migrations first.');
        }

        $item = $this->ratingModel->find($id);
        if ($item === null) {
            return redirect()->to('/admin/ratings')->with('error', 'Rating not found.');
        }

        $this->ratingModel->delete($id);

        return redirect()->to('/admin/ratings')->with('success', 'Rating deleted successfully.');
    }

    private function ratingsTableExists(): bool
    {
        try {
            return Database::connect()->tableExists('resort_ratings');
        } catch (\Throwable $exception) {
            return false;
        }
    }

    public function roomGalleryList(string $room): string|RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff cannot modify website content.');
        }

        $roomFolders = [
            'barcada'  => 'uploads/Barcada-room',
            'standard' => 'uploads/Standard Room',
            'bungalow' => 'uploads/Bungalow Sea',
            'cave'     => 'uploads/Cave',
        ];

        if (! isset($roomFolders[$room])) {
            return redirect()->to('/admin/gallery')->with('error', 'Invalid room specified.');
        }

        $folder = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $roomFolders[$room]);
        $images = [];

        if (is_dir($folder)) {
            $files = scandir($folder);
            if ($files !== false) {
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
                        'name'      => $file,
                        'path'      => $roomFolders[$room],
                        'size'      => filesize($fullPath),
                        'modified'  => filemtime($fullPath),
                    ];
                }
            }
        }

        usort($images, static fn (array $a, array $b): int => $b['modified'] <=> $a['modified']);

        $roomNames = [
            'barcada'  => 'Barcada Room',
            'standard' => 'Standard Room',
            'bungalow' => 'Bungalow Sea View House',
            'cave'     => 'Poseidon Cave',
        ];

        return $this->adminView('room_gallery_list', [
            'title'  => $roomNames[$room] . ' Gallery',
            'room'   => $room,
            'images' => $images,
        ]);
    }

    public function roomGalleryDelete(string $room, string $filename): RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff cannot delete website content.');
        }

        $roomFolders = [
            'barcada'  => 'uploads/Barcada-room',
            'standard' => 'uploads/Standard Room',
            'bungalow' => 'uploads/Bungalow Sea',
            'cave'     => 'uploads/Cave',
        ];

        if (! isset($roomFolders[$room])) {
            return redirect()->back()->with('error', 'Invalid room specified.');
        }

        $filePath = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $roomFolders[$room] . '/' . $filename);
        $expectedDir = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $roomFolders[$room]);

        if (! str_starts_with(realpath($filePath) ?: '', realpath($expectedDir) ?: '')) {
            return redirect()->back()->with('error', 'Invalid file path.');
        }

        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
            return redirect()->back()->with('success', 'Image deleted successfully.');
        }

        return redirect()->back()->with('error', 'Image not found.');
    }

    public function rooms(): string|RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff cannot modify website content.');
        }

        return $this->adminView('room_list', [
            'title' => 'Room Management',
            'rooms' => $this->roomModel->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function roomCreate(): string|RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff cannot modify website content.');
        }

        return $this->adminView('room_form', [
            'title' => 'Add Room',
            'mode'  => 'create',
            'room'  => null,
        ]);
    }

    public function roomStore(): RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff cannot modify website content.');
        }

        $rules = [
            'name'            => 'required|min_length[2]|max_length[120]',
            'description'     => 'permit_empty|max_length[2000]',
            'price_per_night' => 'required|decimal|greater_than_equal_to[0]',
            'amenities'       => 'permit_empty|max_length[3000]',
            'is_active'       => 'required|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/rooms/create')->withInput()->with('error', 'Please fix the highlighted errors.');
        }

        $this->roomModel->insert([
            'name'            => trim((string) $this->request->getPost('name')),
            'description'     => trim((string) $this->request->getPost('description')),
            'price_per_night' => (string) $this->request->getPost('price_per_night'),
            'amenities'       => trim((string) $this->request->getPost('amenities')),
            'is_active'       => (int) $this->request->getPost('is_active'),
        ]);

        return redirect()->to('/admin/rooms')->with('success', 'Room added successfully.');
    }

    public function roomEdit(int $id): string|RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff cannot modify website content.');
        }

        $room = $this->roomModel->find($id);
        if ($room === null) {
            return redirect()->to('/admin/rooms')->with('error', 'Room not found.');
        }

        return $this->adminView('room_form', [
            'title' => 'Edit Room',
            'mode'  => 'edit',
            'room'  => $room,
        ]);
    }

    public function roomUpdate(int $id): RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff cannot modify website content.');
        }

        $room = $this->roomModel->find($id);
        if ($room === null) {
            return redirect()->to('/admin/rooms')->with('error', 'Room not found.');
        }

        $rules = [
            'name'            => 'required|min_length[2]|max_length[120]',
            'description'     => 'permit_empty|max_length[2000]',
            'price_per_night' => 'required|decimal|greater_than_equal_to[0]',
            'amenities'       => 'permit_empty|max_length[3000]',
            'is_active'       => 'required|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/rooms/edit/' . $id)->withInput()->with('error', 'Please fix the highlighted errors.');
        }

        $this->roomModel->update($id, [
            'name'            => trim((string) $this->request->getPost('name')),
            'description'     => trim((string) $this->request->getPost('description')),
            'price_per_night' => (string) $this->request->getPost('price_per_night'),
            'amenities'       => trim((string) $this->request->getPost('amenities')),
            'is_active'       => (int) $this->request->getPost('is_active'),
        ]);

        return redirect()->to('/admin/rooms')->with('success', 'Room updated successfully.');
    }

    public function roomDelete(int $id): RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Staff cannot delete important data.');
        }

        $room = $this->roomModel->find($id);
        if ($room === null) {
            return redirect()->to('/admin/rooms')->with('error', 'Room not found.');
        }

        $this->roomModel->delete($id);

        return redirect()->to('/admin/rooms')->with('success', 'Room deleted successfully.');
    }

    public function staffUsers(): string|RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Only owner can manage staff accounts.');
        }

        return $this->adminView('staff_list', [
            'title' => 'Staff Accounts',
            'users' => $this->adminUserModel->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function staffCreate(): string|RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Only owner can manage staff accounts.');
        }

        return $this->adminView('staff_form', [
            'title' => 'Add Staff User',
        ]);
    }

    public function staffStore(): RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Only owner can manage staff accounts.');
        }

        $rules = [
            'full_name' => 'required|min_length[2]|max_length[120]',
            'email'     => 'required|valid_email|max_length[160]|is_unique[admin_users.email]',
            'password'  => 'required|min_length[8]|max_length[255]',
            'role'      => 'required|in_list[admin,staff]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/staff/create')->withInput()->with('error', 'Please fix the highlighted errors.');
        }

        $this->adminUserModel->insert([
            'full_name'     => trim((string) $this->request->getPost('full_name')),
            'email'         => strtolower(trim((string) $this->request->getPost('email'))),
            'password_hash' => (string) $this->request->getPost('password'),
            'role'          => (string) $this->request->getPost('role'),
            'is_active'     => 1,
        ]);

        return redirect()->to('/admin/staff')->with('success', 'Account created successfully.');
    }

    public function staffDelete(int $id): RedirectResponse
    {
        if (! $this->isOwner()) {
            return redirect()->to('/admin/inquiries')->with('warning', 'Only owner can manage staff accounts.');
        }

        if ($id === (int) session()->get('admin_user_id')) {
            return redirect()->to('/admin/staff')->with('error', 'You cannot delete your own account.');
        }

        $user = $this->adminUserModel->find($id);
        if ($user === null) {
            return redirect()->to('/admin/staff')->with('error', 'User not found.');
        }

        $this->adminUserModel->delete($id);

        return redirect()->to('/admin/staff')->with('success', 'User removed successfully.');
    }

    private function deleteImageIfExists(string $relativePath): void
    {
        if ($relativePath === '') {
            return;
        }

        $target = FCPATH . $relativePath;

        if (is_file($target)) {
            @unlink($target);
        }
    }

    private function adminView(string $view, array $data = []): string
    {
        $adminRole = (string) session()->get('admin_role');
        $baseData = [
            'adminName' => (string) session()->get('admin_name'),
            'adminRole' => $adminRole,
            'isOwner'   => $adminRole === 'admin',
        ];

        return view('admin/' . $view, $data + $baseData);
    }

    private function isOwner(): bool
    {
        return (string) session()->get('admin_role') === 'admin';
    }

    private function normalizeRole(string $role): string
    {
        $role = strtolower(trim($role));

        if ($role === 'superadmin') {
            return 'admin';
        }

        return in_array($role, ['admin', 'staff'], true) ? $role : 'staff';
    }
}
