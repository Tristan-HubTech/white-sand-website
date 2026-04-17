<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<section>
    <p class="eyebrow">Room Gallery</p>
    <h1><?= esc($heading ?? 'Room Gallery') ?></h1>
    <p class="lead"><?= esc($lead ?? 'Browse the room photos below.') ?></p>
    <p><a class="btn btn-outline" href="<?= esc($backLink ?? base_url('/gallery')) ?>"><?= esc($backLabel ?? 'Back') ?></a></p>
</section>

<?php if (empty($images)): ?>
    <div class="card empty-state">No room images available right now.</div>
<?php else: ?>
    <div class="gallery-grid">
        <?php foreach ($images as $image): ?>
            <article class="gallery-item reveal" data-lightbox="<?= base_url($image['image_path']) ?>" data-title="<?= esc($image['title']) ?>">
                <img src="<?= base_url($image['image_path']) ?>" alt="<?= esc($image['title']) ?>" loading="lazy" class="gallery-lightbox-trigger" style="cursor: pointer;">
                <div class="gallery-meta">
                    <h3><?= esc($image['title']) ?></h3>
                    <p><?= esc((string) $image['description']) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>