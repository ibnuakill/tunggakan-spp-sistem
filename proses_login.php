<?php
// 1. Memulai Session untuk manajemen login
session_start();

// 2. Memanggil koneksi database
include('includes/koneksi.php');

// Cek apakah tombol 'login' sudah ditekan
if (isset($_POST['login'])) {

    // 3. Ambil data dari form (gunakan mysqli_real_escape_string untuk keamanan dasar)
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // 4. Query MySQL: Cari Admin dengan username yang sesuai
    $query_login = "SELECT * FROM tabel_admin WHERE username='$username'";
    $result = mysqli_query($koneksi, $query_login);
    $data_admin = mysqli_fetch_assoc($result);
    $cek = mysqli_num_rows($result);

    // 5. Cek Hasil Query
    if ($cek > 0) {
        // Username ditemukan, sekarang cek password

        // Catatan Penting: Di sistem nyata, password harus di-hash (misal: password_verify),
        // namun untuk TA pemula, perbandingan langsung ($password == $data_admin['password'])
        // seringkali masih diterima, asalkan dijelaskan keterbatasan ini.

        // Misal kita asumsikan password di database masih berupa teks biasa
        if ($password == $data_admin['password']) {

            // Login Berhasil!

            // Set session untuk menandai Admin sudah login
            $_SESSION['admin_login'] = true;
            $_SESSION['id_admin'] = $data_admin['id_admin'];
            $_SESSION['nama_admin'] = $data_admin['nama_lengkap'];

            // Arahkan ke halaman Dashboard Admin
            header('location: admin/index.php');
            exit; // Penting untuk menghentikan eksekusi script

        } else {
            // Password Salah
            echo "<script>alert('Password salah!'); window.location='index.php';</script>";
        }
    } else {
        // Username tidak ditemukan
        echo "<script>alert('Username tidak ditemukan!'); window.location='index.php';</script>";
    }
}
