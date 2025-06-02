<?php
include 'config.php';

// Ambil semua data lomba
$query = $conn->query('SELECT * FROM lomba');
$lombas = $query->fetch_all(MYSQLI_ASSOC);
$total_lomba = count($lombas);

// Hitung lomba aktif
$active_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM lomba WHERE tgl_lomba >= CURDATE()");
$active_count = mysqli_fetch_assoc($active_query)['count'] ?? 0;

// Hitung total kategori
$category_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM kategori");
$category_count = mysqli_fetch_assoc($category_query)['count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Lomba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .card-lomba {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card-lomba:hover {
            transform: translateY(-5px);
        }
        .badge-kategori {
            font-size: 0.75rem;
        }
        .stats-card {
            background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="bi bi-trophy-fill"></i> InfoLomba</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="index.php"><i class="bi bi-house"></i> Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="kategori.php"><i class="bi bi-tags"></i> Kategori</a></li>
                <li class="nav-item"><a class="nav-link" href="hadiah.php"><i class="bi bi-gift"></i> Hadiah</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold"><i class="bi bi-trophy"></i> Sistem Informasi Lomba</h1>
        <p class="lead">Kelola dan pantau berbagai lomba dengan mudah</p>
    </div>
</div>

<div class="container">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stats-card text-center p-3">
                <h3 class="fw-bold"><?= $total_lomba ?></h3>
                <p class="mb-0">Total Lomba</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white text-center p-3">
                <h3 class="fw-bold"><?= $active_count ?></h3>
                <p class="mb-0">Lomba Aktif</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white text-center p-3">
                <h3 class="fw-bold"><?= $category_count ?></h3>
                <p class="mb-0">Kategori</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary"><i class="bi bi-list-ul"></i> Daftar Lomba</h2>
        <a href="tambah.php" class="btn btn-success btn-lg"><i class="bi bi-plus-circle"></i> Tambah Lomba</a>
    </div>

    <?php if ($total_lomba > 0): ?>
        <div class="row">
            <?php foreach ($lombas as $index => $lomba): 
                $is_active = strtotime($lomba['tgl_lomba']) >= time();
                $status_class = $is_active ? 'success' : 'secondary';
            ?>
            <div class="col-md-6 mb-4">
                <div class="card card-lomba">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><?= htmlspecialchars($lomba['nama_lomba']) ?></h5>
                        <p><?= nl2br(htmlspecialchars($lomba['deskripsi'])) ?></p>
                        <div class="mb-2">
                            <i class="bi bi-calendar-event"></i>
                            <?= date('d M Y', strtotime($lomba['tgl_lomba'])) ?>
                        </div>
                        <div class="mb-2">
                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($lomba['lokasi']) ?>
                        </div>
                        <div class="mb-2">
                            <i class="bi bi-link-45deg"></i> 
                            <a href="<?= htmlspecialchars($lomba['link_daftar']) ?>" target="_blank">Daftar</a>
                        </div>
                        <span class="badge bg-<?= $status_class ?>"><?= $is_active ? 'Aktif' : 'Selesai' ?></span>
                        <div class="mt-3">
                            <a href="detail.php?id=<?= $lomba['id_lomba'] ?>" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            <a href="edit.php?id=<?= $lomba['id_lomba'] ?>" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="hapus.php?id=<?= $lomba['id_lomba'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Yakin ingin menghapus lomba ini?')">
                                <i class="bi bi-trash"></i> Hapus
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Jika tidak ada data lomba -->
        <div class="text-center py-5">
            <div class="mb-4"><i class="bi bi-inbox display-1 text-muted"></i></div>
            <h3 class="text-muted">Belum Ada Data Lomba</h3>
            <p class="text-muted mb-4">Mulai dengan menambahkan lomba pertama Anda</p>
            <a href="tambah.php" class="btn btn-primary btn-lg">
                <i class="bi bi-plus-circle"></i> Tambah Lomba Pertama
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-4 mt-5">
    <div class="container">
        <p class="mb-0"><i class="bi bi-trophy-fill"></i> &copy; 2024 Sistem Informasi Lomba</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>