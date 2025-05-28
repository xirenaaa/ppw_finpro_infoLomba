<?php
require_once 'config.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Ambil data lomba untuk konfirmasi
$sql = "SELECT nama_lomba, gambar FROM lomba WHERE id_lomba = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$lomba = mysqli_fetch_assoc($result);

if (!$lomba) {
    header("Location: index.php");
    exit();
}

// Hapus gambar jika bukan placeholder dan file ada
if (strpos($lomba['gambar'], 'placeholder.com') === false && file_exists($lomba['gambar'])) {
    unlink($lomba['gambar']);
}

// Hapus data
$sql = "DELETE FROM lomba WHERE id_lomba = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    $message = "Lomba '" . htmlspecialchars($lomba['nama_lomba']) . "' berhasil dihapus";
    header("Location: index.php?message=" . urlencode($message));
} else {
    $message = "Gagal menghapus lomba: " . mysqli_error($conn);
    header("Location: index.php?message=" . urlencode($message));
}

exit();
?>
