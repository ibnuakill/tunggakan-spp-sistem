<?php
// File: admin/proses_transaksi.php
include('../includes/koneksi.php');
include('../includes/proteksi.php');
include('../includes/validation.php');

if (isset($_POST['simpan_pembayaran'])) {

    // 1. Ambil data dari form
    $nis          = $_POST['nis'];
    $id_biaya     = $_POST['id_biaya'];
    $bulan_bayar  = $_POST['bulan_bayar'];
    $tahun_bayar  = $_POST['tahun_bayar'];
    $jumlah_bayar = $_POST['jumlah_bayar'];
    $tanggal_bayar = $_POST['tgl_pembayaran'];
    
    // 2. Validasi input
    $validation_errors = [];
    
    $nis_check = validate_nis($nis);
    if (!$nis_check['valid']) {
        $validation_errors[] = $nis_check['message'];
    }
    
    $bulan_check = validate_bulan($bulan_bayar);
    if (!$bulan_check['valid']) {
        $validation_errors[] = $bulan_check['message'];
    }
    
    $tahun_check = validate_tahun($tahun_bayar);
    if (!$tahun_check['valid']) {
        $validation_errors[] = $tahun_check['message'];
    }
    
    $nominal_check = validate_nominal($jumlah_bayar);
    if (!$nominal_check['valid']) {
        $validation_errors[] = $nominal_check['message'];
    }
    
    if (!empty($validation_errors)) {
        $error_message = implode("\\n", $validation_errors);
        echo "<script>alert('Error validasi:\\n$error_message'); window.location='transaksi.php?nis=" . htmlspecialchars($nis) . "';</script>";
        exit();
    }
    
    // 3. Sanitasi input
    $nis = sanitize_input($nis);
    $bulan_bayar = sanitize_input($bulan_bayar);
    $tanggal_bayar = sanitize_input($tanggal_bayar);

    // Ambil data penting dari session
    $id_admin     = $_SESSION['id_admin'];

    // 4. Prepared Statement untuk INSERT ke tabel_pembayaran
    $stmt = $koneksi->prepare("INSERT INTO tabel_pembayaran 
                     (nis, id_biaya, tanggal_bayar, bulan_bayar, tahun_bayar, jumlah_bayar, id_admin) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissiii", $nis, $id_biaya, $tanggal_bayar, $bulan_bayar, $tahun_bayar, $jumlah_bayar, $id_admin);

    if ($stmt->execute()) {
        echo "<script>alert('Transaksi pembayaran berhasil disimpan!'); window.location='status_pembayaran.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan transaksi: " . $stmt->error . "'); window.location='transaksi.php';</script>";
    }
    $stmt->close();
} else {
    header('location: transaksi.php');
}
?>