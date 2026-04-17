<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section class="inline-head">
    <div>
        <p class="eyebrow">Content</p>
        <h1>Room Management</h1>
    </div>
    <a class="btn btn-primary" href="<?= base_url('/admin/rooms/create') ?>">Add Room</a>
</section>

<div class="card table-wrap">
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Price / Night</th>
            <th>Status</th>
            <th>Amenities</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($rooms)): ?>
            <tr>
                <td colspan="5">No rooms added yet.</td>
            </tr>
        <?php endif; ?>
        <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?= esc($room['name']) ?></td>
                <td>PHP <?= esc(number_format((float) $room['price_per_night'], 2)) ?></td>
                <td><span class="badge"><?= (int) $room['is_active'] === 1 ? 'active' : 'hidden' ?></span></td>
                <td><?= esc(mb_strimwidth((string) ($room['amenities'] ?? ''), 0, 60, '...')) ?></td>
                <td class="actions-row">
                    <a href="<?= base_url('/admin/rooms/edit/' . $room['id']) ?>">Edit</a>
                    <form action="<?= base_url('/admin/rooms/delete/' . $room['id']) ?>" method="post" onsubmit="return confirm('Delete this room?');">
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
