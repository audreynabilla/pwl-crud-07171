<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/koneksi.php';

if (!in_array($_SERVER['REQUEST_METHOD'], ['DELETE', 'POST'], true)) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'Method tidak valid. Gunakan DELETE atau POST.',
    ]);
    exit;
}

function getRequestData()
{
    $rawInput = file_get_contents('php://input');
    $json = json_decode($rawInput, true);
    if (is_array($json)) {
        return $json;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        parse_str($rawInput, $deleteData);
        return $deleteData;
    }

    return $_POST;
}

function getProdukById(PDO $conn, $id)
{
    $stmt = $conn->prepare('SELECT * FROM produk WHERE id = ? LIMIT 1');
    $stmt->execute([(int) $id]);

    return $stmt->fetch();
}

$data = getRequestData();
$id = (int) ($data['id'] ?? $_GET['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'ID produk wajib diisi.',
    ]);
    exit;
}

try {
    $produk = getProdukById($conn, $id);
    if (!$produk) {
        http_response_code(404);
        echo json_encode([
            'status' => false,
            'message' => 'Produk tidak ditemukan.',
        ]);
        exit;
    }

    $stmt = $conn->prepare('DELETE FROM produk WHERE id = ?');
    $stmt->execute([$id]);

    http_response_code(200);
    echo json_encode([
        'status' => true,
        'message' => 'Data produk berhasil dihapus.',
        'data' => [
            'id' => $id,
            'kode_produk' => $produk['kode_produk'],
            'nama_produk' => $produk['nama_produk'],
        ],
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Gagal menghapus data produk.',
        'error' => $e->getMessage(),
    ]);
}

