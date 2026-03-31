<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';
?>

<?php
	include 'koneksi.php';
	$page_title = "Detail Produk";
	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	$stmt = $conn->prepare("SELECT * FROM produk WHERE id = ?");
	$stmt->execute([$id]);
	$produk = $stmt->fetch(PDO::FETCH_ASSOC);


	if (!$produk) {
		$_SESSION['pesan'] = "Produk tidak ditemukan!";
		$_SESSION['tipe']  = "error";
		header("Location: index.php?page=data_barang");
		exit();
	}
?>

<?php include 'includes/header.php'; ?>

<div class="content-wrapper">
	<main class="main-content">
		<div class="page-header">
			<h2>Detail Produk</h2>
			<div class="breadcrumb">
				<a href="index.php?page=data_barang">Data Barang</a>
				<i class="fas fa-chevron-right"></i>
				<span>Detail Produk</span>
			</div>
		</div>

		<div class="content">
			<div class="card">
				<div class="card-header">
					<h3>Informasi Lengkap Produk</h3>
					<a href="index.php?page=data_barang" class="btn btn-secondary">
						<i class="fas fa-arrow-left"></i> Kembali
					</a>
				</div>

				<div class="card-body">
					<form class="form-vertical">
						<div class="form-row">
							<div class="form-group">
								<label>
									<i class="fas fa-barcode"></i> Kode Produk
								</label>
								<input
									type="text"
									value="<?php echo htmlspecialchars($produk['kode_produk']); ?>"
									disabled
								>
							</div>

							<div class="form-group">
								<label>
									<i class="fas fa-box"></i> Nama Produk
								</label>
								<input
									type="text"
									value="<?php echo htmlspecialchars($produk['nama_produk']); ?>"
									disabled
								>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group">
								<label>
									<i class="fas fa-tags"></i> Kategori
								</label>
								<input
									type="text"
									value="<?php echo htmlspecialchars($produk['kategori_id']); ?>"
									disabled
								>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group">
								<label>
									<i class="fas fa-money-bill-wave"></i> Harga
								</label>
								<input
									type="text"
									value="Rp <?php echo number_format($produk['harga'],0,',','.'); ?>"
									disabled
								>
							</div>

							<div class="form-group">
								<label>
									<i class="fas fa-cubes"></i> Stok
								</label>
								<input
									type="number"
									value="<?php echo $produk['stok']; ?>"
									disabled
								>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</main>
</div>
