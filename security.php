<?php
// Tips keamanan untuk production

// 1. Ganti password default
// Jangan gunakan password kosong di production

// 2. Buat user database khusus
// CREATE USER 'lomba_user'@'localhost' IDENTIFIED BY 'strong_password';
// GRANT SELECT, INSERT, UPDATE, DELETE ON ppwfinpro.* TO 'lomba_user'@'localhost';

// 3. Gunakan prepared statements (sudah diimplementasi)
$stmt = mysqli_prepare($conn, "SELECT * FROM lomba WHERE id_lomba = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

// 4. Validasi input
function validate_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// 5. Error handling yang aman
// Jangan tampilkan error database di production
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Terjadi kesalahan sistem. Silakan coba lagi nanti.");
}
?>
