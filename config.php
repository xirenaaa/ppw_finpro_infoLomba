<?php
// Konfigurasi database
$host = "localhost";
$username = "root";
$password = ""; // Sesuaikan dengan password MySQL Anda, biasanya kosong
$database = "ppwfinpro";

// Membuat koneksi
$conn = mysqli_connect($host, $username, $password);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Membuat database jika belum ada
$sql_create_db = "CREATE DATABASE IF NOT EXISTS $database";
if (mysqli_query($conn, $sql_create_db)) {
    // Pilih database
    mysqli_select_db($conn, $database);
    
    // Membuat tabel lomba jika belum ada
    $sql_create_table = "CREATE TABLE IF NOT EXISTS lomba (
        id_lomba INT(11) AUTO_INCREMENT PRIMARY KEY,
        nama_lomba VARCHAR(255) NOT NULL,
        deskripsi TEXT NOT NULL,
        tgl_lomba DATE NOT NULL,
        lokasi VARCHAR(255) NOT NULL,
        link_daftar TEXT NOT NULL,
        gambar VARCHAR(255),
        penyelenggara_lomba TEXT NOT NULL
    )";
    
    if (!mysqli_query($conn, $sql_create_table)) {
        echo "Error creating table: " . mysqli_error($conn);
    }
    
    // Cek dan tambah kolom created_at jika belum ada
    $check_created_at = mysqli_query($conn, "SHOW COLUMNS FROM lomba LIKE 'created_at'");
    if (mysqli_num_rows($check_created_at) == 0) {
        mysqli_query($conn, "ALTER TABLE lomba ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    }
    
    // Cek dan tambah kolom updated_at jika belum ada
    $check_updated_at = mysqli_query($conn, "SHOW COLUMNS FROM lomba LIKE 'updated_at'");
    if (mysqli_num_rows($check_updated_at) == 0) {
        mysqli_query($conn, "ALTER TABLE lomba ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    }
    
} else {
    die("Error creating database: " . mysqli_error($conn));
}
?>