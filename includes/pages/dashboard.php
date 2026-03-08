dashboard

<?php
	include __DIR__ . '/../../koneksi.php';

	if (!isset($conn)) {
		die("Koneksi database tidak ditemukan");
	}

	$qBarang = $conn->query("SELECT COUNT(*) as total FROM produk");
	$totalBarang = $qBarang->fetch(PDO::FETCH_ASSOC)['total'];

	$qStok = $conn->query("SELECT COUNT(*) as total FROM produk WHERE stok < 10");
	$totalStokRendah = $qStok->fetch(PDO::FETCH_ASSOC)['total'];

	$qListStok = $conn->query("SELECT * FROM produk WHERE stok < 10 ORDER BY stok ASC LIMIT 5");
	$dataStok = $qListStok->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard-container">
	<div class="dashboard-header">
		<h2>Dashboard Inventaris</h2>
		<p>Ringkasan data inventaris barang</p>
	</div>

	<div class="stats-grid">

		<div class="stat-card gradient1">
			<div class="stat-info">
				<h4>Total Produk</h4>
				<h2><?php echo $totalBarang; ?></h2>
				<span>Data Inventaris</span>
			</div>
			<i class="fas fa-box"></i>
		</div>

		<div class="stat-card gradient2">
			<div class="stat-info">
				<h4>Stok Menipis</h4>
				<h2><?php echo $totalStokRendah; ?></h2>
				<span>Perlu Restock</span>
			</div>
			<i class="fas fa-exclamation-triangle"></i>
		</div>

		<div class="stat-card gradient3">
			<div class="stat-info">
				<h4>Stok Aman</h4>
				<h2><?php echo $totalBarang - $totalStokRendah; ?></h2>
				<span>Produk Aman</span>
			</div>
			<i class="fas fa-check-circle"></i>
		</div>

		<div class="stat-card gradient4">
			<div class="stat-info">
				<h4>Total Kategori</h4>
				<h2>1</h2>
				<span>Kategori Aktif</span>
			</div>
			<i class="fas fa-layer-group"></i>
		</div>
	</div>


	<div class="dashboard-grid">
		<div class="dashboard-card">
			<h3>Stok Menipis</h3>

			<div class="stock-list">
				<?php if (count($dataStok) > 0): ?>
					<?php foreach ($dataStok as $row): ?>

						<div class="stock-item">
							<div>
								<strong><?php echo $row['nama_produk']; ?></strong>
								<span>Kode : <?php echo $row['kode_produk']; ?></span>
							</div>

							<div class="stock-badge">
								<?php echo $row['stok']; ?>
							</div>
						</div>

					<?php endforeach; ?>
				<?php else: ?>

					<div class="empty">
						<i class="fas fa-check-circle"></i>
						<p>Tidak ada stok yang menipis</p>
					</div>
				<?php endif; ?>
			</div>

		</div>

		<div class="dashboard-card">
			<h3>Aktivitas Sistem</h3>
			<ul class="activity">
				<li>
					<i class="fas fa-circle"></i>
					Login Admin
					<span>Baru saja</span>
				</li>

				<li>
					<i class="fas fa-circle"></i>
					Update stok produk
					<span>15 menit lalu</span>
				</li>

				<li>
					<i class="fas fa-circle"></i>
					Tambah produk baru
					<span>1 jam lalu</span>
				</li>

				<li>
					<i class="fas fa-circle"></i>
					Backup database
					<span>Kemarin</span>
				</li>
			</ul>
		</div>
	</div>s
</div>