<?php
    $page_title = "Dashboard";
    $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<?php include 'includes/header.php'; ?>

<?php include 'includes/menu.php'; ?>


<div class="content-wrapper">

    <main class="main-content">
        <div class="page-header">

        <?php
        $page_titles = array(
            'data_barang' => 'Data Barang',
            'tambah' => 'Tambah Barang',
            'edit' => 'Edit Barang',
            'detail' => 'Detail Barang'
        );
        ?>

        <h2><?php echo $page_titles[$page] ?? 'Dashboard'; ?></h2>

        </div>

        <div class="content">
            <?php
            switch($page){

            case 'dashboard':
            include 'includes/pages/dashboard.php';
            break;

            case 'data_barang':
            include 'includes/pages/data_barang.php';
            break;

            case 'tambah':
            include 'tambah.php';
            break;

            case 'edit':
            include 'edit.php';
            break;

            case 'detail':
            include 'detail.php';
            break;

            case 'hapus':
            include 'hapus.php';
            break;

            default:
            include 'includes/pages/dashboard.php';
            }
            ?>
        </div>
    </main>
</div>

