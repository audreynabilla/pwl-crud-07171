<?php require __DIR__ . '/../templates/header.php'; ?>

<main class="main-content">
    <div class="page-header">
        <h2>Detail Produk</h2>
        <div class="breadcrumb">
            <a href="index.php?url=produk/index">Data Barang</a>
            <i class="fas fa-chevron-right"></i>
            <span>Detail Produk</span>
        </div>
    </div>

    <div class="card detail-card">
        <div class="card-header">
            <h3>Informasi Lengkap Produk</h3>
            <a href="index.php?url=produk/index" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        <div class="card-body detail-layout">
            <div class="detail-image">
                <?php $imagePath = __DIR__ . '/../../../public/uploads/' . ($produk['gambar'] ?? ''); ?>
                <?php if (!empty($produk['gambar']) && is_file($imagePath)): ?>
                    <img src="/crud_mvc/public/uploads/<?= e($produk['gambar']); ?>" alt="<?= e($produk['nama_produk']); ?>">
                <?php else: ?>
                    <div class="empty-image"><i class="fas fa-image"></i><span>Tidak ada gambar</span></div>
                <?php endif; ?>
            </div>

            <div class="detail-fields">
                <div class="detail-item">
                    <span>Kode Produk</span>
                    <strong><?= e($produk['kode_produk']); ?></strong>
                </div>
                <div class="detail-item">
                    <span>Nama Produk</span>
                    <strong><?= e($produk['nama_produk']); ?></strong>
                </div>
                <div class="detail-item">
                    <span>Kategori</span>
                    <strong><?= e($produk['kategori_id']); ?></strong>
                </div>
                <div class="detail-item">
                    <span>Harga</span>
                    <strong>Rp <?= number_format((float) $produk['harga'], 0, ',', '.'); ?></strong>
                </div>
                <div class="detail-item">
                    <span>Stok</span>
                    <strong><?= e($produk['stok']); ?></strong>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>
