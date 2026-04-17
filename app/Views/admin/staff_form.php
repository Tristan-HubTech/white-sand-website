<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section>
    <p class="eyebrow">Users</p>
    <h1>Add Staff or Admin</h1>
</section>

<div class="card form-card add-page-card">
    <?= validation_list_errors() ?>
    <form action="<?= base_url('/admin/staff/store') ?>" method="post" class="form-grid">
        <?= csrf_field() ?>
        <label>
            Full Name
            <input type="text" name="full_name" value="<?= old('full_name') ?>" required>
        </label>
        <label>
            Email
            <input type="email" name="email" value="<?= old('email') ?>" required>
        </label>
        <label>
            Role
            <select name="role" required>
                <option value="staff" <?= old('role', 'staff') === 'staff' ? 'selected' : '' ?>>Staff</option>
                <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin (Owner)</option>
            </select>
        </label>
        <label>
            Password
            <input type="password" name="password" required minlength="8">
        </label>

        <div class="span-2 actions-row">
            <button class="btn btn-primary" type="submit">Create Account</button>
            <a class="btn btn-outline" href="<?= base_url('/admin/staff') ?>">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
