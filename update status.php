<?php
require_once 'config.php';

// Cek apakah request adalah POST dan ada parameter yang diperlukan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_lomba']) && isset($_POST['status'])) {
    $id_lomba = (int)$_POST['id_lomba'];
    $status = $_POST['status'];
    
    // Validasi status
    if (!in_array($status, ['available', 'unavailable'])) {
        header("Location: index.php?message=" . urlencode("Status tidak valid"));
        exit();
    }
    
    // Cek apakah kolom status ada, jika tidak buat kolom baru
    $check_status_column = mysqli_query($conn, "SHOW COLUMNS FROM lomba LIKE 'status'");
    if (mysqli_num_rows($check_status_column) == 0) {
        mysqli_query($conn, "ALTER TABLE lomba ADD COLUMN status ENUM('available', 'unavailable') DEFAULT 'available'");
    }
    
    // Update status lomba
    $sql = "UPDATE lomba SET status = ? WHERE id_lomba = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $id_lomba);
    
    if (mysqli_stmt_execute($stmt)) {
        $status_text = ($status == 'available') ? 'tersedia' : 'tidak tersedia';
        $message = "Status lomba berhasil diubah menjadi " . $status_text;
        header("Location: index.php?message=" . urlencode($message));
    } else {
        $message = "Gagal mengubah status lomba: " . mysqli_error($conn);
        header("Location: index.php?message=" . urlencode($message));
    }
} else {
    header("Location: index.php");
}

exit();
?>
