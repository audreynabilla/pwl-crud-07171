<?php
session_start();
include 'csrf.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("Akses tidak sah (CSRF)");
    }

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    // Ambil nama gambar sebelum hapus
    $stmt = $conn->prepare("SELECT gambar FROM produk WHERE id = ?");
    $stmt->execute([$id]);
    $produk = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produk && !empty($produk['gambar']) && file_exists('uploads/' . $produk['gambar'])) {
        unlink('uploads/' . $produk['gambar']);
    }

    $stmt = $conn->prepare("DELETE FROM produk WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['pesan'] = "Produk berhasil dihapus!";
    $_SESSION['tipe'] = "success";
    header("Location: index.php?page=data_barang");
    exit;
} else {
    // Jika bukan method POST, redirect
    header("Location: index.php?page=data_barang");
    exit;
}
?>