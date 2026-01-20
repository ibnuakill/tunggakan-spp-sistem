<?php
// File ini hanya untuk debugging - harus login sebagai admin
session_start();
if (!isset($_SESSION['username_admin'])) {
    header("Location: login_admin.php");
    exit();
}

include('includes/koneksi.php');

function check_table($koneksi, $table) {
    $query = "SHOW COLUMNS FROM $table";
    $result = mysqli_query($koneksi, $query);

    echo "<h2>Columns in $table:</h2><ul>";
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
        }
    } else {
        echo "<li>Error: " . mysqli_error($koneksi) . "</li>";
    }
    echo "</ul>";
}

check_table($koneksi, 'tabel_siswa');
check_table($koneksi, 'tabel_siswa_aktif');
?>
