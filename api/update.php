<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/koneksi.php';

if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'], true)) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'Method tidak valid. Gunakan PUT atau POST.',
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

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        parse_str($rawInput, $putData);
        return $putData;
    }

    return $_POST;
}

function getProdukById(PDO $conn, $id)
{
    $stmt = $conn->prepare('SELECT * FROM produk WHERE id = ? LIMIT 1');
    $stmt->execute([(int) $id]);

    return $stmt->fetch();
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

function isKodeProdukUnik(PDO $conn, $kodeProduk, $id)
{
    $stmt = $conn->prepare('SELECT id FROM produk WHERE kode_produk = ? AND id != ? LIMIT 1');
    $stmt->execute([$kodeProduk, (int) $id]);

    return $stmt->fetch() === false;
}

$data = getRequestData();

$id = (int) ($data['id'] ?? $_GET['id'] ?? 0);
$kodeProduk = trim($data['kode_produk'] ?? '');
$namaProduk = trim($data['nama_produk'] ?? '');
$kategoriId = trim($data['kategori_id'] ?? '');
$harga = trim($data['harga'] ?? '');
$stok = trim($data['stok'] ?? '');

$errors = [];

if ($id <= 0) {
    $errors[] = 'ID produk wajib diisi.';
}
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
    $produk = getProdukById($conn, $id);
    if (!$produk) {
        http_response_code(404);
        echo json_encode([
            'status' => false,
            'message' => 'Produk tidak ditemukan.',
        ]);
        exit;
    }

    if ($kodeProduk === '') {
        $kodeProduk = generateKodeProduk($conn);
    } elseif (!isKodeProdukUnik($conn, $kodeProduk, $id)) {
        http_response_code(400);
        echo json_encode([
            'status' => false,
            'message' => 'Kode produk sudah digunakan.',
        ]);
        exit;
    }

    $gambar = array_key_exists('gambar', $data) ? trim($data['gambar']) : $produk['gambar'];
    $gambar = $gambar !== '' ? $gambar : null;

    $stmt = $conn->prepare('
        UPDATE produk
        SET kode_produk = :kode_produk,
            nama_produk = :nama_produk,
            kategori_id = :kategori_id,
            harga = :harga,
            stok = :stok,
            gambar = :gambar
        WHERE id = :id
    ');

    $stmt->execute([
        ':kode_produk' => $kodeProduk,
        ':nama_produk' => $namaProduk,
        ':kategori_id' => $kategoriId,
        ':harga' => $harga,
        ':stok' => (int) $stok,
        ':gambar' => $gambar,
        ':id' => $id,
    ]);

    http_response_code(200);
    echo json_encode([
        'status' => true,
        'message' => 'Data produk berhasil diperbarui.',
        'data' => [
            'id' => $id,
            'kode_produk' => $kodeProduk,
            'nama_produk' => $namaProduk,
            'kategori_id' => $kategoriId,
            'harga' => $harga,
            'stok' => (int) $stok,
            'gambar' => $gambar,
        ],
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Gagal memperbarui data produk.',
        'error' => $e->getMessage(),
    ]);
}

