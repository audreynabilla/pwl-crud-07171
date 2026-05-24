<?php
$pageTitle = isset($title) ? $title . ' | Sistem Manajemen Inventaris' : 'Sistem Manajemen Inventaris';
$currentUrl = $_GET['url'] ?? 'produk/index';
$bodyClass = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle); ?></title>
    <link rel="stylesheet" href="/crud_mvc/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="<?= e($bodyClass); ?>">
<div class="container">
    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="notification <?= e($_SESSION['tipe'] ?? 'success'); ?>">
            <div class="notification-content">
                <i class="fas <?= ($_SESSION['tipe'] ?? '') === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <span><?= e($_SESSION['pesan']); ?></span>
            </div>
            <button class="notification-close" onclick="this.parentElement.style.display='none'" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php unset($_SESSION['pesan'], $_SESSION['tipe']); ?>
    <?php endif; ?>

    <?php if (isLoggedIn()): ?>
        <header class="header">
            <div class="logo">
                <i class="fas fa-box"></i>
                <div>
                    <h1>Sistem Manajemen Inventaris</h1>
                    <span>Halo, <?= e($_SESSION['username']); ?></span>
                </div>
            </div>
            <div class="date-time"><?= date('d M Y'); ?></div>
        </header>

        <nav class="top-nav">
            <div class="nav-container">
                <a href="index.php?url=produk/index" class="nav-item <?= str_starts_with($currentUrl, 'produk') ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i>
                    <span>Data Barang</span>
                </a>
                <a href="index.php?url=auth/logout" class="nav-item logout" onclick="return confirm('Yakin ingin logout?')">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    <?php endif; ?>
