<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<?php
$selectedType = old('request_type', $requestType ?? 'inquiry');
$isStayRequest = in_array($selectedType, ['booking', 'reservation'], true);
$facebookMessageUrl = (string) env('app.facebookMessageUrl', 'https://www.facebook.com/profile.php?id=61578406907963');
$titleMap = [
    'booking'     => 'Booking Request',
    'inquiry'     => 'General Inquiry',
    'reservation' => 'Reservation Request',
];
$leadMap = [
    'booking'     => 'Share your stay details and we will confirm availability and pricing.',
    'inquiry'     => 'Send us your questions and our team will get back to you promptly.',
    'reservation' => 'Submit your reservation details and we will contact you for final confirmation.',
];
$submitMap = [
    'booking'     => 'Send Booking Request',
    'inquiry'     => 'Send Inquiry',
    'reservation' => 'Send Reservation Request',
];
?>
<section>
    <p class="eyebrow">Contact Concierge</p>
    <h1><?= esc($titleMap[$selectedType] ?? 'General Inquiry') ?></h1>
    <p class="lead"><?= esc($leadMap[$selectedType] ?? $leadMap['inquiry']) ?></p>
    <nav class="request-mode-tabs" aria-label="Request type navigation">
        <a class="request-tab <?= $selectedType === 'booking' ? 'active' : '' ?>" href="<?= base_url('/booking') ?>">Booking</a>
        <a class="request-tab <?= $selectedType === 'inquiry' ? 'active' : '' ?>" href="<?= base_url('/inquiry') ?>">Inquiry</a>
        <a class="request-tab <?= $selectedType === 'reservation' ? 'active' : '' ?>" href="<?= base_url('/reservation') ?>">Reservation</a>
    </nav>
</section>

<div class="card form-card">
    <?= validation_list_errors() ?>
    <form action="<?= base_url('/inquiry/submit') ?>" method="post" class="form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="request_type" value="<?= esc($selectedType) ?>">
        <label>
            Full Name
            <input type="text" name="name" value="<?= old('name') ?>" required>
        </label>
        <label>
            Email
            <input type="email" name="email" value="<?= old('email') ?>" required>
        </label>
        <label>
            Phone
            <input type="text" name="phone" value="<?= old('phone') ?>">
        </label>
        <?php if ($isStayRequest): ?>
            <label>
                Guests
                <input type="number" name="guests" value="<?= old('guests', 2) ?>" min="1" max="20" required>
            </label>
            <label>
                Check-In
                <input type="date" name="check_in" value="<?= old('check_in') ?>" required>
            </label>
            <label>
                Check-Out
                <input type="date" name="check_out" value="<?= old('check_out') ?>" required>
            </label>
        <?php else: ?>
            <input type="hidden" name="guests" value="1">
            <input type="hidden" name="check_in" value="">
            <input type="hidden" name="check_out" value="">
        <?php endif; ?>
        <label class="span-2">
            Message
            <textarea name="message" rows="6" required><?= old('message') ?></textarea>
        </label>
        <div class="span-2">
            <button class="btn btn-primary" type="submit"><?= esc($submitMap[$selectedType] ?? 'Send Inquiry') ?></button>
            <a class="btn btn-outline" href="<?= esc($facebookMessageUrl, 'attr') ?>" target="_blank" rel="noopener noreferrer" style="margin-left: 0.6rem;">Message us on Facebook</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
