<?php
require_once 'config.php';
$page_title = 'Tambah Lomba';

$errors = [];

// Cek apakah tabel relasi ada
$check_bidang = mysqli_query($conn, "SHOW TABLES LIKE 'bidang_lomba'");
$check_kategori = mysqli_query($conn, "SHOW TABLES LIKE 'kategori'");
$check_hadiah = mysqli_query($conn, "SHOW TABLES LIKE 'hadiah'");

$use_relations = (mysqli_num_rows($check_bidang) > 0 && 
                  mysqli_num_rows($check_kategori) > 0 && 
                  mysqli_num_rows($check_hadiah) > 0);

// Ambil data untuk dropdown jika tabel ada
if ($use_relations) {
    $bidang_lomba = mysqli_query($conn, "SELECT * FROM bidang_lomba ORDER BY nama_bidang ASC");
    $kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
    $hadiah = mysqli_query($conn, "SELECT * FROM hadiah ORDER BY nama_hadiah ASC");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Ambil data dari form
    $namaLomba = trim($_POST['nama_lomba']);
    $penyelenggaraLomba = trim($_POST['penyelenggara']);
    $deskripsi = trim($_POST['deskripsi']);
    $status = trim($_POST['status']);
    
    // Data relasi (jika ada)
    $idBidang = $use_relations ? trim($_POST['id_bidang']) : null;
    $idKategori = $use_relations ? trim($_POST['id_kategori']) : null;
    $idHadiah = $use_relations ? trim($_POST['id_hadiah']) : null;
    
    // Data jadwal (jika tabel ada)
    $tanggalMulai = isset($_POST['tanggal_mulai']) ? trim($_POST['tanggal_mulai']) : null;
    $tanggalSelesai = isset($_POST['tanggal_selesai']) ? trim($_POST['tanggal_selesai']) : null;
    
    // Data syarat (jika tabel ada)
    $syaratUmum = isset($_POST['syarat_umum']) ? trim($_POST['syarat_umum']) : null;
    $syaratKhusus = isset($_POST['syarat_khusus']) ? trim($_POST['syarat_khusus']) : null;

    // Validasi data
    if (empty($namaLomba)) {
        $errors[] = 'Nama lomba harus diisi';
    }
    if (empty($penyelenggaraLomba)) {
        $errors[] = 'Penyelenggara harus diisi';
    }
    if ($use_relations && empty($idBidang)) {
        $errors[] = 'Bidang lomba harus dipilih';
    }
    if ($use_relations && empty($idKategori)) {
        $errors[] = 'Kategori harus dipilih';
    }
    if (!empty($tanggalSelesai) && !empty($tanggalMulai) && $tanggalSelesai < $tanggalMulai) {
        $errors[] = 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai';
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
        mysqli_begin_transaction($conn);
        
        try {
            // Insert ke tabel lomba
            if ($use_relations) {
                $sql_lomba = "INSERT INTO lomba (nama_lomba, id_bidang, id_kategori, id_hadiah, penyelenggara, deskripsi, gambar, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_lomba = mysqli_prepare($conn, $sql_lomba);
                mysqli_stmt_bind_param($stmt_lomba, "siiissss", $namaLomba, $idBidang, $idKategori, $idHadiah, $penyelenggaraLomba, $deskripsi, $gambar, $status);
            } else {
                // Fallback untuk struktur lama
                $sql_lomba = "INSERT INTO lomba (nama_lomba, penyelenggara_lomba, deskripsi, gambar, status) VALUES (?, ?, ?, ?, ?)";
                $stmt_lomba = mysqli_prepare($conn, $sql_lomba);
                mysqli_stmt_bind_param($stmt_lomba, "sssss", $namaLomba, $penyelenggaraLomba, $deskripsi, $gambar, $status);
            }
            
            mysqli_stmt_execute($stmt_lomba);
            $idLomba = mysqli_insert_id($conn);
            
            // Insert ke tabel jadwal_lomba jika ada
            if ($use_relations && !empty($tanggalMulai)) {
                $check_jadwal = mysqli_query($conn, "SHOW TABLES LIKE 'jadwal_lomba'");
                if (mysqli_num_rows($check_jadwal) > 0) {
                    $sql_jadwal = "INSERT INTO jadwal_lomba (id_lomba, tanggal_mulai, tanggal_selesai) VALUES (?, ?, ?)";
                    $stmt_jadwal = mysqli_prepare($conn, $sql_jadwal);
                    mysqli_stmt_bind_param($stmt_jadwal, "iss", $idLomba, $tanggalMulai, $tanggalSelesai);
                    mysqli_stmt_execute($stmt_jadwal);
                }
            }
            
            // Insert ke tabel syarat jika ada
            if ($use_relations && (!empty($syaratUmum) || !empty($syaratKhusus))) {
                $check_syarat = mysqli_query($conn, "SHOW TABLES LIKE 'syarat'");
                if (mysqli_num_rows($check_syarat) > 0) {
                    $sql_syarat = "INSERT INTO syarat (id_lomba, syarat_umum, syarat_khusus) VALUES (?, ?, ?)";
                    $stmt_syarat = mysqli_prepare($conn, $sql_syarat);
                    mysqli_stmt_bind_param($stmt_syarat, "iss", $idLomba, $syaratUmum, $syaratKhusus);
                    mysqli_stmt_execute($stmt_syarat);
                }
            }
            
            mysqli_commit($conn);
            header("Location: index.php?message=Lomba berhasil ditambahkan");
            exit();
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $errors[] = 'Gagal menyimpan data: ' . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <?php if (!$use_relations): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Mode Sederhana!</strong> Beberapa tabel relasi belum dibuat. Form ini akan menggunakan mode sederhana.
                    <a href="index.php" class="alert-link">Kembali ke halaman utama</a> untuk setup database lengkap.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-plus-circle"></i> Tambah Lomba Baru
                        <?php if (!$use_relations): ?>
                            <span class="badge bg-warning ms-2">Mode Sederhana</span>
                        <?php endif; ?>
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

                    <form method="POST" action="create.php" enctype="multipart/form-data">
                        <!-- Informasi Dasar Lomba -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Dasar</h6>
                            </div>
                            <div class="card-body">
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
                                            <label for="penyelenggara" class="form-label">
                                                <i class="bi bi-building"></i> Penyelenggara *
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="penyelenggara" 
                                                   name="penyelenggara" 
                                                   value="<?php echo isset($_POST['penyelenggara']) ? htmlspecialchars($_POST['penyelenggara']) : ''; ?>"
                                                   placeholder="Masukkan nama penyelenggara"
                                                   required>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if ($use_relations): ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="id_bidang" class="form-label">
                                                <i class="bi bi-tags"></i> Bidang Lomba *
                                            </label>
                                            <select class="form-select" id="id_bidang" name="id_bidang" required>
                                                <option value="">-- Pilih Bidang --</option>
                                                <?php while ($bidang = mysqli_fetch_assoc($bidang_lomba)): ?>
                                                    <option value="<?php echo $bidang['id_bidang']; ?>" <?php echo (isset($_POST['id_bidang']) && $_POST['id_bidang'] == $bidang['id_bidang']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($bidang['nama_bidang']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="id_kategori" class="form-label">
                                                <i class="bi bi-collection"></i> Kategori *
                                            </label>
                                            <select class="form-select" id="id_kategori" name="id_kategori" required>
                                                <option value="">-- Pilih Kategori --</option>
                                                <?php while ($kat = mysqli_fetch_assoc($kategori)): ?>
                                                    <option value="<?php echo $kat['id_kategori']; ?>" <?php echo (isset($_POST['id_kategori']) && $_POST['id_kategori'] == $kat['id_kategori']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="id_hadiah" class="form-label">
                                                <i class="bi bi-gift"></i> Hadiah
                                            </label>
                                            <select class="form-select" id="id_hadiah" name="id_hadiah">
                                                <option value="">-- Pilih Hadiah --</option>
                                                <?php while ($h = mysqli_fetch_assoc($hadiah)): ?>
                                                    <option value="<?php echo $h['id_hadiah']; ?>" <?php echo (isset($_POST['id_hadiah']) && $_POST['id_hadiah'] == $h['id_hadiah']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($h['nama_hadiah']); ?>
                                                        <?php if ($h['nilai_hadiah']): ?>
                                                            (Rp <?php echo number_format($h['nilai_hadiah'], 0, ',', '.'); ?>)
                                                        <?php endif; ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="deskripsi" class="form-label">
                                                <i class="bi bi-file-text"></i> Deskripsi
                                            </label>
                                            <textarea class="form-control" 
                                                      id="deskripsi" 
                                                      name="deskripsi" 
                                                      rows="3" 
                                                      placeholder="Masukkan deskripsi lomba"><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
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
                            </div>
                        </div>

                        <?php if ($use_relations): ?>
                        <!-- Jadwal Lomba -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-calendar"></i> Jadwal Lomba</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tanggal_mulai" class="form-label">
                                                <i class="bi bi-calendar-event"></i> Tanggal Mulai
                                            </label>
                                            <input type="date" 
                                                   class="form-control" 
                                                   id="tanggal_mulai" 
                                                   name="tanggal_mulai" 
                                                   value="<?php echo isset($_POST['tanggal_mulai']) ? htmlspecialchars($_POST['tanggal_mulai']) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tanggal_selesai" class="form-label">
                                                <i class="bi bi-calendar-check"></i> Tanggal Selesai
                                            </label>
                                            <input type="date" 
                                                   class="form-control" 
                                                   id="tanggal_selesai" 
                                                   name="tanggal_selesai" 
                                                   value="<?php echo isset($_POST['tanggal_selesai']) ? htmlspecialchars($_POST['tanggal_selesai']) : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Syarat Lomba -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-list-check"></i> Syarat Lomba</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="syarat_umum" class="form-label">
                                                <i class="bi bi-check-circle"></i> Syarat Umum
                                            </label>
                                            <textarea class="form-control" 
                                                      id="syarat_umum" 
                                                      name="syarat_umum" 
                                                      rows="4" 
                                                      placeholder="Masukkan syarat umum lomba"><?php echo isset($_POST['syarat_umum']) ? htmlspecialchars($_POST['syarat_umum']) : ''; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="syarat_khusus" class="form-label">
                                                <i class="bi bi-check-square"></i> Syarat Khusus
                                            </label>
                                            <textarea class="form-control" 
                                                      id="syarat_khusus" 
                                                      name="syarat_khusus" 
                                                      rows="4" 
                                                      placeholder="Masukkan syarat khusus lomba"><?php echo isset($_POST['syarat_khusus']) ? htmlspecialchars($_POST['syarat_khusus']) : ''; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
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

<?php include 'includes/footer.php'; ?>
