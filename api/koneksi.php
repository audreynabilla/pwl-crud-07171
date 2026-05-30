<?php
header("Content-Type: application/json; charset=UTF-8");

try {
    $conn = require __DIR__ . '/../config/database.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Koneksi database gagal.',
        'error' => $e->getMessage(),
    ]);
    exit;
}

