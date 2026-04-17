<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section class="inline-head">
    <div>
        <p class="eyebrow">Inquiry</p>
        <h1><?= esc($inquiry['name']) ?></h1>
    </div>
    <a class="btn btn-outline" href="<?= base_url('/admin/inquiries') ?>">Back</a>
</section>

<div class="card detail-grid">
    <div>
        <h3>Contact</h3>
        <p><strong>Type:</strong> <?= esc(ucfirst((string) ($inquiry['request_type'] ?? 'inquiry'))) ?></p>
        <p><strong>Email:</strong> <?= esc($inquiry['email']) ?></p>
        <p><strong>Phone:</strong> <?= esc((string) $inquiry['phone']) ?></p>
        <p><strong>Guests:</strong> <?= esc((string) $inquiry['guests']) ?></p>
        <p><strong>Check-In:</strong> <?= esc((string) $inquiry['check_in']) ?></p>
        <p><strong>Check-Out:</strong> <?= esc((string) $inquiry['check_out']) ?></p>
    </div>
    <div>
        <h3>Status</h3>
        <form action="<?= base_url('/admin/inquiries/status/' . $inquiry['id']) ?>" method="post" class="status-form">
            <?= csrf_field() ?>
            <select name="status">
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= esc($status) ?>" <?= $inquiry['status'] === $status ? 'selected' : '' ?>><?= esc($status) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Update Status</button>
        </form>
    </div>
</div>

<div class="card">
    <h3>Message</h3>
    <p><?= nl2br(esc((string) $inquiry['message'])) ?></p>
</div>
<?= $this->endSection() ?>
