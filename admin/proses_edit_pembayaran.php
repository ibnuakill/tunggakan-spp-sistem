<?php
include('../includes/koneksi.php');
include('../includes/proteksi.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Method not allowed'); window.location='status_pembayaran.php';</script>";
    exit;
}

$id_pembayaran = isset($_POST['id_pembayaran']) ? mysqli_real_escape_string($koneksi, $_POST['id_pembayaran']) : '';
$nis = isset($_POST['nis']) ? mysqli_real_escape_string($koneksi, $_POST['nis']) : '';
$tanggal_bayar = isset($_POST['tanggal_bayar']) ? mysqli_real_escape_string($koneksi, $_POST['tanggal_bayar']) : '';
$id_biaya = isset($_POST['id_biaya']) ? mysqli_real_escape_string($koneksi, $_POST['id_biaya']) : '';
$bulan_bayar = isset($_POST['bulan_bayar']) ? mysqli_real_escape_string($koneksi, $_POST['bulan_bayar']) : '';
$tahun_bayar = isset($_POST['tahun_bayar']) ? mysqli_real_escape_string($koneksi, $_POST['tahun_bayar']) : '';
$jumlah_bayar = isset($_POST['jumlah_bayar']) ? mysqli_real_escape_string($koneksi, $_POST['jumlah_bayar']) : '';

// Validasi
if (empty($id_pembayaran) || empty($nis) || empty($tanggal_bayar) || empty($id_biaya) || empty($bulan_bayar) || empty($tahun_bayar) || empty($jumlah_bayar)) {
    echo "<script>alert('Semua field harus diisi!'); window.location='status_pembayaran.php';</script>";
    exit;
}

// Cek apakah data sudah valid (tidak bisa diedit jika valid)
$query_check = "SELECT status_valid FROM tabel_pembayaran WHERE id_pembayaran = '$id_pembayaran'";
$result_check = mysqli_query($koneksi, $query_check);
$data_check = mysqli_fetch_assoc($result_check);

if ($data_check && $data_check['status_valid'] == 1) {
    echo "<script>alert('Data tidak dapat diedit karena status Valid sudah dicentang. Silakan uncek status Valid terlebih dahulu.'); window.location='status_pembayaran.php';</script>";
    exit;
}

// Update data pembayaran
$query_update = "UPDATE tabel_pembayaran SET 
                    nis = '$nis',
                    tanggal_bayar = '$tanggal_bayar',
                    id_biaya = '$id_biaya',
                    bulan_bayar = '$bulan_bayar',
                    tahun_bayar = '$tahun_bayar',
                    jumlah_bayar = '$jumlah_bayar',
                    id_admin = '{$_SESSION['id_admin']}'
                WHERE id_pembayaran = '$id_pembayaran'";

if (mysqli_query($koneksi, $query_update)) {
    echo "<script>alert('Data pembayaran berhasil diupdate!'); window.location='status_pembayaran.php';</script>";
} else {
    echo "<script>alert('Gagal mengupdate data: " . mysqli_error($koneksi) . "'); window.location='status_pembayaran.php';</script>";
}

mysqli_close($koneksi);
?>

