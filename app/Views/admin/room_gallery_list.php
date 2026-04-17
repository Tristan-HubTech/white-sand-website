<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section class="inline-head">
    <div>
        <p class="eyebrow">Content</p>
        <h1><?= esc($title) ?></h1>
        <p class="hint">Manage images for this room gallery. Delete images to remove them from the public page.</p>
    </div>
    <a class="btn btn-secondary" href="<?= base_url('/admin/gallery') ?>">Back to Gallery</a>
</section>

<div class="card table-wrap">
    <table>
        <thead>
        <tr>
            <th>Preview</th>
            <th>Filename</th>
            <th>Size</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($images)): ?>
            <tr>
                <td colspan="4">No images in this room gallery.</td>
            </tr>
        <?php endif; ?>
        <?php foreach ($images as $image): ?>
            <tr>
                <td><img class="thumb" src="<?= base_url($image['path'] . '/' . $image['name']) ?>" alt="<?= esc($image['name']) ?>"></td>
                <td><?= esc($image['name']) ?></td>
                <td><?= esc(number_format($image['size'] / 1024, 2)) ?> KB</td>
                <td class="actions-row">
                    <form action="<?= base_url('/admin/room-gallery/' . $room . '/delete/' . urlencode($image['name'])) ?>" method="post" onsubmit="return confirm('Delete this image?');">
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
