<?php
// File: admin/hapus_siswa.php
include('../includes/koneksi.php');
include('../includes/proteksi.php');
include('../includes/validation.php');

// Cek apakah NIS dikirim melalui URL (menggunakan metode GET)
if (isset($_GET['nis'])) {
    $nis_dihapus = sanitize_input($_GET['nis']);
    
    // Validasi NIS
    $nis_check = validate_nis($nis_dihapus);
    if (!$nis_check['valid']) {
        echo "<script>alert('" . $nis_check['message'] . "'); window.location='data_siswa.php';</script>";
        exit();
    }

    // Prepared Statement untuk DELETE
    $stmt = $koneksi->prepare("DELETE FROM tabel_siswa WHERE nis = ?");
    $stmt->bind_param("s", $nis_dihapus);

    if ($stmt->execute()) {
        echo "<script>alert('Data siswa berhasil dihapus!'); window.location='data_siswa.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data. Kemungkinan data ini masih terhubung dengan data pembayaran!'); window.location='data_siswa.php';</script>";
    }
    $stmt->close();
} else {
    header('location: data_siswa.php');
}
?>
