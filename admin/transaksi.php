<?php
include('../includes/koneksi.php');
include('../includes/proteksi.php');

// Ambil NIS atau Nama yang dicari dari URL (GET)
$cari = isset($_GET['nis']) ? trim(mysqli_real_escape_string($koneksi, $_GET['nis'])) : '';

// Query untuk mencari data siswa berdasarkan NIS atau Nama
$data_siswa = null;
if (!empty($cari)) {
    // Cari berdasarkan NIS (exact match) ATAU Nama (partial match dengan LIKE)
    $query_siswa = "SELECT s.*, k.nama_kelas 
                    FROM tabel_siswa s 
                    JOIN tabel_kelas k ON s.id_kelas = k.id_kelas
                    WHERE s.nis = '$cari' OR s.nama_siswa LIKE '%$cari%'
                    LIMIT 1";

    $result_siswa = mysqli_query($koneksi, $query_siswa);
    $data_siswa = mysqli_fetch_assoc($result_siswa);
}

// Query untuk mengambil data biaya dari tabel_jenis_biaya (untuk dropdown)
$query_biaya = "SELECT * FROM tabel_jenis_biaya ORDER BY nama_biaya ASC";
$result_biaya = mysqli_query($koneksi, $query_biaya);

// Ambil daftar tahun ajaran dari tabel_siswa_aktif (mapping NIS per tahun ajaran)
$daftar_tahun_ajaran = [];
$result_tahun_ajaran = mysqli_query($koneksi, "SELECT DISTINCT TRIM(tahun_ajaran) AS tahun_ajaran FROM tabel_siswa_aktif WHERE tahun_ajaran IS NOT NULL AND TRIM(tahun_ajaran) <> '' ORDER BY tahun_ajaran DESC");
if ($result_tahun_ajaran) {
    while ($row_tahun = mysqli_fetch_assoc($result_tahun_ajaran)) {
        $daftar_tahun_ajaran[] = $row_tahun['tahun_ajaran'];
    }
}

// --- Rekapitulasi Pembayaran Per Semester / Tahun Ajaran ---
$filter_tahun_ajaran = isset($_GET['tahun_ajaran']) ? $_GET['tahun_ajaran'] : '';
$daftar_bulan_rekap = [];
$judul_semester = "";

if (!empty($filter_tahun_ajaran)) {
    // Mode Filter: Tampilkan Full 1 Tahun Ajaran (Juli - Juni)
    $judul_semester = "Tahun Ajaran " . htmlspecialchars($filter_tahun_ajaran);

    // Parse tahun ajaran (misal 2024/2025)
    $parts_tahun = explode('/', $filter_tahun_ajaran);
    $tahun_awal = (int)$parts_tahun[0];
    $tahun_akhir = isset($parts_tahun[1]) ? (int)$parts_tahun[1] : ($tahun_awal + 1);

    // Set 12 Bulan (Juli th_awal s/d Juni th_akhir)
    $bulan_urutan = [
        ['Juli', $tahun_awal],
        ['Agustus', $tahun_awal],
        ['September', $tahun_awal],
        ['Oktober', $tahun_awal],
        ['November', $tahun_awal],
        ['Desember', $tahun_awal],
        ['Januari', $tahun_akhir],
        ['Februari', $tahun_akhir],
        ['Maret', $tahun_akhir],
        ['April', $tahun_akhir],
        ['Mei', $tahun_akhir],
        ['Juni', $tahun_akhir]
    ];

    foreach ($bulan_urutan as $bu) {
        $daftar_bulan_rekap[] = [
            'bulan' => $bu[0],
            'tahun' => $bu[1]
        ];
    }
} else {
    // Mode Default: Tampilkan Semester Saat Ini (6 Bulan) berdasarkan tanggal server
    $bulan_saat_ini = date('n');
    $tahun_saat_ini = date('Y');

    if ($bulan_saat_ini >= 7) {
        // Semester Ganjil (Juli - Desember)
        $judul_semester = "Semester Ganjil " . $tahun_saat_ini;
        $tahun_semester = $tahun_saat_ini;
        $daftar_bulan_semester = ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    } else {
        // Semester Genap (Januari - Juni)
        $judul_semester = "Semester Genap " . $tahun_saat_ini;
        $tahun_semester = $tahun_saat_ini;
        $daftar_bulan_semester = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];
    }

    foreach ($daftar_bulan_semester as $bulan) {
        $daftar_bulan_rekap[] = [
            'bulan' => $bulan,
            'tahun' => $tahun_semester
        ];
    }
}

$total_siswa = 0;
$nis_filter_list = [];
$where_nis_clause = '';

// Jika ada filter tahun ajaran, gunakan daftar siswa dari tabel_siswa_aktif untuk tahun tersebut
if (!empty($filter_tahun_ajaran)) {
    $tahun_norm = mysqli_real_escape_string($koneksi, preg_replace('/\s+/', '', $filter_tahun_ajaran));
    $query_siswa_aktif = mysqli_query(
        $koneksi,
        "SELECT nis, tahun_ajaran 
         FROM tabel_siswa_aktif 
         WHERE REPLACE(REPLACE(REPLACE(tahun_ajaran, ' ', ''), '\r', ''), '\n', '') = '$tahun_norm'"
    );

    if ($query_siswa_aktif) {
        while ($row_sa = mysqli_fetch_assoc($query_siswa_aktif)) {
            $nis_filter_list[] = $row_sa['nis'];
        }
    }

    if (!empty($nis_filter_list)) {
        $total_siswa = count($nis_filter_list);
        $escaped_nis = array_map(function ($v) use ($koneksi) {
            return mysqli_real_escape_string($koneksi, $v);
        }, $nis_filter_list);
        $nis_in = "'" . implode("','", $escaped_nis) . "'";
        $where_nis_clause = " AND nis IN ($nis_in)";
    } else {
        $total_siswa = 0;
    }
} else {
    // Tanpa filter tahun ajaran, pakai total semua siswa
    $query_total_siswa = mysqli_query($koneksi, "SELECT COUNT(*) AS total_siswa FROM tabel_siswa");
    if ($query_total_siswa) {
        $data_total_siswa = mysqli_fetch_assoc($query_total_siswa);
        $total_siswa = (int) ($data_total_siswa['total_siswa'] ?? 0);
    }
}

$rekap_semester = [];
foreach ($daftar_bulan_rekap as $item_rekap) {
    $bulan_semester = $item_rekap['bulan'];
    $tahun_target_rekap = $item_rekap['tahun'];

    $bulan_semester_safe = mysqli_real_escape_string($koneksi, $bulan_semester);
    $query_bayar_semester = mysqli_query(
        $koneksi,
        "SELECT COUNT(DISTINCT nis) AS total_bayar 
         FROM tabel_pembayaran 
         WHERE bulan_bayar = '$bulan_semester_safe' 
           AND tahun_bayar = '$tahun_target_rekap'"
            . $where_nis_clause
    );

    $total_bayar = 0;
    if ($query_bayar_semester) {
        $data_bayar_semester = mysqli_fetch_assoc($query_bayar_semester);
        $total_bayar = (int) ($data_bayar_semester['total_bayar'] ?? 0);
    }

    $total_belum = max($total_siswa - $total_bayar, 0);
    $persentase = $total_siswa > 0 ? round(($total_bayar / $total_siswa) * 100, 1) : 0;

    $rekap_semester[] = [
        'bulan' => $bulan_semester,
        'total_bayar' => $total_bayar,
        'total_belum' => $total_belum,
        'persentase' => $persentase
    ];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Transaksi Pembayaran</title>

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
                    <span class="me-3 d-none d-md-inline">Selamat Datang, <strong><?php echo $_SESSION['nama_admin']; ?></strong>!</span>
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
            <h2 class="mt-4">Input Transaksi Pembayaran SPP</h2>
            <p class="lead">Cari siswa terlebih dahulu untuk melanjutkan pencatatan transaksi.</p>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-search"></i> Cari Siswa
                </div>
                <div class="card-body">
                    <form action="transaksi.php" method="get" class="row g-3">
                        <div class="col-md-8">
                            <input type="text" id="cari_nis" name="nis" class="form-control" placeholder="Masukkan NIS Siswa atau Nama Siswa..." required value="<?php echo htmlspecialchars($cari); ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Cari Data</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php
            // --- Bagian B: Tampilkan Form Pembayaran setelah siswa ditemukan ---
            if (!empty($cari)) {
                if ($data_siswa) {
                    // Siswa Ditemukan
            ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <i class="fas fa-info-circle"></i> Detail Siswa
                                </div>
                                <div class="card-body">
                                    <p><strong>NIS:</strong> <?php echo $data_siswa['nis']; ?></p>
                                    <p><strong>Nama:</strong> <?php echo $data_siswa['nama_siswa']; ?></p>
                                    <p><strong>Kelas:</strong> <?php echo $data_siswa['nama_kelas']; ?></p>
                                    <p><strong>Kelamin:</strong>
                                        <?php
                                        if (!isset($data_siswa['kelamin']) || $data_siswa['kelamin'] === '') {
                                            echo '-';
                                        } elseif ($data_siswa['kelamin'] === 'L') {
                                            echo 'Laki-laki';
                                        } elseif ($data_siswa['kelamin'] === 'P') {
                                            echo 'Perempuan';
                                        } else {
                                            echo htmlspecialchars($data_siswa['kelamin']);
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <i class="fas fa-money-bill-wave"></i> Form Pencatatan Pembayaran
                                </div>
                                <div class="card-body">
                                    <form action="proses_transaksi.php" method="post">
                                        <input type="hidden" name="nis" value="<?php echo $data_siswa['nis']; ?>">

                                        <div class="mb-3">
                                            <label for="id_biaya" class="form-label">Jenis Pembayaran:</label>
                                            <select id="id_biaya" name="id_biaya" class="form-select" required>
                                                <option value="">-- Pilih Jenis Biaya --</option>
                                                <?php
                                                // Tampilkan pilihan biaya dari database
                                                while ($data_biaya = mysqli_fetch_assoc($result_biaya)) {
                                                    echo "<option value='{$data_biaya['id_biaya']}'>{$data_biaya['nama_biaya']} (Rp " . number_format($data_biaya['nominal'], 0, ',', '.') . ")</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="bulan_bayar" class="form-label">Bulan SPP yang Dibayar:</label>
                                                <select id="bulan_bayar" name="bulan_bayar" class="form-select" required>
                                                    <option value="">-- Pilih Bulan --</option>
                                                    <option value="Januari">Januari</option>
                                                    <option value="Februari">Februari</option>
                                                    <option value="Maret">Maret</option>
                                                    <option value="April">April</option>
                                                    <option value="Mei">Mei</option>
                                                    <option value="Juni">Juni</option>
                                                    <option value="Juli">Juli</option>
                                                    <option value="Agustus">Agustus</option>
                                                    <option value="September">September</option>
                                                    <option value="Oktober">Oktober</option>
                                                    <option value="November">November</option>
                                                    <option value="Desember">Desember</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="tahun_bayar" class="form-label">Tahun Pembayaran:</label>
                                                <input type="number" id="tahun_bayar" name="tahun_bayar" class="form-control" required min="2020" max="2099" value="<?php echo date('Y'); ?>">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="tgl_pembayaran" class="form-label">Tanggal Pembayaran:</label>
                                                <input type="date" id="tgl_pembayaran" name="tgl_pembayaran" class="form-control" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="jumlah_bayar" class="form-label">Jumlah Bayar (Nominal):</label>
                                                <input type="number" id="jumlah_bayar" name="jumlah_bayar" class="form-control" required min="1000">
                                            </div>
                                        </div>

                                        <button type="submit" name="simpan_pembayaran" class="btn btn-success mt-3 w-100">
                                            <i class="fas fa-save"></i> Simpan Transaksi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

            <?php
                } else {
                    // Siswa tidak ditemukan
                    echo "<div class='alert alert-danger' role='alert'><i class='fas fa-exclamation-triangle'></i> Data siswa dengan NIS/Nama <strong>" . htmlspecialchars($cari) . "</strong> tidak ditemukan. Silakan cek kembali.</div>";
                }
            }
            ?>

            <!-- Rekapitulasi Pembayaran Semester -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <i class="fas fa-chart-pie"></i> Rekap Pembayaran SPP - <?php echo $judul_semester; ?>
                        </div>
                        <small>Data siswa aktif: <?php echo number_format($total_siswa); ?> siswa</small>
                    </div>
                </div>
                <div class="card-body">
                    <form action="transaksi.php" method="get" class="row g-3 align-items-end mb-4">
                        <div class="col-md-4 col-lg-3">
                            <label for="tahunAjaranCetak" class="form-label mb-1">Pilih Tahun Ajaran:</label>
                            <select id="tahunAjaranCetak" name="tahun_ajaran" class="form-select" <?php echo empty($daftar_tahun_ajaran) ? 'disabled' : ''; ?> required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php foreach ($daftar_tahun_ajaran as $tahun_ajaran_option) {
                                    $selected = ($filter_tahun_ajaran == $tahun_ajaran_option) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo htmlspecialchars($tahun_ajaran_option); ?>" <?php echo $selected; ?>>
                                        <?php echo htmlspecialchars($tahun_ajaran_option); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-auto">
                            <!-- Tombol Tampilkan Data di Tabel -->
                            <button type="submit" class="btn btn-primary text-white mt-3 mt-md-0 me-2" <?php echo empty($daftar_tahun_ajaran) ? 'disabled' : ''; ?>>
                                <i class="fas fa-eye"></i> Tampilkan
                            </button>
                            <!-- Tombol Cetak PDF/Print -->
                            <button type="submit" formaction="rekap_pembayaran_print.php" formtarget="_blank" class="btn btn-info text-white mt-3 mt-md-0" <?php echo empty($daftar_tahun_ajaran) ? 'disabled' : ''; ?>>
                                <i class="fas fa-print"></i> Cetak Rekap Tahunan
                            </button>
                        </div>
                        <?php if (empty($daftar_tahun_ajaran)) { ?>
                            <div class="col-12">
                                <div class="alert alert-warning mb-0 py-2">
                                    <small><i class="fas fa-info-circle"></i> Belum ada data tahun ajaran yang tersedia untuk dicetak.</small>
                                </div>
                            </div>
                        <?php } ?>
                    </form>

                    <?php if ($total_siswa === 0) { ?>
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-circle"></i> Belum ada data siswa yang terdaftar sehingga rekap tidak dapat ditampilkan.
                        </div>
                    <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 20%;">Bulan</th>
                                        <th style="width: 25%;">Sudah Bayar</th>
                                        <th style="width: 25%;">Belum Bayar</th>
                                        <th style="width: 30%;">Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rekap_semester as $rekap) { ?>
                                        <tr>
                                            <td><strong><?php echo $rekap['bulan']; ?></strong></td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <?php echo number_format($rekap['total_bayar']); ?> siswa
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger">
                                                    <?php echo number_format($rekap['total_belum']); ?> siswa
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height: 20px;">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $rekap['persentase']; ?>%;" aria-valuenow="<?php echo $rekap['persentase']; ?>" aria-valuemin="0" aria-valuemax="100">
                                                            <?php echo $rekap['persentase']; ?>%
                                                        </div>
                                                    </div>
                                                    <small><?php echo $rekap['persentase']; ?>%</small>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted mb-0"><small>* Rekap otomatis mengikuti semester aktif berdasarkan bulan saat ini.</small></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js?v=<?php echo time(); ?>"></script>
    <script src="../assets/js/admin-responsive.js?v=<?php echo time(); ?>"></script>
    <script>
        // Tunggu sampai DOM selesai dimuat
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Auto-update tanggal pembayaran: Script loaded');

            // Fungsi untuk update tanggal pembayaran berdasarkan bulan dan tahun
            function updateTanggalPembayaran() {
                const bulanSelect = document.getElementById('bulan_bayar');
                const tahunInput = document.getElementById('tahun_bayar');
                const tanggalInput = document.getElementById('tgl_pembayaran');

                // Pastikan semua elemen ada
                if (!bulanSelect || !tahunInput || !tanggalInput) {
                    console.log('Elemen tidak ditemukan');
                    return;
                }

                const bulan = bulanSelect.value;
                const tahun = tahunInput.value;

                console.log('Bulan:', bulan, 'Tahun:', tahun);

                if (bulan && tahun) {
                    // Mapping nama bulan ke nomor bulan
                    const bulanMap = {
                        'Januari': '01',
                        'Februari': '02',
                        'Maret': '03',
                        'April': '04',
                        'Mei': '05',
                        'Juni': '06',
                        'Juli': '07',
                        'Agustus': '08',
                        'September': '09',
                        'Oktober': '10',
                        'November': '11',
                        'Desember': '12'
                    };

                    const nomorBulan = bulanMap[bulan];
                    if (nomorBulan) {
                        // Set tanggal ke tanggal 1 dari bulan yang dipilih
                        const tanggalBaru = tahun + '-' + nomorBulan + '-01';
                        tanggalInput.value = tanggalBaru;
                        console.log('Tanggal diupdate menjadi:', tanggalBaru);
                    }
                }
            }

            // Event listener untuk bulan dan tahun
            const bulanSelect = document.getElementById('bulan_bayar');
            const tahunInput = document.getElementById('tahun_bayar');

            if (bulanSelect) {
                bulanSelect.addEventListener('change', function() {
                    console.log('Bulan berubah');
                    updateTanggalPembayaran();
                });
                console.log('Event listener bulan terpasang');
            }

            if (tahunInput) {
                tahunInput.addEventListener('input', function() {
                    console.log('Tahun berubah');
                    updateTanggalPembayaran();
                });
                console.log('Event listener tahun terpasang');
            }
        });
    </script>
</body>

</html>