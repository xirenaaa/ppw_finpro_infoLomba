<?php
include 'config.php';

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM lomba WHERE id = $id"));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama_lomba'];
    $kategori = $_POST['id_kategori'];
    $jadwal = $_POST['id_jadwal'];
    $hadiah = $_POST['id_hadiah'];

    $sql = "UPDATE lomba SET nama_lomba='$nama', id_kategori=$kategori, id_jadwal=$jadwal, id_hadiah=$hadiah WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data berhasil diubah!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah data!');</script>";
    }
}

$kategori = mysqli_query($conn, "SELECT * FROM kategori");
$jadwal = mysqli_query($conn, "SELECT * FROM jadwal_lomba");
$hadiah = mysqli_query($conn, "SELECT * FROM hadiah");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Lomba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h2>Edit Data Lomba</h2>
<form method="POST">
    <div class="mb-3">
        <label class="form-label">Nama Lomba:</label>
        <input type="text" name="nama_lomba" value="<?= $data['nama_lomba'] ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Kategori:</label>
        <select name="id_kategori" class="form-select">
            <?php while($k = mysqli_fetch_assoc($kategori)) {
                $selected = $k['id'] == $data['id_kategori'] ? "selected" : "";
                echo "<option value='{$k['id']}' $selected>{$k['nama_kategori']}</option>";
            } ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Jadwal:</label>
        <select name="id_jadwal" class="form-select">
            <?php while($j = mysqli_fetch_assoc($jadwal)) {
                $selected = $j['id'] == $data['id_jadwal'] ? "selected" : "";
                echo "<option value='{$j['id']}' $selected>{$j['tanggal_mulai']} - {$j['tanggal_selesai']}</option>";
            } ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Hadiah:</label>
        <select name="id_hadiah" class="form-select">
            <?php while($h = mysqli_fetch_assoc($hadiah)) {
                $selected = $h['id'] == $data['id_hadiah'] ? "selected" : "";
                echo "<option value='{$h['id']}' $selected>{$h['nama_hadiah']}</option>";
            } ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>

</body>
</html>
