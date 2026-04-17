<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section>
    <p class="eyebrow">Overview</p>
    <h1>Dashboard</h1>
</section>

<div class="stats-grid">
    <article class="card stat-card reveal">
        <h3>Total Inquiries</h3>
        <p><?= esc((string) $metrics['total_inquiry']) ?></p>
    </article>
    <?php if (($isOwner ?? false) === true): ?>
        <article class="card stat-card reveal">
            <h3>Gallery Images</h3>
            <p><?= esc((string) $metrics['gallery_count']) ?></p>
        </article>
        <article class="card stat-card reveal">
            <h3>Rooms</h3>
            <p><?= esc((string) $metrics['rooms_count']) ?></p>
        </article>
        <article class="card stat-card reveal">
            <h3>Ratings</h3>
            <p><?= esc((string) $metrics['rating_count']) ?></p>
        </article>
        <article class="card stat-card reveal">
            <h3>Avg Rating</h3>
            <p><?= esc(number_format((float) ($metrics['rating_average'] ?? 0), 1)) ?>/5</p>
        </article>
    <?php endif; ?>
    <article class="card stat-card reveal">
        <h3>Pending Inquiries</h3>
        <p><?= esc((string) $metrics['pending_inquiry']) ?></p>
    </article>
    <article class="card stat-card reveal">
        <h3>Replied Inquiries</h3>
        <p><?= esc((string) $metrics['replied_inquiry']) ?></p>
    </article>
</div>

<section class="card">
    <h2>Quick Actions</h2>
    <div class="actions">
        <a class="btn btn-primary" href="<?= base_url('/admin/inquiries') ?>">Open Inquiries</a>
        <?php if (($isOwner ?? false) === true): ?>
            <a class="btn btn-outline" href="<?= base_url('/admin/rooms/create') ?>">Add Room</a>
            <a class="btn btn-outline" href="<?= base_url('/admin/gallery/create') ?>">Upload Photo</a>
            <a class="btn btn-outline" href="<?= base_url('/admin/room-gallery/barcada') ?>">Manage Room Galleries</a>
            <a class="btn btn-outline" href="<?= base_url('/admin/staff/create') ?>">Add Staff</a>
        <?php endif; ?>
    </div>
</section>

<?php if (($isOwner ?? false) === true): ?>
    <section class="card">
        <h2>Room Gallery Shortcuts</h2>
        <div class="actions">
            <a class="btn btn-outline" href="<?= base_url('/admin/room-gallery/barcada') ?>">Barcada Room</a>
            <a class="btn btn-outline" href="<?= base_url('/admin/room-gallery/standard') ?>">Standard Room</a>
            <a class="btn btn-outline" href="<?= base_url('/admin/room-gallery/bungalow') ?>">Bungalow Sea View</a>
            <a class="btn btn-outline" href="<?= base_url('/admin/room-gallery/cave') ?>">Poseidon Cave</a>
        </div>
    </section>
<?php endif; ?>

<?php if (($isOwner ?? false) !== true): ?>
    <div class="alert alert-warning">
        Staff access: You can view and update inquiries only.
    </div>
<?php endif; ?>

<section class="card">
    <h2>Recent Messages</h2>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($metrics['recent_inquiry'])): ?>
                <tr>
                    <td colspan="5">No recent messages yet. New customer requests will appear here.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($metrics['recent_inquiry'] as $inquiry): ?>
                <tr>
                    <td><?= esc($inquiry['name']) ?></td>
                    <td><?= esc($inquiry['email']) ?></td>
                    <td><span class="badge"><?= esc($inquiry['status']) ?></span></td>
                    <td><?= esc((string) $inquiry['created_at']) ?></td>
                    <td><a href="<?= base_url('/admin/inquiries/' . $inquiry['id']) ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
