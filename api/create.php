<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'Method tidak valid. Gunakan POST.',
    ]);
    exit;
}

function getRequestData()
{
    $json = json_decode(file_get_contents('php://input'), true);
    if (is_array($json)) {
        return $json;
    }

    return $_POST;
}

function generateKodeProduk(PDO $conn)
{
    $stmt = $conn->prepare("
        SELECT MAX(CAST(SUBSTRING(kode_produk, 4) AS UNSIGNED)) AS max_code
        FROM produk
        WHERE kode_produk LIKE ?
    ");
    $stmt->execute(['PRD%']);
    $row = $stmt->fetch();
    $next = ((int) ($row['max_code'] ?? 0)) + 1;

    return 'PRD' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
}

function isKodeProdukUnik(PDO $conn, $kodeProduk)
{
    $stmt = $conn->prepare('SELECT id FROM produk WHERE kode_produk = ? LIMIT 1');
    $stmt->execute([$kodeProduk]);

    return $stmt->fetch() === false;
}

$data = getRequestData();

$kodeProduk = trim($data['kode_produk'] ?? '');
$namaProduk = trim($data['nama_produk'] ?? '');
$kategoriId = trim($data['kategori_id'] ?? '');
$harga = trim($data['harga'] ?? '');
$stok = trim($data['stok'] ?? '');
$gambar = trim($data['gambar'] ?? '');

$errors = [];

if ($namaProduk === '') {
    $errors[] = 'Nama produk wajib diisi.';
}
if ($kategoriId === '') {
    $errors[] = 'Kategori wajib diisi.';
}
if (!is_numeric($harga) || $harga < 0) {
    $errors[] = 'Harga harus berupa angka dan tidak boleh negatif.';
}
if (!is_numeric($stok) || $stok < 0 || (string) (int) $stok !== (string) $stok) {
    $errors[] = 'Stok harus berupa angka bulat dan tidak boleh negatif.';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'Data produk tidak valid.',
        'errors' => $errors,
    ]);
    exit;
}

try {
    if ($kodeProduk === '') {
        $kodeProduk = generateKodeProduk($conn);
    } elseif (!isKodeProdukUnik($conn, $kodeProduk)) {
        http_response_code(400);
        echo json_encode([
            'status' => false,
            'message' => 'Kode produk sudah digunakan.',
        ]);
        exit;
    }

    $stmt = $conn->prepare('
        INSERT INTO produk (kode_produk, nama_produk, kategori_id, harga, stok, gambar)
        VALUES (:kode_produk, :nama_produk, :kategori_id, :harga, :stok, :gambar)
    ');

    $stmt->execute([
        ':kode_produk' => $kodeProduk,
        ':nama_produk' => $namaProduk,
        ':kategori_id' => $kategoriId,
        ':harga' => $harga,
        ':stok' => (int) $stok,
        ':gambar' => $gambar !== '' ? $gambar : null,
    ]);

    http_response_code(201);
    echo json_encode([
        'status' => true,
        'message' => 'Data produk berhasil ditambahkan.',
        'data' => [
            'id' => (int) $conn->lastInsertId(),
            'kode_produk' => $kodeProduk,
            'nama_produk' => $namaProduk,
            'kategori_id' => $kategoriId,
            'harga' => $harga,
            'stok' => (int) $stok,
            'gambar' => $gambar !== '' ? $gambar : null,
        ],
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Gagal menambahkan data produk.',
        'error' => $e->getMessage(),
    ]);
}

