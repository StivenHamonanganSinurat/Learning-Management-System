<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LMS Stikes Nauli Husada</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>LMS Stikes</h2>
                <p>Silakan login ke akun Anda</p>
            </div>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('login/process') ?>" method="post">
                <div class="form-group">
                    <label for="username">Email / NIM / NIDN</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?= old('username') ?>" required autofocus placeholder="Masukkan Email atau NIM/NIDN">
                    <?php if (session('errors.username')) : ?>
                        <small style="color: #d32f2f; margin-top: 5px; display: block;"><?= session('errors.username') ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Masukkan password">
                    <?php if (session('errors.password')) : ?>
                        <small style="color: #d32f2f; margin-top: 5px; display: block;"><?= session('errors.password') ?></small>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>
            </form>


        </div>
    </div>
</body>
</html>
