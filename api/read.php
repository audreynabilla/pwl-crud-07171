<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'Method tidak valid. Gunakan GET.',
    ]);
    exit;
}

try {
    $stmt = $conn->prepare('SELECT * FROM produk ORDER BY id DESC');
    $stmt->execute();
    $produk = $stmt->fetchAll();

    http_response_code(200);
    echo json_encode([
        'status' => true,
        'message' => 'Data produk berhasil diambil.',
        'data' => $produk,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Gagal mengambil data produk.',
        'error' => $e->getMessage(),
    ]);
}

