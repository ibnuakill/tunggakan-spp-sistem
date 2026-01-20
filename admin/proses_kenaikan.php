<?php
// File: admin/proses_kenaikan.php
include('../includes/koneksi.php');
include('../includes/proteksi.php');
include('../includes/validation.php');

if (isset($_POST['btn_proses_naik'])) {

    // 1. Ambil input dari form
    $tahun_baru = $_POST['tahun_ajaran_baru'];
    $id_kelas_baru = isset($_POST['id_kelas_baru']) ? $_POST['id_kelas_baru'] : '';
    $daftar_nis = isset($_POST['nis_dipilih']) ? $_POST['nis_dipilih'] : [];

    // 2. Validasi
    if (empty($daftar_nis)) {
        echo "<script>alert('Pilih siswa terlebih dahulu!'); window.location='data_siswa.php';</script>";
        exit;
    }
    
    $tahun_check = validate_tahun_ajaran($tahun_baru);
    if (!$tahun_check['valid']) {
        echo "<script>alert('" . $tahun_check['message'] . "'); window.location='data_siswa.php';</script>";
        exit;
    }
    
    $tahun_baru = sanitize_input($tahun_baru);

    $sukses = 0;

    foreach ($daftar_nis as $nis) {
        $nis = sanitize_input($nis);

        // --- BAGIAN 1: INSERT HISTORY TAHUN AJARAN dengan Prepared Statement ---
        // Cek agar tidak duplikat
        $stmt_cek = $koneksi->prepare("SELECT id FROM tabel_siswa_aktif WHERE nis = ? AND tahun_ajaran = ?");
        $stmt_cek->bind_param("ss", $nis, $tahun_baru);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();

        if ($result_cek->num_rows == 0) {
            $stmt_insert = $koneksi->prepare("INSERT INTO tabel_siswa_aktif (nis, tahun_ajaran) VALUES (?, ?)");
            $stmt_insert->bind_param("ss", $nis, $tahun_baru);
            if ($stmt_insert->execute()) {
                $sukses++;
            }
            $stmt_insert->close();
        }
        $stmt_cek->close();

        // --- BAGIAN 2: UPDATE KELAS dengan Prepared Statement ---
        if (!empty($id_kelas_baru)) {
            $stmt_update = $koneksi->prepare("UPDATE tabel_siswa SET id_kelas = ? WHERE nis = ?");
            $stmt_update->bind_param("is", $id_kelas_baru, $nis);
            $stmt_update->execute();
            $stmt_update->close();
        }
    }

    echo "<script>alert('Berhasil memproses siswa. Tahun Ajaran & Kelas telah diperbarui.'); window.location='data_siswa.php';</script>";
} else {
    header("Location: data_siswa.php");
}
?>
