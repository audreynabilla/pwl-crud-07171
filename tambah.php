<?php
	include 'koneksi.php';

	$page_title = "Tambah Produk";

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$kode_produk = $_POST['kode_produk'];
		$nama_produk = $_POST['nama_produk'];
		$kategori_id = $_POST['kategori_id'];
		$harga       = $_POST['harga'];
		$stok        = $_POST['stok'];

		if (empty($kode_produk)) {
			$prefix = "PRD";
			$query = $conn->query("
				SELECT MAX(SUBSTRING(kode_produk,4)) AS max_code
				FROM produk
				WHERE kode_produk LIKE '$prefix%'
			");
			$row = $query->fetch(PDO::FETCH_ASSOC);
			$next = ($row['max_code'] ?? 0) + 1;
			$kode_produk = $prefix . str_pad($next,3,'0',STR_PAD_LEFT);
		}

		$check = $conn->prepare("SELECT id FROM produk WHERE kode_produk = ?");
		$check->execute([$kode_produk]);

		if ($check->rowCount() > 0) {
			$_SESSION['pesan'] = "Kode produk sudah digunakan!";
			$_SESSION['tipe']  = "error";
		} else {
			$stmt = $conn->prepare("
				INSERT INTO produk
				(kode_produk,nama_produk,kategori_id,harga,stok)
				VALUES
				(?,?,?,?,?)
			");

			if ($stmt->execute([$kode_produk,$nama_produk,$kategori_id,$harga,$stok])) {
				$_SESSION['pesan'] = "Produk berhasil ditambahkan!";
				$_SESSION['tipe']  = "success";
				header("Location: index.php?page=data_barang");
				exit();
			} else {
				$_SESSION['pesan'] = "Gagal menambahkan produk";
				$_SESSION['tipe']  = "error";
			}
		}
	}
?>

<?php include 'includes/header.php'; ?>

<div class="content-wrapper">
	<main class="main-content">
		<div class="page-header">
			<h2>Tambah Produk Baru</h2>
			<div class="breadcrumb">
				<a href="index.php?page=data_barang">Data Barang</a>
				<i class="fas fa-chevron-right"></i>
				<span>Tambah Produk</span>
			</div>
		</div>

		<div class="content">
			<div class="card">
				<div class="card-header">
					<h3>Form Tambah Produk</h3>
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
									name="kode_produk"
									placeholder="Kosongkan untuk otomatis"
								>
							</div>
							<div class="form-group">
								<label>
									<i class="fas fa-box"></i> Nama Produk *
								</label>
								<input
									type="text"
									name="nama_produk"
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
									<option>Pilih Kategori</option>
									<option>Aksesoris</option>
									<option>Pakaian</option>
									<option>Sepatu</option>
									<option>Perhiasan</option>
								</select>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group">
								<label>
									<i class="fas fa-money-bill-wave"></i> Harga *
								</label>
								<input
									type="number"
									name="harga"
									min="0"
									required
								>
							</div>
							<div class="form-group">
								<label>
									<i class="fas fa-cubes"></i> Stok *
								</label>
								<input
									type="number"
									name="stok"
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
								<i class="fas fa-save"></i> Simpan Produk
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</main>
</div>

