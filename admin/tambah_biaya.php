<?php
include('../includes/koneksi.php');
include('../includes/proteksi.php');

// Handle submit
if (isset($_POST['simpan'])) {
    $nama_biaya = mysqli_real_escape_string($koneksi, $_POST['nama_biaya']);
    // Normalisasi nominal: ambil hanya digit
    $nominal_raw = preg_replace('/[^0-9]/', '', $_POST['nominal']);
    $nominal = mysqli_real_escape_string($koneksi, $nominal_raw);
    $periode = mysqli_real_escape_string($koneksi, $_POST['periode']);

    $query_insert = "INSERT INTO tabel_jenis_biaya (nama_biaya, nominal, periode) VALUES ('$nama_biaya', '$nominal', '$periode')";
    if (mysqli_query($koneksi, $query_insert)) {
        echo "<script>alert('Jenis biaya berhasil ditambahkan!'); window.location='data_biaya.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal menambah data: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jenis Biaya - Sistem Pembayaran SPP</title>

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
            $current_page = basename($_SERVER['PHP_SELF']);
            $active_dashboard = ($current_page == 'index.php') ? 'active' : '';
            $active_siswa = (in_array($current_page, ['data_siswa.php', 'tambah_siswa.php', 'edit_siswa.php'])) ? 'active' : '';
            $active_status = ($current_page == 'status_pembayaran.php') ? 'active' : '';
            $active_biaya = (in_array($current_page, ['data_biaya.php', 'tambah_biaya.php', 'edit_biaya.php'])) ? 'active' : '';
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mt-4 mb-2">
                        <i class="fas fa-tag text-primary"></i> Tambah Jenis Biaya Baru
                    </h2>
                    <p class="lead text-muted">Isi form untuk menambahkan jenis biaya baru.</p>
                </div>
                <a href="data_biaya.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <hr>

            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-edit"></i> Form Tambah Jenis Biaya
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" id="formTambahBiaya">
                                <div class="mb-3">
                                    <label for="nama_biaya" class="form-label">
                                        <i class="fas fa-list text-primary"></i> Nama Biaya <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="nama_biaya" name="nama_biaya" required placeholder="Contoh: SPP Bulanan">
                                </div>

                                <div class="mb-3">
                                    <label for="nominal" class="form-label">
                                        <i class="fas fa-money-bill-wave text-primary"></i> Nominal (Rp) <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="nominal" name="nominal" required placeholder="Contoh: 150000">
                                    <small class="form-text text-muted">Masukkan angka tanpa titik/koma.</small>
                                </div>

                                <div class="mb-3">
                                    <label for="periode" class="form-label">
                                        <i class="fas fa-calendar-alt text-primary"></i> Periode <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="periode" name="periode" required maxlength="20" placeholder="Contoh: 2024/2025 atau 2024">
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <a href="data_biaya.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
                                    <button type="submit" name="simpan" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Data
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin-responsive.js"></script>
    <script>
        document.getElementById('formTambahBiaya').addEventListener('submit', function(e) {
            const nama = document.getElementById('nama_biaya').value.trim();
            const nominal = document.getElementById('nominal').value.trim();
            const periode = document.getElementById('periode').value.trim();
            if (!nama || !nominal || !periode) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi!');
            }
            if (!/^\d+$/.test(nominal)) {
                e.preventDefault();
                alert('Nominal harus berupa angka tanpa pemisah.');
            }
        });
    </script>
</body>

</html>