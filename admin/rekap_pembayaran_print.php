<?php
include('../includes/koneksi.php');
include('../includes/proteksi.php');

$tahun_ajaran = isset($_GET['tahun_ajaran']) ? trim(mysqli_real_escape_string($koneksi, $_GET['tahun_ajaran'])) : '';
$tahun_ajaran_tampilan = $tahun_ajaran;
$tahun_ajaran_normalized = preg_replace('/\s+/', '', $tahun_ajaran);

$bulan_tahunan = ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];
$map_tahun_bulan = [];
$tahun_mulai = date('Y');
$tahun_selesai = $tahun_mulai + 1;

if (!empty($tahun_ajaran) && (strpos($tahun_ajaran, '/') !== false || strpos($tahun_ajaran, '-') !== false)) {
    $parts = preg_split('/[\/\-]/', $tahun_ajaran);
    $tahun_mulai = (int) trim($parts[0]);
    $tahun_selesai = isset($parts[1]) ? (int) trim($parts[1]) : ($tahun_mulai + 1);
} else {
    $tahun_ajaran = $tahun_mulai . '/' . $tahun_selesai;
    $tahun_ajaran_tampilan = $tahun_ajaran;
    $tahun_ajaran_normalized = preg_replace('/\s+/', '', $tahun_ajaran);
}

foreach ($bulan_tahunan as $bulan) {
    if (in_array($bulan, ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'])) {
        $map_tahun_bulan[$bulan] = $tahun_mulai;
    } else {
        $map_tahun_bulan[$bulan] = $tahun_selesai;
    }
}

$sql_siswa = "SELECT s.nis, s.nama_siswa FROM tabel_siswa s";
// Selalu filter berdasarkan tabel_siswa_aktif untuk tahun ajaran yang dipilih
if (!empty($tahun_ajaran_normalized)) {
    $sql_siswa .= " JOIN tabel_siswa_aktif sa 
                    ON sa.nis = s.nis 
                    AND REPLACE(REPLACE(REPLACE(sa.tahun_ajaran, ' ', ''), '\r', ''), '\n', '') = '$tahun_ajaran_normalized'";
}
$sql_siswa .= " ORDER BY s.nama_siswa ASC";

$query_siswa = mysqli_query($koneksi, $sql_siswa);
$data_siswa = [];
if ($query_siswa) {
    while ($row = mysqli_fetch_assoc($query_siswa)) {
        $data_siswa[] = $row;
    }
}

// Ambil data pembayaran untuk tahun ajaran terkait
$status_pembayaran = [];
if (!empty($data_siswa)) {
    $nis_list = array_column($data_siswa, 'nis');
    $nis_in = "'" . implode("','", $nis_list) . "'";

    $tahun_filter = "'" . $tahun_mulai . "','" . $tahun_selesai . "'";
    $query_bayar = "
        SELECT p.nis, p.bulan_bayar, p.tahun_bayar
        FROM tabel_pembayaran p
        WHERE p.nis IN ($nis_in)
          AND p.tahun_bayar IN ($tahun_filter)
    ";

    $result_bayar = mysqli_query($koneksi, $query_bayar);
    if ($result_bayar) {
        while ($row_bayar = mysqli_fetch_assoc($result_bayar)) {
            $key = $row_bayar['bulan_bayar'] . '-' . $row_bayar['tahun_bayar'];
            $status_pembayaran[$row_bayar['nis']][$key] = true;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Pembayaran SPP <?php echo htmlspecialchars($tahun_ajaran_tampilan); ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            padding: 30px;
            background-color: #fff;
        }

        .table-rekap th,
        .table-rekap td {
            text-align: center;
            vertical-align: middle;
            font-size: 12px;
            border: 1px solid #444 !important;
        }

        .table-rekap thead th {
            background-color: #00a6d6;
            color: #fff;
        }

        .paid {
            background-color: #d4edda !important;
            color: #155724;
            font-weight: bold;
        }

        .unpaid {
            background-color: #f8d7da !important;
            color: #721c24;
            font-weight: bold;
        }

        .legend span {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            margin-right: 10px;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h3 class="mb-0">Rekap Pembayaran SPP <?php echo htmlspecialchars($tahun_ajaran_tampilan); ?></h3>
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print</button>
    </div>

    <div class="mb-4">
        <p class="mb-1"><strong>Tahun Ajaran:</strong> <?php echo htmlspecialchars($tahun_ajaran_tampilan); ?></p>
        <div class="legend">
            <span style="background:#d4edda; color:#155724;">✓ Lunas</span>
            <span style="background:#f8d7da; color:#721c24;">× Belum</span>
        </div>
    </div>

    <?php if (empty($tahun_ajaran_tampilan)) { ?>
        <div class="alert alert-warning">
            <strong>Perhatian!</strong> Silakan pilih tahun ajaran terlebih dahulu melalui halaman transaksi.
        </div>
    <?php } elseif (empty($data_siswa)) { ?>
        <div class="alert alert-info">
            Tidak ditemukan siswa untuk tahun ajaran <strong><?php echo htmlspecialchars($tahun_ajaran_tampilan); ?></strong>.
        </div>
    <?php } else { ?>
        <div class="table-responsive">
            <table class="table table-bordered table-rekap">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 5%;">No</th>
                        <th rowspan="2" style="width: 20%;">Nama</th>
                        <th colspan="12">Rekap Pembayaran SPP <?php echo htmlspecialchars($tahun_ajaran_tampilan); ?></th>
                        <th rowspan="2" style="width: 10%;">Keterangan</th>
                    </tr>
                    <tr>
                        <?php foreach ($bulan_tahunan as $bulan) { ?>
                            <th><?php echo $bulan; ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($data_siswa as $siswa) {
                        $semua_lunas = true;
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td style="text-align:left;"><?php echo htmlspecialchars($siswa['nama_siswa']); ?></td>
                            <?php foreach ($bulan_tahunan as $bulan) {
                                $tahun_bulan = $map_tahun_bulan[$bulan];
                                $key = $bulan . '-' . $tahun_bulan;
                                $sudah_bayar = !empty($status_pembayaran[$siswa['nis']][$key]);
                                if (!$sudah_bayar) {
                                    $semua_lunas = false;
                                }
                            ?>
                                <td class="<?php echo $sudah_bayar ? 'paid' : 'unpaid'; ?>">
                                    <?php echo $sudah_bayar ? '&#10003;' : '&#10007;'; ?>
                                </td>
                            <?php } ?>
                            <td><?php echo $semua_lunas ? 'LUNAS' : 'BELUM LUNAS'; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
</body>

</html>