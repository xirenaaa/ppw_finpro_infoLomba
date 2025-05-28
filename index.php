<?php
require_once 'config.php';
$page_title = 'Daftar Lomba';

// Query untuk mendapatkan data lomba dengan join ke tabel terkait
$sql = "SELECT l.*, 
               bl.nama_bidang,
               k.nama_kategori,
               h.nama_hadiah,
               h.nilai_hadiah,
               jl.tanggal_mulai,
               jl.tanggal_selesai
        FROM lomba l
        LEFT JOIN bidang_lomba bl ON l.id_bidang = bl.id_bidang
        LEFT JOIN kategori k ON l.id_kategori = k.id_kategori  
        LEFT JOIN hadiah h ON l.id_hadiah = h.id_hadiah
        LEFT JOIN jadwal_lomba jl ON l.id_lomba = jl.id_lomba
        ORDER BY l.id_lomba DESC";

$result = mysqli_query($conn, $sql);

include 'includes/header.php';
?>

<div class="container mt-4">
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
                                        <th>Nama Lomba</th>
                                        <th>Bidang & Kategori</th>
                                        <th>Jadwal</th>
                                        <th>Hadiah</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?php echo $row['id_lomba']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($row['nama_lomba']); ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="bi bi-building"></i> <?php echo htmlspecialchars($row['penyelenggara'] ?? 'Tidak ada'); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($row['nama_bidang'] ?? 'Tidak ada'); ?></span>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($row['nama_kategori'] ?? 'Tidak ada'); ?></small>
                                            </td>
                                            <td>
                                                <?php if ($row['tanggal_mulai']): ?>
                                                    <i class="bi bi-calendar-event"></i> 
                                                    <?php echo date('d/m/Y', strtotime($row['tanggal_mulai'])); ?>
                                                    <?php if ($row['tanggal_selesai']): ?>
                                                        <br><small class="text-muted">s/d <?php echo date('d/m/Y', strtotime($row['tanggal_selesai'])); ?></small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <small class="text-muted">Belum dijadwalkan</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($row['nama_hadiah']): ?>
                                                    <strong><?php echo htmlspecialchars($row['nama_hadiah']); ?></strong>
                                                    <?php if ($row['nilai_hadiah']): ?>
                                                        <br><small class="text-success">Rp <?php echo number_format($row['nilai_hadiah'], 0, ',', '.'); ?></small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <small class="text-muted">Belum ada hadiah</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $status = isset($row['status']) ? $row['status'] : 'available';
                                                $status_class = ($status == 'available') ? 'badge-available' : 'badge-unavailable';
                                                $status_text = ($status == 'available') ? 'Tersedia' : 'Tidak Tersedia';
                                                ?>
                                                <span class="badge <?php echo $status_class; ?>">
                                                    <?php echo $status_text; ?>
                                                </span>
                                                <br>
                                                <div class="btn-group mt-1" role="group">
                                                    <?php if ($status == 'available'): ?>
                                                        <button onclick="updateStatus(<?php echo $row['id_lomba']; ?>, 'unavailable')" 
                                                                class="btn btn-sm btn-outline-warning" 
                                                                title="Nonaktifkan">
                                                            <i class="bi bi-eye-slash"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button onclick="updateStatus(<?php echo $row['id_lomba']; ?>, 'available')" 
                                                                class="btn btn-sm btn-outline-success" 
                                                                title="Aktifkan">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
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
                                                            <i class="bi bi-trophy"></i> Detail Lomba: <?php echo htmlspecialchars($row['nama_lomba']); ?>
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6><i class="bi bi-info-circle"></i> Informasi Lomba:</h6>
                                                                <table class="table table-borderless">
                                                                    <tr>
                                                                        <td>Nama</td>
                                                                        <td>: <?php echo htmlspecialchars($row['nama_lomba']); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Bidang</td>
                                                                        <td>: <?php echo htmlspecialchars($row['nama_bidang'] ?? 'Tidak ada'); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Kategori</td>
                                                                        <td>: <?php echo htmlspecialchars($row['nama_kategori'] ?? 'Tidak ada'); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Status</td>
                                                                        <td>: <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6><i class="bi bi-calendar"></i> Jadwal & Hadiah:</h6>
                                                                <table class="table table-borderless">
                                                                    <tr>
                                                                        <td>Mulai</td>
                                                                        <td>: <?php echo $row['tanggal_mulai'] ? date('d F Y', strtotime($row['tanggal_mulai'])) : 'Belum dijadwalkan'; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Selesai</td>
                                                                        <td>: <?php echo $row['tanggal_selesai'] ? date('d F Y', strtotime($row['tanggal_selesai'])) : 'Belum dijadwalkan'; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Hadiah</td>
                                                                        <td>: <?php echo htmlspecialchars($row['nama_hadiah'] ?? 'Belum ada'); ?></td>
                                                                    </tr>
                                                                    <?php if ($row['nilai_hadiah']): ?>
                                                                    <tr>
                                                                        <td>Nilai</td>
                                                                        <td>: Rp <?php echo number_format($row['nilai_hadiah'], 0, ',', '.'); ?></td>
                                                                    </tr>
                                                                    <?php endif; ?>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mt-3">
                                                            <a href="edit.php?id=<?php echo $row['id_lomba']; ?>" class="btn btn-sm btn-primary">
                                                                <i class="bi bi-pencil"></i> Edit Data
                                                            </a>
                                                            <a href="peserta/index.php?lomba=<?php echo $row['id_lomba']; ?>" class="btn btn-sm btn-info">
                                                                <i class="bi bi-people"></i> Lihat Peserta
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <?php if (isset($row['created_at'])): ?>
                                                            <small class="text-muted me-auto">
                                                                Dibuat: <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                                                                <?php if (isset($row['updated_at']) && $row['updated_at'] != $row['created_at']): ?>
                                                                    | Diperbarui: <?php echo date('d/m/Y H:i', strtotime($row['updated_at'])); ?>
                                                                <?php endif; ?>
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
                                <i class="bi bi-plus-circle"></i> Tambah Lomba Baru
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Dashboard Cards -->
            <div class="row mt-4">
                <?php
                // Reset pointer dan hitung statistik
                mysqli_data_seek($result, 0);
                $total_lomba = mysqli_num_rows($result);
                $available_count = 0;
                $unavailable_count = 0;
                $total_hadiah = 0;
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $status = isset($row['status']) ? $row['status'] : 'available';
                    if ($status == 'available') {
                        $available_count++;
                    } else {
                        $unavailable_count++;
                    }
                    if ($row['nilai_hadiah']) {
                        $total_hadiah += $row['nilai_hadiah'];
                    }
                }
                ?>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-trophy text-primary fs-3"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title">Total Lomba</h5>
                                    <h3 class="mb-0"><?php echo $total_lomba; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-check-circle text-success fs-3"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title">Lomba Tersedia</h5>
                                    <h3 class="mb-0"><?php echo $available_count; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-danger bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-x-circle text-danger fs-3"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title">Tidak Tersedia</h5>
                                    <h3 class="mb-0"><?php echo $unavailable_count; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-gift text-warning fs-3"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title">Total Hadiah</h5>
                                    <h6 class="mb-0">Rp <?php echo number_format($total_hadiah, 0, ',', '.'); ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Konfirmasi hapus
function confirmDelete(id, nama) {
    if (confirm(`Apakah Anda yakin ingin menghapus lomba "${nama}"?`)) {
        window.location.href = `delete.php?id=${id}`;
    }
}

// Function untuk update status
function updateStatus(id, status) {
    const statusText = (status === 'available') ? 'mengaktifkan' : 'menonaktifkan';
    const confirmText = `Apakah Anda yakin ingin ${statusText} lomba ini?`;
    
    if (confirm(confirmText)) {
        // Buat form tersembunyi untuk submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'update_status.php';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id_lomba';
        idInput.value = id;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        
        form.appendChild(idInput);
        form.appendChild(statusInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include 'includes/footer.php'; ?>
