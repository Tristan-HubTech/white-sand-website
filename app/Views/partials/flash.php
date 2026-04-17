<?php if (session()->has('success')): ?>
    <div class="alert alert-success"><?= esc((string) session('success')) ?></div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-error"><?= esc((string) session('error')) ?></div>
<?php endif; ?>

<?php if (session()->has('warning')): ?>
    <div class="alert alert-warning"><?= esc((string) session('warning')) ?></div>
<?php endif; ?>
