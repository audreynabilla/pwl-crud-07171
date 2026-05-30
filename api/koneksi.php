<?php
header("Content-Type: application/json; charset=UTF-8");

$apiConnectionReady = false;
ob_start();

register_shutdown_function(function () use (&$apiConnectionReady) {
    if ($apiConnectionReady) {
        return;
    }

    if (ob_get_length()) {
        ob_end_clean();
    }

    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Koneksi database gagal.',
    ]);
});

try {
    $conn = require __DIR__ . '/../config/database.php';
    $apiConnectionReady = true;

    if (ob_get_length()) {
        ob_end_clean();
    }
} catch (Throwable $e) {
    if (ob_get_length()) {
        ob_end_clean();
    }

    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Koneksi database gagal.',
        'error' => $e->getMessage(),
    ]);
    exit;
}
