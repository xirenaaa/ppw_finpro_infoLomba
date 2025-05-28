<?php
require_once 'config.php';
$page_title = 'Beranda - Daftar Lomba';

// Cek apakah kolom created_at ada, jika tidak gunakan id_lomba untuk sorting
$check_column = mysqli_query($conn, "SHOW COLUMNS FROM lomba LIKE 'created_at'");
if (mysqli_num_rows($check_column) > 0) {
    $sql = "SELECT * FROM lomba ORDER BY created_at DESC";
} else {
    $sql = "SELECT * FROM lomba ORDER BY id_lomba DESC";
}

$result = mysqli_query($conn, $sql);

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-trophy"></i> Daftar Lomba</h2>
            <a href="create.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Lomba
            </a>
        </div>

        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($_GET['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-table"></i> Data Lomba
                    <span class="badge bg-primary ms-2"><?php echo mysqli_num_rows($result); ?> lomba</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Gambar</th>
                                    <th>Nama Lomba</th>
                                    <th>Tanggal</th>
                                    <th>Lokasi</th>
                                    <th>Penyelenggara</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $row['id_lomba']; ?></td>
                                        <td>
                                            <img src="<?php echo htmlspecialchars($row['gambar']); ?>" 
                                                 alt="<?php echo htmlspecialchars($row['nama_lomba']); ?>" 
                                                 class="img-thumbnail" 
                                                 style="width: 60px; height: 60px; object-fit: cover;"
                                                 onerror="this.src='https://via.placeholder.com/60x60?text=No+Image'">
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($row['nama_lomba']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars(substr($row['deskripsi'], 0, 50)) . (strlen($row['deskripsi']) > 50 ? '...' : ''); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <i class="bi bi-calendar-event"></i> 
                                            <?php echo date('d/m/Y', strtotime($row['tgl_lomba'])); ?>
                                        </td>
                                        <td>
                                            <i class="bi bi-geo-alt"></i> 
                                            <?php echo htmlspecialchars($row['lokasi']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars(substr($row['penyelenggara_lomba'], 0, 30)) . (strlen($row['penyelenggara_lomba']) > 30 ? '...' : ''); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-info" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#detailModal<?php echo $row['id_lomba']; ?>"
                                                        title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <a href="edit.php?id=<?php echo $row['id_lomba']; ?>" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?php echo $row['id_lomba']; ?>, '<?php echo htmlspecialchars($row['nama_lomba']); ?>')" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal Detail -->
                                    <div class="modal fade" id="detailModal<?php echo $row['id_lomba']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        <i class="bi bi-trophy"></i> <?php echo htmlspecialchars($row['nama_lomba']); ?>
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <img src="<?php echo htmlspecialchars($row['gambar']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($row['nama_lomba']); ?>" 
                                                                 class="img-fluid rounded"
                                                                 onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                                                        </div>
                                                        <div class="col-md-8">
                                                            <h6><i class="bi bi-file-text"></i> Deskripsi:</h6>
                                                            <p><?php echo nl2br(htmlspecialchars($row['deskripsi'])); ?></p>
                                                            
                                                            <h6><i class="bi bi-calendar-event"></i> Tanggal:</h6>
                                                            <p><?php echo date('d F Y', strtotime($row['tgl_lomba'])); ?></p>
                                                            
                                                            <h6><i class="bi bi-geo-alt"></i> Lokasi:</h6>
                                                            <p><?php echo htmlspecialchars($row['lokasi']); ?></p>
                                                            
                                                            <h6><i class="bi bi-building"></i> Penyelenggara:</h6>
                                                            <p><?php echo nl2br(htmlspecialchars($row['penyelenggara_lomba'])); ?></p>
                                                            
                                                            <h6><i class="bi bi-link-45deg"></i> Link Pendaftaran:</h6>
                                                            <p>
                                                                <?php if (filter_var($row['link_daftar'], FILTER_VALIDATE_URL)): ?>
                                                                    <a href="<?php echo htmlspecialchars($row['link_daftar']); ?>" target="_blank" class="btn btn-sm btn-primary">
                                                                        <i class="bi bi-box-arrow-up-right"></i> Daftar Sekarang
                                                                    </a>
                                                                <?php else: ?>
                                                                    <?php echo nl2br(htmlspecialchars($row['link_daftar'])); ?>
                                                                <?php endif; ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <?php if (isset($row['created_at'])): ?>
                                                        <small class="text-muted me-auto">
                                                            Dibuat: <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                                                        </small>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-trophy display-1 text-muted"></i>
                        <h4 class="text-muted mt-3">Belum ada lomba</h4>
                        <p class="text-muted">Silakan tambah lomba baru untuk memulai.</p>
                        <a href="create.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tambah Lomba Pertama
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>