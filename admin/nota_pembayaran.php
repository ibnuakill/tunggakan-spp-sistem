<?php
include('../includes/koneksi.php');
include('../includes/proteksi.php');

// Ambil id pembayaran dari GET
$id_pembayaran = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_pembayaran <= 0) {
    header('location: transaksi.php');
    exit();
}

// Ambil data pembayaran lengkap dengan join ke tabel terkait
$stmt = $koneksi->prepare("
    SELECT 
        p.id_pembayaran,
        p.nis,
        p.tanggal_bayar,
        p.bulan_bayar,
        p.tahun_bayar,
        p.jumlah_bayar,
        s.nama_siswa,
        k.nama_kelas,
        s.kelamin,
        jb.nama_biaya,
        jb.nominal,
        a.nama_lengkap AS nama_admin
    FROM tabel_pembayaran p
    JOIN tabel_siswa s ON p.nis = s.nis
    JOIN tabel_kelas k ON s.id_kelas = k.id_kelas
    JOIN tabel_jenis_biaya jb ON p.id_biaya = jb.id_biaya
    JOIN tabel_admin a ON p.id_admin = a.id_admin
    WHERE p.id_pembayaran = ?
");
$stmt->bind_param("i", $id_pembayaran);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    // ID tidak ditemukan
    echo "<script>alert('Data nota tidak ditemukan!'); window.location='transaksi.php';</script>";
    exit();
}

// Format tanggal Indonesia
$bulan_indo = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
    '04' => 'April',   '05' => 'Mei',      '06' => 'Juni',
    '07' => 'Juli',    '08' => 'Agustus',  '09' => 'September',
    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

$tgl = new DateTime($data['tanggal_bayar']);
$tgl_format = $tgl->format('d') . ' ' . $bulan_indo[$tgl->format('m')] . ' ' . $tgl->format('Y');

// Nomor kwitansi dengan format: SPP-TAHUN-ID (zero-padded)
$no_kwitansi = 'SPP-' . $data['tahun_bayar'] . '-' . str_pad($data['id_pembayaran'], 5, '0', STR_PAD_LEFT);

// Terbilang sederhana untuk nominal
function terbilang($angka) {
    $angka = (int)abs($angka);
    $kata = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
             'sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas',
             'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'];

    if ($angka === 0) return 'nol';
    if ($angka < 20)  return $kata[$angka];

    if ($angka < 100) {
        $puluhan = (int)($angka / 10);
        $sisa    = $angka % 10;
        return trim($kata[$puluhan] . ' puluh' . ($sisa > 0 ? ' ' . $kata[$sisa] : ''));
    }
    if ($angka < 1000) {
        $ratusan = (int)($angka / 100);
        $sisa    = $angka % 100;
        $prefix  = ($ratusan === 1) ? 'seratus' : $kata[$ratusan] . ' ratus';
        return trim($prefix . ($sisa > 0 ? ' ' . terbilang($sisa) : ''));
    }
    if ($angka < 1000000) {
        $ribuan = (int)($angka / 1000);
        $sisa   = $angka % 1000;
        $prefix = ($ribuan === 1) ? 'seribu' : terbilang($ribuan) . ' ribu';
        return trim($prefix . ($sisa > 0 ? ' ' . terbilang($sisa) : ''));
    }
    if ($angka < 1000000000) {
        $jutaan = (int)($angka / 1000000);
        $sisa   = $angka % 1000000;
        return trim(terbilang($jutaan) . ' juta' . ($sisa > 0 ? ' ' . terbilang($sisa) : ''));
    }
    return 'Nominal tidak terdukung';
}

$terbilang_nominal = ucfirst(terbilang((int)$data['jumlah_bayar'])) . ' Rupiah';

$autoprint = isset($_GET['autoprint']) && $_GET['autoprint'] == '1';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pembayaran SPP - <?php echo htmlspecialchars($no_kwitansi); ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <style>
        /* ===== GLOBAL ===== */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            padding: 30px 20px;
            color: #333;
        }

        /* ===== ACTION BAR (NO PRINT) ===== */
        .action-bar {
            max-width: 780px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .action-bar .title-bar {
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .action-bar .title-bar span {
            color: #60c2e7;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-print {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            box-shadow: 0 4px 15px rgba(34,197,94,0.35);
        }
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(34,197,94,0.50);
            color: #fff;
        }

        .btn-back {
            background: rgba(255,255,255,0.15);
            color: #fff;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.25);
        }
        .btn-back:hover {
            background: rgba(255,255,255,0.25);
            color: #fff;
            transform: translateY(-2px);
        }

        /* ===== NOTA WRAPPER ===== */
        .nota-wrapper {
            max-width: 780px;
            margin: 0 auto;
        }

        /* ===== NOTA CARD ===== */
        .nota-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        }

        /* ===== HEADER ===== */
        .nota-header {
            background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 50%, #2563eb 100%);
            padding: 30px 36px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .nota-header::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 160px; height: 160px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }
        .nota-header::after {
            content: '';
            position: absolute;
            bottom: -60px; left: 30%;
            width: 220px; height: 220px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 16px;
            position: relative;
            z-index: 1;
        }

        .school-info h1 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .school-info p {
            font-size: 12px;
            opacity: 0.82;
            line-height: 1.6;
        }

        .nota-badge {
            background: rgba(255,255,255,0.18);
            border: 1.5px solid rgba(255,255,255,0.35);
            border-radius: 10px;
            padding: 10px 18px;
            text-align: center;
            backdrop-filter: blur(8px);
        }

        .nota-badge .label {
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.8;
            margin-bottom: 4px;
        }

        .nota-badge .no-kwitansi {
            font-family: 'Inter', monospace;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .header-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.2);
            margin: 18px 0 14px;
            position: relative;
            z-index: 1;
        }

        .nota-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .nota-title {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .nota-tanggal {
            font-size: 13px;
            opacity: 0.85;
        }

        /* ===== BODY ===== */
        .nota-body {
            padding: 32px 36px;
        }

        /* ===== SISWA INFO ===== */
        .siswa-section {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border: 1.5px solid #bfdbfe;
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .siswa-avatar {
            width: 52px; height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e40af, #2563eb);
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: 22px;
            flex-shrink: 0;
        }

        .siswa-details h3 {
            font-size: 17px;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 4px;
        }

        .siswa-details .meta {
            font-size: 12.5px;
            color: #3b82f6;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .siswa-details .meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ===== DETAIL TABLE ===== */
        .detail-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 24px;
        }

        .detail-table tr td {
            padding: 11px 14px;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        .detail-table tr:last-child td {
            border-bottom: none;
        }

        .detail-table tr:nth-child(odd) {
            background: #fafafa;
        }

        .detail-table tr:first-child td:first-child { border-radius: 10px 0 0 0; }
        .detail-table tr:first-child td:last-child  { border-radius: 0 10px 0 0; }
        .detail-table tr:last-child  td:first-child { border-radius: 0 0 0 10px; }
        .detail-table tr:last-child  td:last-child  { border-radius: 0 0 10px 0; }

        .detail-table .lbl {
            width: 42%;
            color: #64748b;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-table .lbl i {
            width: 18px;
            color: #93c5fd;
            text-align: center;
        }

        .detail-table .val {
            color: #1e293b;
            font-weight: 500;
        }

        .detail-table .colon {
            width: 18px;
            color: #94a3b8;
            text-align: center;
        }

        /* ===== TOTAL BOX ===== */
        .total-box {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            border-radius: 12px;
            padding: 20px 24px;
            color: #fff;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .total-box .left .label-total {
            font-size: 12px;
            opacity: 0.8;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .total-box .left .amount {
            font-size: 30px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .total-box .right .terbilang {
            font-size: 12px;
            font-style: italic;
            opacity: 0.85;
            text-align: right;
            max-width: 280px;
            line-height: 1.5;
        }

        /* ===== STATUS LUNAS ===== */
        .status-lunas {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #15803d;
            border: 1.5px solid #86efac;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }



        /* ===== NOTA FOOTER BAR ===== */
        .nota-footer {
            background: #f8fafc;
            border-top: 1.5px solid #e2e8f0;
            padding: 14px 36px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .nota-footer .footer-text {
            font-size: 11px;
            color: #94a3b8;
        }

        .nota-footer .watermark {
            font-size: 11px;
            color: #cbd5e1;
            font-style: italic;
        }

        /* ===== PRINT STYLES ===== */
        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
            }

            .action-bar {
                display: none !important;
            }

            .nota-card {
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .nota-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .nota-header,
            .nota-header .nota-badge,
            .total-box,
            .siswa-section,
            .detail-table tr:nth-child(odd) {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                margin: 0;
                size: A5 landscape;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar (disembunyikan saat print) -->
    <div class="action-bar no-print">
        <div class="title-bar">
            <i class="fas fa-receipt"></i>&nbsp;
            Nota Pembayaran &nbsp;<span><?php echo htmlspecialchars($no_kwitansi); ?></span>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="transaksi.php" class="btn-action btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fas fa-print"></i> Cetak / Print
            </button>
        </div>
    </div>

    <!-- Nota Card -->
    <div class="nota-wrapper">
        <div class="nota-card">

            <!-- HEADER -->
            <div class="nota-header">
                <div class="header-top">
                    <div class="school-info">
                        <h1>&#127891; Sistem Pembayaran SPP</h1>
                        <p>
                            Kwitansi Resmi Pembayaran Sumbangan Pembinaan Pendidikan<br>
                            Diterbitkan oleh Sistem Informasi Pembayaran
                        </p>
                    </div>
                    <div class="nota-badge">
                        <div class="label">No. Kwitansi</div>
                        <div class="no-kwitansi"><?php echo htmlspecialchars($no_kwitansi); ?></div>
                    </div>
                </div>
                <hr class="header-divider">
                <div class="nota-title-row">
                    <div class="nota-title">&#128196; Nota Pembayaran SPP</div>
                    <div class="nota-tanggal">
                        <i class="fas fa-calendar-alt"></i>&nbsp;
                        <?php echo $tgl_format; ?>
                    </div>
                </div>
            </div>

            <!-- BODY -->
            <div class="nota-body">

                <!-- Info Siswa -->
                <div class="siswa-section">
                    <div class="siswa-avatar">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="siswa-details">
                        <h3><?php echo htmlspecialchars($data['nama_siswa']); ?></h3>
                        <div class="meta">
                            <span><i class="fas fa-id-card"></i> NIS: <?php echo htmlspecialchars($data['nis']); ?></span>
                            <span><i class="fas fa-school"></i> Kelas: <?php echo htmlspecialchars($data['nama_kelas']); ?></span>
                            <span>
                                <i class="fas fa-venus-mars"></i>
                                <?php
                                if ($data['kelamin'] === 'L') echo 'Laki-laki';
                                elseif ($data['kelamin'] === 'P') echo 'Perempuan';
                                else echo '-';
                                ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Detail Pembayaran -->
                <table class="detail-table">
                    <tr>
                        <td class="lbl"><i class="fas fa-tags"></i> Jenis Pembayaran</td>
                        <td class="colon">:</td>
                        <td class="val"><?php echo htmlspecialchars($data['nama_biaya']); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl"><i class="fas fa-calendar"></i> Periode SPP</td>
                        <td class="colon">:</td>
                        <td class="val">
                            <?php echo htmlspecialchars($data['bulan_bayar']); ?>
                            <?php echo htmlspecialchars($data['tahun_bayar']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl"><i class="fas fa-calendar-check"></i> Tanggal Bayar</td>
                        <td class="colon">:</td>
                        <td class="val"><?php echo $tgl_format; ?></td>
                    </tr>
                    <tr>
                        <td class="lbl"><i class="fas fa-user-tie"></i> Diterima Oleh</td>
                        <td class="colon">:</td>
                        <td class="val"><?php echo htmlspecialchars($data['nama_admin']); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl"><i class="fas fa-hashtag"></i> ID Transaksi</td>
                        <td class="colon">:</td>
                        <td class="val">#<?php echo str_pad($data['id_pembayaran'], 5, '0', STR_PAD_LEFT); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl"><i class="fas fa-check-circle"></i> Status</td>
                        <td class="colon">:</td>
                        <td class="val">
                            <span class="status-lunas">
                                <i class="fas fa-check-circle"></i> LUNAS
                            </span>
                        </td>
                    </tr>
                </table>

                <!-- Total Nominal -->
                <div class="total-box">
                    <div class="left">
                        <div class="label-total">Total Pembayaran</div>
                        <div class="amount">Rp <?php echo number_format($data['jumlah_bayar'], 0, ',', '.'); ?></div>
                    </div>
                    <div class="right">
                        <div class="terbilang">
                            "<?php echo $terbilang_nominal; ?>"
                        </div>
                    </div>
                </div>


            </div><!-- end nota-body -->

            <!-- FOOTER BAR -->
            <div class="nota-footer">
                <div class="footer-text">
                    <i class="fas fa-shield-check"></i>
                    Nota ini merupakan bukti pembayaran yang sah. Harap disimpan dengan baik.
                </div>
                <div class="watermark">
                    Dicetak: <?php echo date('d/m/Y H:i'); ?>
                </div>
            </div>

        </div><!-- end nota-card -->
    </div><!-- end nota-wrapper -->

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if ($autoprint): ?>
        // Auto-print jika dipanggil dari proses transaksi
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 800);
        });
        <?php endif; ?>
    </script>

</body>
</html>
