<?php
session_start();
// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: dashboard_admin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sistem Pembayaran SPP</title>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <!-- Background Overlay -->
    <div class="login-overlay"></div>

    <!-- Back to Home Button -->
    <a href="index.php" class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali ke Home
    </a>

    <!-- Login Container -->
    <div class="login-container">
        <div class="login-box">
            <!-- Logo & Header -->
            <div class="login-header">
                <div class="logo-circle">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h2>Login Administrator</h2>
                <p class="text-muted">Sistem Pembayaran SPP</p>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php
                    if ($_GET['error'] == 'invalid') {
                        echo 'Username atau Password salah!';
                    } elseif ($_GET['error'] == 'empty') {
                        echo 'Semua field harus diisi!';
                    } elseif ($_GET['error'] == 'logout') {
                        echo 'Anda telah logout!';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success']) && $_GET['success'] == 'registered'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    Registrasi berhasil! Silakan login.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="proses_login.php" method="POST" id="loginForm" class="needs-validation" novalidate>

                <!-- Username Field -->
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-user-circle"></i>
                        <input
                            type="text"
                            class="form-control"
                            id="username"
                            name="username"
                            placeholder="Masukkan username"
                            required
                            autocomplete="username">
                        <div class="invalid-feedback">
                            Username tidak boleh kosong!
                        </div>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            required
                            autocomplete="current-password">
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                        <div class="invalid-feedback">
                            Password tidak boleh kosong!
                        </div>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="form-options">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                        <label class="form-check-label" for="rememberMe">
                            Ingat Saya
                        </label>
                    </div>
                    <a href="#" class="forgot-password" data-bs-toggle="modal" data-bs-target="#forgotModal">
                        Lupa Password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="login" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>

            </form>

            <!-- Footer -->
            <div class="login-footer">
                <p class="text-muted small">
                    <i class="fas fa-shield-alt"></i>
                    Login ini hanya untuk Administrator sistem
                </p>
            </div>

        </div>
    </div>

    <!-- Modal Forgot Password -->
    <div class="modal fade" id="forgotModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-key"></i> Lupa Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Silakan hubungi Administrator Sistem untuk reset password:</p>
                    <div class="alert alert-info">
                        <i class="fas fa-phone"></i> Telp: (022) 1234-5678<br>
                        <i class="fas fa-envelope"></i> Email: admin@sekolah.sch.id
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form Validation
        (function() {
            'use strict';
            const form = document.getElementById('loginForm');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        })();

        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        // Input Focus Animation
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    </script>

</body>

</html>