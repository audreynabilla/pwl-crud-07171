<?php

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';
?>

<?php
include __DIR__ . '/../../koneksi.php';

$query = $conn->query("SELECT * FROM produk ORDER BY id DESC");
$result = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="table-card">

    <div class="table-header">

        <div>
            <h3>Daftar Barang</h3>
            <span>Total Data : <?php echo count($result); ?></span>
        </div>

        <div class="table-actions">

            <button class="btn-outline" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak
            </button>

            <a href="tambah.php" class="btn-primary">
                <i class="fas fa-plus"></i> Tambah Barang
            </a>

        </div>

    </div>


    <div class="table-container">

        <table class="inventory-table">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                </tr>
            </thead>

            <tbody>

            <?php if(count($result) > 0): ?>
                <?php $no = 1; foreach($result as $row): ?>

                <tr>
                    <td><?php echo $no++; ?></td>
                    <td>
                        <span class="kode">
                            #<?php echo htmlspecialchars($row['kode_produk']); ?>
                        </span>
                    </td>

                    <td>
                        <strong><?php echo htmlspecialchars($row['nama_produk']); ?></strong>
                    </td>

                    <td>
                    <?php echo htmlspecialchars($row['kategori_id']); ?>
                    </td>

                    <td class="harga">
                        Rp <?php echo number_format($row['harga'],0,",","."); ?>
                    </td>

                    <td>
                        <?php
                        $class = "stok-normal";

                        if($row['stok'] <= 5){
                            $class = "stok-danger";
                        }
                        elseif($row['stok'] <= 10){
                            $class = "stok-warning";
                        }
                        ?>

                        <span class="stok-badge <?php echo $class; ?>">
                            <?php echo $row['stok']; ?>
                        </span>
                    </td>

                    <td class="aksi">
                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-edit">
                            <i class="fas fa-pen"></i>
                        </a>
                        <a href="hapus.php?id=<?php echo $row['id']; ?>" 
                        class="btn-delete"
                        onclick="return confirm('Yakin hapus barang ini?')">
                            <i class="fas fa-trash"></i>
                        </a>
                        <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn-view">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="6">
                        <div class="empty-table">
                            <i class="fas fa-box-open"></i>
                            <p style="margin-bottom:15px;">Belum ada data barang</p>
                            <a href="tambah.php" class="btn-primary">
                                Tambah Barang
                            </a>
                        </div>
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>