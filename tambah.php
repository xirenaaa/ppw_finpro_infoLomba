<?php
include 'config.php';

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lomba = mysqli_real_escape_string($conn, $_POST['nama_lomba']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $tgl_lomba = mysqli_real_escape_string($conn, $_POST['tgl_lomba']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $link_daftar = mysqli_real_escape_string($conn, $_POST['link_daftar']);
    $penyelenggara = mysqli_real_escape_string($conn, $_POST['penyelenggara_lomba']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Upload gambar (jika ada)
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $file_name = basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . time() . '_' . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["gambar"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = $target_file;
            } else {
                $error_message = "Gagal meng-upload gambar.";
            }
        } else {
            $error_message = "File yang di-upload bukan gambar.";
        }
    }

    if (!$error_message) {
        $sql = "INSERT INTO lomba (nama_lomba, deskripsi, tgl_lomba, lokasi, link_daftar, gambar, penyelenggara_lomba, status) 
        VALUES ('$nama_lomba', '$deskripsi', '$tgl_lomba', '$lokasi', '$link_daftar', '$gambar', '$penyelenggara', '$status')";

        
        if (mysqli_query($conn, $sql)) {
            $success_message = "Data lomba berhasil ditambahkan!";
            $_POST = []; // reset form
        } else {
            $error_message = "Gagal menambahkan data: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Tambah Lomba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2>Tambah Lomba Baru</h2>

    <?php if ($success_message): ?>
    <div class="alert alert-success"><?= $success_message ?></div>
    <?php endif; ?>

    <?php if ($error_message): ?>
    <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nama_lomba" class="form-label">Nama Lomba *</label>
            <input type="text" class="form-control" id="nama_lomba" name="nama_lomba" required
                   value="<?= isset($_POST['nama_lomba']) ? htmlspecialchars($_POST['nama_lomba']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : '' ?></textarea>
        </div>

        <div class="mb-3">
            <label for="tgl_lomba" class="form-label">Tanggal Lomba *</label>
            <input type="date" class="form-control" id="tgl_lomba" name="tgl_lomba" required
                   value="<?= isset($_POST['tgl_lomba']) ? $_POST['tgl_lomba'] : '' ?>">
        </div>

        <div class="mb-3">
            <label for="lokasi" class="form-label">Lokasi</label>
            <input type="text" class="form-control" id="lokasi" name="lokasi"
                   value="<?= isset($_POST['lokasi']) ? htmlspecialchars($_POST['lokasi']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="link_daftar" class="form-label">Link Daftar</label>
            <input type="url" class="form-control" id="link_daftar" name="link_daftar"
                   value="<?= isset($_POST['link_daftar']) ? htmlspecialchars($_POST['link_daftar']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="gambar" class="form-label">Gambar (opsional)</label>
            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" />
        </div>

        <div class="mb-3">
            <label for="penyelenggara_lomba" class="form-label">Penyelenggara Lomba</label>
            <input type="text" class="form-control" id="penyelenggara_lomba" name="penyelenggara_lomba"
                   value="<?= isset($_POST['penyelenggara_lomba']) ? htmlspecialchars($_POST['penyelenggara_lomba']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status *</label>
            <select class="form-select" id="status" name="status" required>
                <option value="aktif" <?= (isset($_POST['status']) && $_POST['status'] == 'aktif') ? 'selected' : '' ?>>Aktif</option>
                <option value="nonaktif" <?= (isset($_POST['status']) && $_POST['status'] == 'nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Lomba</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
