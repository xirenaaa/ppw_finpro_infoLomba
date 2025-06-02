<?php
require_once '../config.php';
$page_title = 'Tambah Peserta';

$errors = [];

// Ambil daftar lomba untuk dropdown
$sql_lomba = "SELECT id_lomba, nama_lomba, status FROM lomba WHERE status = 'available' ORDER BY nama_lomba ASC";
$result_lomba = mysqli_query($conn, $sql_lomba);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
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

    // Jika tidak ada error, simpan data ke database
    if (empty($errors)) {
        $sql = "INSERT INTO peserta (id_lomba, nama_peserta, email, no_telepon, institusi, tanggal_daftar, status_pendaftaran, catatan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isssssss", $idLomba, $namaPeserta, $email, $noTelepon, $institusi, $tanggalDaftar, $statusPendaftaran, $catatan);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?message=Peserta berhasil ditambahkan");
            exit();
        } else {
            $errors[] = 'Gagal menyimpan data: ' . mysqli_error($conn);
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
                        <i class="bi bi-person-plus"></i> Tambah Peserta Baru
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

                    <form method="POST" action="tambah.php">
                        <div class="mb-3">
                            <label for="id_lomba" class="form-label">
                                <i class="bi bi-trophy"></i> Pilih Lomba *
                            </label>
                            <select class="form-select" id="id_lomba" name="id_lomba" required>
                                <option value="">-- Pilih Lomba --</option>
                                <?php while ($lomba = mysqli_fetch_assoc($result_lomba)): ?>
                                    <option value="<?php echo $lomba['id_lomba']; ?>" <?php echo (isset($_POST['id_lomba']) && $_POST['id_lomba'] == $lomba['id_lomba']) ? 'selected' : ''; ?>>
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
                                           value="<?php echo isset($_POST['nama_peserta']) ? htmlspecialchars($_POST['nama_peserta']) : ''; ?>"
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
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
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
                                           value="<?php echo isset($_POST['no_telepon']) ? htmlspecialchars($_POST['no_telepon']) : ''; ?>"
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
                                           value="<?php echo isset($_POST['institusi']) ? htmlspecialchars($_POST['institusi']) : ''; ?>"
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
                                           value="<?php echo isset($_POST['tanggal_daftar']) ? htmlspecialchars($_POST['tanggal_daftar']) : date('Y-m-d'); ?>"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status_pendaftaran" class="form-label">
                                        <i class="bi bi-check-circle"></i> Status Pendaftaran *
                                    </label>
                                    <select class="form-select" id="status_pendaftaran" name="status_pendaftaran" required>
                                        <option value="Menunggu" <?php echo (isset($_POST['status_pendaftaran']) && $_POST['status_pendaftaran'] == 'Menunggu') ? 'selected' : 'selected'; ?>>Menunggu</option>
                                        <option value="Diterima" <?php echo (isset($_POST['status_pendaftaran']) && $_POST['status_pendaftaran'] == 'Diterima') ? 'selected' : ''; ?>>Diterima</option>
                                        <option value="Ditolak" <?php echo (isset($_POST['status_pendaftaran']) && $_POST['status_pendaftaran'] == 'Ditolak') ? 'selected' : ''; ?>>Ditolak</option>
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
                                      placeholder="Catatan tambahan (opsional)"><?php echo isset($_POST['catatan']) ? htmlspecialchars($_POST['catatan']) : ''; ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
