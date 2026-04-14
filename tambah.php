<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

$page_title = "Tambah Produk";

// Inisialisasi variabel untuk retain input
$kode_produk = '';
$nama_produk = '';
$kategori_id = '';
$harga = '';
$stok = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $kode_produk = trim($_POST['kode_produk']);
    $nama_produk = trim($_POST['nama_produk']);
    $kategori_id = trim($_POST['kategori_id']);
    $harga = trim($_POST['harga']);
    $stok = trim($_POST['stok']);
    
    // ========== VALIDASI SISI SERVER ==========
    $errors = [];
    
    // 1. Nama produk tidak boleh kosong
    if (empty($nama_produk)) {
        $errors[] = "Nama produk wajib diisi.";
    }
    
    // 2. Harga harus numerik dan tidak negatif
    if (!is_numeric($harga) || $harga < 0) {
        $errors[] = "Harga harus berupa angka positif.";
    }
    
    // 3. Stok harus numerik dan tidak negatif
    if (!is_numeric($stok) || $stok < 0) {
        $errors[] = "Stok harus berupa angka positif.";
    }
    
    // 4. Kategori harus dipilih
    if (empty($kategori_id)) {
        $errors[] = "Kategori wajib dipilih.";
    }
    
    // ========== PROSES UPLOAD GAMBAR ==========
    $gambar_name = null;
    $upload_dir = 'uploads/';
    
    // Pastikan folder uploads ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_size = $_FILES['gambar']['size'];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file_info, $file_tmp);
        finfo_close($file_info);
        
        // Validasi tipe file (hanya gambar)
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mime_type, $allowed_mimes)) {
            $errors[] = "File harus berupa gambar (JPG, PNG, GIF).";
        }
        
        // Validasi ukuran (max 2MB)
        if ($file_size > 2 * 1024 * 1024) {
            $errors[] = "Ukuran gambar maksimal 2MB.";
        }
        
        // Jika lolos validasi, buat nama unik dan pindahkan
        if (empty($errors)) {
            $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $gambar_name = uniqid() . '.' . $ext;
            $destination = $upload_dir . $gambar_name;
            
            if (!move_uploaded_file($file_tmp, $destination)) {
                $errors[] = "Gagal mengupload gambar.";
                $gambar_name = null;
            }
        }
    }
    
    // Jika ada error, tampilkan
    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    } else {
        // Generate kode produk jika kosong
        if (empty($kode_produk)) {
            $prefix = "PRD";
            $query = $conn->query("SELECT MAX(CAST(SUBSTRING(kode_produk, 4) AS UNSIGNED)) AS max_code FROM produk WHERE kode_produk LIKE '$prefix%'");
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $next = ($row['max_code'] ?? 0) + 1;
            $kode_produk = $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
        } else {
            // Cek apakah kode produk sudah ada
            $check = $conn->prepare("SELECT id FROM produk WHERE kode_produk = ?");
            $check->execute([$kode_produk]);
            if ($check->rowCount() > 0) {
                $error = "Kode produk sudah digunakan!";
            }
        }
        
        if (empty($error)) {
            // Simpan ke database dengan prepared statement
            $stmt = $conn->prepare("
                INSERT INTO produk (kode_produk, nama_produk, kategori_id, harga, stok, gambar)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$kode_produk, $nama_produk, $kategori_id, $harga, $stok, $gambar_name])) {
                $_SESSION['pesan'] = "Produk berhasil ditambahkan!";
                $_SESSION['tipe']  = "success";
                header("Location: index.php?page=data_barang");
                exit();
            } else {
                $error = "Gagal menyimpan data ke database.";
                // Hapus file gambar jika sudah terupload tapi gagal simpan
                if ($gambar_name && file_exists($upload_dir . $gambar_name)) {
                    unlink($upload_dir . $gambar_name);
                }
            }
        }
    }
}

include 'includes/header.php';
?>

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
                    <?php if ($error): ?>
                        <div class="alert alert-danger" style="background:#f8d7da; color:#721c24; padding:10px; border-radius:8px; margin-bottom:20px;">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data" class="form-vertical">
                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="fas fa-barcode"></i> Kode Produk</label>
                                <input type="text" name="kode_produk" value="<?php echo htmlspecialchars($kode_produk); ?>" placeholder="Kosongkan untuk otomatis">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-box"></i> Nama Produk *</label>
                                <input type="text" name="nama_produk" value="<?php echo htmlspecialchars($nama_produk); ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="fas fa-tags"></i> Kategori *</label>
                                <select name="kategori_id" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Aksesoris" <?php echo $kategori_id == 'Aksesoris' ? 'selected' : ''; ?>>Aksesoris</option>
                                    <option value="Pakaian" <?php echo $kategori_id == 'Pakaian' ? 'selected' : ''; ?>>Pakaian</option>
                                    <option value="Sepatu" <?php echo $kategori_id == 'Sepatu' ? 'selected' : ''; ?>>Sepatu</option>
                                    <option value="Perhiasan" <?php echo $kategori_id == 'Perhiasan' ? 'selected' : ''; ?>>Perhiasan</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="fas fa-money-bill-wave"></i> Harga *</label>
                                <input type="number" name="harga" min="0" value="<?php echo htmlspecialchars($harga); ?>" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-cubes"></i> Stok *</label>
                                <input type="number" name="stok" min="0" value="<?php echo htmlspecialchars($stok); ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="fas fa-image"></i> Gambar Produk</label>
                                <input type="file" name="gambar" accept="image/jpeg,image/png,image/gif">
                                <small style="color:#666;">Format: JPG, PNG, GIF (Max 2MB)</small>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="reset" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Produk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

