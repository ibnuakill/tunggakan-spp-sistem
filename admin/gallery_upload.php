<?php
include '../includes/koneksi.php';
include '../includes/proteksi.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'archive') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
        $result = mysqli_query($koneksi, "SELECT id, file_path FROM tabel_galeri WHERE id=" . $id);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        if ($row) {
            $currentRel = $row['file_path'];
            if (strpos($currentRel, 'assets/img/gallery/') === 0) {
                $archiveDir = '../assets/img/gallery_archived';
                if (!is_dir($archiveDir)) {
                    mkdir($archiveDir, 0775, true);
                }
                $basename = basename($currentRel);
                $currentAbs = '../' . $currentRel;
                $targetAbs = $archiveDir . '/' . $basename;
                if (is_file($currentAbs) && rename($currentAbs, $targetAbs)) {
                    $newRel = 'assets/img/gallery_archived/' . $basename;
                    $u = mysqli_prepare($koneksi, "UPDATE tabel_galeri SET file_path=? WHERE id=?");
                    mysqli_stmt_bind_param($u, 'si', $newRel, $id);
                    mysqli_stmt_execute($u);
                    mysqli_stmt_close($u);
                } else {
                    $message = 'Gagal mengarsipkan file.';
                }
            }
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
        $result = mysqli_query($koneksi, "SELECT id, file_path FROM tabel_galeri WHERE id=" . $id);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        if ($row) {
            $abs = '../' . $row['file_path'];
            if (is_file($abs)) {
                @unlink($abs);
            }
        }
        $d = mysqli_prepare($koneksi, "DELETE FROM tabel_galeri WHERE id=?");
        mysqli_stmt_bind_param($d, 'i', $id);
        mysqli_stmt_execute($d);
        mysqli_stmt_close($d);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = isset($_POST['judul']) ? mysqli_real_escape_string($koneksi, trim($_POST['judul'])) : null;
    $deskripsi = isset($_POST['deskripsi']) ? mysqli_real_escape_string($koneksi, trim($_POST['deskripsi'])) : null;

    // Validasi file upload
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $message = 'Gagal mengunggah file. Pastikan Anda memilih gambar.';
    } else {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['foto']['tmp_name']);
        $fileSize = $_FILES['foto']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $message = 'Format gambar tidak didukung. Gunakan JPG, PNG, atau GIF.';
        } elseif ($fileSize > 5 * 1024 * 1024) { // 5 MB
            $message = 'Ukuran file terlalu besar. Maksimal 5MB.';
        } else {
            // Pastikan folder tujuan ada
            $uploadDir = '../assets/img/gallery';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            // Buat nama file aman dan unik
            $originalName = basename($_FILES['foto']['name']);
            $ext = pathinfo($originalName, PATHINFO_EXTENSION);
            $safeName = preg_replace('/[^A-Za-z0-9\-_.]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $newFileName = time() . '_' . $safeName . '.' . strtolower($ext);
            $targetPath = $uploadDir . '/' . $newFileName;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath)) {
                // Simpan path relatif untuk dipakai di gallery.php
                $relativePath = 'assets/img/gallery/' . $newFileName;
                $sql = "INSERT INTO tabel_galeri (judul, deskripsi, file_path) VALUES (?,?,?)";
                $stmt = mysqli_prepare($koneksi, $sql);
                mysqli_stmt_bind_param($stmt, 'sss', $judul, $deskripsi, $relativePath);
                $ok = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                if ($ok) {
                    header('Location: ../gallery.php?uploaded=1');
                    exit;
                } else {
                    $message = 'Gagal menyimpan data ke database.';
                }
            } else {
                $message = 'Gagal memindahkan file yang diunggah.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Galeri - Sistem Pembayaran SPP</title>
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
            $active_biaya = ($current_page == 'data_biaya.php') ? 'active' : '';
            $active_transaksi = ($current_page == 'transaksi.php') ? 'active' : '';
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
                        <i class="fas fa-images text-primary"></i> Upload Gambar Galeri
                    </h2>
                    <p class="lead text-muted">Unggah gambar beserta deskripsi. Hasilnya akan tampil di halaman galeri publik.</p>
                </div>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-upload"></i> Form Upload Galeri
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="judul" class="form-label">Judul (opsional)</label>
                                    <input type="text" id="judul" name="judul" class="form-control" placeholder="Contoh: Kegiatan Upacara" />
                                </div>
                                <div class="mb-3">
                                    <label for="foto" class="form-label">Pilih Gambar <span class="text-danger">*</span></label>
                                    <input type="file" id="foto" name="foto" class="form-control" accept="image/*" required />
                                </div>
                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi (opsional)</label>
                                    <textarea id="deskripsi" name="deskripsi" class="form-control" placeholder="Tambahkan deskripsi singkat..."></textarea>
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <h4 class="mb-3"><i class="fas fa-list"></i> Daftar Galeri</h4>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Preview</th>
                                <th>Info</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $list = mysqli_query($koneksi, "SELECT id, judul, deskripsi, file_path, uploaded_at FROM tabel_galeri ORDER BY uploaded_at DESC");
                            if ($list && mysqli_num_rows($list) > 0):
                                while ($row = mysqli_fetch_assoc($list)):
                                    $isArchived = strpos($row['file_path'], 'assets/img/gallery_archived/') === 0;
                                    $title = htmlspecialchars($row['judul'] ?: 'Dokumentasi');
                                    $date = date('d M Y', strtotime($row['uploaded_at']));
                                    $rel = htmlspecialchars($row['file_path']);
                            ?>
                            <tr>
                                <td style="width:120px"><img src="../<?= $rel ?>" alt="<?= $title ?>" class="img-thumbnail" style="max-width:100px"></td>
                                <td>
                                    <div class="fw-semibold"><?= $title ?></div>
                                    <div class="text-muted small"><?= htmlspecialchars($row['deskripsi'] ?: '') ?></div>
                                </td>
                                <td><span class="badge <?= $isArchived ? 'bg-secondary' : 'bg-success' ?>"><?= $isArchived ? 'Diarsipkan' : 'Aktif' ?></span></td>
                                <td><?= $date ?></td>
                                <td class="text-end">
                                    <?php if (!$isArchived): ?>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="action" value="archive">
                                        <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                        <button type="submit" class="btn btn-warning btn-sm"><i class="fas fa-box-archive"></i> Arsipkan</button>
                                    </form>
                                    <?php endif; ?>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Hapus gambar ini?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                            <tr><td colspan="5" class="text-center text-muted">Belum ada data galeri.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin-responsive.js"></script>

</body>

</html>