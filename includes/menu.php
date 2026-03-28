<?php
$current_page = isset($_GET['page']) ? $_GET['page'] : 'data_barang';

$menu_items = array(
    'data_barang' => array(
        'icon' => 'fas fa-box',
        'title' => 'Data Barang',
        'link' => 'index.php?page=data_barang',
        'active' => (
            $current_page == 'data_barang' ||
            basename($_SERVER['PHP_SELF']) == 'tambah.php' ||
            basename($_SERVER['PHP_SELF']) == 'edit.php' ||
            basename($_SERVER['PHP_SELF']) == 'detail.php'
        )
    )
);
?>

<div class="top-nav">
    <div class="nav-container">

        <?php foreach($menu_items as $key => $item): ?>
            <a href="<?php echo $item['link']; ?>"
               class="nav-item <?php echo $item['active'] ? 'active' : ''; ?>">
                <i class="<?php echo $item['icon']; ?>"></i>
                <span><?php echo $item['title']; ?></span>
            </a>
        <?php endforeach; ?>

        <a href="logout.php"
           class="nav-item logout"
           onclick="return confirm('Yakin ingin logout?')">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>

    </div>
</div>