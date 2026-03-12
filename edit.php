<?php
	include 'koneksi.php';

	$page_title = "Edit Produk";
	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	$stmt = $conn->prepare("SELECT * FROM produk WHERE id = ?");
	$stmt->execute([$id]);
	$produk = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!$produk) {

		$_SESSION['pesan'] = "Produk tidak ditemukan!";
		$_SESSION['tipe'] = "error";

		header("Location: index.php?page=data_barang");
		exit();
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$nama_produk = $_POST['nama_produk'];
		$kategori_id = $_POST['kategori_id'];
		$stok        = $_POST['stok'];
		$harga       = $_POST['harga'];

		$stmt = $conn->prepare("
			UPDATE produk SET
				nama_produk = ?,
				kategori_id = ?,
				stok = ?,
				harga = ?
			WHERE id = ?
		");

		if ($stmt->execute([$nama_produk,$kategori_id,$stok,$harga,$id])) {

			$_SESSION['pesan'] = "Produk berhasil diperbarui!";
			$_SESSION['tipe']  = "success";

			header("Location: index.php?page=data_barang");
			exit();

		} else {

			$_SESSION['pesan'] = "Gagal memperbarui produk!";
			$_SESSION['tipe']  = "error";

		}
	}
?>

<?php include 'includes/header.php'; ?>
<div class="content-wrapper">
	<?php include 'includes/menu.php'; ?>
	<main class="main-content">
		<div class="page-header">
			<h2>Edit Produk</h2>
			<div class="breadcrumb">
				<a href="index.php">Home</a>
				<i class="fas fa-chevron-right"></i>
				<a href="index.php?page=data_barang">Data Barang</a>
				<i class="fas fa-chevron-right"></i>
				<span>Edit Produk</span>
			</div>
		</div>

		<div class="content">
			<div class="card">
				<div class="card-header">
					<h3>Edit Data Produk</h3>
					<a href="index.php?page=data_barang" class="btn btn-secondary">
						<i class="fas fa-arrow-left"></i> Kembali
					</a>
				</div>

				<div class="card-body">
					<form method="POST" class="form-vertical">
						<div class="form-row">
							<div class="form-group">
								<label>
									<i class="fas fa-barcode"></i> Kode Produk
								</label>
								<input
									type="text"
									value="<?php echo htmlspecialchars($produk['kode_produk']); ?>"
									readonly
									style="background:#f3f4f6; cursor:not-allowed;"
								>
							</div>

							<div class="form-group">
								<label>
									<i class="fas fa-box"></i> Nama Produk *
								</label>
								<input
									type="text"
									name="nama_produk"
									value="<?php echo htmlspecialchars($produk['nama_produk']); ?>"
									required
								>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group">
								<label>
									<i class="fas fa-tags"></i> Kategori *
								</label>
								<select name="kategori_id" required>
									<option value="">Pilih Kategori</option>
									<option value="Aksesoris" <?php echo $produk['kategori_id']=='Aksesoris'?'selected':''; ?>>Aksesoris</option>
									<option value="Pakaian" <?php echo $produk['kategori_id']=='Pakaian'?'selected':''; ?>>Pakaian</option>
									<option value="Sepatu" <?php echo $produk['kategori_id']=='Sepatu'?'selected':''; ?>>Sepatu</option>
									<option value="Perhiasan" <?php echo $produk['kategori_id']=='Perhiasan'?'selected':''; ?>>Perhiasan</option>
								</select>
							</div>

							<div class="form-group">
								<label>
									<i class="fas fa-cubes"></i> Stok *
								</label>
								<input
									type="number"
									name="stok"
									value="<?php echo $produk['stok']; ?>"
									min="0"
									required
								>
							</div>

							<div class="form-group">
								<label>
									<i class="fas fa-money-bill-wave"></i> Harga *
								</label>
								<input
									type="number"
									name="harga"
									value="<?php echo $produk['harga']; ?>"
									min="0"
									required
								>
							</div>
						</div>

						<div class="form-actions">
							<button type="reset" class="btn btn-secondary">
								<i class="fas fa-redo"></i> Reset
							</button>
							<button type="submit" class="btn btn-primary">
								<i class="fas fa-save"></i> Simpan Perubahan
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</main>
</div>

<?php include 'includes/footer.php'; ?>