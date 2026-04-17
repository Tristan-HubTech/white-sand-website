<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<?php
$slides = $heroSlides ?? [];
if (empty($slides)) {
    $slides = [base_url('assets/img/hero-panorama.jpg')];
}
$encodedSlides = esc(json_encode(array_values($slides)), 'attr');
$facebookMessageUrl = (string) env('app.facebookMessageUrl', 'https://www.facebook.com/profile.php?id=61578406907963');
$directionUrl = (string) env('app.directionUrl', 'https://www.google.com/maps/search/?api=1&query=Cagcagan,+Poro,+Camotes+Islands,+Cebu,+6049,+Philippines');
$ratingSummary = $ratingSummary ?? ['count' => 0, 'average' => 0.0, 'recent' => []];
$ratingExpanded = old('name') !== null || old('rating') !== null || old('comment') !== null;
?>
<section class="kingdom-hero reveal" data-hero-slider data-slides="<?= $encodedSlides ?>" style="--hero-image: url('<?= esc($slides[0], 'attr') ?>');">
    <div class="kingdom-hero-overlay">
        <p class="eyebrow">Welcome to Poseidon White Sand Resort &amp; Cave</p>
        <h1>Oceanfront escape in Camotes.</h1>
        <p>Discover sea-view stays, relaxing coastal scenery, and warm island hospitality.</p>
    </div>
    <div class="kingdom-hero-dots" aria-hidden="true">
        <?php foreach ($slides as $idx => $slide): ?>
            <span class="<?= $idx === 0 ? 'active' : '' ?>"></span>
        <?php endforeach; ?>
    </div>
</section>

<section class="portal-grid">
    <aside class="portal-left">
        <article class="card portal-contact">
            <p style="margin: 0 0 1rem 0;"><strong>You can contact us anytime.</strong></p>
            
            <p style="margin: 0 0 1rem 0; color: #1f3f69;">The best resort for satisfaction.<br>We are ready to assist your booking and inquiry needs.<br>To make<br>I will do my best.</p>
            
            <p style="margin-top: 1.5rem; margin-bottom: 0.5rem;"><strong>📞 Philippines Phone Numbers</strong></p>
            <p style="margin: 0.3rem 0; font-size: 0.95rem; user-select: none;">
                +63 965 046 9085
            </p>
            
            <p style="margin-top: 1rem; margin-bottom: 0.5rem;"><strong>✉ Email</strong></p>
            <p style="margin: 0.3rem 0; font-size: 0.95rem; user-select: none;">
                poseidonresort854@gmail.com
            </p>
            <p style="margin-top: 0.9rem; margin-bottom: 0; font-size: 0.95rem;">
                📘 Facebook: <a href="<?= esc($facebookMessageUrl, 'attr') ?>" target="_blank" rel="noopener noreferrer">Poseidon White Sand Resort</a>
            </p>
        </article>

        <article class="card portal-card">
            <h3>Our Accommodations</h3>
            <ul>
                <li><a href="<?= base_url('/rooms/barcada-room') ?>"><strong>Barcada Room</strong></a><br><span style="font-size: 0.9rem; color: #54657d;">Group-friendly bunk room for barkada stays</span></li>
                <li><a href="<?= base_url('/rooms/standard-room') ?>"><strong>Standard Room Aircon</strong></a><br><span style="font-size: 0.9rem; color: #54657d;">Air-conditioned comfort room</span></li>
                <li><a href="<?= base_url('/rooms/bungalow') ?>"><strong>Bungalow Sea View House</strong></a><br><span style="font-size: 0.9rem; color: #54657d;">2 bedrooms, PHP 4,500/night</span></li>
            </ul>
        </article>

        <article class="card portal-card">
            <h3>Resort Updates</h3>
            <p>Follow our page for new photo updates and seasonal offers.</p>
            <p class="hint">Social feed integration can be added next.</p>
        </article>

        <article class="card portal-card">
            <h3>📍 Direction</h3>
            <p>Cagcagan, Poro, Camotes Islands, Cebu, 6049, Philippines</p>
            <p style="margin: 0.65rem 0 0;">
                <a class="btn btn-outline" href="<?= esc($directionUrl, 'attr') ?>" target="_blank" rel="noopener noreferrer">Open in Maps</a>
            </p>
        </article>
    </aside>

    <div class="portal-center">
        <article class="card portal-card">
            <h3>Booking Status</h3>
            <?php $calendar = $bookingCalendar ?? null; ?>
            <?php if ($calendar !== null): ?>
                <div class="booking-calendar-head">
                    <span class="booking-calendar-title">View Booking Status</span>
                    <a class="booking-calendar-arrow" href="<?= base_url('/?month=' . $calendar['prevMonth']) ?>" aria-label="Previous month">&lsaquo;</a>
                    <strong><?= esc($calendar['label']) ?></strong>
                    <a class="booking-calendar-arrow" href="<?= base_url('/?month=' . $calendar['nextMonth']) ?>" aria-label="Next month">&rsaquo;</a>
                </div>
                <div class="booking-calendar-grid booking-calendar-grid-head">
                    <?php foreach ($calendar['weekdays'] as $weekday): ?>
                        <span><?= esc($weekday) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php foreach ($calendar['weeks'] as $week): ?>
                    <div class="booking-calendar-grid">
                        <?php foreach ($week as $day): ?>
                            <span class="booking-calendar-cell booking-status-<?= esc($day['status']) ?><?= $day['inMonth'] ? '' : ' is-outside' ?><?= $day['isToday'] ? ' is-today' : '' ?>" title="<?= esc(ucfirst($day['status'])) ?><?= $day['bookingCount'] > 0 ? ' (' . $day['bookingCount'] . ' booking)' . ($day['bookingCount'] > 1 ? 's' : '') : '' ?>">
                                <?= esc($day['day']) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                <div class="booking-calendar-legend">
                    <span><i class="legend-dot booking-status-available"></i> Available</span>
                    <span><i class="legend-dot booking-status-limited"></i> Limited</span>
                    <span><i class="legend-dot booking-status-full"></i> Fully Booked</span>
                </div>
                <p class="booking-calendar-note">Calendar shows general room availability based on saved booking and reservation dates.</p>
                <p class="booking-calendar-note">Check-in: 2:00 PM | Check-out: 12:00 NN</p>
            <?php endif; ?>
        </article>

        <article class="card portal-card">
            <h3>Notice</h3>
            <ul class="portal-list">
                <li>Sea-view bungalow is available for booking this month.</li>
                <li>Check-in starts at 2:00 PM and check-out is 12:00 NN.</li>
                <li>Please bring a valid ID during check-in.</li>
                <li>For custom requests, contact us before arrival.</li>
                <li><strong>Coming Next Month:</strong> 30 new rooms with swimming pools will be built. Stay tuned!</li>
            </ul>
        </article>

        <article class="card portal-card">
            <h3>Gallery</h3>
            <?php if (empty($featured)): ?>
                <p class="empty-state">No photos yet. Upload from admin gallery.</p>
            <?php else: ?>
                <div class="portal-thumb-grid">
                    <?php foreach (array_slice($featured, 0, 9) as $image): ?>
                        <img src="<?= base_url($image['image_path']) ?>" alt="<?= esc($image['title']) ?>" loading="lazy">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>

    </div>

    <aside class="portal-right">
        <article class="card portal-card">
            <h3>Inquiries &amp; Reservations</h3>
            <ul class="portal-list">
                <li>Book your preferred date and room type.</li>
                <li>Ask about rates, amenities, and capacity.</li>
                <li>We reply quickly to all booking inquiries.</li>
                <li>Or you can message us on our Facebook page.</li>
            </ul>
            <div class="actions inquiries-actions">
                <a class="btn btn-primary" href="<?= base_url('/booking') ?>">Book Now</a>
                <a class="btn btn-outline" href="<?= base_url('/inquiry') ?>">Send Inquiry</a>
                <a class="btn btn-outline btn-facebook" href="<?= esc($facebookMessageUrl, 'attr') ?>" target="_blank" rel="noopener noreferrer">Message us on Facebook</a>
            </div>
        </article>

        <article class="card portal-card">
            <h3><a href="<?= base_url('/rooms/barcada-room') ?>">Barcada Room</a></h3>
            <ul>
                <li>Ideal for barkada, family, and group stays</li>
                <li>Bunk-bed setup with multiple sleeping spaces</li>
                <li>Comfortable fan-cooled shared room setup</li>
                <li>Spacious layout for shared accommodation</li>
            </ul>
            <p>
                <a class="room-gallery-link" href="<?= base_url('/rooms/barcada-room') ?>">
                    <span class="room-gallery-link-icon">+</span>
                    <span>View room images</span>
                </a>
            </p>
        </article>

        <article class="card portal-card">
            <h3><a href="<?= base_url('/rooms/standard-room') ?>">Standard Room Aircon</a></h3>
            <ul>
                <li>Air-conditioned comfort and modern amenities</li>
                <li>Perfect for couples and small groups</li>
                <li>Private bathroom with hot water</li>
                <li>Island view from your window</li>
            </ul>
            <p>
                <a class="room-gallery-link" href="<?= base_url('/rooms/standard-room') ?>">
                    <span class="room-gallery-link-icon">+</span>
                    <span>View room images</span>
                </a>
            </p>
        </article>

        <article class="card portal-card">
            <h3><a href="<?= base_url('/rooms/bungalow') ?>">Bungalow Sea View House</a></h3>
            <ul>
                <li>2 spacious bedrooms with sea views</li>
                <li>1 fully-equipped kitchen with complete utensils</li>
                <li>1 large balcony overlooking the ocean</li>
                <li>PHP 4,500 per night</li>
            </ul>
            <p>
                <a class="room-gallery-link" href="<?= base_url('/rooms/bungalow') ?>">
                    <span class="room-gallery-link-icon">+</span>
                    <span>View room images</span>
                </a>
            </p>
        </article>

        <article class="card portal-card">
            <h3><a href="<?= base_url('/cave') ?>">Poseidon Cave</a></h3>
            <p>Discover the natural wonder of our oceanfront cave system. Featuring stunning stalactites, underground pools, and mystical chambers.</p>
            <ul style="margin-bottom: 1rem;">
                <li>Magnificent stalactite formations</li>
                <li>Crystal-clear underground pools</li>
                <li>Guided cave exploration available</li>
                <li>Perfect photo opportunity at sunset</li>
            </ul>
            <p>
                <a class="room-gallery-link" href="<?= base_url('/cave') ?>">
                    <span class="room-gallery-link-icon">+</span>
                    <span>View cave photos</span>
                </a>
            </p>
        </article>

        <article class="card portal-card rating-card-compact">
            <h3>Rate Our Resort</h3>
            <p class="hint rating-summary-line">
                <strong><?= esc(number_format((float) ($ratingSummary['average'] ?? 0), 1)) ?>/5</strong>
                · <?= esc((string) ($ratingSummary['count'] ?? 0)) ?> review<?= ((int) ($ratingSummary['count'] ?? 0)) === 1 ? '' : 's' ?>
            </p>

            <details class="rating-collapsible" <?= $ratingExpanded ? 'open' : '' ?>>
                <summary>Rate now</summary>

                <form action="<?= base_url('/ratings/submit') ?>" method="post" class="form-grid compact-rating-form">
                    <?= csrf_field() ?>
                    <label>
                        Your Name
                        <input type="text" name="name" value="<?= old('name') ?>" required>
                    </label>
                    <label>
                        Rating
                        <span class="star-rating" role="radiogroup" aria-label="Rate from 1 to 5 stars">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input
                                    type="radio"
                                    id="rating-<?= $i ?>"
                                    name="rating"
                                    value="<?= $i ?>"
                                    <?= old('rating') == (string) $i ? 'checked' : '' ?>
                                    required
                                >
                                <label for="rating-<?= $i ?>" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                            <?php endfor; ?>
                        </span>
                    </label>
                    <label class="span-2">
                        Comment (optional)
                        <textarea name="comment" rows="3" maxlength="500"><?= old('comment') ?></textarea>
                    </label>
                    <div class="span-2">
                        <button class="btn btn-primary" type="submit">Submit Rating</button>
                    </div>
                </form>

                <?php if (! empty($ratingSummary['recent'])): ?>
                    <div class="rating-recent-list">
                        <?php foreach (array_slice($ratingSummary['recent'], 0, 3) as $ratingItem): ?>
                            <div class="rating-recent-item">
                                <p>
                                    <strong><?= esc((string) $ratingItem['name']) ?></strong>
                                    <span><?= esc(str_repeat('★', (int) $ratingItem['rating']) . str_repeat('☆', 5 - (int) $ratingItem['rating'])) ?></span>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </details>
        </article>
    </aside>
</section>
<?= $this->endSection() ?>
