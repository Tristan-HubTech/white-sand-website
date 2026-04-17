<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section>
    <p class="eyebrow">Gallery</p>
    <h1><?= $mode === 'create' ? 'Add New Image' : 'Edit Image' ?></h1>
</section>

<div class="card form-card add-page-card">
    <?= validation_list_errors() ?>
    <p class="hint">Choose Show in slider = Yes to include images in the homepage hero slider. Sort order controls position.</p>
    <form action="<?= $mode === 'create' ? base_url('/admin/gallery/store') : base_url('/admin/gallery/update/' . $item['id']) ?>" method="post" enctype="multipart/form-data" class="form-grid">
        <?= csrf_field() ?>
        <label>
            Title <?= $mode === 'create' ? '(optional for bulk upload)' : '' ?>
            <input type="text" name="title" value="<?= old('title', $item['title'] ?? '') ?>" <?= $mode === 'create' ? '' : 'required' ?>>
        </label>
        <label>
            Sort Order
            <input type="number" name="sort_order" value="<?= old('sort_order', $item['sort_order'] ?? 0) ?>">
        </label>
        <label class="span-2">
            Description
            <textarea name="description" rows="4"><?= old('description', $item['description'] ?? '') ?></textarea>
        </label>
        <label>
            Status
            <select name="is_active">
                <option value="1" <?= (string) old('is_active', $item['is_active'] ?? '1') === '1' ? 'selected' : '' ?>>Active</option>
                <option value="0" <?= (string) old('is_active', $item['is_active'] ?? '1') === '0' ? 'selected' : '' ?>>Hidden</option>
            </select>
        </label>
        <label>
            Show in slider
            <select name="show_in_slider">
                <option value="1" <?= (string) old('show_in_slider', $item['show_in_slider'] ?? '1') === '1' ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= (string) old('show_in_slider', $item['show_in_slider'] ?? '1') === '0' ? 'selected' : '' ?>>No</option>
            </select>
        </label>
        <label>
            Image <?= $mode === 'create' ? '(you can select multiple)' : '(optional)' ?>
            <?php if ($mode === 'create'): ?>
                <input type="file" name="images[]" accept="image/*" multiple required>
            <?php else: ?>
                <input type="file" name="image" accept="image/*">
            <?php endif; ?>
        </label>

        <?php if ($mode === 'edit' && ! empty($item['image_path'])): ?>
            <div class="span-2">
                <img class="preview-image" src="<?= base_url($item['image_path']) ?>" alt="Current image">
            </div>
        <?php endif; ?>

        <div class="span-2 actions-row">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-outline" href="<?= base_url('/admin/gallery') ?>">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
