<?php
session_start();

// Hapus semua variabel session
session_unset();

// Hancurkan session
session_destroy();

// Arahkan kembali ke halaman login
header('location: index.php');
exit;
