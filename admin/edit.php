<?php
include 'config.php';

$success_message = '';
$error_message = '';

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan.");
}

$id = intval($_GET['id']);

// Ambil data lama
$result = mysqli_query($conn, "SELECT * FROM lomba WHERE id_lomba = $id");
if (mysqli_num_rows($result) == 0) {
    die("Data tidak ditemukan.");
}
$data = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lomba = mysqli_real_escape_string($conn, $_POST['nama_lomba']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $tgl_lomba = mysqli_real_escape_string($conn, $_POST['tgl_lomba']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $link_daftar = mysqli_real_escape_string($conn, $_POST['link_daftar']);
    $penyelenggara = mysqli_real_escape_string($conn, $_POST['penyelenggara_lomba']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Upload gambar baru jika diisi
    $gambar = $data['gambar']; // default gambar lama
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
                $error_message = "Gagal meng-upload gambar baru.";
            }
        } else {
            $error_message = "File yang di-upload bukan gambar.";
        }
    }

    if (!$error_message) {
        $sql = "UPDATE lomba SET 
            nama_lomba = '$nama_lomba',
            deskripsi = '$deskripsi',
            tgl_lomba = '$tgl_lomba',
            lokasi = '$lokasi',
            link_daftar = '$link_daftar',
            gambar = '$gambar',
            penyelenggara_lomba = '$penyelenggara',
            status = '$status'
            WHERE id_lomba = $id";

        if (mysqli_query($conn, $sql)) {
            $success_message = "Data berhasil diperbarui!";
            $result = mysqli_query($conn, "SELECT * FROM lomba WHERE id_lomba = $id");
            $data = mysqli_fetch_assoc($result);
        } else {
            $error_message = "Gagal memperbarui data: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Edit Lomba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2>Edit Lomba</h2>

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
                   value="<?= htmlspecialchars($data['nama_lomba']) ?>">
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="tgl_lomba" class="form-label">Tanggal Lomba *</label>
            <input type="date" class="form-control" id="tgl_lomba" name="tgl_lomba" required
                   value="<?= $data['tgl_lomba'] ?>">
        </div>

        <div class="mb-3">
            <label for="lokasi" class="form-label">Lokasi</label>
            <input type="text" class="form-control" id="lokasi" name="lokasi"
                   value="<?= htmlspecialchars($data['lokasi']) ?>">
        </div>

        <div class="mb-3">
            <label for="link_daftar" class="form-label">Link Daftar</label>
            <input type="url" class="form-control" id="link_daftar" name="link_daftar"
                   value="<?= htmlspecialchars($data['link_daftar']) ?>">
        </div>

        <div class="mb-3">
            <label for="gambar" class="form-label">Gambar (kosongkan jika tidak diubah)</label><br>
            <?php if ($data['gambar']): ?>
                <img src="<?= $data['gambar'] ?>" width="150" class="mb-2"/><br>
            <?php endif; ?>
            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" />
        </div>

        <div class="mb-3">
            <label for="penyelenggara_lomba" class="form-label">Penyelenggara Lomba</label>
            <input type="text" class="form-control" id="penyelenggara_lomba" name="penyelenggara_lomba"
                   value="<?= htmlspecialchars($data['penyelenggara_lomba']) ?>">
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status *</label>
            <select class="form-select" id="status" name="status" required>
                <option value="aktif" <?= $data['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="nonaktif" <?= $data['status'] == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Perbarui Lomba</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
