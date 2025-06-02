<?php
require_once '../config.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Ambil data peserta untuk konfirmasi
$sql = "SELECT nama_peserta FROM peserta WHERE id_peserta = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$peserta = mysqli_fetch_assoc($result);

if (!$peserta) {
    header("Location: index.php");
    exit();
}

// Hapus data
$sql = "DELETE FROM peserta WHERE id_peserta = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    $message = "Peserta '" . htmlspecialchars($peserta['nama_peserta']) . "' berhasil dihapus";
    header("Location: index.php?message=" . urlencode($message));
} else {
    $message = "Gagal menghapus peserta: " . mysqli_error($conn);
    header("Location: index.php?message=" . urlencode($message));
}

exit();
?>
