<?php
include('../includes/koneksi.php');
include('../includes/proteksi.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$id_pembayaran = isset($_POST['id_pembayaran']) ? mysqli_real_escape_string($koneksi, $_POST['id_pembayaran']) : '';
$status_valid = isset($_POST['status_valid']) ? intval($_POST['status_valid']) : 0;

if (empty($id_pembayaran)) {
    echo json_encode(['success' => false, 'message' => 'ID pembayaran tidak ditemukan']);
    exit;
}

// Update status valid
$query_update = "UPDATE tabel_pembayaran SET status_valid = $status_valid WHERE id_pembayaran = '$id_pembayaran'";

if (mysqli_query($koneksi, $query_update)) {
    echo json_encode(['success' => true, 'message' => 'Status valid berhasil diupdate']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal mengupdate status valid: ' . mysqli_error($koneksi)]);
}

mysqli_close($koneksi);
?>

