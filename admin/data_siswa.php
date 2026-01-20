<?php
// A. Panggil file koneksi dan proteksi
include('../includes/koneksi.php');
include('../includes/proteksi.php');

// Tentukan bulan dan tahun saat ini
$bulan_numerik = date('n'); // 1-12
$bulan_indonesia = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];
$bulan_sekarang = $bulan_indonesia[$bulan_numerik];
$tahun_sekarang = date('Y');

// TAMBAHAN 1: Ambil Data Kelas untuk Dropdown Pindah Kelas
$query_kelas = mysqli_query($koneksi, "SELECT * FROM tabel_kelas ORDER BY nama_kelas ASC");

// Keyword pencarian (NIS / Nama)
$keyword = isset($_GET['q']) ? mysqli_real_escape_string($koneksi, $_GET['q']) : '';

// Tambahkan filter pencarian jika keyword tidak kosong
$where_clause = '';
if ($keyword !== '') {
    $where_clause = "WHERE (s.nis LIKE '%$keyword%' OR s.nama_siswa LIKE '%$keyword%')";
}

// Query Data Siswa (Termasuk Subquery Tahun Terbaru)
$query_siswa = "SELECT s.*, k.nama_kelas, p.id_pembayaran,
                (SELECT tahun_ajaran FROM tabel_siswa_aktif tsa 
                 WHERE tsa.nis = s.nis 
                 ORDER BY tsa.id DESC LIMIT 1) as tahun_terbaru
                FROM tabel_siswa s 
                JOIN tabel_kelas k ON s.id_kelas = k.id_kelas
                LEFT JOIN tabel_pembayaran p ON 
                    s.nis = p.nis AND 
                    p.bulan_bayar = '$bulan_sekarang' AND 
                    p.tahun_bayar = '$tahun_sekarang'
                $where_clause
                ORDER BY s.nis ASC";

$result_siswa = mysqli_query($koneksi, $query_siswa);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Siswa</title>

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


    <?php include('../includes/sidebar.php'); ?>


    <div id="content">
        <div class="container-fluid">
            <h2 class="mt-4">Kelola Data Master Siswa</h2>
            <p class="lead">Tambah, ubah, dan hapus data siswa di sini.</p>

            <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'sukses') { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Berhasil!</strong> Data siswa terpilih telah diproses.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <div class="row g-2 align-items-center mb-3">
                <div class="col-lg-6 col-md-7 col-sm-8 mb-2 mb-sm-0">
                    <form class="d-flex" method="get" action="">
                        <input type="text" name="q" class="form-control me-2" placeholder="Cari NIS atau nama siswa..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i> Cari</button>
                    </form>
                </div>
                <div class="col-auto">
                    <a href="data_siswa.php" class="btn btn-outline-secondary"><i class="fas fa-undo"></i> Reset</a>
                </div>
                <div class="col-auto ms-auto">
                    <a href="tambah_siswa.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Data Siswa Baru</a>
                </div>
            </div>

            <form action="proses_kenaikan.php" method="POST" onsubmit="return confirm('Yakin ingin memproses siswa yang dipilih? Pastikan Tahun dan Kelas tujuan sudah benar.');">

                <div class="card mb-2 bg-light border-0">
                    <div class="card-body p-2">
                        <div class="d-flex align-items-center flex-wrap gap-2">

                            <span class="fw-bold text-primary"><i class="fas fa-level-up-alt"></i> Opsi Kenaikan:</span>

                            <select name="tahun_ajaran_baru" class="form-select form-select-sm w-auto" required>
                                <option value="">-- Pilih Tahun Baru --</option>
                                <option value="2024-2025">2024-2025</option>
                                <option value="2025-2026">2025-2026</option>
                                <option value="2026-2027">2026-2027</option>
                            </select>

                            <select name="id_kelas_baru" class="form-select form-select-sm w-auto">
                                <option value="">-- Tetap di Kelas Lama --</option>
                                <?php while ($row_kelas = mysqli_fetch_assoc($query_kelas)) { ?>
                                    <option value="<?php echo $row_kelas['id_kelas']; ?>">
                                        Pindah ke: <?php echo $row_kelas['nama_kelas']; ?>
                                    </option>
                                <?php } ?>
                            </select>

                            <button type="submit" name="btn_proses_naik" class="btn btn-success btn-sm">
                                <i class="fas fa-check-circle"></i> Proses Pindah
                            </button>
                        </div>
                        <small class="text-muted fst-italic ms-1">*Centang siswa di tabel, pilih tujuan tahun & kelas, lalu klik Proses.</small>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover border">
                        <thead class="table-dark text-white">
                            <tr>
                                <th class="text-center" width="40">
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th>No.</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Tahun Aktif</th>
                                <th>Kelamin</th>
                                <th>Status SPP (<?php echo $bulan_sekarang; ?>)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($data = mysqli_fetch_assoc($result_siswa)) {
                            ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="nis_dipilih[]" value="<?php echo $data['nis']; ?>" class="checkItem">
                                    </td>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $data['nis']; ?></td>
                                    <td><?php echo $data['nama_siswa']; ?></td>
                                    <td><?php echo $data['nama_kelas']; ?></td>

                                    <td>
                                        <?php if (!empty($data['tahun_terbaru'])) { ?>
                                            <span class="badge bg-info text-dark">
                                                <?php echo $data['tahun_terbaru']; ?>
                                            </span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary">-</span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <?php
                                        if (!isset($data['kelamin']) || $data['kelamin'] === '') {
                                            echo '-';
                                        } elseif ($data['kelamin'] === 'L') {
                                            echo 'Laki-laki';
                                        } elseif ($data['kelamin'] === 'P') {
                                            echo 'Perempuan';
                                        } else {
                                            echo htmlspecialchars($data['kelamin']);
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <?php if ($data['id_pembayaran'] != null) { ?>
                                            <span class="badge bg-success">LUNAS</span>
                                        <?php } else { ?>
                                            <span class="badge bg-danger">BELUM BAYAR</span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <a href="edit_siswa.php?nis=<?php echo $data['nis']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="hapus_siswa.php?nis=<?php echo $data['nis']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin?');"><i class="fas fa-trash"></i> Hapus</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </form>

        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin-responsive.js"></script>

    <script>
        document.getElementById('checkAll').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.checkItem');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });
    </script>
</body>

</html>