<?php
include('../includes/koneksi.php');
include('../includes/proteksi.php');

// Ambil input tanggal dari form (POST) untuk filter
$tgl_awal = isset($_POST['tgl_awal']) ? mysqli_real_escape_string($koneksi, $_POST['tgl_awal']) : '';
$tgl_akhir = isset($_POST['tgl_akhir']) ? mysqli_real_escape_string($koneksi, $_POST['tgl_akhir']) : '';

// Query untuk mengambil semua history pembayaran dengan detail lengkap
$query_history = "SELECT 
                    p.id_pembayaran,
                    p.tanggal_bayar,
                    p.bulan_bayar,
                    p.tahun_bayar,
                    p.jumlah_bayar,
                    p.id_biaya,
                    COALESCE(p.status_valid, 0) as status_valid,
                    s.nis,
                    s.nama_siswa,
                    k.nama_kelas,
                    b.nama_biaya,
                    b.nominal AS nominal_biaya,
                    a.nama_lengkap AS nama_admin
                FROM tabel_pembayaran p
                JOIN tabel_siswa s ON p.nis = s.nis
                JOIN tabel_kelas k ON s.id_kelas = k.id_kelas
                JOIN tabel_jenis_biaya b ON p.id_biaya = b.id_biaya
                LEFT JOIN tabel_admin a ON p.id_admin = a.id_admin";

// Tambahkan filter tanggal jika ada
if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $query_history .= " WHERE DATE(p.tanggal_bayar) BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$query_history .= " ORDER BY p.tanggal_bayar DESC, s.nis ASC";

$result_history = mysqli_query($koneksi, $query_history);

// Query untuk total dengan filter yang sama
$query_total_filter = "SELECT 
                        COUNT(*) as total_count,
                        SUM(jumlah_bayar) as total_sum
                      FROM tabel_pembayaran p";
if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $query_total_filter .= " WHERE DATE(p.tanggal_bayar) BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}
$result_total_filter = mysqli_query($koneksi, $query_total_filter);
$data_total_filter = mysqli_fetch_assoc($result_total_filter);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Lunas Biaya</title>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* Styling untuk tombol edit dan hapus yang disabled */
        .btn-edit:disabled,
        .btn-hapus:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #6c757d !important;
            border-color: #6c757d !important;
        }

        /* Styling untuk grup tombol */
        .btn-group {
            display: inline-flex;
            gap: 2px;
        }

        /* Styling untuk checkbox valid */
        .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }

        /* Header untuk Print */
        .print-header {
            display: none;
        }

        /* Sembunyikan tombol filter, navbar, dan sidebar saat mencetak */
        @media print {

            .no-print,
            #sidebar,
            .navbar,
            .btn,
            .card-header {
                display: none !important;
            }

            /* Tampilkan header print */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 3px solid #000;
            }

            .print-header h3 {
                margin: 0;
                font-size: 20px;
                font-weight: bold;
            }

            .print-header p {
                margin: 5px 0;
                font-size: 12px;
            }

            /* Konten penuh saat dicetak */
            body {
                background: white !important;
                font-size: 11pt;
            }

            #content {
                margin-left: 0 !important;
                padding: 15px !important;
                width: 100% !important;
            }

            .container-fluid {
                padding: 0 !important;
            }

            /* Card styling untuk print */
            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                margin-bottom: 15px;
                page-break-inside: avoid;
            }

            .card-body {
                padding: 10px !important;
            }

            /* Table styling untuk print */
            .table {
                font-size: 9pt;
                width: 100%;
                border-collapse: collapse;
            }

            .table thead {
                background-color: #333 !important;
                color: white !important;
            }

            .table thead th {
                border: 1px solid #000 !important;
                padding: 8px 5px !important;
                font-weight: bold;
                text-align: center;
            }

            .table tbody td {
                border: 1px solid #ddd !important;
                padding: 6px 5px !important;
                vertical-align: middle;
            }

            .table tbody tr {
                page-break-inside: avoid;
            }

            .table tbody tr:nth-child(even) {
                background-color: #f9f9f9 !important;
            }

            /* Hilangkan badge dan icon saat print */
            .badge {
                background: transparent !important;
                color: #000 !important;
                border: none !important;
                padding: 0 !important;
                font-weight: normal;
            }

            .badge::before {
                content: "";
            }

            i.fas,
            i.far {
                display: none !important;
            }

            /* Format tanggal dan waktu lebih sederhana */
            .table td small {
                display: none;
            }

            .table td strong {
                font-weight: bold;
            }

            /* Summary card untuk print */
            .card .row {
                margin: 0 !important;
            }

            .card h4 {
                font-size: 14pt;
                margin: 5px 0 !important;
            }

            .card h6 {
                font-size: 10pt;
                margin: 0 !important;
            }

            /* Hapus warna pada text saat print */
            .text-primary,
            .text-success,
            .text-muted {
                color: #000 !important;
            }

            /* Footer untuk print */
            .print-footer {
                display: block !important;
                margin-top: 30px;
                padding-top: 15px;
                border-top: 2px solid #000;
                text-align: center;
                font-size: 10pt;
            }

            /* Page break */
            @page {
                size: A4 landscape;
                margin: 15mm 10mm;
            }

            /* Pastikan tabel tidak terpotong */
            .table-responsive {
                overflow: visible !important;
            }
        }
    </style>
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
            <!-- Header untuk Print -->
            <div class="print-header">
                <h3>LAPORAN HISTORY PEMBAYARAN SPP</h3>
                <p><strong>TK GGFC</strong></p>
                <p>Jl. Alamat Sekolah, Kota, Provinsi</p>
                <?php if (!empty($tgl_awal) && !empty($tgl_akhir)) { ?>
                    <p>Periode: <?php echo date('d F Y', strtotime($tgl_awal)); ?> - <?php echo date('d F Y', strtotime($tgl_akhir)); ?></p>
                <?php } else { ?>
                    <p>Semua Periode</p>
                <?php } ?>
                <p>Tanggal Cetak: <?php echo date('d F Y, H:i'); ?> WIB</p>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 no-print flex-wrap">
                <div>
                    <h2 class="mt-4 mb-2">
                        <i class="fas fa-history text-primary"></i> History Pembayaran SPP
                    </h2>
                    <p class="lead">Riwayat lengkap semua transaksi pembayaran yang telah dilakukan.</p>
                </div>
                <div class="d-flex align-items-end gap-2 flex-wrap">
                    <!-- Filter Tanggal - Compact Version -->
                    <form action="status_pembayaran.php" method="post" class="d-flex align-items-end gap-2 flex-wrap">
                        <div>
                            <label for="tgl_awal" class="form-label small mb-1 d-block">Dari:</label>
                            <input type="date" id="tgl_awal" name="tgl_awal" class="form-control form-control-sm" value="<?php echo $tgl_awal; ?>" style="width: 150px;">
                        </div>
                        <div>
                            <label for="tgl_akhir" class="form-label small mb-1 d-block">Sampai:</label>
                            <input type="date" id="tgl_akhir" name="tgl_akhir" class="form-control form-control-sm" value="<?php echo $tgl_akhir; ?>" style="width: 150px;">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                        <?php if (!empty($tgl_awal) || !empty($tgl_akhir)) { ?>
                            <div>
                                <a href="status_pembayaran.php" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            </div>
                        <?php } ?>
                    </form>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover border" id="tableHistory">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 3%;">No.</th>
                            <th style="width: 10%;">Tanggal Bayar</th>
                            <th style="width: 8%;">NIS</th>
                            <th style="width: 18%;">Nama Siswa</th>
                            <th style="width: 8%;">Kelas</th>
                            <th style="width: 13%;">Jenis Biaya</th>
                            <th style="width: 10%;">Bulan/Tahun</th>
                            <th style="width: 10%;">Jumlah Bayar</th>
                            <th class="no-print" style="width: 8%;">Admin</th>
                            <th class="no-print" style="width: 5%;">Valid</th>
                            <th class="no-print" style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($result_history) > 0) {
                            while ($data = mysqli_fetch_assoc($result_history)) {
                                // Format tanggal
                                $tanggal_bayar = date('d/m/Y', strtotime($data['tanggal_bayar']));
                                $waktu_bayar = date('H:i', strtotime($data['tanggal_bayar']));
                        ?>
                                <tr id="row_<?php echo $data['id_pembayaran']; ?>">
                                    <td style="text-align: center;"><?php echo $no++; ?></td>
                                    <td><?php echo $tanggal_bayar; ?></td>
                                    <td><?php echo $data['nis']; ?></td>
                                    <td><?php echo htmlspecialchars($data['nama_siswa']); ?></td>
                                    <td><?php echo $data['nama_kelas']; ?></td>
                                    <td><?php echo htmlspecialchars($data['nama_biaya']); ?></td>
                                    <td><?php echo $data['bulan_bayar'] . ' ' . $data['tahun_bayar']; ?></td>
                                    <td style="text-align: right;">Rp <?php echo number_format($data['jumlah_bayar'], 0, ',', '.'); ?></td>
                                    <td class="no-print">
                                        <small><?php echo htmlspecialchars($data['nama_admin'] ?? 'System'); ?></small>
                                    </td>
                                    <td class="no-print" style="text-align: center;">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input valid-checkbox"
                                                type="checkbox"
                                                id="valid_<?php echo $data['id_pembayaran']; ?>"
                                                data-id="<?php echo $data['id_pembayaran']; ?>"
                                                <?php echo ($data['status_valid'] == 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="valid_<?php echo $data['id_pembayaran']; ?>"></label>
                                        </div>
                                    </td>
                                    <td class="no-print" style="text-align: center;">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm <?php echo ($data['status_valid'] == 1) ? 'btn-secondary' : 'btn-primary'; ?> btn-edit"
                                                data-id="<?php echo $data['id_pembayaran']; ?>"
                                                data-nis="<?php echo $data['nis']; ?>"
                                                data-id-biaya="<?php echo $data['id_biaya']; ?>"
                                                data-bulan="<?php echo $data['bulan_bayar']; ?>"
                                                data-tahun="<?php echo $data['tahun_bayar']; ?>"
                                                data-jumlah="<?php echo $data['jumlah_bayar']; ?>"
                                                data-tanggal="<?php echo $data['tanggal_bayar']; ?>"
                                                data-valid="<?php echo $data['status_valid']; ?>"
                                                <?php echo ($data['status_valid'] == 1) ? 'disabled title="Tidak bisa edit karena status Valid"' : ''; ?>>
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-sm <?php echo ($data['status_valid'] == 1) ? 'btn-secondary' : 'btn-danger'; ?> btn-hapus"
                                                data-id="<?php echo $data['id_pembayaran']; ?>"
                                                data-nis="<?php echo $data['nis']; ?>"
                                                data-nama="<?php echo htmlspecialchars($data['nama_siswa']); ?>"
                                                data-bulan="<?php echo $data['bulan_bayar']; ?>"
                                                data-tahun="<?php echo $data['tahun_bayar']; ?>"
                                                data-jumlah="<?php echo number_format($data['jumlah_bayar'], 0, ',', '.'); ?>"
                                                data-valid="<?php echo $data['status_valid']; ?>"
                                                <?php echo ($data['status_valid'] == 1) ? 'disabled title="Tidak bisa hapus karena status Valid"' : ''; ?>>
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted"><?php echo (!empty($tgl_awal) && !empty($tgl_akhir)) ? 'Tidak ada data pembayaran pada periode yang dipilih' : 'Belum ada data pembayaran'; ?></p>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f0f0f0 !important; font-weight: bold;">
                            <td colspan="6" style="text-align: right; padding: 10px !important;">TOTAL:</td>
                            <td colspan="1" style="text-align: right; padding: 10px !important;">
                                Rp <?php echo number_format($data_total_filter['total_sum'] ?? 0, 0, ',', '.'); ?>
                            </td>
                            <td colspan="3" class="no-print"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Footer untuk Print -->
            <div class="print-footer">
                <p><strong>Total Transaksi:</strong> <?php echo number_format($data_total_filter['total_count'] ?? 0); ?> transaksi</p>
                <p><strong>Total Penerimaan:</strong> Rp <?php echo number_format($data_total_filter['total_sum'] ?? 0, 0, ',', '.'); ?></p>
                <p style="margin-top: 20px;">Dicetak oleh: <?php echo htmlspecialchars($_SESSION['nama_admin'] ?? 'Admin'); ?> | <?php echo date('d F Y, H:i'); ?> WIB</p>
            </div>
        </div>
    </div>

    <!-- Modal Edit History Pembayaran -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editModalLabel">
                        <i class="fas fa-edit"></i> Edit History Pembayaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post" action="proses_edit_pembayaran.php">
                    <div class="modal-body">
                        <input type="hidden" name="id_pembayaran" id="edit_id_pembayaran">
                        <input type="hidden" name="nis" id="edit_nis">

                        <div class="mb-3">
                            <label for="edit_tanggal_bayar" class="form-label">Tanggal Bayar:</label>
                            <input type="date" class="form-control" id="edit_tanggal_bayar" name="tanggal_bayar" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_id_biaya" class="form-label">Jenis Biaya:</label>
                            <select class="form-select" id="edit_id_biaya" name="id_biaya" required>
                                <option value="">-- Pilih Jenis Biaya --</option>
                                <?php
                                // Query untuk mengambil data biaya
                                $query_biaya_edit = "SELECT * FROM tabel_jenis_biaya ORDER BY nama_biaya ASC";
                                $result_biaya_edit = mysqli_query($koneksi, $query_biaya_edit);
                                while ($biaya = mysqli_fetch_assoc($result_biaya_edit)) {
                                    echo "<option value='{$biaya['id_biaya']}'>{$biaya['nama_biaya']} (Rp " . number_format($biaya['nominal'], 0, ',', '.') . ")</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_bulan_bayar" class="form-label">Bulan SPP:</label>
                                <select class="form-select" id="edit_bulan_bayar" name="bulan_bayar" required>
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
                            <div class="col-md-6 mb-3">
                                <label for="edit_tahun_bayar" class="form-label">Tahun:</label>
                                <input type="number" class="form-control" id="edit_tahun_bayar" name="tahun_bayar" min="2020" max="2099" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_jumlah_bayar" class="form-label">Jumlah Bayar:</label>
                            <input type="number" class="form-control" id="edit_jumlah_bayar" name="jumlah_bayar" min="1000" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin-responsive.js"></script>
    <script>
        // Toggle Valid Status
        document.querySelectorAll('.valid-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const idPembayaran = this.getAttribute('data-id');
                const isValid = this.checked ? 1 : 0;
                const row = document.getElementById('row_' + idPembayaran);
                const editBtn = row.querySelector('.btn-edit');
                const hapusBtn = row.querySelector('.btn-hapus');

                // AJAX untuk update status valid
                fetch('proses_toggle_valid.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id_pembayaran=' + idPembayaran + '&status_valid=' + isValid
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update edit button state
                            if (isValid == 1) {
                                editBtn.disabled = true;
                                editBtn.setAttribute('title', 'Tidak bisa edit karena status Valid');
                                editBtn.classList.remove('btn-primary');
                                editBtn.classList.add('btn-secondary');

                                hapusBtn.disabled = true;
                                hapusBtn.setAttribute('title', 'Tidak bisa hapus karena status Valid');
                                hapusBtn.classList.remove('btn-danger');
                                hapusBtn.classList.add('btn-secondary');
                            } else {
                                editBtn.disabled = false;
                                editBtn.removeAttribute('title');
                                editBtn.classList.remove('btn-secondary');
                                editBtn.classList.add('btn-primary');

                                hapusBtn.disabled = false;
                                hapusBtn.removeAttribute('title');
                                hapusBtn.classList.remove('btn-secondary');
                                hapusBtn.classList.add('btn-danger');
                            }
                            // Update data-valid attribute
                            editBtn.setAttribute('data-valid', isValid);
                            hapusBtn.setAttribute('data-valid', isValid);
                        } else {
                            alert('Gagal mengupdate status valid: ' + (data.message || 'Error tidak diketahui'));
                            this.checked = !this.checked; // Revert checkbox
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat mengupdate status valid');
                        this.checked = !this.checked; // Revert checkbox
                    });
            });
        });

        // Fungsi untuk update tanggal bayar berdasarkan bulan dan tahun
        function updateTanggalBayar() {
            const bulanSelect = document.getElementById('edit_bulan_bayar');
            const tahunInput = document.getElementById('edit_tahun_bayar');
            const tanggalInput = document.getElementById('edit_tanggal_bayar');

            const bulan = bulanSelect.value;
            const tahun = tahunInput.value;

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
                }
            }
        }

        // Event listener untuk bulan dan tahun
        document.getElementById('edit_bulan_bayar').addEventListener('change', updateTanggalBayar);
        document.getElementById('edit_tahun_bayar').addEventListener('input', updateTanggalBayar);

        // Edit Button Click Handler
        document.querySelectorAll('.btn-edit').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const idPembayaran = this.getAttribute('data-id');
                const isValid = this.getAttribute('data-valid');

                // Check if valid
                if (isValid == 1) {
                    alert('Data ini tidak dapat diedit karena status Valid sudah dicentang. Silakan uncek status Valid terlebih dahulu.');
                    return;
                }

                // Populate modal with data
                document.getElementById('edit_id_pembayaran').value = idPembayaran;
                document.getElementById('edit_nis').value = this.getAttribute('data-nis');
                document.getElementById('edit_id_biaya').value = this.getAttribute('data-id-biaya');
                document.getElementById('edit_bulan_bayar').value = this.getAttribute('data-bulan');
                document.getElementById('edit_tahun_bayar').value = this.getAttribute('data-tahun');
                document.getElementById('edit_jumlah_bayar').value = this.getAttribute('data-jumlah');

                // Format tanggal untuk input date (YYYY-MM-DD)
                const tanggalBayar = this.getAttribute('data-tanggal');
                document.getElementById('edit_tanggal_bayar').value = tanggalBayar;

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('editModal'));
                modal.show();
            });
        });

        // Hapus Button Click Handler
        document.querySelectorAll('.btn-hapus').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const idPembayaran = this.getAttribute('data-id');
                const isValid = this.getAttribute('data-valid');
                const namaSiswa = this.getAttribute('data-nama');
                const bulan = this.getAttribute('data-bulan');
                const tahun = this.getAttribute('data-tahun');
                const jumlah = this.getAttribute('data-jumlah');

                // Check if valid
                if (isValid == 1) {
                    alert('Data ini tidak dapat dihapus karena status Valid sudah dicentang. Silakan uncek status Valid terlebih dahulu.');
                    return;
                }

                // Konfirmasi hapus
                const konfirmasi = confirm(
                    'Apakah Anda yakin ingin menghapus data pembayaran ini?\n\n' +
                    'NIS: ' + this.getAttribute('data-nis') + '\n' +
                    'Nama: ' + namaSiswa + '\n' +
                    'Bulan/Tahun: ' + bulan + ' ' + tahun + '\n' +
                    'Jumlah: Rp ' + jumlah + '\n\n' +
                    'Tindakan ini tidak dapat dibatalkan!'
                );

                if (konfirmasi) {
                    // Redirect ke proses hapus
                    window.location.href = 'proses_hapus_pembayaran.php?id=' + idPembayaran;
                }
            });
        });
    </script>
</body>

</html>