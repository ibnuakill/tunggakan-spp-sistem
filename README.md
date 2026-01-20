# Sistem Pembayaran SPP (Tunggakan SPP)

Sistem informasi pembayaran SPP berbasis web untuk mengelola data siswa, biaya SPP, dan transaksi pembayaran.

## Fitur Utama

- **Manajemen Data Siswa**: Tambah, edit, hapus data siswa dengan validasi input
- **Manajemen Biaya SPP**: Kelola biaya SPP per kelas
- **Transaksi Pembayaran**: Input dan tracking pembayaran SPP siswa
- **History Pembayaran**: Lihat riwayat pembayaran lengkap
- **Kenaikan Kelas**: Proses kenaikan kelas dan tahun ajaran otomatis
- **Galeri**: Upload dan kelola galeri foto sekolah
- **Dashboard**: Statistik dan grafik pembayaran
- **Export Excel**: Export laporan pembayaran ke format CSV

## Teknologi

- **Backend**: PHP 7.4+ dengan MySQLi
- **Database**: MySQL/MariaDB
- **Frontend**: Bootstrap 5, HTML5, CSS3, JavaScript
- **Charts**: Chart.js untuk visualisasi data
- **Security**: 
  - Prepared Statements untuk SQL Injection protection
  - Input validation dan sanitization
  - Session-based authentication

## Instalasi

1. Clone repository ini
```bash
git clone https://github.com/ibnuakill/tunggakan-spp-sistem.git
```

2. Import database `db_spp_ta.sql` ke MySQL/MariaDB

3. Konfigurasi database di `includes/koneksi.php`:
```php
$server = "localhost";
$username = "root";
$password = "";
$database = "db_spp_ta";
```

4. Akses melalui browser:
```
http://localhost/tunggakan-spp-sistem/
```

## Login Default

- **Username**: admin
- **Password**: admin123

## Struktur Database

- `tabel_admin`: Data administrator
- `tabel_siswa`: Data siswa
- `tabel_siswa_aktif`: History tahun ajaran siswa
- `tabel_kelas`: Data kelas
- `tabel_biaya`: Data biaya SPP
- `tabel_pembayaran`: Transaksi pembayaran
- `tabel_galeri`: Galeri foto

## Keamanan

Sistem ini menggunakan:
- ✅ Prepared Statements untuk mencegah SQL Injection
- ✅ Input validation (NIS, nama, tahun ajaran, nominal)
- ✅ Session protection untuk halaman admin
- ✅ Sanitasi input dengan htmlspecialchars()

## Screenshot

_(Tambahkan screenshot aplikasi di sini)_

## Lisensi

MIT License

## Kontak

Untuk pertanyaan atau saran, silakan hubungi melalui GitHub Issues.
