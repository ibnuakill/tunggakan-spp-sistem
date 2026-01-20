<?php
// File Proteksi untuk Halaman Admin
// File ini harus di-include di setiap halaman admin untuk memastikan hanya admin yang sudah login yang bisa mengakses

// 1. Memulai Session (jika belum dimulai)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Cek apakah Admin sudah login
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    // Jika belum login, redirect ke halaman login
    header('Location: ../login_admin.php');
    exit(); // Penting untuk menghentikan eksekusi script
}

// 3. Jika sudah login, script akan dilanjutkan ke halaman yang memanggil file ini
// Session sudah aktif, $_SESSION['nama_admin'] dan $_SESSION['id_admin'] sudah tersedia
?>
