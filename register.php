<?php
	session_start();
	include 'koneksi.php';

	$error = '';
	$success = '';

	if (isset($_POST['daftar'])) {

		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
		$confirm  = trim($_POST['confirm']);

		if (empty($username) || empty($password) || empty($confirm)) {
			$error = "Semua field wajib diisi!";
		}
		elseif ($password !== $confirm) {
			$error = "Konfirmasi password tidak sama!";
		}
		else {

			$cek = $conn->prepare("SELECT * FROM users WHERE username = ?");
			$cek->execute([$username]);

			if ($cek->rowCount() > 0) {
				$error = "Username sudah digunakan!";
			}
			else {

				$hash = password_hash($password, PASSWORD_DEFAULT);

				$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");

				if ($stmt->execute([$username, $hash])) {
					$success = "Akun berhasil dibuat!";
				}
				else {
					$error = "Terjadi kesalahan!";
				}
			}
		}
	}
?>

<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register Sistem Inventaris</title>

	<link rel="stylesheet" href="css/style.css">

	<link rel="stylesheet"
	href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="register-body">

	<div class="register-wrapper">
		<div class="register-card">

			<div class="register-header">
				<i class="fas fa-user-plus"></i>
				<h2>Sistem Manajemen Inventaris</h2>
				<p>Silakan daftar untuk membuat akun</p>
			</div>

			<form method="POST" class="register-form">

				<?php if ($error): ?>
					<div class="register-error">
						<?php echo $error; ?>
					</div>
				<?php endif; ?>

				<?php if ($success): ?>
					<div class="register-success">
						<?php echo $success; ?>
					</div>
				<?php endif; ?>

				<div class="form-group">
					<label>Username</label>
					<input type="text" name="username" required>
				</div>

				<div class="form-group">
					<label>Password</label>
					<input type="password" name="password" required>
				</div>

				<div class="form-group">
					<label>Konfirmasi Password</label>
					<input type="password" name="confirm" required>
				</div>

				<button type="submit" name="daftar" class="btn-register">
					<i class="fas fa-user-plus"></i>
					Daftar
				</button>

				<div class="register-footer">
					Sudah punya akun?
					<a href="login.php">Login di sini</a>
				</div>

			</form>

		</div>
	</div>

</body>
</html>