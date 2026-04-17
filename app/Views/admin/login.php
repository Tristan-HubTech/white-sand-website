<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/site.css') ?>">
</head>
<body>
<main class="site-main auth-main">
    <div class="container auth-wrap">
        <div class="card auth-card">
            <h1>Admin Login</h1>
            <p class="lead">Role-based login for owner admin and staff accounts.</p>
            <?= view('partials/flash') ?>
            <?= validation_list_errors() ?>
            <form action="<?= base_url('/admin/authenticate') ?>" method="post" class="form-grid">
                <?= csrf_field() ?>
                <label>
                    Email
                    <input type="email" name="email" value="<?= old('email') ?>" required>
                </label>
                <label>
                    Password
                    <input type="password" name="password" required>
                </label>
                <button class="btn btn-primary" type="submit">Login</button>
            </form>
            <p class="hint">Default seed user: admin@whitesandresort.local / Admin@12345</p>
        </div>
    </div>
</main>
<script src="<?= base_url('assets/js/site.js') ?>"></script>
</body>
</html>
