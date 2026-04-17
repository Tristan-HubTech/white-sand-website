<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section>
    <p class="eyebrow">Messages</p>
    <h1>Inquiry Management</h1>
</section>

<div class="card">
    <form action="<?= base_url('/admin/inquiries') ?>" method="get" class="form-grid">
        <label>
            Search
            <input type="text" name="q" value="<?= esc((string) ($filters['q'] ?? '')) ?>" placeholder="Name, email, or message">
        </label>
        <label>
            Status
            <select name="status">
                <option value="">All</option>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= esc($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= esc(ucfirst($status)) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Type
            <select name="type">
                <option value="">All</option>
                <option value="booking" <?= ($filters['type'] ?? '') === 'booking' ? 'selected' : '' ?>>Booking</option>
                <option value="inquiry" <?= ($filters['type'] ?? '') === 'inquiry' ? 'selected' : '' ?>>Inquiry</option>
                <option value="reservation" <?= ($filters['type'] ?? '') === 'reservation' ? 'selected' : '' ?>>Reservation</option>
            </select>
        </label>
        <div class="actions-row">
            <button class="btn btn-primary" type="submit">Apply Filters</button>
            <a class="btn btn-outline" href="<?= base_url('/admin/inquiries') ?>">Reset</a>
        </div>
    </form>
</div>

<div class="card table-wrap">
    <table>
        <thead>
        <tr>
            <th>Type</th>
            <th>Name</th>
            <th>Email</th>
            <th>Message</th>
            <th>Guests</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($inquiries)): ?>
            <tr>
                <td colspan="8">No matching inquiries found. Try changing filters.</td>
            </tr>
        <?php endif; ?>
        <?php foreach ($inquiries as $inquiry): ?>
            <tr>
                <td><span class="badge"><?= esc(ucfirst((string) ($inquiry['request_type'] ?? 'inquiry'))) ?></span></td>
                <td><?= esc($inquiry['name']) ?></td>
                <td><?= esc($inquiry['email']) ?></td>
                <td title="<?= esc((string) ($inquiry['message'] ?? '')) ?>"><?= esc(mb_strimwidth((string) ($inquiry['message'] ?? ''), 0, 90, '...')) ?></td>
                <td><?= esc((string) $inquiry['guests']) ?></td>
                <td>
                    <form action="<?= base_url('/admin/inquiries/status/' . $inquiry['id']) ?>" method="post" class="status-form">
                        <?= csrf_field() ?>
                        <select name="status" onchange="this.form.submit()">
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= esc($status) ?>" <?= $inquiry['status'] === $status ? 'selected' : '' ?>><?= esc($status) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </td>
                <td><?= esc((string) $inquiry['created_at']) ?></td>
                <td><a href="<?= base_url('/admin/inquiries/' . $inquiry['id']) ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="pager-wrap">
        <?= $pager->links() ?>
    </div>
</div>
<?= $this->endSection() ?>
