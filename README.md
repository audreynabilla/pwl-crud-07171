# Sistem Manajemen Inventaris

Sistem Manajemen Inventaris adalah aplikasi web berbasis PHP native dengan arsitektur MVC untuk mengelola data produk/inventaris. Aplikasi ini mendukung autentikasi pengguna, operasi CRUD produk, upload gambar produk, serta RESTful API sederhana untuk integrasi data.

## Fitur Utama

- Registrasi dan login pengguna
- Keamanan password menggunakan `password_hash()` dan `password_verify()`
- CRUD data produk/inventaris
- Upload gambar produk
- RESTful API untuk create, read, update, dan delete data
- Arsitektur MVC dengan pemisahan controller, model, view, helper, dan konfigurasi
- Koneksi database menggunakan PHP PDO

## Teknologi

- PHP native
- PHP PDO
- MySQL
- XAMPP
- HTML dan CSS
- Git dan GitHub

## Cara Instalasi

1. Clone repository ke folder `htdocs` XAMPP:

   ```bash
   git clone https://github.com/username/nama-repository.git C:/xampp/htdocs/crud
   ```

2. Jalankan XAMPP, lalu aktifkan Apache dan MySQL.

3. Buat database MySQL melalui phpMyAdmin.

4. Import file database SQL proyek ke database yang sudah dibuat.

5. Konfigurasi koneksi database pada file:

   ```text
   config/database.php
   ```

   Sesuaikan nilai host, nama database, username, dan password MySQL.

6. Akses aplikasi melalui browser:

   ```text
   http://localhost/crud/public/
   ```

## Struktur Folder

```text
crud/
|-- api/                # Endpoint RESTful API
|-- app/
|   |-- controllers/    # Controller aplikasi
|   |-- helpers/        # Helper keamanan dan fungsi pendukung
|   |-- models/         # Model untuk akses database
|   `-- views/          # Tampilan halaman aplikasi
|-- assets/             # Aset pendukung aplikasi
|-- config/             # Konfigurasi database
|-- public/
|   |-- css/            # File CSS
|   |-- uploads/        # File gambar hasil upload
|   `-- index.php       # Entry point aplikasi
`-- README.md
```

## Catatan Keamanan

File konfigurasi database seperti `config/database.php` dan `api/koneksi.php` tidak disertakan ke repository karena berisi kredensial lokal. Pastikan file tersebut dibuat dan dikonfigurasi secara manual setelah clone proyek.

## Panduan Commit Tugas 6

Contoh pesan commit yang disarankan:

```bash
git add .gitignore README.md
git commit -m "docs: add portfolio README and gitignore"
git push origin main
```

Jika repository menggunakan branch `master`, ganti `main` menjadi `master`.
