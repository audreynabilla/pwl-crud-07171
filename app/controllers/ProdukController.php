<?php
require_once __DIR__ . '/../models/ProdukModel.php';

class ProdukController
{
    private $produkModel;
    private $uploadDir;

    public function __construct(PDO $conn)
    {
        $this->produkModel = new ProdukModel($conn);
        $this->uploadDir = __DIR__ . '/../../public/uploads/';

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function index()
    {
        $this->requireLogin();

        $data = [
            'title' => 'Data Barang',
            'produk' => $this->produkModel->getAll(),
            'totalProduk' => $this->produkModel->getTotalCount(),
            'totalStok' => $this->produkModel->getTotalStok(),
        ];

        $this->view('produk/index', $data);
    }

    public function create()
    {
        $this->requireLogin();

        $data = [
            'title' => 'Tambah Produk',
            'errors' => [],
            'produk' => $this->emptyProduk(),
        ];

        $this->view('produk/create', $data);
    }

    public function store()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectTo('index.php?url=produk/create');
        }

        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlash('Akses tidak sah (CSRF).', 'error');
            redirectTo('index.php?url=produk/create');
        }

        $dataProduk = $this->getInputData();
        $errors = $this->validateInput($dataProduk);

        if ($dataProduk['kode_produk'] === '') {
            $dataProduk['kode_produk'] = $this->produkModel->generateKodeProduk();
        } elseif (!$this->produkModel->cekKodeUnik($dataProduk['kode_produk'])) {
            $errors[] = 'Kode produk sudah digunakan!';
        }

        $upload = $this->handleUpload();
        if ($upload['error']) {
            $errors[] = $upload['error'];
        }
        $dataProduk['gambar'] = $upload['filename'];

        if (!empty($errors)) {
            if ($dataProduk['gambar']) {
                $this->deleteImage($dataProduk['gambar']);
            }
            $this->view('produk/create', [
                'title' => 'Tambah Produk',
                'errors' => $errors,
                'produk' => $dataProduk,
            ]);
            return;
        }

        if ($this->produkModel->create($dataProduk)) {
            setFlash('Produk berhasil ditambahkan!', 'success');
            redirectTo('index.php?url=produk/index');
        }

        if ($dataProduk['gambar']) {
            $this->deleteImage($dataProduk['gambar']);
        }

        $this->view('produk/create', [
            'title' => 'Tambah Produk',
            'errors' => ['Gagal menyimpan data ke database.'],
            'produk' => $dataProduk,
        ]);
    }

    public function edit($id = null)
    {
        $this->requireLogin();
        $produk = $this->findProdukOrRedirect($id);

        $this->view('produk/edit', [
            'title' => 'Edit Produk',
            'errors' => [],
            'produk' => $produk,
        ]);
    }

    public function update($id = null)
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectTo('index.php?url=produk/edit/' . (int) $id);
        }

        $produk = $this->findProdukOrRedirect($id);

        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlash('Akses tidak sah (CSRF).', 'error');
            redirectTo('index.php?url=produk/edit/' . (int) $id);
        }

        $dataProduk = $this->getInputData();
        $errors = $this->validateInput($dataProduk);

        if ($dataProduk['kode_produk'] === '') {
            $dataProduk['kode_produk'] = $this->produkModel->generateKodeProduk();
        } elseif (!$this->produkModel->cekKodeUnik($dataProduk['kode_produk'], $id)) {
            $errors[] = 'Kode produk sudah digunakan!';
        }

        $dataProduk['gambar'] = $produk['gambar'];
        $upload = $this->handleUpload();

        if ($upload['error']) {
            $errors[] = $upload['error'];
        } elseif ($upload['filename']) {
            $dataProduk['gambar'] = $upload['filename'];
        }

        if (!empty($errors)) {
            if ($upload['filename']) {
                $this->deleteImage($upload['filename']);
            }
            $dataProduk['id'] = $produk['id'];
            $this->view('produk/edit', [
                'title' => 'Edit Produk',
                'errors' => $errors,
                'produk' => $dataProduk,
            ]);
            return;
        }

        if ($this->produkModel->update($id, $dataProduk)) {
            if ($upload['filename'] && $produk['gambar']) {
                $this->deleteImage($produk['gambar']);
            }

            setFlash('Produk berhasil diperbarui!', 'success');
            redirectTo('index.php?url=produk/index');
        }

        if ($upload['filename']) {
            $this->deleteImage($upload['filename']);
        }

        $dataProduk['id'] = $produk['id'];
        $this->view('produk/edit', [
            'title' => 'Edit Produk',
            'errors' => ['Gagal memperbarui produk!'],
            'produk' => $dataProduk,
        ]);
    }

    public function delete($id = null)
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectTo('index.php?url=produk/index');
        }

        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlash('Akses tidak sah (CSRF).', 'error');
            redirectTo('index.php?url=produk/index');
        }

        $id = (int) ($_POST['id'] ?? $id);
        $produk = $this->produkModel->getById($id);

        if (!$produk) {
            setFlash('Produk tidak ditemukan!', 'error');
            redirectTo('index.php?url=produk/index');
        }

        if ($this->produkModel->delete($id)) {
            if ($produk['gambar']) {
                $this->deleteImage($produk['gambar']);
            }
            setFlash('Produk berhasil dihapus!', 'success');
        } else {
            setFlash('Produk gagal dihapus!', 'error');
        }

        redirectTo('index.php?url=produk/index');
    }

    public function detail($id = null)
    {
        $this->requireLogin();
        $produk = $this->findProdukOrRedirect($id);

        $this->view('produk/detail', [
            'title' => 'Detail Produk',
            'produk' => $produk,
        ]);
    }

    private function getInputData()
    {
        return [
            'kode_produk' => trim($_POST['kode_produk'] ?? ''),
            'nama_produk' => trim($_POST['nama_produk'] ?? ''),
            'kategori_id' => trim($_POST['kategori_id'] ?? ''),
            'harga' => trim($_POST['harga'] ?? ''),
            'stok' => trim($_POST['stok'] ?? ''),
            'gambar' => null,
        ];
    }

    private function validateInput($data)
    {
        $errors = [];

        if ($data['nama_produk'] === '') {
            $errors[] = 'Nama produk wajib diisi.';
        }
        if ($data['kategori_id'] === '') {
            $errors[] = 'Kategori wajib dipilih.';
        }
        if (!is_numeric($data['harga']) || $data['harga'] < 0) {
            $errors[] = 'Harga harus berupa angka dan tidak boleh negatif.';
        }
        if (!is_numeric($data['stok']) || $data['stok'] < 0 || (string) (int) $data['stok'] !== (string) $data['stok']) {
            $errors[] = 'Stok harus berupa angka bulat dan tidak boleh negatif.';
        }

        return $errors;
    }

    private function handleUpload()
    {
        if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] === UPLOAD_ERR_NO_FILE) {
            return ['filename' => null, 'error' => null];
        }

        if ($_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
            return ['filename' => null, 'error' => 'Gagal mengupload gambar.'];
        }

        if ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
            return ['filename' => null, 'error' => 'Ukuran gambar maksimal 2MB.'];
        }

        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($fileInfo, $_FILES['gambar']['tmp_name']);
        finfo_close($fileInfo);

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
        ];

        if (!array_key_exists($mime, $allowed)) {
            return ['filename' => null, 'error' => 'File harus berupa gambar JPG, PNG, atau GIF.'];
        }

        $filename = uniqid() . '.' . $allowed[$mime];
        $destination = $this->uploadDir . $filename;

        if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $destination)) {
            return ['filename' => null, 'error' => 'Gagal menyimpan gambar.'];
        }

        return ['filename' => $filename, 'error' => null];
    }

    private function deleteImage($filename)
    {
        $path = $this->uploadDir . basename($filename);
        if ($filename && is_file($path)) {
            unlink($path);
        }
    }

    private function findProdukOrRedirect($id)
    {
        $produk = $this->produkModel->getById((int) $id);
        if (!$produk) {
            setFlash('Produk tidak ditemukan!', 'error');
            redirectTo('index.php?url=produk/index');
        }

        return $produk;
    }

    private function emptyProduk()
    {
        return [
            'kode_produk' => '',
            'nama_produk' => '',
            'kategori_id' => '',
            'harga' => '',
            'stok' => '',
            'gambar' => '',
        ];
    }

    private function requireLogin()
    {
        if (!isLoggedIn()) {
            redirectTo('index.php?url=auth/login');
        }
    }

    private function view($view, $data = [])
    {
        extract($data);
        require __DIR__ . '/../views/' . $view . '.php';
    }
}
