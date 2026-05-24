<?php
class ProdukModel
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare('SELECT * FROM produk ORDER BY id DESC');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare('SELECT * FROM produk WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare('
            INSERT INTO produk (kode_produk, nama_produk, kategori_id, harga, stok, gambar)
            VALUES (:kode_produk, :nama_produk, :kategori_id, :harga, :stok, :gambar)
        ');

        return $stmt->execute([
            ':kode_produk' => $data['kode_produk'],
            ':nama_produk' => $data['nama_produk'],
            ':kategori_id' => $data['kategori_id'],
            ':harga' => $data['harga'],
            ':stok' => $data['stok'],
            ':gambar' => $data['gambar'],
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare('
            UPDATE produk
            SET kode_produk = :kode_produk,
                nama_produk = :nama_produk,
                kategori_id = :kategori_id,
                harga = :harga,
                stok = :stok,
                gambar = :gambar
            WHERE id = :id
        ');

        return $stmt->execute([
            ':kode_produk' => $data['kode_produk'],
            ':nama_produk' => $data['nama_produk'],
            ':kategori_id' => $data['kategori_id'],
            ':harga' => $data['harga'],
            ':stok' => $data['stok'],
            ':gambar' => $data['gambar'],
            ':id' => (int) $id,
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare('DELETE FROM produk WHERE id = ?');
        return $stmt->execute([(int) $id]);
    }

    public function generateKodeProduk()
    {
        $stmt = $this->conn->prepare("
            SELECT MAX(CAST(SUBSTRING(kode_produk, 4) AS UNSIGNED)) AS max_code
            FROM produk
            WHERE kode_produk LIKE ?
        ");
        $stmt->execute(['PRD%']);
        $row = $stmt->fetch();
        $next = ((int) ($row['max_code'] ?? 0)) + 1;

        return 'PRD' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    public function cekKodeUnik($kode, $excludeId = null)
    {
        if ($excludeId) {
            $stmt = $this->conn->prepare('SELECT id FROM produk WHERE kode_produk = ? AND id != ? LIMIT 1');
            $stmt->execute([$kode, (int) $excludeId]);
        } else {
            $stmt = $this->conn->prepare('SELECT id FROM produk WHERE kode_produk = ? LIMIT 1');
            $stmt->execute([$kode]);
        }

        return $stmt->fetch() === false;
    }

    public function getTotalCount()
    {
        $stmt = $this->conn->prepare('SELECT COUNT(*) AS total FROM produk');
        $stmt->execute();
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    public function getTotalStok()
    {
        $stmt = $this->conn->prepare('SELECT COALESCE(SUM(stok), 0) AS total FROM produk');
        $stmt->execute();
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }
}
