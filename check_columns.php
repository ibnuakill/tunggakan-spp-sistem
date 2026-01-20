<?php
// File ini hanya untuk debugging - harus login sebagai admin
session_start();
if (!isset($_SESSION['username_admin'])) {
    header("Location: login_admin.php");
    exit();
}

include('includes/koneksi.php');

$query = "SHOW COLUMNS FROM tabel_siswa";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    echo "Error: " . mysqli_error($koneksi);
} else {
    echo "<h2>Columns in tabel_siswa:</h2><ul>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
    }
    echo "</ul>";
}
?>
