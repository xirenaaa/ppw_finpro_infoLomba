<?php
require_once 'config.php';

// Pagination settings
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? escape_string($_GET['search']) : '';
$search_condition = $search ? "WHERE l.nama_lomba LIKE '%$search%'" : '';

// Filter functionality
$bidang_filter = isset($_GET['bidang']) ? (int)$_GET['bidang'] : 0;
$filter_condition = '';
if ($bidang_filter) {
    $filter_condition = ($search_condition ? ' AND ' : ' WHERE ') . "l.id_bidang = $bidang_filter";
}

// Get competitions with pagination
$sql = "SELECT l.*, b.nama_bidang 
        FROM lomba l 
        LEFT JOIN bidang_lomba b ON l.id_bidang = b.id_bidang 
        $search_condition $filter_condition 
        ORDER BY l.tgl_lomba DESC 
        LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, 'SELECT  * FROM lomba');
$lombas = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM lomba l $search_condition $filter_condition";
$count_result = mysqli_query($conn, $count_sql);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $limit);

// Get filter options
$bidang_options = mysqli_query($conn, "SELECT * FROM bidang_lomba ORDER BY nama_bidang");

// Statistics
$stats_sql = "SELECT 
    COUNT(*) as total_lomba,
    COUNT(CASE WHEN tgl_lomba >= CURDATE() THEN 1 END) as lomba_aktif,
    COUNT(CASE WHEN status = 'available' THEN 1 END) as lomba_tersedia
    FROM lomba";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfoLomba - Platform Informasi Lomba Terlengkap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: var(--primary-gradient);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        /* Glassmorphism Navbar */
        .navbar-glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
            transition: all 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
            color: #f093fb !important;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover {
            color: #f093fb !important;
            transform: translateY(-2px);
        }
        
        /* Hero Section */
        .hero-section {
            padding: 4rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
        }
        
        /* Search Section */
        .search-section {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        .search-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            color: white;
            padding: 15px 20px;
            font-size: 1.1rem;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #f093fb;
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(240, 147, 251, 0.25);
        }
        
        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .btn-search {
            background: var(--secondary-gradient);
            border: none;
            border-radius: 15px;
            padding: 15px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            color: white;
        }
        
        /* Filter Section */
        .filter-section {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .filter-select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: white;
            backdrop-filter: blur(5px);
        }
        
        .filter-select:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #f093fb;
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(240, 147, 251, 0.25);
        }
        
        .filter-select option {
            background: #333;
            color: white;
        }
        
        /* Stats Cards */
        .stats-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .stats-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        
        .stats-number {
            font-size: 3rem;
            font-weight: 800;
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stats-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            margin-top: 0.5rem;
        }
        
        /* Competition Cards */
        .competition-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
        }
        
        .competition-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        .competition-image {
            height: 200px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .competition-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }
        
        .competition-card:hover .competition-image img {
            transform: scale(1.1);
        }
        
        .competition-content {
            padding: 1.5rem;
            color: white;
        }
        
        .competition-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: white;
        }
        
        .competition-description {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .competition-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .meta-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .competition-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .badge-custom {
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-bidang {
            background: var(--secondary-gradient);
            color: white;
        }
        
        .badge-status {
            background: rgba(25, 135, 84, 0.8);
            color: white;
        }
        
        .badge-status.unavailable {
            background: rgba(220, 53, 69, 0.8);
        }
        
        .competition-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .btn-action {
            flex: 1;
            min-width: 100px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-detail {
            background: rgba(13, 110, 253, 0.8);
            color: white;
        }
        
        .btn-detail:hover {
            background: rgba(13, 110, 253, 1);
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-edit {
            background: rgba(255, 193, 7, 0.8);
            color: white;
        }
        
        .btn-edit:hover {
            background: rgba(255, 193, 7, 1);
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-delete {
            background: rgba(220, 53, 69, 0.8);
            color: white;
        }
        
        .btn-delete:hover {
            background: rgba(220, 53, 69, 1);
            transform: translateY(-2px);
            color: white;
        }
        
        /* Pagination */
        .pagination-glass {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            padding: 1rem;
        }
        
        .page-link {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            margin: 0 0.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .page-link:hover {
            background: var(--secondary-gradient);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
        }
        
        .page-item.active .page-link {
            background: var(--secondary-gradient);
            border-color: transparent;
            color: white;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: white;
        }
        
        .empty-icon {
            font-size: 5rem;
            opacity: 0.5;
            margin-bottom: 1rem;
        }
        
        .empty-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .empty-description {
            font-size: 1.1rem;
            opacity: 0.8;
            margin-bottom: 2rem;
        }
        
        /* Floating Action Button */
        .fab {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--secondary-gradient);
            border: none;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .fab:hover {
            transform: scale(1.1) rotate(90deg);
            box-shadow: 0 12px 35px rgba(0,0,0,0.4);
            color: white;
        }
        
        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .floating {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .pulse {
            animation: pulse 2s ease-in-out infinite;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .stats-number {
                font-size: 2rem;
            }
            
            .competition-actions {
                flex-direction: column;
            }
            
            .btn-action {
                min-width: auto;
            }
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--secondary-gradient);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, #f5576c 0%, #f093fb 100%);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-glass fixed-top">
        <div class="container">
            <a class="navbar-brand floating" href="index.php">
                <i class="bi bi-trophy-fill"></i> InfoLomba
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-house"></i> Beranda
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="peserta/">
                            <i class="bi bi-people"></i> Peserta
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-tags"></i> Bidang Lomba
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?">Semua Bidang</a></li>
                            <?php 
                            mysqli_data_seek($bidang_options, 0);
                            while ($bidang = mysqli_fetch_assoc($bidang_options)): 
                            ?>
                                <li><a class="dropdown-item" href="?bidang=<?= $bidang['id_bidang'] ?>">
                                    <?= $bidang['nama_bidang'] ?>
                                </a></li>
                            <?php endwhile; ?>
                        </ul>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="admin/">
                            <i class="bi bi-gear"></i> Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" style="margin-top: 76px;">
        <div class="container">
            <div class="hero-content" data-aos="fade-up">
                <h1 class="hero-title pulse">
                    <i class="bi bi-trophy-fill"></i> InfoLomba
                </h1>
                <p class="hero-subtitle">
                    Platform informasi lomba terlengkap untuk mengembangkan potensi dan meraih prestasi
                </p>
                
                <!-- Search Section -->
                <div class="search-section" data-aos="fade-up" data-aos-delay="200">
                    <form method="GET" action="index.php">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-0">
                                        <i class="bi bi-search text-white"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control search-input border-start-0" 
                                           name="search" 
                                           placeholder="Cari nama lomba..." 
                                           value="<?= htmlspecialchars($search) ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select filter-select" name="bidang">
                                    <option value="">Semua Bidang</option>
                                    <?php 
                                    mysqli_data_seek($bidang_options, 0);
                                    while ($bidang = mysqli_fetch_assoc($bidang_options)): 
                                    ?>
                                        <option value="<?= $bidang['id_bidang'] ?>" <?= $bidang_filter == $bidang['id_bidang'] ? 'selected' : '' ?>>
                                            <?= $bidang['nama_bidang'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-search w-100">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <!-- Statistics -->
        <div class="row mb-5" data-aos="fade-up">
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['total_lomba'] ?></div>
                    <div class="stats-label">Total Lomba</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['lomba_aktif'] ?></div>
                    <div class="stats-label">Lomba Aktif</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['lomba_tersedia'] ?></div>
                    <div class="stats-label">Lomba Tersedia</div>
                </div>
            </div>
        </div>

        <!-- Active Filters Display -->
        <?php if ($search || $bidang_filter): ?>
            <div class="filter-section" data-aos="fade-up">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="text-white me-2">
                        <i class="bi bi-funnel"></i> Filter aktif:
                    </span>
                    
                    <?php if ($search): ?>
                        <span class="badge bg-info">
                            <i class="bi bi-search"></i> "<?= htmlspecialchars($search) ?>"
                            <a href="?" class="text-white ms-1"><i class="bi bi-x"></i></a>
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($bidang_filter): ?>
                        <?php
                        $bidang_name = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_bidang FROM bidang_lomba WHERE id_bidang = $bidang_filter"))['nama_bidang'];
                        ?>
                        <span class="badge bg-primary">
                            <i class="bi bi-tags"></i> <?= $bidang_name ?>
                            <a href="?<?= http_build_query(array_filter(['search' => $search])) ?>" class="text-white ms-1"><i class="bi bi-x"></i></a>
                        </span>
                    <?php endif; ?>
                    
                    <a href="index.php" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-arrow-clockwise"></i> Reset Semua
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Competition Cards -->
        <?php if (!empty($lombas)): ?>
            <div class="row">
                <?php foreach ($lombas as $index => $lomba): ?>
                    <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="competition-card">
                            <div class="competition-image">
                                <?php if ($lomba['gambar'] && file_exists($lomba['gambar'])): ?>
                                    <img src="<?= htmlspecialchars($lomba['gambar']) ?>" alt="<?= htmlspecialchars($lomba['nama_lomba']) ?>">
                                <?php else: ?>
                                    <div class="text-center text-white">
                                        <i class="bi bi-trophy-fill" style="font-size: 4rem; opacity: 0.7;"></i>
                                        <div class="mt-2"><?= htmlspecialchars($lomba['nama_lomba']) ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="competition-content">
                                <h5 class="competition-title"><?= htmlspecialchars($lomba['nama_lomba']) ?></h5>
                                
                                <p class="competition-description">
                                    <?= htmlspecialchars($lomba['deskripsi']) ?>
                                </p>
                                
                                <div class="competition-meta">
                                    <div class="meta-item">
                                        <i class="bi bi-calendar-event"></i>
                                        <?= formatTanggalIndonesia($lomba['tgl_lomba']) ?>
                                    </div>
                                    <div class="meta-item">
                                        <i class="bi bi-building"></i>
                                        <?= htmlspecialchars($lomba['penyelenggara_lomba']) ?>
                                    </div>
                                    <div class="meta-item">
                                        <i class="bi bi-geo-alt"></i>
                                        <?= htmlspecialchars($lomba['lokasi']) ?>
                                    </div>
                                </div>
                                
                                <div class="competition-badges">
                                    <?php if ($lomba['nama_bidang']): ?>
                                        <span class="badge badge-custom badge-bidang">
                                            <i class="bi bi-tags"></i> <?= $lomba['nama_bidang'] ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <span class="badge badge-custom badge-status <?= $lomba['status'] == 'unavailable' ? 'unavailable' : '' ?>">
                                        <i class="bi bi-<?= $lomba['status'] == 'available' ? 'check-circle' : 'x-circle' ?>"></i> 
                                        <?= $lomba['status'] == 'available' ? 'Tersedia' : 'Tidak Tersedia' ?>
                                    </span>
                                </div>
                                
                                <div class="competition-actions">
                                    <a href="detail.php?id=<?= $lomba['id_lomba'] ?>" class="btn btn-action btn-detail">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    
                                    <a href="admin/edit.php?id=<?= $lomba['id_lomba'] ?>" class="btn btn-action btn-edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    
                                    <button onclick="confirmDelete(<?= $lomba['id_lomba'] ?>, '<?= htmlspecialchars($lomba['nama_lomba']) ?>')" 
                                            class="btn btn-action btn-delete">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination-glass mt-5" data-aos="fade-up">
                    <nav>
                        <ul class="pagination justify-content-center mb-0">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>&<?= http_build_query(['search' => $search, 'bidang' => $bidang_filter]) ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query(['search' => $search, 'bidang' => $bidang_filter]) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>&<?= http_build_query(['search' => $search, 'bidang' => $bidang_filter]) ?>">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state" data-aos="fade-up">
                <div class="empty-icon">
                    <i class="bi bi-search"></i>
                </div>
                <h3 class="empty-title">Tidak Ada Lomba Ditemukan</h3>
                <p class="empty-description">
                    <?php if ($search): ?>
                        Tidak ada lomba dengan kata kunci "<?= htmlspecialchars($search) ?>"
                    <?php else: ?>
                        Belum ada lomba yang tersedia saat ini
                    <?php endif; ?>
                </p>
                <a href="index.php" class="btn btn-search">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Floating Action Button -->
    <a href="admin/tambah.php" class="fab" title="Tambah Lomba">
        <i class="bi bi-plus"></i>
    </a>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
        
        // Delete confirmation
        function confirmDelete(id, nama) {
            if (confirm(`Apakah Anda yakin ingin menghapus lomba "${nama}"?`)) {
                window.location.href = `admin/hapus.php?id=${id}`;
            }
        }
        
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-glass');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.15)';
                navbar.style.backdropFilter = 'blur(15px)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.1)';
                navbar.style.backdropFilter = 'blur(10px)';
            }
        });
    </script>
</body>
</html>
