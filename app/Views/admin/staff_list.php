<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section class="inline-head">
    <div>
        <p class="eyebrow">Users</p>
        <h1>Staff Accounts</h1>
    </div>
    <a class="btn btn-primary" href="<?= base_url('/admin/staff/create') ?>">Add Account</a>
</section>

<div class="card table-wrap">
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Last Login</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr>
                <td colspan="6">No admin users found.</td>
            </tr>
        <?php endif; ?>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= esc($user['full_name']) ?></td>
                <td><?= esc($user['email']) ?></td>
                <td><span class="badge"><?= esc($user['role']) ?></span></td>
                <td><span class="badge"><?= (int) $user['is_active'] === 1 ? 'active' : 'inactive' ?></span></td>
                <td><?= esc((string) ($user['last_login_at'] ?? '-')) ?></td>
                <td class="actions-row">
                    <?php if ((int) $user['id'] !== (int) session()->get('admin_user_id')): ?>
                        <form action="<?= base_url('/admin/staff/delete/' . $user['id']) ?>" method="post" onsubmit="return confirm('Delete this account?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="link-btn danger">Delete</button>
                        </form>
                    <?php else: ?>
                        <span class="hint">Current account</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
