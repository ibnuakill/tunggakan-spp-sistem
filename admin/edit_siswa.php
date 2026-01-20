<?php
include('../includes/koneksi.php');
include('../includes/proteksi.php');
include('../includes/validation.php');

$nis_edit = sanitize_input($_GET['nis']);

// 1. Prepared Statement untuk mengambil data siswa lama
$stmt_lama = $koneksi->prepare("SELECT s.*, 
               (SELECT tahun_ajaran FROM tabel_siswa_aktif WHERE nis = s.nis ORDER BY id DESC LIMIT 1) as tahun_ajaran
               FROM tabel_siswa s 
               WHERE s.nis = ?");
$stmt_lama->bind_param("s", $nis_edit);
$stmt_lama->execute();
$result_lama = $stmt_lama->get_result();
$data_lama = $result_lama->fetch_assoc();
$stmt_lama->close();

// Jika data tidak ditemukan, redirect
if (!$data_lama) {
    echo "<script>alert('Data siswa tidak ditemukan!'); window.location='data_siswa.php';</script>";
    exit();
}

// Query untuk data dropdown kelas (Sama seperti Tambah Siswa)
$query_kelas = "SELECT * FROM tabel_kelas ORDER BY nama_kelas ASC";
$result_kelas = mysqli_query($koneksi, $query_kelas);

if (isset($_POST['update'])) {
    // 2. Ambil data dari form
    $nis_lama = $_POST['nis_lama'];
    $nama_siswa_baru = $_POST['nama_siswa'];
    $id_kelas_baru = $_POST['id_kelas'];
    $tahun_ajaran_baru = $_POST['tahun_ajaran'];
    $kelamin_baru = $_POST['kelamin'];
    
    // 3. Validasi input
    $validation_errors = [];
    
    $nama_check = validate_nama($nama_siswa_baru);
    if (!$nama_check['valid']) {
        $validation_errors[] = $nama_check['message'];
    }
    
    $tahun_check = validate_tahun_ajaran($tahun_ajaran_baru);
    if (!$tahun_check['valid']) {
        $validation_errors[] = $tahun_check['message'];
    }
    
    if (!empty($validation_errors)) {
        $error_message = implode("\\n", $validation_errors);
        echo "<script>alert('Error validasi:\\n$error_message'); window.history.back();</script>";
        exit();
    }
    
    // 4. Sanitasi input
    $nama_siswa_baru = sanitize_input($nama_siswa_baru);
    $tahun_ajaran_baru = sanitize_input($tahun_ajaran_baru);

    // 5. Prepared Statement untuk UPDATE tabel_siswa
    $stmt_update = $koneksi->prepare("UPDATE tabel_siswa SET nama_siswa = ?, id_kelas = ?, kelamin = ? WHERE nis = ?");
    $stmt_update->bind_param("siss", $nama_siswa_baru, $id_kelas_baru, $kelamin_baru, $nis_lama);

    if ($stmt_update->execute()) {
        $stmt_update->close();
        
        // Cek apakah sudah ada record untuk NIS ini di tabel_siswa_aktif
        $stmt_check = $koneksi->prepare("SELECT id FROM tabel_siswa_aktif WHERE nis = ? ORDER BY id DESC LIMIT 1");
        $stmt_check->bind_param("s", $nis_lama);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            // Update record yang sudah ada
            $data_aktif = $result_check->fetch_assoc();
            $stmt_update_aktif = $koneksi->prepare("UPDATE tabel_siswa_aktif SET tahun_ajaran = ? WHERE id = ?");
            $stmt_update_aktif->bind_param("si", $tahun_ajaran_baru, $data_aktif['id']);
            $stmt_update_aktif->execute();
            $stmt_update_aktif->close();
        } else {
            // Insert record baru jika belum ada
            $stmt_insert_aktif = $koneksi->prepare("INSERT INTO tabel_siswa_aktif (nis, tahun_ajaran) VALUES (?, ?)");
            $stmt_insert_aktif->bind_param("ss", $nis_lama, $tahun_ajaran_baru);
            $stmt_insert_aktif->execute();
            $stmt_insert_aktif->close();
        }
        $stmt_check->close();
        
        echo "<script>alert('Data siswa berhasil diubah!'); window.location='data_siswa.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah data: " . $stmt_update->error . "');</script>";
        $stmt_update->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Siswa - Sistem Pembayaran SPP</title>

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
                        <i class="fas fa-user-edit text-primary"></i> Edit Data Siswa
                    </h2>
                    <p class="lead text-muted">Ubah data siswa: <strong><?php echo htmlspecialchars($data_lama['nama_siswa']); ?></strong></p>
                </div>
                <a href="data_siswa.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <hr>

            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-edit"></i> Form Edit Siswa
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" id="formEditSiswa">
                                <div class="mb-3">
                                    <label for="nis" class="form-label">
                                        <i class="fas fa-id-card text-primary"></i> NIS (Nomor Induk Siswa)
                                    </label>
                                    <input type="text"
                                        class="form-control"
                                        id="nis"
                                        name="nis"
                                        value="<?php echo htmlspecialchars($data_lama['nis']); ?>"
                                        disabled
                                        style="background-color: #e9ecef;">
                                    <input type="hidden" name="nis_lama" value="<?php echo htmlspecialchars($data_lama['nis']); ?>">
                                    <small class="form-text text-muted">NIS tidak dapat diubah</small>
                                </div>

                                <div class="mb-3">
                                    <label for="nama_siswa" class="form-label">
                                        <i class="fas fa-user text-primary"></i> Nama Siswa <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control"
                                        id="nama_siswa"
                                        name="nama_siswa"
                                        value="<?php echo htmlspecialchars($data_lama['nama_siswa']); ?>"
                                        required
                                        placeholder="Masukkan nama lengkap siswa">
                                </div>

                                <div class="mb-3">
                                    <label for="kelamin" class="form-label">
                                        <i class="fas fa-venus-mars text-primary"></i> Jenis Kelamin <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="kelamin" name="kelamin" required>
                                        <option value="">-- Pilih Jenis Kelamin --</option>
                                        <option value="L" <?php echo ($data_lama['kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                        <option value="P" <?php echo ($data_lama['kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="id_kelas" class="form-label">
                                        <i class="fas fa-chalkboard text-primary"></i> Kelas <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="id_kelas" name="id_kelas" required>
                                        <?php
                                        // Reset pointer result untuk looping ulang
                                        mysqli_data_seek($result_kelas, 0);
                                        while ($data_kelas = mysqli_fetch_assoc($result_kelas)) {
                                            // Membuat kelas lama terpilih secara otomatis (SELECTED)
                                            $selected = ($data_kelas['id_kelas'] == $data_lama['id_kelas']) ? 'selected' : '';
                                            echo "<option value='{$data_kelas['id_kelas']}' {$selected}>{$data_kelas['nama_kelas']}</option>";
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
                                        value="<?php echo htmlspecialchars($data_lama['tahun_ajaran']); ?>"
                                        required
                                        maxlength="10"
                                        placeholder="Contoh: 2024/2025">
                                    <small class="form-text text-muted">Format: YYYY/YYYY (contoh: 2024/2025)</small>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <a href="data_siswa.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
                                    <button type="submit" name="update" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Simpan Perubahan
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
        document.getElementById('formEditSiswa').addEventListener('submit', function(e) {
            const namaSiswa = document.getElementById('nama_siswa').value.trim();
            const kelamin = document.getElementById('kelamin').value;
            const kelas = document.getElementById('id_kelas').value;
            const tahunAjaran = document.getElementById('tahun_ajaran').value.trim();

            if (!namaSiswa || !kelamin || !kelas || !tahunAjaran) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi!');
                return false;
            }
        });
    </script>
</body>

</html>