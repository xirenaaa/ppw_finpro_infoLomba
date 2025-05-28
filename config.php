<?php
// Konfigurasi database untuk PHPMyAdmin
$host = "localhost";        
$username = "root";         
$password = "";             
$database = "ppwfinpro";    

// Membuat koneksi
$conn = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset untuk mendukung karakter Indonesia
mysqli_set_charset($conn, "utf8mb4");

// Cek dan tambah kolom status jika belum ada di tabel lomba
$check_status_column = mysqli_query($conn, "SHOW COLUMNS FROM lomba LIKE 'status'");
if (mysqli_num_rows($check_status_column) == 0) {
    mysqli_query($conn, "ALTER TABLE lomba ADD COLUMN status ENUM('available', 'unavailable') DEFAULT 'available'");
}

// Cek dan tambah kolom created_at jika belum ada di tabel lomba
$check_created_at = mysqli_query($conn, "SHOW COLUMNS FROM lomba LIKE 'created_at'");
if (mysqli_num_rows($check_created_at) == 0) {
    mysqli_query($conn, "ALTER TABLE lomba ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
}

// Cek dan tambah kolom updated_at jika belum ada di tabel lomba
$check_updated_at = mysqli_query($conn, "SHOW COLUMNS FROM lomba LIKE 'updated_at'");
if (mysqli_num_rows($check_updated_at) == 0) {
    mysqli_query($conn, "ALTER TABLE lomba ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
}

// Cek dan tambah kolom relasi jika belum ada
$check_bidang = mysqli_query($conn, "SHOW COLUMNS FROM lomba LIKE 'id_bidang'");
if (mysqli_num_rows($check_bidang) == 0) {
    mysqli_query($conn, "ALTER TABLE lomba ADD COLUMN id_bidang INT(11) DEFAULT NULL");
}

$check_kategori = mysqli_query($conn, "SHOW COLUMNS FROM lomba LIKE 'id_kategori'");
if (mysqli_num_rows($check_kategori) == 0) {
    mysqli_query($conn, "ALTER TABLE lomba ADD COLUMN id_kategori INT(11) DEFAULT NULL");
}

$check_hadiah = mysqli_query($conn, "SHOW COLUMNS FROM lomba LIKE 'id_hadiah'");
if (mysqli_num_rows($check_hadiah) == 0) {
    mysqli_query($conn, "ALTER TABLE lomba ADD COLUMN id_hadiah INT(11) DEFAULT NULL");
}

$check_penyelenggara = mysqli_query($conn, "SHOW COLUMNS FROM lomba LIKE 'penyelenggara'");
if (mysqli_num_rows($check_penyelenggara) == 0) {
    mysqli_query($conn, "ALTER TABLE lomba ADD COLUMN penyelenggara VARCHAR(255) DEFAULT NULL");
}

// Fungsi untuk escape string (keamanan)
function escape_string($string) {
    global $conn;
    return mysqli_real_escape_string($conn, $string);
}

// Fungsi untuk menjalankan query dengan error handling
function run_query($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        die("Query error: " . mysqli_error($conn));
    }
    
    return $result;
}
?>
