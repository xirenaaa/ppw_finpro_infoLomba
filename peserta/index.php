<?php
require_once '../config.php';

// Get all participants with competition info
$sql = "SELECT p.*, l.nama_lomba 
        FROM peserta p 
        LEFT JOIN lomba l ON p.id_lomba = l.id_lomba 
        ORDER BY p.tanggal_daftar DESC";
$result = mysqli_query($conn, $sql);
$peserta_list = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total_peserta,
    COUNT(CASE WHEN status_pendaftaran = 'Menunggu' THEN 1 END) as menunggu,
    COUNT(CASE WHEN status_pendaftaran = 'Diterima' THEN 1 END) as diterima,
    COUNT(CASE WHEN status_pendaftaran = 'Ditolak' THEN 1 END) as ditolak
    FROM peserta";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peserta - InfoLomba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
        }
        
        body {
            background: var(--primary-gradient);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .peserta-container {
            padding: 2rem 0;
        }
        
        .peserta-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .peserta-title {
            color: white;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .stats-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1.5rem;
            text-align: center;
            color: white;
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .table-glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }
        
        .table-dark {
            --bs-table-bg: transparent;
            --bs-table-border-color: rgba(255, 255, 255, 0.2);
        }
        
        .btn-glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateY(-2px);
        }
        
        .badge-status {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }
        
        .badge-menunggu {
            background: rgba(255, 193, 7, 0.8);
        }
        
        .badge-diterima {
            background: rgba(25, 135, 84, 0.8);
        }
        
        .badge-ditolak {
            background: rgba(220, 53, 69, 0.8);
        }
    </style>
</head>
<body>
    <div class="container peserta-container">
        <div class="peserta-card">
            <h1 class="peserta-title">
                <i class="bi bi-people"></i> Data Peserta Lomba
            </h1>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="../index.php" class="btn btn-glass">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                </a>
                <a href="tambah.php" class="btn btn-glass">
                    <i class="bi bi-plus"></i> Tambah Peserta
                </a>
            </div>
            
            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= $stats['total_peserta'] ?></div>
                        <div>Total Peserta</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= $stats['menunggu'] ?></div>
                        <div>Menunggu</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= $stats['diterima'] ?></div>
                        <div>Diterima</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= $stats['ditolak'] ?></div>
                        <div>Ditolak</div>
                    </div>
                </div>
            </div>
            
            <!-- Participants Table -->
            <div class="table-glass">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Peserta</th>
                                <th>Email</th>
                                <th>Lomba</th>
                                <th>Institusi</th>
                                <th>Tanggal Daftar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($peserta_list)): ?>
                                <?php foreach ($peserta_list as $peserta): ?>
                                    <tr>
                                        <td><?= $peserta['id_peserta'] ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($peserta['nama_peserta']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($peserta['no_telepon']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($peserta['email']) ?></td>
                                        <td>
                                            <?php if ($peserta['nama_lomba']): ?>
                                                <span class="badge bg-primary"><?= htmlspecialchars($peserta['nama_lomba']) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Lomba Dihapus</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($peserta['institusi']) ?></td>
                                        <td><?= formatTanggalIndonesia($peserta['tanggal_daftar']) ?></td>
                                        <td>
                                            <span class="badge badge-status badge-<?= strtolower($peserta['status_pendaftaran']) ?>">
                                                <?= $peserta['status_pendaftaran'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="edit.php?id=<?= $peserta['id_peserta'] ?>" class="btn btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?= $peserta['id_peserta'] ?>, '<?= htmlspecialchars($peserta['nama_peserta']) ?>')" 
                                                        class="btn btn-outline-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.5;"></i>
                                        <br>
                                        <span class="text-muted">Belum ada peserta yang terdaftar</span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function confirmDelete(id, nama) {
            if (confirm(`Apakah Anda yakin ingin menghapus peserta "${nama}"?`)) {
                window.location.href = `hapus.php?id=${id}`;
            }
        }
    </script>
</body>
</html>
