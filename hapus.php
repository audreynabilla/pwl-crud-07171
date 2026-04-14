<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil nama gambar sebelum hapus
$stmt = $conn->prepare("SELECT gambar FROM produk WHERE id = ?");
$stmt->execute([$id]);
$produk = $stmt->fetch(PDO::FETCH_ASSOC);

if ($produk && !empty($produk['gambar']) && file_exists('uploads/' . $produk['gambar'])) {
    unlink('uploads/' . $produk['gambar']);
}

$stmt = $conn->prepare("DELETE FROM produk WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?page=data_barang");
exit;
?>