<?php
require_once 'config.php';
$page_title = 'Tambah Lomba Baru';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $namaLomba = trim($_POST['nama_lomba']);
    $deskripsi = trim($_POST['deskripsi']);
    $tanggalLomba = trim($_POST['tgl_lomba']);
    $lokasi = trim($_POST['lokasi']);
    $linkPendaftaran = trim($_POST['link_daftar']);
    $gambar = trim($_POST['gambar']);
    $penyelenggaraLomba = trim($_POST['penyelenggara_lomba']);
    
    // Validasi form
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
    
    if (empty($gambar)) {
        $errors[] = 'URL gambar tidak boleh kosong';
    }
    
    if (empty($penyelenggaraLomba)) {
        $errors[] = 'Penyelenggara lomba tidak boleh kosong';
    }
    
    // Validasi tanggal tidak boleh masa lalu
    if (!empty($tanggalLomba) && strtotime($tanggalLomba) < strtotime(date('Y-m-d'))) {
        $errors[] = 'Tanggal lomba tidak boleh di masa lalu';
    }
    
    // Jika tidak ada error, simpan data
    if (empty($errors)) {
        $sql = "INSERT INTO lomba (nama_lomba, deskripsi, tgl_lomba, lokasi, link_daftar, gambar, penyelenggara_lomba) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssss", $namaLomba, $deskripsi, $tanggalLomba, $lokasi, $linkPendaftaran, $gambar, $penyelenggaraLomba);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?message=Lomba berhasil ditambahkan");
            exit();
        } else {
            $errors[] = 'Gagal menyimpan data: ' . mysqli_error($conn);
        }
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-10">
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

                <form method="POST" action="create.php">
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
                                <div class="form-text">Contoh: Lomba Karya Tulis Ilmiah Nasional 2024</div>
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
                                       min="<?php echo date('Y-m-d'); ?>"
                                       required>
                                <div class="form-text">Pilih tanggal pelaksanaan lomba</div>
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
                                  placeholder="Masukkan deskripsi lengkap tentang lomba"
                                  required><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                        <div class="form-text">Jelaskan tujuan, kategori, dan ketentuan lomba</div>
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
                                <div class="form-text">Contoh: Jakarta, Indonesia atau Online</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="gambar" class="form-label">
                                    <i class="bi bi-image"></i> URL Gambar *
                                </label>
                                <input type="url" 
                                       class="form-control" 
                                       id="gambar" 
                                       name="gambar" 
                                       value="<?php echo isset($_POST['gambar']) ? htmlspecialchars($_POST['gambar']) : ''; ?>"
                                       placeholder="https://example.com/gambar.jpg"
                                       required>
                                <div class="form-text">Masukkan URL gambar poster lomba</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="link_daftar" class="form-label">
                            <i class="bi bi-link-45deg"></i> Link Pendaftaran *
                        </label>
                        <textarea class="form-control" 
                                  id="link_daftar" 
                                  name="link_daftar" 
                                  rows="2" 
                                  placeholder="Masukkan link atau informasi pendaftaran lomba"
                                  required><?php echo isset($_POST['link_daftar']) ? htmlspecialchars($_POST['link_daftar']) : ''; ?></textarea>
                        <div class="form-text">Bisa berupa URL website atau informasi cara mendaftar</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="penyelenggara_lomba" class="form-label">
                            <i class="bi bi-building"></i> Penyelenggara Lomba *
                        </label>
                        <textarea class="form-control" 
                                  id="penyelenggara_lomba" 
                                  name="penyelenggara_lomba" 
                                  rows="3" 
                                  placeholder="Masukkan informasi penyelenggara lomba"
                                  required><?php echo isset($_POST['penyelenggara_lomba']) ? htmlspecialchars($_POST['penyelenggara_lomba']) : ''; ?></textarea>
                        <div class="form-text">Nama organisasi/institusi penyelenggara dan kontak</div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" name="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Lomba
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Preview Card -->
        <div class="card mt-3" id="previewCard" style="display: none;">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-eye"></i> Preview Gambar
                </h6>
            </div>
            <div class="card-body text-center">
                <img id="previewImage" 
                     src="/placeholder.svg" 
                     alt="Preview" 
                     class="img-fluid rounded" 
                     style="max-height: 200px;">
            </div>
        </div>
    </div>
</div>

<script>
// Preview gambar saat URL dimasukkan
document.getElementById('gambar').addEventListener('input', function() {
    const url = this.value;
    const previewCard = document.getElementById('previewCard');
    const previewImage = document.getElementById('previewImage');
    
    if (url && url.startsWith('http')) {
        previewImage.src = url;
        previewImage.onerror = function() {
            previewCard.style.display = 'none';
        };
        previewImage.onload = function() {
            previewCard.style.display = 'block';
        };
    } else {
        previewCard.style.display = 'none';
    }
});
</script>

<?php include 'includes/footer.php'; ?>