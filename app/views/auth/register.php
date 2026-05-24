<?php $bodyClass = 'register-body'; require __DIR__ . '/../templates/header.php'; ?>
    <div class="register-wrapper">
        <div class="register-card">
            <div class="register-header">
                <i class="fas fa-user-plus"></i>
                <h2>Sistem Manajemen Inventaris</h2>
                <p>Silakan daftar untuk membuat akun</p>
            </div>

            <form method="POST" action="index.php?url=auth/register" class="register-form">
                <?= getCSRFField(); ?>
                <?php if (!empty($error)): ?>
                    <div class="register-error"><?= e($error); ?></div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class="register-success"><?= e($success); ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?= e($username ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="confirm" required>
                </div>

                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus"></i> Daftar
                </button>

                <div class="register-footer">
                    Sudah punya akun? <a href="index.php?url=auth/login">Login di sini</a>
                </div>
            </form>
        </div>
    </div>
<?php require __DIR__ . '/../templates/footer.php'; ?>
