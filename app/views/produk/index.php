<?php require __DIR__ . '/../templates/header.php'; ?>

<main class="main-content">
    <div class="page-header">
        <h2>Data Barang</h2>
        <p>Kelola produk inventaris yang tersimpan di database.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card gradient1">
            <div>
                <span>Total Produk</span>
                <strong><?= e($totalProduk ?? count($produk)); ?></strong>
            </div>
            <i class="fas fa-boxes-stacked"></i>
        </div>
        <div class="stat-card gradient2">
            <div>
                <span>Total Stok</span>
                <strong><?= e($totalStok ?? 0); ?></strong>
            </div>
            <i class="fas fa-cubes"></i>
        </div>
    </div>

    <div class="table-card">
        <div class="table-header">
            <div>
                <h3>Daftar Barang</h3>
                <span>Total Data : <?= count($produk); ?></span>
            </div>
            <div class="table-actions">
                <button class="btn-outline" onclick="window.print()" type="button"><i class="fas fa-print"></i> Cetak</button>
                <a href="index.php?url=produk/create" class="btn-primary"><i class="fas fa-plus"></i> Tambah Barang</a>
            </div>
        </div>

        <div class="table-container">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($produk) > 0): ?>
                        <?php $no = 1; foreach ($produk as $row): ?>
                            <?php
                            $stokClass = 'stok-normal';
                            if ((int) $row['stok'] <= 5) {
                                $stokClass = 'stok-danger';
                            } elseif ((int) $row['stok'] <= 10) {
                                $stokClass = 'stok-warning';
                            }
                            $imagePath = __DIR__ . '/../../../public/uploads/' . ($row['gambar'] ?? '');
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <?php if (!empty($row['gambar']) && is_file($imagePath)): ?>
                                        <img src="/crud_mvc/public/uploads/<?= e($row['gambar']); ?>" class="product-thumb" alt="<?= e($row['nama_produk']); ?>">
                                    <?php else: ?>
                                        <span class="no-image">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="kode">#<?= e($row['kode_produk']); ?></span></td>
                                <td><strong><?= e($row['nama_produk']); ?></strong></td>
                                <td><?= e($row['kategori_id']); ?></td>
                                <td class="harga">Rp <?= number_format((float) $row['harga'], 0, ',', '.'); ?></td>
                                <td><span class="stok-badge <?= $stokClass; ?>"><?= e($row['stok']); ?></span></td>
                                <td class="aksi">
                                    <a href="index.php?url=produk/edit/<?= e($row['id']); ?>" class="btn-edit" title="Edit"><i class="fas fa-pen"></i></a>
                                    <form id="hapus-form-<?= e($row['id']); ?>" method="POST" action="index.php?url=produk/delete/<?= e($row['id']); ?>" class="inline-form">
                                        <?= getCSRFField(); ?>
                                        <input type="hidden" name="id" value="<?= e($row['id']); ?>">
                                        <button type="submit" class="btn-delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <a href="index.php?url=produk/detail/<?= e($row['id']); ?>" class="btn-view" title="Detail"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-table">
                                    <i class="fas fa-box-open"></i>
                                    <p>Belum ada data barang</p>
                                    <a href="index.php?url=produk/create" class="btn-primary">Tambah Barang</a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

