<?php
require_once 'config.php';

echo "<h2>Insert Data Sample</h2>";

// Data sample untuk bidang lomba
$bidang_data = [
    ['Teknologi Informasi', 'Lomba di bidang IT, programming, dan teknologi'],
    ['Seni dan Budaya', 'Lomba seni, musik, tari, dan budaya'],
    ['Olahraga', 'Lomba dan kompetisi olahraga'],
    ['Akademik', 'Lomba akademik dan ilmiah'],
    ['Kewirausahaan', 'Lomba bisnis dan kewirausahaan']
];

// Data sample untuk kategori
$kategori_data = [
    ['Mahasiswa', 'Kategori untuk mahasiswa'],
    ['Pelajar SMA', 'Kategori untuk pelajar SMA'],
    ['Umum', 'Kategori untuk umum'],
    ['Profesional', 'Kategori untuk profesional']
];

// Data sample untuk hadiah
$hadiah_data = [
    ['Juara 1', 5000000.00, 'Hadiah untuk juara pertama'],
    ['Juara 2', 3000000.00, 'Hadiah untuk juara kedua'],
    ['Juara 3', 2000000.00, 'Hadiah untuk juara ketiga'],
    ['Juara Harapan', 1000000.00, 'Hadiah untuk juara harapan'],
    ['Sertifikat', 0.00, 'Sertifikat penghargaan']
];

try {
    // Insert bidang lomba
    echo "<h3>Insert Bidang Lomba:</h3>";
    foreach ($bidang_data as $bidang) {
        $check = mysqli_query($conn, "SELECT id_bidang FROM bidang_lomba WHERE nama_bidang = '" . mysqli_real_escape_string($conn, $bidang[0]) . "'");
        if (mysqli_num_rows($check) == 0) {
            $sql = "INSERT INTO bidang_lomba (nama_bidang, deskripsi) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $bidang[0], $bidang[1]);
            if (mysqli_stmt_execute($stmt)) {
                echo "<p style='color: green;'>✅ Berhasil insert: " . $bidang[0] . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Gagal insert: " . $bidang[0] . "</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ Sudah ada: " . $bidang[0] . "</p>";
        }
    }

    // Insert kategori
    echo "<h3>Insert Kategori:</h3>";
    foreach ($kategori_data as $kategori) {
        $check = mysqli_query($conn, "SELECT id_kategori FROM kategori WHERE nama_kategori = '" . mysqli_real_escape_string($conn, $kategori[0]) . "'");
        if (mysqli_num_rows($check) == 0) {
            $sql = "INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $kategori[0], $kategori[1]);
            if (mysqli_stmt_execute($stmt)) {
                echo "<p style='color: green;'>✅ Berhasil insert: " . $kategori[0] . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Gagal insert: " . $kategori[0] . "</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ Sudah ada: " . $kategori[0] . "</p>";
        }
    }

    // Insert hadiah
    echo "<h3>Insert Hadiah:</h3>";
    foreach ($hadiah_data as $hadiah) {
        $check = mysqli_query($conn, "SELECT id_hadiah FROM hadiah WHERE nama_hadiah = '" . mysqli_real_escape_string($conn, $hadiah[0]) . "'");
        if (mysqli_num_rows($check) == 0) {
            $sql = "INSERT INTO hadiah (nama_hadiah, nilai_hadiah, deskripsi) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sds", $hadiah[0], $hadiah[1], $hadiah[2]);
            if (mysqli_stmt_execute($stmt)) {
                echo "<p style='color: green;'>✅ Berhasil insert: " . $hadiah[0] . " (Rp " . number_format($hadiah[1], 0, ',', '.') . ")</p>";
            } else {
                echo "<p style='color: red;'>❌ Gagal insert: " . $hadiah[0] . "</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ Sudah ada: " . $hadiah[0] . "</p>";
        }
    }

    echo "<h3>✅ Selesai!</h3>";
    echo "<p>Data sample berhasil diinsert. Sekarang Anda bisa:</p>";
    echo "<ul>";
    echo "<li><a href='index.php'>Lihat Daftar Lomba</a></li>";
    echo "<li><a href='create.php'>Tambah Lomba Baru</a></li>";
    echo "<li><a href='test_connection.php'>Test Koneksi Database</a></li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
