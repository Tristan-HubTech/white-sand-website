<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<section>
    <p class="eyebrow">Booking Confirmation</p>
    <h1>Poseidon White Sand Resort &amp; Cave</h1>
    <p class="lead">Your reservation details are confirmed below.</p>
</section>

<div class="card booking-confirm-card">
    <h2 class="section-title">Reservation Details</h2>
    <div class="detail-grid">
        <div>
            <p><strong>Guest Name:</strong> <?= esc($details['guest_name']) ?></p>
            <p><strong>Booking Ref #:</strong> <?= esc($details['booking_ref']) ?></p>
        </div>
        <div>
            <p><strong>Room Type:</strong> <?= esc($details['room_type']) ?></p>
        </div>
    </div>

    <h2 class="section-title">Booking Information</h2>
    <div class="detail-grid">
        <div>
            <p><strong>Check-in Date:</strong> <?= esc($details['check_in_date']) ?></p>
            <p><strong>Check-out Date:</strong> <?= esc($details['check_out_date']) ?></p>
        </div>
        <div>
            <p><strong>Check-in Time:</strong> <?= esc($details['check_in_time']) ?></p>
            <p><strong>Check-out Time:</strong> <?= esc($details['check_out_time']) ?></p>
        </div>
    </div>

    <h2 class="section-title">Payment Details</h2>
    <div class="detail-grid">
        <div>
            <p><strong>Rate per Night:</strong> <?= esc($details['rate_per_night']) ?></p>
            <p><strong>Length of Stay:</strong> <?= esc($details['length_of_stay']) ?></p>
        </div>
        <div>
            <p><strong>Total Amount:</strong> <?= esc($details['total_amount']) ?></p>
            <p><strong>Payment Method:</strong> <?= esc($details['payment_method']) ?></p>
        </div>
    </div>

    <h2 class="section-title">Important Notes</h2>
    <ul>
        <?php foreach ($details['notes'] as $note): ?>
            <li><?= esc($note) ?></li>
        <?php endforeach; ?>
    </ul>

    <div class="booking-confirm-footer">
        <p>We look forward to welcoming you to Poseidon White Sand Resort &amp; Cave. Enjoy your stay.</p>
        <p><strong>Contact Number:</strong> <a href="tel:+639650469085">+63 965 046 9085</a></p>
        <p><strong>Email:</strong> <a href="mailto:poseidonresort854@gmail.com">poseidonresort854@gmail.com</a></p>
    </div>
</div>
<?= $this->endSection() ?>
