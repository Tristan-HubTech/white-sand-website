<h2>New Inquiry Received</h2>
<p>A visitor submitted a new resort inquiry.</p>
<ul>
    <li><strong>Request Type:</strong> <?= esc(ucfirst((string) ($inquiry['request_type'] ?? 'inquiry'))) ?></li>
    <li><strong>Name:</strong> <?= esc($inquiry['name']) ?></li>
    <li><strong>Email:</strong> <?= esc($inquiry['email']) ?></li>
    <li><strong>Phone:</strong> <?= esc((string) ($inquiry['phone'] ?? '')) ?></li>
    <li><strong>Guests:</strong> <?= esc((string) $inquiry['guests']) ?></li>
    <li><strong>Check-In:</strong> <?= esc((string) ($inquiry['check_in'] ?? '')) ?></li>
    <li><strong>Check-Out:</strong> <?= esc((string) ($inquiry['check_out'] ?? '')) ?></li>
</ul>
<p><strong>Message:</strong></p>
<p><?= nl2br(esc((string) $inquiry['message'])) ?></p>
