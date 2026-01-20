<?php
include('../includes/koneksi.php');
include('../includes/proteksi.php');

// Ambil ID pembayaran dari URL
$id_pembayaran = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : '';

if (empty($id_pembayaran)) {
    echo "<script>alert('ID pembayaran tidak ditemukan!'); window.location='status_pembayaran.php';</script>";
    exit;
}

// Cek apakah data sudah valid (tidak bisa dihapus jika valid)
$query_check = "SELECT status_valid, nis, bulan_bayar, tahun_bayar, jumlah_bayar FROM tabel_pembayaran WHERE id_pembayaran = '$id_pembayaran'";
$result_check = mysqli_query($koneksi, $query_check);

if (!$result_check) {
    echo "<script>alert('Error: " . mysqli_error($koneksi) . "'); window.location='status_pembayaran.php';</script>";
    exit;
}

$data_check = mysqli_fetch_assoc($result_check);

if (!$data_check) {
    echo "<script>alert('Data pembayaran tidak ditemukan!'); window.location='status_pembayaran.php';</script>";
    exit;
}

// Cek status valid
if ($data_check['status_valid'] == 1) {
    echo "<script>alert('Data tidak dapat dihapus karena status Valid sudah dicentang. Silakan uncek status Valid terlebih dahulu.'); window.location='status_pembayaran.php';</script>";
    exit;
}

// Hapus data pembayaran
$query_hapus = "DELETE FROM tabel_pembayaran WHERE id_pembayaran = '$id_pembayaran'";

if (mysqli_query($koneksi, $query_hapus)) {
    echo "<script>alert('Data pembayaran berhasil dihapus!'); window.location='status_pembayaran.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus data: " . mysqli_error($koneksi) . "'); window.location='status_pembayaran.php';</script>";
}

mysqli_close($koneksi);
?>

