<?php
// File: includes/validation.php
// Fungsi-fungsi validasi input untuk keamanan

/**
 * Validasi NIS - hanya angka, maksimal 15 karakter
 * @param string $nis
 * @return array ['valid' => bool, 'message' => string]
 */
function validate_nis($nis) {
    if (empty($nis)) {
        return ['valid' => false, 'message' => 'NIS tidak boleh kosong'];
    }
    
    if (!ctype_digit($nis)) {
        return ['valid' => false, 'message' => 'NIS hanya boleh berisi angka'];
    }
    
    if (strlen($nis) > 15) {
        return ['valid' => false, 'message' => 'NIS maksimal 15 karakter'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validasi Tahun Ajaran - format YYYY/YYYY
 * @param string $tahun_ajaran
 * @return array ['valid' => bool, 'message' => string]
 */
function validate_tahun_ajaran($tahun_ajaran) {
    if (empty($tahun_ajaran)) {
        return ['valid' => false, 'message' => 'Tahun ajaran tidak boleh kosong'];
    }
    
    // Format: YYYY/YYYY atau YYYY-YYYY
    if (!preg_match('/^\d{4}[\/\-]\d{4}$/', $tahun_ajaran)) {
        return ['valid' => false, 'message' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2024/2025)'];
    }
    
    // Validasi tahun pertama < tahun kedua
    $parts = preg_split('/[\/\-]/', $tahun_ajaran);
    if (intval($parts[0]) >= intval($parts[1])) {
        return ['valid' => false, 'message' => 'Tahun pertama harus lebih kecil dari tahun kedua'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validasi Nominal - hanya angka positif
 * @param mixed $nominal
 * @return array ['valid' => bool, 'message' => string]
 */
function validate_nominal($nominal) {
    if (empty($nominal) && $nominal !== 0 && $nominal !== '0') {
        return ['valid' => false, 'message' => 'Nominal tidak boleh kosong'];
    }
    
    if (!is_numeric($nominal)) {
        return ['valid' => false, 'message' => 'Nominal hanya boleh berisi angka'];
    }
    
    if (floatval($nominal) < 0) {
        return ['valid' => false, 'message' => 'Nominal tidak boleh negatif'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validasi Bulan - nama bulan dalam bahasa Indonesia
 * @param string $bulan
 * @return array ['valid' => bool, 'message' => string]
 */
function validate_bulan($bulan) {
    $bulan_valid = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    if (empty($bulan)) {
        return ['valid' => false, 'message' => 'Bulan tidak boleh kosong'];
    }
    
    if (!in_array($bulan, $bulan_valid)) {
        return ['valid' => false, 'message' => 'Bulan tidak valid'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Sanitasi input umum - menghilangkan tag HTML dan trim
 * @param string $input
 * @return string
 */
function sanitize_input($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Validasi Nama - hanya huruf dan spasi
 * @param string $nama
 * @return array ['valid' => bool, 'message' => string]
 */
function validate_nama($nama) {
    if (empty($nama)) {
        return ['valid' => false, 'message' => 'Nama tidak boleh kosong'];
    }
    
    if (!preg_match("/^[a-zA-Z\s\.,']+$/", $nama)) {
        return ['valid' => false, 'message' => 'Nama hanya boleh berisi huruf dan spasi'];
    }
    
    if (strlen($nama) > 100) {
        return ['valid' => false, 'message' => 'Nama maksimal 100 karakter'];
    }
    
    return ['valid' => true, 'message' => ''];
}

/**
 * Validasi Tahun - format YYYY
 * @param mixed $tahun
 * @return array ['valid' => bool, 'message' => string]
 */
function validate_tahun($tahun) {
    if (empty($tahun)) {
        return ['valid' => false, 'message' => 'Tahun tidak boleh kosong'];
    }
    
    if (!ctype_digit(strval($tahun))) {
        return ['valid' => false, 'message' => 'Tahun harus berupa angka'];
    }
    
    if (strlen(strval($tahun)) != 4) {
        return ['valid' => false, 'message' => 'Tahun harus 4 digit'];
    }
    
    $tahun_int = intval($tahun);
    if ($tahun_int < 2000 || $tahun_int > 2100) {
        return ['valid' => false, 'message' => 'Tahun harus antara 2000-2100'];
    }
    
    return ['valid' => true, 'message' => ''];
}
?>
