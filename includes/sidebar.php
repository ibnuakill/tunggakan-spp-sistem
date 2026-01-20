<?php
// File: includes/sidebar.php
// Sidebar navigation untuk semua halaman admin

// Tentukan halaman aktif berdasarkan nama file
$current_page = basename($_SERVER['PHP_SELF']);
$active_dashboard = ($current_page == 'index.php') ? 'active' : '';
$active_siswa = (in_array($current_page, ['data_siswa.php', 'tambah_siswa.php', 'edit_siswa.php'])) ? 'active' : '';
$active_status = ($current_page == 'status_pembayaran.php') ? 'active' : '';
$active_biaya = (in_array($current_page, ['data_biaya.php', 'tambah_biaya.php', 'edit_biaya.php'])) ? 'active' : '';
$active_transaksi = ($current_page == 'transaksi.php') ? 'active' : '';
$active_laporan = ($current_page == 'laporan_aktif.php') ? 'active' : '';
$active_gallery_upload = ($current_page == 'gallery_upload.php') ? 'active' : '';
?>

<div id="sidebar" class="bg-dark text-white p-3">
    <h5 class="text-white mt-3 mb-4">Menu Utama</h5>
    <div class="list-group list-group-flush">
        <a href="index.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo $active_dashboard; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="data_siswa.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo $active_siswa; ?>"><i class="fas fa-user-graduate"></i> Kelola Data Siswa</a>
        <a href="status_pembayaran.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo $active_status; ?>"><i class="fas fa-clipboard-check"></i> History Pembayaran</a>
        <a href="data_biaya.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo $active_biaya; ?>"><i class="fas fa-tags"></i> Kelola Data Biaya</a>
        <a href="gallery_upload.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo $active_gallery_upload; ?>"><i class="fas fa-images"></i> Upload Galeri</a>
        <a href="transaksi.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo $active_transaksi; ?>"><i class="fas fa-receipt"></i> Input Transaksi Pembayaran</a>
    </div>
</div>
