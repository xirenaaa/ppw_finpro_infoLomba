<?php
require_once 'config.php';

echo "<h2>Test Koneksi Database</h2>";

// Test koneksi database
if ($conn) {
    echo "<p style='color: green;'>✅ Koneksi database berhasil!</p>";
    
    // Test semua tabel
    $tables = ['lomba', 'bidang_lomba', 'kategori', 'hadiah', 'jadwal_lomba', 'syarat', 'peserta'];
    
    echo "<h3>Status Tabel:</h3>";
    echo "<ul>";
    
    foreach ($tables as $table) {
        $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
        if (mysqli_num_rows($result) > 0) {
            // Hitung jumlah data
            $count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM $table");
            $count = mysqli_fetch_assoc($count_result)['total'];
            echo "<li style='color: green;'>✅ Tabel <strong>$table</strong> ada ($count data)</li>";
        } else {
            echo "<li style='color: red;'>❌ Tabel <strong>$table</strong> tidak ada</li>";
        }
    }
    
    echo "</ul>";
    
    // Test query join
    echo "<h3>Test Query Join:</h3>";
    $sql = "SELECT l.nama_lomba, bl.nama_bidang, k.nama_kategori 
            FROM lomba l 
            LEFT JOIN bidang_lomba bl ON l.id_bidang = bl.id_bidang 
            LEFT JOIN kategori k ON l.id_kategori = k.id_kategori 
            LIMIT 5";
    
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "<p style='color: green;'>✅ Query JOIN berhasil!</p>";
        if (mysqli_num_rows($result) > 0) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>Nama Lomba</th><th>Bidang</th><th>Kategori</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['nama_lomba'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['nama_bidang'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['nama_kategori'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠️ Tidak ada data lomba</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Error query: " . mysqli_error($conn) . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Koneksi database gagal: " . mysqli_connect_error() . "</p>";
}

echo "<br><a href='index.php'>← Kembali ke Aplikasi</a>";
?>
