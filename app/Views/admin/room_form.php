<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section>
    <p class="eyebrow">Rooms</p>
    <h1><?= $mode === 'create' ? 'Add New Room' : 'Edit Room' ?></h1>
</section>

<div class="card form-card add-page-card">
    <?= validation_list_errors() ?>
    <form action="<?= $mode === 'create' ? base_url('/admin/rooms/store') : base_url('/admin/rooms/update/' . $room['id']) ?>" method="post" class="form-grid">
        <?= csrf_field() ?>
        <label>
            Room Name
            <input type="text" name="name" value="<?= old('name', $room['name'] ?? '') ?>" required>
        </label>
        <label>
            Price per Night (PHP)
            <input type="number" step="0.01" min="0" name="price_per_night" value="<?= old('price_per_night', $room['price_per_night'] ?? '0.00') ?>" required>
        </label>
        <label>
            Status
            <select name="is_active">
                <option value="1" <?= (string) old('is_active', $room['is_active'] ?? '1') === '1' ? 'selected' : '' ?>>Active</option>
                <option value="0" <?= (string) old('is_active', $room['is_active'] ?? '1') === '0' ? 'selected' : '' ?>>Hidden</option>
            </select>
        </label>
        <label class="span-2">
            Description
            <textarea name="description" rows="4"><?= old('description', $room['description'] ?? '') ?></textarea>
        </label>
        <label class="span-2">
            Amenities
            <textarea name="amenities" rows="4" placeholder="Example: Aircon, Wi-Fi, Ocean View, Hot Shower"><?= old('amenities', $room['amenities'] ?? '') ?></textarea>
        </label>

        <div class="span-2 actions-row">
            <button class="btn btn-primary" type="submit">Save Room</button>
            <a class="btn btn-outline" href="<?= base_url('/admin/rooms') ?>">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
