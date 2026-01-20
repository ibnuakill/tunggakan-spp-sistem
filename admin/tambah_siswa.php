<?php
include('../includes/koneksi.php');
include('../includes/proteksi.php');
include('../includes/validation.php');


if (isset($_POST['simpan'])) {
    // 1. Ambil data dari form
    $nis = $_POST['nis'];
    $nama_siswa = $_POST['nama_siswa'];
    $kelamin = $_POST['kelamin'];
    $id_kelas = $_POST['id_kelas'];
    $tahun_ajaran = $_POST['tahun_ajaran'];
    
    // 2. Validasi input
    $validation_errors = [];
    
    $nis_check = validate_nis($nis);
    if (!$nis_check['valid']) {
        $validation_errors[] = $nis_check['message'];
    }
    
    $nama_check = validate_nama($nama_siswa);
    if (!$nama_check['valid']) {
        $validation_errors[] = $nama_check['message'];
    }
    
    $tahun_check = validate_tahun_ajaran($tahun_ajaran);
    if (!$tahun_check['valid']) {
        $validation_errors[] = $tahun_check['message'];
    }
    
    // Jika ada error validasi, tampilkan
    if (!empty($validation_errors)) {
        $error_message = implode("\\n", $validation_errors);
        echo "<script>alert('Error validasi:\\n$error_message'); window.history.back();</script>";
        exit();
    }
    
    // 3. Sanitasi input
    $nis = sanitize_input($nis);
    $nama_siswa = sanitize_input($nama_siswa);
    $tahun_ajaran = sanitize_input($tahun_ajaran);
    
    // 4. Prepared Statement untuk INSERT ke tabel_siswa
    $stmt = $koneksi->prepare("INSERT INTO tabel_siswa (nis, nama_siswa, kelamin, id_kelas) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $nis, $nama_siswa, $kelamin, $id_kelas);
    
    if ($stmt->execute()) {
        // Berhasil disimpan ke tabel_siswa, sekarang simpan tahun_ajaran ke tabel_siswa_aktif
        $stmt_aktif = $koneksi->prepare("INSERT INTO tabel_siswa_aktif (nis, tahun_ajaran) VALUES (?, ?)");
        $stmt_aktif->bind_param("ss", $nis, $tahun_ajaran);
        
        if ($stmt_aktif->execute()) {
            echo "<script>alert('Data siswa berhasil disimpan!'); window.location='data_siswa.php';</script>";
        } else {
            echo "<script>alert('Data siswa tersimpan, tapi gagal menyimpan tahun ajaran: " . $stmt_aktif->error . "');</script>";
        }
        $stmt_aktif->close();
    } else {
        // Gagal disimpan (misal: NIS duplikat)
        echo "<script>alert('Gagal menyimpan data: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Query untuk mengambil semua data kelas
$query_kelas = "SELECT * FROM tabel_kelas ORDER BY nama_kelas ASC";
$result_kelas = mysqli_query($koneksi, $query_kelas);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Siswa - Sistem Pembayaran SPP</title>

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


    <?php include('../includes/sidebar.php'); ?>


    <div id="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mt-4 mb-2">
                        <i class="fas fa-user-plus text-primary"></i> Tambah Data Siswa Baru
                    </h2>
                    <p class="lead text-muted">Isi form di bawah ini untuk menambahkan data siswa baru ke sistem.</p>
                </div>
                <a href="data_siswa.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <hr>

            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-edit"></i> Form Tambah Siswa
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" id="formTambahSiswa">
                                <div class="mb-3">
                                    <label for="nis" class="form-label">
                                        <i class="fas fa-id-card text-primary"></i> NIS (Nomor Induk Siswa) <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control"
                                        id="nis"
                                        name="nis"
                                        required
                                        maxlength="15"
                                        placeholder="Masukkan NIS siswa">
                                    <small class="form-text text-muted">Maksimal 15 karakter</small>
                                </div>

                                <div class="mb-3">
                                    <label for="nama_siswa" class="form-label">
                                        <i class="fas fa-user text-primary"></i> Nama Siswa <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control"
                                        id="nama_siswa"
                                        name="nama_siswa"
                                        required
                                        placeholder="Masukkan nama lengkap siswa">
                                </div>

                                <div class="mb-3">
                                    <label for="kelamin" class="form-label">
                                        <i class="fas fa-venus-mars text-primary"></i> Jenis Kelamin <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="kelamin" name="kelamin" required>
                                        <option value="">-- Pilih Jenis Kelamin --</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="id_kelas" class="form-label">
                                        <i class="fas fa-chalkboard text-primary"></i> Kelas <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="id_kelas" name="id_kelas" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        <?php
                                        // Reset pointer result untuk looping ulang
                                        mysqli_data_seek($result_kelas, 0);
                                        // Looping untuk menampilkan data kelas sebagai pilihan dropdown
                                        while ($data_kelas = mysqli_fetch_assoc($result_kelas)) {
                                            // Value yang disimpan adalah id_kelas, yang merupakan Foreign Key
                                            echo "<option value='{$data_kelas['id_kelas']}'>{$data_kelas['nama_kelas']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="tahun_ajaran" class="form-label">
                                        <i class="fas fa-calendar-alt text-primary"></i> Tahun Aktif <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control"
                                        id="tahun_ajaran"
                                        name="tahun_ajaran"
                                        required
                                        maxlength="10"
                                        placeholder="Contoh: 2024/2025">
                                    <small class="form-text text-muted">Format: YYYY/YYYY (contoh: 2024/2025)</small>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <a href="data_siswa.php" class="btn btn-secondary">
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
        // Form validation
        document.getElementById('formTambahSiswa').addEventListener('submit', function(e) {
            const nis = document.getElementById('nis').value.trim();
            const namaSiswa = document.getElementById('nama_siswa').value.trim();
            const kelamin = document.getElementById('kelamin').value;
            const kelas = document.getElementById('id_kelas').value;
            const tahunAjaran = document.getElementById('tahun_ajaran').value.trim();

            if (!nis || !namaSiswa || !kelamin || !kelas || !tahunAjaran) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi!');
                return false;
            }
        });
    </script>
</body>

</html>