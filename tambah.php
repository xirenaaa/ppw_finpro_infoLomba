<?php
require_once 'config.php';
$page_title = 'Tambah Lomba';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Ambil data dari form
    $namaLomba = trim($_POST['nama_lomba']);
    $deskripsi = trim($_POST['deskripsi']);
    $tanggalLomba = trim($_POST['tgl_lomba']);
    $lokasi = trim($_POST['lokasi']);
    $linkPendaftaran = trim($_POST['link_daftar']);
    $penyelenggaraLomba = trim($_POST['penyelenggara_lomba']);
    $status = trim($_POST['status']);

    // Validasi data
    if (empty($namaLomba)) {
        $errors[] = 'Nama lomba harus diisi';
    }
    if (empty($deskripsi)) {
        $errors[] = 'Deskripsi harus diisi';
    }
    if (empty($tanggalLomba)) {
        $errors[] = 'Tanggal lomba harus diisi';
    }
    if (empty($lokasi)) {
        $errors[] = 'Lokasi harus diisi';
    }
    if (empty($linkPendaftaran)) {
        $errors[] = 'Link pendaftaran harus diisi';
    }
    if (empty($penyelenggaraLomba)) {
        $errors[] = 'Penyelenggara harus diisi';
    }

    // Validasi dan proses gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['gambar']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (!in_array(strtolower($filetype), $allowed)) {
            $errors[] = 'Format gambar tidak valid. Format yang diizinkan: ' . implode(', ', $allowed);
        }
        
        if ($_FILES['gambar']['size'] > 2097152) {
            $errors[] = 'Ukuran gambar terlalu besar (maksimal 2MB)';
        }
        
        if (empty($errors)) {
            $upload_dir = 'uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_filename = uniqid() . '.' . $filetype;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $gambar = $upload_path;
            } else {
                $errors[] = 'Gagal mengupload gambar';
            }
        }
    } else {
        $gambar = 'https://via.placeholder.com/800x400?text=Lomba+' . urlencode($namaLomba);
    }

    // Jika tidak ada error, simpan data ke database
    if (empty($errors)) {
        $sql = "INSERT INTO lomba (nama_lomba, deskripsi, tgl_lomba, lokasi, link_daftar, gambar, penyelenggara_lomba, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssss", $namaLomba, $deskripsi, $tanggalLomba, $lokasi, $linkPendaftaran, $gambar, $penyelenggaraLomba, $status);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?message=Lomba berhasil ditambahkan");
            exit();
        } else {
            $errors[] = 'Gagal menyimpan data: ' . mysqli_error($conn);
        }
    }
}

include 'header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-plus-circle"></i> Tambah Lomba Baru
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

                    <form method="POST" action="tambah.php" enctype="multipart/form-data">
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
                                           value="<?php echo isset($_POST['nama_lomba']) ? htmlspecialchars($_POST['nama_lomba']) : ''; ?>"
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
                                           value="<?php echo isset($_POST['tgl_lomba']) ? htmlspecialchars($_POST['tgl_lomba']) : ''; ?>"
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
                                      required><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
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
                                           value="<?php echo isset($_POST['lokasi']) ? htmlspecialchars($_POST['lokasi']) : ''; ?>"
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
                                   value="<?php echo isset($_POST['link_daftar']) ? htmlspecialchars($_POST['link_daftar']) : ''; ?>"
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
                                           value="<?php echo isset($_POST['penyelenggara_lomba']) ? htmlspecialchars($_POST['penyelenggara_lomba']) : ''; ?>"
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
                                        <option value="available" <?php echo (isset($_POST['status']) && $_POST['status'] == 'available') ? 'selected' : 'selected'; ?>>Tersedia</option>
                                        <option value="unavailable" <?php echo (isset($_POST['status']) && $_POST['status'] == 'unavailable') ? 'selected' : ''; ?>>Tidak Tersedia</option>
                                    </select>
                                </div>
                            </div>
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

<?php include 'footer.php'; ?>
