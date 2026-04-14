<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = trim($_POST['nama_produk']);
    $kategori_id = trim($_POST['kategori_id']);
    $stok = trim($_POST['stok']);
    $harga = trim($_POST['harga']);
    
    // ========== VALIDASI ==========
    $errors = [];
    if (empty($nama_produk)) $errors[] = "Nama produk wajib diisi.";
    if (!is_numeric($harga) || $harga < 0) $errors[] = "Harga harus angka positif.";
    if (!is_numeric($stok) || $stok < 0) $errors[] = "Stok harus angka positif.";
    if (empty($kategori_id)) $errors[] = "Kategori wajib dipilih.";
    
    // Proses upload gambar baru jika ada
    $gambar_name = $produk['gambar']; // default pakai gambar lama
    $upload_dir = 'uploads/';
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_size = $_FILES['gambar']['size'];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file_info, $file_tmp);
        finfo_close($file_info);
        
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mime_type, $allowed_mimes)) {
            $errors[] = "File harus berupa gambar (JPG, PNG, GIF).";
        }
        if ($file_size > 2 * 1024 * 1024) {
            $errors[] = "Ukuran gambar maksimal 2MB.";
        }
        
        if (empty($errors)) {
            $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $new_gambar = uniqid() . '.' . $ext;
            $destination = $upload_dir . $new_gambar;
            
            if (move_uploaded_file($file_tmp, $destination)) {
                // Hapus gambar lama jika ada
                if ($produk['gambar'] && file_exists($upload_dir . $produk['gambar'])) {
                    unlink($upload_dir . $produk['gambar']);
                }
                $gambar_name = $new_gambar;
            } else {
                $errors[] = "Gagal mengupload gambar.";
            }
        }
    }
    
    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    } else {
        $stmt = $conn->prepare("
            UPDATE produk SET 
                nama_produk = ?,
                kategori_id = ?,
                stok = ?,
                harga = ?,
                gambar = ?
            WHERE id = ?
        ");
        
        if ($stmt->execute([$nama_produk, $kategori_id, $stok, $harga, $gambar_name, $id])) {
            $_SESSION['pesan'] = "Produk berhasil diperbarui!";
            $_SESSION['tipe']  = "success";
            header("Location: index.php?page=data_barang");
            exit();
        } else {
            $error = "Gagal memperbarui produk!";
        }
    }
}

include 'includes/header.php';
?>

<div class="content-wrapper">
    <main class="main-content">
        <div class="page-header">
            <h2>Edit Produk</h2>
            <div class="breadcrumb">
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
                    <?php if ($error): ?>
                        <div class="alert alert-danger" style="background:#f8d7da; color:#721c24; padding:10px; border-radius:8px; margin-bottom:20px;">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data" class="form-vertical">
                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="fas fa-barcode"></i> Kode Produk</label>
                                <input type="text" value="<?php echo htmlspecialchars($produk['kode_produk']); ?>" readonly style="background:#f3f4f6;">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-box"></i> Nama Produk *</label>
                                <input type="text" name="nama_produk" value="<?php echo htmlspecialchars($produk['nama_produk']); ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="fas fa-tags"></i> Kategori *</label>
                                <select name="kategori_id" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Aksesoris" <?php echo $produk['kategori_id'] == 'Aksesoris' ? 'selected' : ''; ?>>Aksesoris</option>
                                    <option value="Pakaian" <?php echo $produk['kategori_id'] == 'Pakaian' ? 'selected' : ''; ?>>Pakaian</option>
                                    <option value="Sepatu" <?php echo $produk['kategori_id'] == 'Sepatu' ? 'selected' : ''; ?>>Sepatu</option>
                                    <option value="Perhiasan" <?php echo $produk['kategori_id'] == 'Perhiasan' ? 'selected' : ''; ?>>Perhiasan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-cubes"></i> Stok *</label>
                                <input type="number" name="stok" value="<?php echo $produk['stok']; ?>" min="0" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-money-bill-wave"></i> Harga *</label>
                                <input type="number" name="harga" value="<?php echo $produk['harga']; ?>" min="0" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="fas fa-image"></i> Gambar Saat Ini</label>
                                <?php if ($produk['gambar'] && file_exists('uploads/' . $produk['gambar'])): ?>
                                    <div>
                                        <img src="uploads/<?php echo $produk['gambar']; ?>" style="max-width:100px; max-height:100px; border-radius:8px;">
                                    </div>
                                <?php else: ?>
                                    <p><em>Tidak ada gambar</em></p>
                                <?php endif; ?>
                                <input type="file" name="gambar" accept="image/jpeg,image/png,image/gif" style="margin-top:10px;">
                                <small>Kosongkan jika tidak ingin mengubah gambar</small>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="reset" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

