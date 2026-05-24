<?php $bodyClass = 'login-body'; require __DIR__ . '/../templates/header.php'; ?>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <i class="fa-solid fa-box"></i>
                <h2>Sistem Manajemen Inventaris</h2>
                <p>Silakan login untuk melanjutkan</p>
            </div>
            <form method="POST" action="index.php?url=auth/login" class="login-form">
                <?= getCSRFField(); ?>
                <?php if (!empty($error)): ?>
                    <div class="login-error"><?= e($error); ?></div>
                <?php endif; ?>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?= e($username ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="remember-me">
                    <label>
                        <input type="checkbox" name="remember" <?= !empty($username) ? 'checked' : ''; ?>>
                        Remember Me
                    </label>
                </div>
                <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</button>
                <p class="auth-link">Belum punya akun? <a href="index.php?url=auth/register">Daftar di sini</a></p>
            </form>
        </div>
    </div>
