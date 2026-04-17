<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section class="inline-head">
    <div>
        <p class="eyebrow">Feedback</p>
        <h1>Resort Ratings</h1>
        <p class="hint">Manage visitor ratings and comments submitted from the homepage.</p>
    </div>
</section>

<div class="card table-wrap">
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($ratings)): ?>
            <tr>
                <td colspan="5">No ratings yet.</td>
            </tr>
        <?php endif; ?>
        <?php foreach ($ratings as $rating): ?>
            <tr>
                <td><?= esc((string) $rating['name']) ?></td>
                <td><?= esc(str_repeat('★', (int) $rating['rating']) . str_repeat('☆', 5 - (int) $rating['rating'])) ?> (<?= esc((string) $rating['rating']) ?>/5)</td>
                <td><?= esc(mb_strimwidth((string) ($rating['comment'] ?? ''), 0, 120, '...')) ?></td>
                <td><?= esc((string) $rating['created_at']) ?></td>
                <td>
                    <form action="<?= base_url('/admin/ratings/delete/' . $rating['id']) ?>" method="post" onsubmit="return confirm('Delete this rating?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="link-btn danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="pager-wrap">
        <?= $pager->links() ?>
    </div>
</div>
<?= $this->endSection() ?>
