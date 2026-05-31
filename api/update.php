<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'PUT' && $method !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method harus PUT atau POST']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'Format request harus JSON']);
    exit;
}

if (empty($data['id'])) {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'ID produk wajib diisi']);
    exit;
}

$id = (int) $data['id'];

$stmt = $conn->prepare("SELECT * FROM produk WHERE id = ?");
$stmt->execute([$id]);
$produk = $stmt->fetch();
if (!$produk) {
    http_response_code(404);
    echo json_encode(['status' => false, 'message' => 'Produk tidak ditemukan']);
    exit;
}

$fields = [];
$params = [':id' => $id];

if (isset($data['kode_produk'])) {
    $fields[] = "kode_produk = :kode";
    $params[':kode'] = $data['kode_produk'];
}
if (isset($data['nama_produk'])) {
    $fields[] = "nama_produk = :nama";
    $params[':nama'] = $data['nama_produk'];
}
if (isset($data['kategori_id'])) {
    $fields[] = "kategori_id = :kategori";
    $params[':kategori'] = $data['kategori_id'];
}
if (isset($data['harga'])) {
    $fields[] = "harga = :harga";
    $params[':harga'] = $data['harga'];
}
if (isset($data['stok'])) {
    $fields[] = "stok = :stok";
    $params[':stok'] = $data['stok'];
}
if (isset($data['gambar'])) {
    $fields[] = "gambar = :gambar";
    $params[':gambar'] = $data['gambar'];
}

if (count($fields) == 0) {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'Tidak ada field yang diupdate']);
    exit;
}

$sql = "UPDATE produk SET " . implode(", ", $fields) . " WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute($params);

echo json_encode(['status' => true, 'message' => 'Data berhasil diupdate']);
?>