<?php
// Gallery Dokumentasi - tidak perlu proteksi session
include('includes/koneksi.php');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Dokumentasi - Sistem Pembayaran SPP</title>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="assets/css/gallery.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-graduation-cap"></i> TK GGFC
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#tentang">
                            <i class="fas fa-info-circle"></i> Tentang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="gallery.php">
                            <i class="fas fa-images"></i> Dokumentasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#kontakModal">
                            <i class="fas fa-envelope"></i> Kontak
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="login_admin.php" class="btn btn-warning">
                            <i class="fas fa-sign-in-alt"></i> Login Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Gallery Section -->
    <section class="gallery-section py-5" style="margin-top: 80px;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">
                    <i class="fas fa-images text-primary"></i> Gallery Dokumentasi
                </h2>
                <p class="lead text-muted">Koleksi foto dokumentasi kegiatan dan sejarah sekolah</p>
            </div>
            <?php $galeri = mysqli_query($koneksi, "SELECT id, judul, deskripsi, file_path, uploaded_at FROM tabel_galeri WHERE file_path LIKE 'assets/img/gallery/%' ORDER BY uploaded_at DESC"); ?>
            <div class="gallery-grid">
                <?php if ($galeri && mysqli_num_rows($galeri) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($galeri)): ?>
                        <div class="gallery-item">
                            <div class="gallery-image-wrapper">
                                <img src="<?= htmlspecialchars($row['file_path']) ?>" alt="<?= htmlspecialchars($row['judul'] ?: 'Dokumentasi') ?>" class="gallery-image">
                                <div class="gallery-overlay">
                                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#galleryModal<?= (int)$row['id'] ?>">
                                        <i class="fas fa-search-plus"></i> Lihat Detail
                                    </button>
                                </div>
                            </div>
                            <div class="gallery-caption">
                                <h5 class="gallery-title"><?= htmlspecialchars($row['judul'] ?: 'Dokumentasi') ?></h5>
                                <p class="gallery-description"><?= htmlspecialchars($row['deskripsi'] ?: 'Dokumentasi kegiatan di sekolah.') ?></p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> Tanggal: <?= date('d M Y', strtotime($row['uploaded_at'])) ?>
                                </small>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">Belum ada gambar galeri. Unggah dari halaman admin.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Sistem Pembayaran SPP. All Rights Reserved.</p>
            <p class="mb-0 small">Dibuat dengan <i class="fas fa-heart text-danger"></i> untuk pendidikan Indonesia</p>
        </div>
    </footer>

    <!-- Modal Kontak -->
    <div class="modal fade" id="kontakModal" tabindex="-1" aria-labelledby="kontakModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="kontakModalLabel">
                        <i class="fas fa-envelope"></i> Hubungi Kami
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form Email -->
                    <div class="mb-4">
                        <h6 class="mb-3">
                            <i class="fas fa-envelope text-primary"></i> Kirim Email
                        </h6>
                        <form id="emailForm">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="subjek" class="form-label">Subjek</label>
                                <input type="text" class="form-control" id="subjek" name="subjek" required>
                            </div>
                            <div class="mb-3">
                                <label for="pesan" class="form-label">Pesan</label>
                                <textarea class="form-control" id="pesan" name="pesan" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane"></i> Kirim Email
                            </button>
                        </form>
                    </div>

                    <hr class="my-4">

                    <!-- WhatsApp -->
                    <div class="text-center">
                        <h6 class="mb-3">
                            <i class="fab fa-whatsapp text-success"></i> Chat via WhatsApp
                        </h6>
                        <a href="#" id="whatsappLink" class="whatsapp-btn" target="_blank">
                            <i class="fab fa-whatsapp whatsapp-icon"></i>
                            <span>Hubungi via WhatsApp Business</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Modals untuk Lightbox (dinamis) -->
    <?php
    $galeriModal = mysqli_query($koneksi, "SELECT id, judul, deskripsi, file_path, uploaded_at FROM tabel_galeri WHERE file_path LIKE 'assets/img/gallery/%' ORDER BY uploaded_at DESC");
    if ($galeriModal && mysqli_num_rows($galeriModal) > 0):
        while ($row = mysqli_fetch_assoc($galeriModal)):
            $modalId = (int)$row['id'];
            $judul = htmlspecialchars($row['judul'] ?: 'Dokumentasi');
            $deskripsi = htmlspecialchars($row['deskripsi'] ?: 'Dokumentasi kegiatan di sekolah.');
            $img = htmlspecialchars($row['file_path']);
            $tanggal = date('d M Y', strtotime($row['uploaded_at']));
    ?>
    <div class="modal fade" id="galleryModal<?= $modalId ?>" tabindex="-1" aria-labelledby="galleryModalLabel<?= $modalId ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="galleryModalLabel<?= $modalId ?>"><?= $judul ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="<?= $img ?>" alt="<?= $judul ?>" class="img-fluid rounded mb-3">
                    <p class="text-muted"><?= $deskripsi ?></p>
                    <small class="text-muted">
                        <i class="fas fa-calendar"></i> Tanggal: <?= $tanggal ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
    <?php
        endwhile;
    endif;
    ?>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Konfigurasi WhatsApp Business
        const whatsappNumber = '6281234567890'; // Ganti dengan nomor WhatsApp Business Anda
        const defaultMessage = 'Halo, saya ingin bertanya tentang Sistem Pembayaran SPP.';

        // Setup WhatsApp Link
        document.getElementById('whatsappLink').href = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(defaultMessage)}`;

        // Handle Form Email
        document.getElementById('emailForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const nama = document.getElementById('nama').value;
            const email = document.getElementById('email').value;
            const subjek = document.getElementById('subjek').value;
            const pesan = document.getElementById('pesan').value;

            const emailTujuan = 'admin@sekolah.com'; // Ganti dengan email sekolah/admin Anda

            const mailtoLink = `mailto:${emailTujuan}?subject=${encodeURIComponent(subjek)}&body=${encodeURIComponent('Nama: ' + nama + '\nEmail: ' + email + '\n\nPesan:\n' + pesan)}`;
            window.location.href = mailtoLink;

            alert('Email client akan dibuka. Silakan kirim email Anda.');
            this.reset();
        });
    </script>
</body>

</html>