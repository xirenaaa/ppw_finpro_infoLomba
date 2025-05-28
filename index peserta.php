<?php
require_once '../config.php';
$page_title = 'Daftar Peserta Lomba';

// Ambil filter lomba jika ada
$filter_lomba = isset($_GET['lomba']) ? (int)$_GET['lomba'] : 0;

// Query untuk mendapatkan daftar lomba untuk filter
$sql_lomba = "SELECT id_lomba, nama_lomba FROM lomba ORDER BY nama_lomba ASC";
$result_lomba = mysqli_query($conn, $sql_lomba);

// Query untuk mendapatkan data peserta
if ($filter_lomba > 0) {
    $sql = "SELECT p.*, l.nama_lomba, l.status as lomba_status
            FROM peserta p 
            JOIN lomba l ON p.id_lomba = l.id_lomba 
            WHERE p.id_lomba = $filter_lomba 
            ORDER BY p.tanggal_daftar DESC";
} else {
    $sql = "SELECT p.*, l.nama_lomba, l.status as lomba_status
            FROM peserta p 
            JOIN lomba l ON p.id_lomba = l.id_lomba 
            ORDER BY p.tanggal_daftar DESC";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE
