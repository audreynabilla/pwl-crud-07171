<?php
ob_start(); // mencegah error headers already sent

session_start();
include 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Set cookie (/crud/)
        if (isset($_POST['remember'])) {
            setcookie('username', $username, time() + (86400 * 7), '/crud/');
        } else {
            setcookie('username', '', time() - 3600, '/crud/');
        }

        header("Location: index.php?page=data_barang");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
    <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login Sistem Inventaris</title>
            <link rel="stylesheet" href="css/style.css">
            <link rel="stylesheet" href="css/login.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        </head>
        <body class="login-body">
            <div class="login-wrapper">
                <div class="login-card">
                    <div class="login-header">
                        <i class="fa-solid fa-box"></i>
                        <h2>Sistem Manajemen Inventaris</h2>
                        <p>Silakan login untuk melanjutkan</p>
                    </div>
                    <form method="POST" class="login-form">
                        <?php if ($error): ?>
                            <div class="login-error"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" value="<?php echo isset($_COOKIE['username']) ? htmlspecialchars($_COOKIE['username']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" required>
                        </div>
                        <div class="remember-me">
                            <label>
                                <input type="checkbox" name="remember">
                                Remember Me
                            </label>
                        </div>
                        <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</button>
                        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                    </form>
                </div>
            </div>
        </body>
    </html>
<?php
ob_end_flush();
?>