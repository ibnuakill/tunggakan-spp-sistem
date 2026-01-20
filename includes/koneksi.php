<?php
// Konfigurasi Database MySQL
$server = "localhost"; // Biasanya localhost
$username = "root";    // Username default XAMPP
$password = "";        // Password default XAMPP (kosong)
$database = "db_spp_ta"; // Nama database yang akan dibuat

// Membuat Koneksi
$koneksi = mysqli_connect($server, $username, $password, $database);

// Cek Koneksi (Untuk debugging)
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
// echo "Koneksi berhasil!"; // Hanya tampilkan saat tes
