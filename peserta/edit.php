<?php
require_once '../config.php';
$page_title = 'Edit Peserta';

$errors = [];
$peserta = null;

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Ambil data peserta
$sql = "SELECT * FROM peserta WHERE id_peserta = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$peserta = mysqli_fetch_assoc($result);

if (!$peserta) {
    header("Location: index.php");
    exit();
}

// Ambil daftar lomba untuk dropdown
$sql_lomba = "SELECT id_lomba, nama_lomba FROM lomba ORDER BY nama_lomba ASC";
$result_lomba = mysqli_query($conn, $sql_lomba);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    // Ambil data dari form
    $idLomba = trim($_POST['id_lomba']);
    $namaPeserta = trim($_POST['nama_peserta']);
    $email = trim($_POST['email']);
    $noTelepon = trim($_POST['no_telepon']);
    $institusi = trim($_POST['institusi']);
    $tanggalDaftar = trim($_POST['tanggal_daftar']);
    $statusPendaftaran = trim($_POST['status_pendaftaran']);
    $catatan = trim($_POST['catatan']);

    // Validasi data
    if (empty($idLomba)) {
        $errors[] = 'Lomba harus dipilih';
    }
    if (empty($namaPeserta)) {
        $errors[] = 'Nama peserta harus diisi';
    }
    if (empty($email)) {
        $errors[] = 'Email harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid';
    }
    if (empty($noTelepon)) {
        $errors[] = 'Nomor telepon harus diisi';
    }
    if (empty($institusi)) {
        $errors[] = 'Institusi harus diisi';
    }
    if (empty($tanggalDaftar)) {
        $errors[] = 'Tanggal daftar harus diisi';
    }

    // Update data jika tidak ada error
    if (empty($errors)) {
        $sql = "UPDATE peserta SET id_lomba = ?, nama_peserta = ?, email = ?, no_telepon = ?, institusi = ?, tanggal_daftar = ?, status_pendaftaran = ?, catatan = ? WHERE id_peserta = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isssssssi", $idLomba, $namaPeserta, $email, $noTelepon, $institusi, $tanggalDaftar, $statusPendaftaran, $catatan, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?message=Data peserta berhasil diperbarui");
            exit();
        } else {
            $errors[] = 'Gagal memperbarui data: ' . mysqli_error($conn);
        }
    }
}

include '../header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-pencil-square"></i> Edit Peserta
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="edit.php?id=<?php echo $id; ?>">
                        <div class="mb-3">
                            <label for="id_lomba" class="form-label">
                                <i class="bi bi-trophy"></i> Pilih Lomba *
                            </label>
                            <select class="form-select" id="id_lomba" name="id_lomba" required>
                                <option value="">-- Pilih Lomba --</option>
                                <?php 
                                mysqli_data_seek($result_lomba, 0);
                                while ($lomba = mysqli_fetch_assoc($result_lomba)): 
                                    $selected = (isset($_POST['id_lomba']) ? $_POST['id_lomba'] : $peserta['id_lomba']) == $lomba['id_lomba'];
                                ?>
                                    <option value="<?php echo $lomba['id_lomba']; ?>" <?php echo $selected ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($lomba['nama_lomba']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_peserta" class="form-label">
                                        <i class="bi bi-person"></i> Nama Peserta *
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nama_peserta" 
                                           name="nama_peserta" 
                                           value="<?php echo isset($_POST['nama_peserta']) ? htmlspecialchars($_POST['nama_peserta']) : htmlspecialchars($peserta['nama_peserta']); ?>"
                                           placeholder="Masukkan nama peserta"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope"></i> Email *
                                    </label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($peserta['email']); ?>"
                                           placeholder="contoh@email.com"
                                           required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_telepon" class="form-label">
                                        <i class="bi bi-telephone"></i> Nomor Telepon *
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="no_telepon" 
                                           name="no_telepon" 
                                           value="<?php echo isset($_POST['no_telepon']) ? htmlspecialchars($_POST['no_telepon']) : htmlspecialchars($peserta['no_telepon']); ?>"
                                           placeholder="08xxxxxxxxxx"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="institusi" class="form-label">
                                        <i class="bi bi-building"></i> Institusi *
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="institusi" 
                                           name="institusi" 
                                           value="<?php echo isset($_POST['institusi']) ? htmlspecialchars($_POST['institusi']) : htmlspecialchars($peserta['institusi']); ?>"
                                           placeholder="Nama institusi/sekolah/universitas"
                                           required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_daftar" class="form-label">
                                        <i class="bi bi-calendar"></i> Tanggal Daftar *
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="tanggal_daftar" 
                                           name="tanggal_daftar" 
                                           value="<?php echo isset($_POST['tanggal_daftar']) ? htmlspecialchars($_POST['tanggal_daftar']) : htmlspecialchars($peserta['tanggal_daftar']); ?>"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status_pendaftaran" class="form-label">
                                        <i class="bi bi-check-circle"></i> Status Pendaftaran *
                                    </label>
                                    <?php 
                                    $status = isset($_POST['status_pendaftaran']) ? $_POST['status_pendaftaran'] : $peserta['status_pendaftaran'];
                                    ?>
                                    <select class="form-select" id="status_pendaftaran" name="status_pendaftaran" required>
                                        <option value="Menunggu" <?php echo ($status == 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                        <option value="Diterima" <?php echo ($status == 'Diterima') ? 'selected' : ''; ?>>Diterima</option>
                                        <option value="Ditolak" <?php echo ($status == 'Ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="catatan" class="form-label">
                                <i class="bi bi-sticky"></i> Catatan
                            </label>
                            <textarea class="form-control" 
                                      id="catatan" 
                                      name="catatan" 
                                      rows="3" 
                                      placeholder="Catatan tambahan (opsional)"><?php echo isset($_POST['catatan']) ? htmlspecialchars($_POST['catatan']) : htmlspecialchars($peserta['catatan'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" name="update" class="btn btn-primary">
                                <i class="bi bi-save"></i> Perbarui Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
