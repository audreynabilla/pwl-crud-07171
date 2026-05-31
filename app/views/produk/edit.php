<?php require __DIR__ . '/../templates/header.php'; ?>

<main class="main-content">
    <div class="page-header">
        <h2>Edit Produk</h2>
        <div class="breadcrumb">
            <a href="index.php?url=produk/index">Data Barang</a>
            <i class="fas fa-chevron-right"></i>
            <span>Edit Produk</span>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Edit Data Produk</h3>
            <a href="index.php?url=produk/index" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $message): ?>
                        <div><?= e($message); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?url=produk/update/<?= e($produk['id']); ?>" enctype="multipart/form-data" class="form-vertical">
                <?= getCSRFField(); ?>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-barcode"></i> Kode Produk</label>
                        <input type="text" name="kode_produk" value="<?= e($produk['kode_produk']); ?>" placeholder="Kosongkan untuk otomatis">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-box"></i> Nama Produk *</label>
                        <input type="text" name="nama_produk" value="<?= e($produk['nama_produk']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-tags"></i> Kategori *</label>
                        <select name="kategori_id" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach (['Aksesoris', 'Pakaian', 'Sepatu', 'Perhiasan'] as $kategori): ?>
                                <option value="<?= e($kategori); ?>" <?= $produk['kategori_id'] === $kategori ? 'selected' : ''; ?>><?= e($kategori); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-cubes"></i> Stok *</label>
                        <input type="number" name="stok" value="<?= e($produk['stok']); ?>" min="0" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-money-bill-wave"></i> Harga *</label>
                        <input type="number" name="harga" value="<?= e($produk['harga']); ?>" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-image"></i> Gambar Saat Ini</label>
                        <?php $imagePath = __DIR__ . '/../../../public/uploads/' . ($produk['gambar'] ?? ''); ?>
                        <?php if (!empty($produk['gambar']) && is_file($imagePath)): ?>
                            <div class="image-preview"><img src="/crud/public/uploads/<?= e($produk['gambar']); ?>" alt="<?= e($produk['nama_produk']); ?>"></div>
                        <?php else: ?>
                            <p><em>Tidak ada gambar</em></p>
                        <?php endif; ?>
                        <input type="file" name="gambar" accept="image/jpeg,image/png,image/gif">
                        <small>Kosongkan jika tidak ingin mengubah gambar.</small>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="reset" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../templates/footer.php'; ?>
