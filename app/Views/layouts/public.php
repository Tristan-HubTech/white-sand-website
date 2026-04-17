<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'White Sand Resort') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/site.css') ?>">
</head>
<body>
<?php
$navRatingPercent = 0;
$navRatingStars = '☆☆☆☆☆';

try {
    $db = \Config\Database::connect();
    if ($db->tableExists('resort_ratings')) {
        $ratingModel = new \App\Models\ResortRatingModel();
        $avgRow = $ratingModel
            ->select('AVG(rating) AS avg_rating')
            ->where('is_public', 1)
            ->first();

        $avg = (float) ($avgRow['avg_rating'] ?? 0);
        $rounded = (int) round($avg);
        $rounded = max(0, min(5, $rounded));

        $navRatingPercent = (int) round(($avg / 5) * 100);
        $navRatingStars = str_repeat('★', $rounded) . str_repeat('☆', 5 - $rounded);
    }
} catch (\Throwable $exception) {
    // Keep nav rendering even when ratings table is unavailable.
}
?>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="<?= base_url('/') ?>">
            <?php if (is_file(FCPATH . 'assets/img/logo.png')): ?>
                <img class="brand-logo" src="<?= base_url('assets/img/logo.png') ?>" alt="Poseidon21 Camotes logo">
            <?php endif; ?>
            <span>White Sand Resort</span>
        </a>
        <button class="menu-toggle" type="button" aria-label="Toggle navigation" data-menu-toggle>Menu</button>
        <nav class="nav-links" data-menu>
            <a href="<?= base_url('/') ?>">Home</a>
            <a href="<?= base_url('/gallery') ?>">Gallery</a>
            <span class="nav-compact-group" aria-label="Requests">
                <a class="nav-mini-link" href="<?= base_url('/booking') ?>">Book</a>
                <a class="nav-mini-link" href="<?= base_url('/inquiry') ?>">Inquire</a>
                <a class="nav-mini-link" href="<?= base_url('/reservation') ?>">Reserve</a>
            </span>
            <a href="<?= base_url('/admin/login') ?>">Admin</a>
            <span class="nav-rating-badge" title="Resort rating">
                <strong><?= esc((string) $navRatingPercent) ?>%</strong>
                <span><?= esc($navRatingStars) ?></span>
            </span>
        </nav>
    </div>
</header>

<main class="site-main">
    <div class="container">
        <?= view('partials/flash') ?>
        <div class="content-layout">
            <section class="content-main">
                <?= $this->renderSection('content') ?>
            </section>
        </div>
    </div>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-strip">
            <div class="footer-strip-logo-wrap">
                <?php if (is_file(FCPATH . 'assets/img/logo.png')): ?>
                    <img class="footer-strip-logo" src="<?= base_url('assets/img/logo.png') ?>" alt="Poseidon21 Camotes logo">
                <?php else: ?>
                    <span class="footer-strip-fallback">Poseidon</span>
                <?php endif; ?>
            </div>
            <p class="footer-strip-text">
                Poseidon White Sand Resort &amp; Cave
                <span class="footer-sep">|</span>
                📍 Location: Cagcagan, Poro, Camotes Islands, Cebu, 6049, Philippines
                <span class="footer-sep">|</span>
                📞 Phone: <a href="tel:+639650469085">+63 965 046 9085</a>
                <span class="footer-sep">|</span>
                ✉ Email: <a href="mailto:poseidonresort854@gmail.com">poseidonresort854@gmail.com</a>
                <span class="footer-sep">|</span>
                📘 Facebook: <a href="https://www.facebook.com/profile.php?id=61578406907963" target="_blank" rel="noopener noreferrer">Poseidon White Sand Resort</a>
            </p>
        </div>
    </div>
</footer>
<script src="<?= base_url('assets/js/site.js') ?>"></script>

<!-- Lightbox Modal -->
<div class="lightbox-modal" id="lightboxModal">
    <div class="lightbox-overlay"></div>
    <div class="lightbox-container">
        <button class="lightbox-close" aria-label="Close image viewer">&times;</button>
        <img class="lightbox-image" src="" alt="">
        <div class="lightbox-caption"></div>
        <div class="lightbox-nav">
            <button class="lightbox-prev" aria-label="Previous image">&lsaquo;</button>
            <button class="lightbox-next" aria-label="Next image">&rsaquo;</button>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/lightbox.js') ?>"></script>
</body>
</html>
