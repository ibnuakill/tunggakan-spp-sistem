<?php
include('../includes/koneksi.php');
include('../includes/proteksi.php');

// Ambil input tanggal dari form (POST)
$tgl_awal = isset($_POST['tgl_awal']) ? $_POST['tgl_awal'] : '';
$tgl_akhir = isset($_POST['tgl_akhir']) ? $_POST['tgl_akhir'] : '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Penerimaan SPP</title>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* Sembunyikan tombol filter, navbar, dan sidebar saat mencetak */
        @media print {

            .no-print,
            #sidebar,
            .navbar {
                display: none !important;
            }

            /* Konten penuh saat dicetak */
            #content {
                margin-left: 0 !important;
            }
        }

        /* Tambahkan padding di atas tabel agar tidak nabrak header saat dicetak */
        @page {
            margin-top: 20mm;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top no-print">
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
            <h2 class="mt-4 no-print">Cetak Laporan Penerimaan SPP</h2>
            <p class="lead no-print">Filter laporan berdasarkan periode tanggal.</p>

            <div class="card mb-4 no-print">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-filter"></i> Filter Berdasarkan Tanggal
                </div>
                <div class="card-body">
                    <form action="laporan_aktif.php" method="post" class="row g-3">
                        <div class="col-md-5">
                            <label for="tgl_awal" class="form-label">Dari Tanggal:</label>
                            <input type="date" id="tgl_awal" name="tgl_awal" class="form-control" required value="<?php echo htmlspecialchars($tgl_awal); ?>">
                        </div>
                        <div class="col-md-5">
                            <label for="tgl_akhir" class="form-label">Sampai Tanggal:</label>
                            <input type="date" id="tgl_akhir" name="tgl_akhir" class="form-control" required value="<?php echo htmlspecialchars($tgl_akhir); ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" name="filter" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Tampilkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <?php
            // --- Tampilkan Hasil Laporan jika tombol filter ditekan ---
            if (isset($_POST['filter']) && !empty($tgl_awal) && !empty($tgl_akhir)) {

                // Query Laporan (Menggunakan Multiple JOIN)
                $query_laporan = "SELECT p.*, s.nama_siswa, k.nama_kelas, b.nama_biaya, a.nama_lengkap AS nama_admin
                              FROM tabel_pembayaran p
                              JOIN tabel_siswa s ON p.nis = s.nis
                              JOIN tabel_kelas k ON s.id_kelas = k.id_kelas
                              JOIN tabel_jenis_biaya b ON p.id_biaya = b.id_biaya
                              JOIN tabel_admin a ON p.id_admin = a.id_admin
                              WHERE p.tanggal_bayar BETWEEN '$tgl_awal' AND '$tgl_akhir'
                              ORDER BY p.tanggal_bayar ASC";

                $result_laporan = mysqli_query($koneksi, $query_laporan);
                $total_penerimaan = 0;

                if (mysqli_num_rows($result_laporan) > 0) {
            ?>
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-file-alt"></i> Hasil Laporan
                        </div>
                        <div class="card-body">
                            <h4 class="text-center">LAPORAN PENERIMAAN KEUANGAN</h4>
                            <h5 class="text-center mb-4">Periode: <?php echo date('d F Y', strtotime($tgl_awal)) . " s/d " . date('d F Y', strtotime($tgl_akhir)); ?></h5>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-info">
                                        <tr>
                                            <th>No.</th>
                                            <th>Tgl Bayar</th>
                                            <th>NIS/Nama Siswa</th>
                                            <th>Kelas</th>
                                            <th>Jenis Biaya</th>
                                            <th>Bulan/Tahun</th>
                                            <th>Jumlah Bayar</th>
                                            <th>Admin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($data = mysqli_fetch_assoc($result_laporan)) {
                                            $total_penerimaan += $data['jumlah_bayar'];
                                        ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($data['tanggal_bayar'])); ?></td>
                                                <td><?php echo $data['nis'] . ' / ' . $data['nama_siswa']; ?></td>
                                                <td><?php echo $data['nama_kelas']; ?></td>
                                                <td><?php echo $data['nama_biaya']; ?></td>
                                                <td><?php echo $data['bulan_bayar'] . ' ' . $data['tahun_bayar']; ?></td>
                                                <td>Rp <?php echo number_format($data['jumlah_bayar'], 0, ',', '.'); ?></td>
                                                <td><?php echo $data['nama_admin']; ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-warning">
                                            <td colspan="6" class="text-end"><strong>TOTAL PENERIMAAN:</strong></td>
                                            <td colspan="2"><strong>Rp <?php echo number_format($total_penerimaan, 0, ',', '.'); ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div> <br>
                            <div class="text-center no-print">
                                <button onclick="window.print();" class="btn btn-success">
                                    <i class="fas fa-print"></i> Cetak Dokumen
                                </button>
                            </div>
                        </div>
                    </div> <?php
                        } else {
                            echo "<div class='alert alert-warning'>Tidak ada data transaksi dalam periode yang dipilih.</div>";
                        }
                    }
                            ?>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin-responsive.js"></script>
</body>

</html>