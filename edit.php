<?php
require_once 'config.php';
$page_title = 'Edit Lomba';

$errors = [];
$lomba = null;

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Ambil data lomba
$sql = "SELECT * FROM lomba WHERE id_lomba = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$lomba = mysqli_fetch_assoc($result);

if (!$lomba) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $namaLomba = trim($_POST['nama_lomba']);
    $deskripsi = trim($_POST['deskripsi']);
    $tanggalLomba = trim($_POST['tgl_lomba']);
    $lokasi = trim($_POST['lokasi']);
    $linkPendaftaran = trim($_POST['link_daftar']);
    $penyelenggaraLomba = trim($_POST['penyelenggara_lomba']);
    $status = trim($_POST['status']);
    $gambarLama = $lomba['gambar'];

    // Validasi
    if (empty($namaLomba)) {
        $errors[] = 'Nama lomba tidak boleh kosong';
    }

    if (empty($deskripsi)) {
        $errors[] = 'Deskripsi tidak boleh kosong';
    }

    if (empty($tanggalLomba)) {
        $errors[] = 'Tanggal lomba tidak boleh kosong';
    }

    if (empty($lokasi)) {
        $errors[] = 'Lokasi tidak boleh kosong';
    }

    if (empty($linkPendaftaran)) {
        $errors[] = 'Link pendaftaran tidak boleh kosong';
    }

    if (empty($penyelenggaraLomba)) {
        $errors[] = 'Penyelenggara lomba tidak boleh kosong';
    }

    // Validasi status
    if (empty($status) || !in_array($status, ['available', 'unavailable'])) {
        $errors[] = 'Status lomba harus dipilih';
    }

    // Proses gambar jika ada upload baru
    $gambar = $gambarLama;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['gambar']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        // Verifikasi ekstensi file
        if (!in_array(strtolower($filetype), $allowed)) {
            $errors[] = 'Format gambar tidak valid. Format yang diizinkan: ' . implode(', ', $allowed);
        }
        
        // Verifikasi ukuran file (max 2MB)
        if ($_FILES['gambar']['size'] > 2097152) {
            $errors[] = 'Ukuran gambar terlalu besar (maksimal 2MB)';
        }
        
        // Jika tidak ada error, upload gambar
        if (empty($errors)) {
            $upload_dir = 'uploads/';
            
            // Buat direktori jika belum ada
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate nama file unik
            $new_filename = uniqid() . '.' . $filetype;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $gambar = $upload_path;
                
                // Hapus gambar lama jika bukan placeholder dan file ada
                if (strpos($gambarLama, 'placeholder.com') === false && file_exists($gambarLama)) {
                    unlink($gambarLama);
                }
            } else {
                $errors[] = 'Gagal mengupload gambar';
            }
        }
    }

    // Update data jika tidak ada error
    if (empty($errors)) {
        // Cek apakah kolom updated_at ada
        $check_updated_at = mysqli_query($conn, "SHOW COLUMNS FROM lomba LIKE 'updated_at'");
        if (mysqli_num_rows($check_updated_at) > 0) {
            $sql = "UPDATE lomba SET nama_lomba = ?, deskripsi = ?, tgl_lomba = ?, lokasi = ?, link_daftar = ?, gambar = ?, penyelenggara_lomba = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id_lomba = ?";
        } else {
            $sql = "UPDATE lomba SET nama_lomba = ?, deskripsi = ?, tgl_lomba = ?, lokasi = ?, link_daftar = ?, gambar = ?, penyelenggara_lomba = ?, status = ? WHERE id_lomba = ?";
        }
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssi", $namaLomba, $deskripsi, $tanggalLomba, $lokasi, $linkPendaftaran, $gambar, $penyelenggaraLomba, $status, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?message=Data lomba berhasil diperbarui");
            exit();
        } else {
            $errors[] = 'Gagal memperbarui data: ' . mysqli_error($conn);
        }
    }
}

include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-pencil-square"></i> Edit Lomba
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

                    <form method="POST" action="edit.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_lomba" class="form-label">
                                        <i class="bi bi-trophy"></i> Nama Lomba *
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nama_lomba" 
                                           name="nama_lomba" 
                                           value="<?php echo isset($_POST['nama_lomba']) ? htmlspecialchars($_POST['nama_lomba']) : htmlspecialchars($lomba['nama_lomba']); ?>"
                                           placeholder="Masukkan nama lomba"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tgl_lomba" class="form-label">
                                        <i class="bi bi-calendar-event"></i> Tanggal Lomba *
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="tgl_lomba" 
                                           name="tgl_lomba" 
                                           value="<?php echo isset($_POST['tgl_lomba']) ? htmlspecialchars($_POST['tgl_lomba']) : htmlspecialchars($lomba['tgl_lomba']); ?>"
                                           required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">
                                <i class="bi bi-file-text"></i> Deskripsi *
                            </label>
                            <textarea class="form-control" 
                                      id="deskripsi" 
                                      name="deskripsi" 
                                      rows="4" 
                                      placeholder="Masukkan deskripsi lomba"
                                      required><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : htmlspecialchars($lomba['deskripsi']); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lokasi" class="form-label">
                                        <i class="bi bi-geo-alt"></i> Lokasi *
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="lokasi" 
                                           name="lokasi" 
                                           value="<?php echo isset($_POST['lokasi']) ? htmlspecialchars($_POST['lokasi']) : htmlspecialchars($lomba['lokasi']); ?>"
                                           placeholder="Masukkan lokasi lomba"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gambar" class="form-label">
                                        <i class="bi bi-image"></i> Gambar
                                    </label>
                                    <input type="file" 
                                           class="form-control" 
                                           id="gambar" 
                                           name="gambar"
                                           accept="image/*">
                                    <div class="form-text">Biarkan kosong jika tidak ingin mengubah gambar</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="link_daftar" class="form-label">
                                <i class="bi bi-link-45deg"></i> Link Pendaftaran *
                            </label>
                            <input type="url" 
                                   class="form-control" 
                                   id="link_daftar" 
                                   name="link_daftar" 
                                   value="<?php echo isset($_POST['link_daftar']) ? htmlspecialchars($_POST['link_daftar']) : htmlspecialchars($lomba['link_daftar']); ?>"
                                   placeholder="https://example.com/daftar"
                                   required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="penyelenggara_lomba" class="form-label">
                                        <i class="bi bi-building"></i> Penyelenggara Lomba *
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="penyelenggara_lomba" 
                                           name="penyelenggara_lomba" 
                                           value="<?php echo isset($_POST['penyelenggara_lomba']) ? htmlspecialchars($_POST['penyelenggara_lomba']) : htmlspecialchars($lomba['penyelenggara_lomba']); ?>"
                                           placeholder="Masukkan nama penyelenggara"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">
                                        <i class="bi bi-toggle-on"></i> Status Lomba *
                                    </label>
                                    <select class="form-select" id="status" name="status" required>
                                        <?php 
                                        $current_status = isset($lomba['status']) ? $lomba['status'] : 'available';
                                        if (isset($_POST['status'])) {
                                            $current_status = $_POST['status'];
                                        }
                                        ?>
                                        <option value="available" <?php echo ($current_status == 'available') ? 'selected' : ''; ?>>Tersedia</option>
                                        <option value="unavailable" <?php echo ($current_status == 'unavailable') ? 'selected' : ''; ?>>Tidak Tersedia</option>
                                    </select>
                                    <div class="form-text">Pilih apakah lomba ini tersedia untuk pendaftaran</div>
                                </div>
                            </div>
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
            
            <!-- Preview Gambar -->
            <?php if (!empty($lomba['gambar'])): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-eye"></i> Preview Gambar Saat Ini
                    </h6>
                </div>
                <div class="card-body text-center">
                    <img src="<?php echo htmlspecialchars($lomba['gambar']); ?>" 
                         alt="<?php echo htmlspecialchars($lomba['nama_lomba']); ?>" 
                         class="img-fluid rounded" 
                         style="max-height: 200px;"
                         onerror="this.src='https://via.placeholder.com/300x200?text=Gambar+Tidak+Ditemukan'">
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Info Card -->
            <div class="card mt-3">
                <div class="card-body">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> 
                        <?php if (isset($lomba['created_at'])): ?>
                            Data dibuat: <?php echo date('d/m/Y H:i', strtotime($lomba['created_at'])); ?>
                            <?php if (isset($lomba['updated_at']) && $lomba['updated_at'] != $lomba['created_at']): ?>
                                | Terakhir diperbarui: <?php echo date('d/m/Y H:i', strtotime($lomba['updated_at'])); ?>
                            <?php endif; ?>
                        <?php else: ?>
                            ID Lomba: <?php echo $lomba['id_lomba']; ?>
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Preview gambar saat URL diubah
document.getElementById('gambar').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const currentPreview = document.querySelector('.card:nth-of-type(2) img');
            if (currentPreview) {
                currentPreview.src = e.target.result;
            }
        }
        reader.readAsDataURL(file);
    }
});
</script>

<?php include 'includes/footer.php'; ?>
