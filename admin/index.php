<?php
include('../includes/koneksi.php');
include('../includes/proteksi.php');

// Tentukan Bulan dan Tahun Saat Ini
$bulan_saat_ini = date('n'); // n = bulan tanpa leading zero (1 sampai 12)
$tahun_saat_ini = date('Y');

// 1. Logika Penentuan Semester
if ($bulan_saat_ini >= 7 && $bulan_saat_ini <= 12) {
    // Semester Ganjil (Juli - Desember)
    $tgl_mulai_semester = $tahun_saat_ini . '-07-01';
    $judul_semester = "Semester Ganjil " . $tahun_saat_ini;
} else {
    // Semester Genap (Januari - Juni)
    $tgl_mulai_semester = $tahun_saat_ini . '-01-01';
    $judul_semester = "Semester Genap " . $tahun_saat_ini;
}


// 2. Query untuk Total Siswa
$query_siswa = "SELECT COUNT(nis) AS total_siswa FROM tabel_siswa";
$result_siswa = mysqli_query($koneksi, $query_siswa);
$data_siswa = mysqli_fetch_assoc($result_siswa);
$total_siswa = number_format($data_siswa['total_siswa']);


// 3. Query untuk Penerimaan Semester Ini
$query_penerimaan = "SELECT SUM(jumlah_bayar) AS total_penerimaan 
                     FROM tabel_pembayaran 
                     WHERE tanggal_bayar >= '$tgl_mulai_semester' AND tanggal_bayar <= NOW()";
$result_penerimaan = mysqli_query($koneksi, $query_penerimaan);
$data_penerimaan = mysqli_fetch_assoc($result_penerimaan);
$total_penerimaan_semester = $data_penerimaan['total_penerimaan'] ?? 0; // Set 0 jika NULL
$penerimaan_format = 'Rp ' . number_format($total_penerimaan_semester, 0, ',', '.');
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin SPP</title>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid">
            <button class="btn btn-outline-light me-3 d-lg-none" type="button" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="#">Sistem Pembayaran SPP</a>

            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="ms-auto text-white d-flex align-items-center flex-wrap">
                    <span class="me-3 d-none d-md-inline">Selamat Datang, <strong><?php echo isset($_SESSION['nama_admin']) ? $_SESSION['nama_admin'] : 'Admin'; ?></strong>!</span>
                    <a href="../logout.php" class="btn btn-sm btn-outline-light">Logout</a>
                </div>
            </div>
        </div>
    </nav>


    <div id="sidebar" class="bg-dark text-white p-3">
        <h5 class="text-white mt-3 mb-4">Menu Utama</h5>
        <div class="list-group list-group-flush">
            <?php
            // Tentukan halaman aktif berdasarkan nama file
            $current_page = basename($_SERVER['PHP_SELF']);
            $active_dashboard = ($current_page == 'index.php') ? 'active' : '';
            $active_siswa = (in_array($current_page, ['data_siswa.php', 'tambah_siswa.php', 'edit_siswa.php'])) ? 'active' : '';
            $active_status = ($current_page == 'status_pembayaran.php') ? 'active' : '';
            $active_biaya = ($current_page == 'data_biaya.php') ? 'active' : '';
            $active_transaksi = ($current_page == 'transaksi.php') ? 'active' : '';
            $active_laporan = ($current_page == 'laporan_aktif.php') ? 'active' : '';
            $active_gallery_upload = ($current_page == 'gallery_upload.php') ? 'active' : '';
            ?>
            <a href="index.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo $active_dashboard; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="data_siswa.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo $active_siswa; ?>"><i class="fas fa-user-graduate"></i> Kelola Data Siswa</a>
            <a href="status_pembayaran.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo $active_status; ?>"><i class="fas fa-clipboard-check"></i> History Pembayaran</a>
            <a href="data_biaya.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo $active_biaya; ?>"><i class="fas fa-tags"></i> Kelola Data Biaya</a>
            <a href="gallery_upload.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo $active_gallery_upload; ?>"><i class="fas fa-images"></i> Upload Galeri</a>
            <a href="transaksi.php" class="list-group-item list-group-item-action bg-dark text-white <?php echo $active_transaksi; ?>"><i class="fas fa-receipt"></i> Input Transaksi Pembayaran</a>
        </div>
    </div>

    <div id="content">
        <div class="container-fluid">
            <h2 class="mt-4">Dashboard Administrator</h2>
            <p class="lead">Ringkasan transaksi dan data master Sistem Pembayaran SPP.</p>

            <hr>

            <div class="row mt-4">

                <div class="col-12 col-sm-6 col-md-3 mb-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <i class="fas fa-users card-icon"></i>
                            <h5 class="card-title">Total Siswa</h5>
                            <h1 class="card-text"><?php echo $total_siswa; ?></h1>
                            <a href="data_siswa.php" class="text-white small">Lihat Detail &rarr;</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3 mb-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <i class="fas fa-hand-holding-usd card-icon"></i>
                            <h5 class="card-title">Penerimaan <?php echo $judul_semester; ?></h5>
                            <h1 class="card-text"><?php echo $penerimaan_format; ?></h1>
                            <a href="status_pembayaran.php" class="text-white small">Lihat History &rarr;</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin-responsive.js"></script>

</body>

</html>