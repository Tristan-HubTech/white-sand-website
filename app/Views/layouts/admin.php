<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin Panel') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/site.css') ?>">
</head>
<body class="admin-portal">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <a class="admin-brand" href="<?= base_url('/admin/dashboard') ?>">Resort Console</a>
        <nav class="admin-side-nav">
            <a href="<?= base_url('/admin/dashboard') ?>">Dashboard</a>
            <a href="<?= base_url('/admin/inquiries') ?>">Inquiries</a>
            <?php if (($isOwner ?? false) === true): ?>
                <a href="<?= base_url('/admin/rooms') ?>">Rooms</a>
                <a href="<?= base_url('/admin/gallery') ?>">Gallery Photos</a>
                <a href="<?= base_url('/admin/room-gallery/barcada') ?>">Room Galleries</a>
                <a href="<?= base_url('/admin/ratings') ?>">Ratings</a>
                <a href="<?= base_url('/admin/staff') ?>">Staff Accounts</a>
            <?php endif; ?>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <p class="admin-topbar-label">Signed in as</p>
                <p class="admin-topbar-user"><?= esc($adminName ?? 'Admin') ?> (<?= ($adminRole ?? 'staff') === 'admin' ? 'owner' : 'staff' ?>)</p>
            </div>
            <a class="btn btn-outline" href="<?= base_url('/admin/logout') ?>">Logout</a>
        </header>

        <section class="admin-content">
            <?= view('partials/flash') ?>
            <?= $this->renderSection('content') ?>
        </section>
    </main>
</div>

<button class="menu-toggle admin-fab-toggle" type="button" aria-label="Toggle navigation" data-menu-toggle>
    Menu
</button>
<nav class="nav-links admin-mobile-nav" data-menu>
    <a href="<?= base_url('/admin/dashboard') ?>">Dashboard</a>
    <a href="<?= base_url('/admin/inquiries') ?>">Inquiries</a>
    <?php if (($isOwner ?? false) === true): ?>
        <a href="<?= base_url('/admin/rooms') ?>">Rooms</a>
        <a href="<?= base_url('/admin/gallery') ?>">Gallery Photos</a>
        <a href="<?= base_url('/admin/room-gallery/barcada') ?>">Room Galleries</a>
        <a href="<?= base_url('/admin/ratings') ?>">Ratings</a>
        <a href="<?= base_url('/admin/staff') ?>">Staff Accounts</a>
    <?php endif; ?>
    <a href="<?= base_url('/admin/logout') ?>">Logout</a>
</nav>

<script src="<?= base_url('assets/js/site.js') ?>"></script>
</body>
</html>
