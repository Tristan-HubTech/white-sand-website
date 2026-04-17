<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section class="inline-head">
    <div>
        <p class="eyebrow">Content</p>
        <h1>Gallery Management</h1>
        <p class="hint">Use the Slider column to control which active images appear in the homepage hero slider.</p>
    </div>
    <a class="btn btn-primary" href="<?= base_url('/admin/gallery/create') ?>">Add Image</a>
</section>

<div class="card">
    <h3>Room Galleries</h3>
    <p class="hint">Manage images for specific room pages:</p>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a class="btn btn-outline" href="<?= base_url('/admin/room-gallery/barcada') ?>">Barcada Room</a>
        <a class="btn btn-outline" href="<?= base_url('/admin/room-gallery/standard') ?>">Standard Room</a>
        <a class="btn btn-outline" href="<?= base_url('/admin/room-gallery/bungalow') ?>">Bungalow Sea View</a>
        <a class="btn btn-outline" href="<?= base_url('/admin/room-gallery/cave') ?>">Poseidon Cave</a>
    </div>
</div>

<div class="card table-wrap">
    <table>
        <thead>
        <tr>
            <th>Preview</th>
            <th>Title</th>
            <th>Order</th>
            <th>Status</th>
            <th>Slider</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($images)): ?>
            <tr>
                <td colspan="6">No images uploaded.</td>
            </tr>
        <?php endif; ?>
        <?php foreach ($images as $image): ?>
            <tr>
                <td><img class="thumb" src="<?= base_url($image['image_path']) ?>" alt="<?= esc($image['title']) ?>"></td>
                <td><?= esc($image['title']) ?></td>
                <td><?= esc((string) $image['sort_order']) ?></td>
                <td><span class="badge"><?= (int) $image['is_active'] === 1 ? 'active' : 'hidden' ?></span></td>
                <td><span class="badge"><?= (int) ($image['show_in_slider'] ?? 1) === 1 ? 'yes' : 'no' ?></span></td>
                <td class="actions-row">
                    <a href="<?= base_url('/admin/gallery/edit/' . $image['id']) ?>">Edit</a>
                    <form action="<?= base_url('/admin/gallery/delete/' . $image['id']) ?>" method="post" onsubmit="return confirm('Delete this image?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="link-btn danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
