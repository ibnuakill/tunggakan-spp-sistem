<?php
// Homepage tidak perlu proteksi session, karena siapa pun boleh melihat
include('includes/koneksi.php');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pembayaran SPP - Selamat Datang</title>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid container">
            <a class="navbar-brand" href="index.php">
                <i><img src="./assets/img/paud.png" alt="" srcset="" style="width: 30px; height: 30px;"></i> KB Manggasari
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item"></li>
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#sejarahModal">
                        <i class="fas fa-info-circle"></i> Tentang
                    </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gallery.php">
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

    <!-- Hero Section Full Screen -->
    <div class="hero-section">
        <h1 class="display-3">Sistem Pembayaran SPP Online</h1>
        <p class="lead">Solusi modern untuk manajemen pembayaran SPP yang efisien, transparan, dan mudah digunakan</p>
        <a href="login_admin.php" class="btn btn-primary btn-lg">
            <i class="fas fa-rocket"></i> Masuk ke Portal Admin
        </a>
    </div>

    <!-- Features Section -->
    <div class="container-fluid" id="fitur">
        <div class="row text-center">
            <div class="col-12 col-md-4 mb-4 mb-md-0">
                <i class="fas fa-check-circle fa-4x text-success"></i>
                <h5 class="mt-3">Laporan Akurat</h5>
                <p class="text-muted">Data pembayaran tercatat dengan rapi dan akurat secara real-time</p>
            </div>
            <div class="col-12 col-md-4 mb-4 mb-md-0">
                <i class="fas fa-clock fa-4x text-primary"></i>
                <h5 class="mt-3">Hemat Waktu</h5>
                <p class="text-muted">Proses pembayaran dan pelaporan menjadi lebih cepat dan efisien</p>
            </div>
            <div class="col-12 col-md-4">
                <i class="fas fa-database fa-4x text-info"></i>
                <h5 class="mt-3">Data Terpusat</h5>
                <p class="text-muted">Semua informasi tersimpan dalam satu sistem yang terintegrasi</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Sistem Pembayaran SPP. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Modal Sejarah -->
    <div class="modal fade" id="sejarahModal" tabindex="-1" aria-labelledby="sejarahModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="sejarahModalLabel">
                        <i class="fas fa-book"></i> Sejarah Berdirinya KB Manggasari
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="sejarah-content">
                        <div class="text-center mb-4">
                            <i class="fas fa-school fa-3x text-primary mb-3"></i>
                        </div>

                        <h6 class="text-primary mb-3">
                            <i class="fas fa-calendar-alt"></i> Awal Berdiri
                        </h6>
                        <p class="text-justify mb-4">
                            KB Manggasari didirikan dengan visi mulia untuk memberikan pendidikan usia dini yang berkualitas
                            kepada anak-anak Indonesia. Sekolah ini berdiri atas dasar kepedulian terhadap pentingnya
                            fondasi pendidikan yang kuat sejak usia dini.
                        </p>

                        <h6 class="text-primary mb-3">
                            <i class="fas fa-lightbulb"></i> Visi & Misi
                        </h6>
                        <p class="text-justify mb-3">
                            <strong>Visi:</strong> Menjadi lembaga pendidikan taman kanak-kanak yang unggul dalam
                            membentuk karakter, kreativitas, dan kecerdasan anak didik.
                        </p>
                        <p class="text-justify mb-4">
                            <strong>Misi:</strong> Menyediakan lingkungan belajar yang menyenangkan, aman, dan
                            kondusif untuk mengembangkan potensi anak secara optimal melalui pendekatan pembelajaran
                            yang kreatif dan inovatif.
                        </p>

                        <h6 class="text-primary mb-3">
                            <i class="fas fa-star"></i> Perkembangan
                        </h6>
                        <p class="text-justify mb-4">
                            Sejak didirikan, KB Manggasari terus berkembang dan berkomitmen untuk memberikan pelayanan
                            terbaik kepada siswa dan orang tua. Dengan dukungan tenaga pendidik yang berpengalaman
                            dan fasilitas yang memadai, sekolah ini terus berinovasi dalam metode pembelajaran
                            untuk menghadapi tantangan zaman.
                        </p>

                        <h6 class="text-primary mb-3">
                            <i class="fas fa-heart"></i> Komitmen
                        </h6>
                        <p class="text-justify">
                            KB Manggasari berkomitmen untuk terus memberikan kontribusi positif dalam dunia pendidikan
                            Indonesia, membentuk generasi yang cerdas, berkarakter, dan siap menghadapi masa depan.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kontak -->
    <div class="modal fade" id="kontakModal" tabindex="-1" aria-labelledby="kontakModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
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

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Konfigurasi WhatsApp Business
        // Ganti nomor berikut dengan nomor WhatsApp Business Anda (format: 6281234567890 tanpa +)
        const whatsappNumber = '6285722698699'; // Contoh: 6281234567890
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

            // Ganti email berikut dengan email sekolah/admin Anda
            const emailTujuan = 'kober.manggarsari@gmail.com';

            // Membuka email client dengan data yang sudah diisi
            const mailtoLink = `mailto:${emailTujuan}?subject=${encodeURIComponent(subjek)}&body=${encodeURIComponent('Nama: ' + nama + '\nEmail: ' + email + '\n\nPesan:\n' + pesan)}`;
            window.location.href = mailtoLink;

            // Tampilkan notifikasi
            alert('Email client akan dibuka. Silakan kirim email Anda.');

            // Reset form
            this.reset();
        });
    </script>
</body>

</html>