<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Get competition details
$sql = "SELECT l.*, b.nama_bidang 
        FROM lomba l 
        LEFT JOIN bidang_lomba b ON l.id_bidang = b.id_bidang 
        WHERE l.id_lomba = $id";

$result = mysqli_query($conn, $sql);
$lomba = mysqli_fetch_assoc($result);

if (!$lomba) {
    header("Location: index.php");
    exit();
}

// Get related competitions
$related_sql = "SELECT l.*, b.nama_bidang FROM lomba l 
                LEFT JOIN bidang_lomba b ON l.id_bidang = b.id_bidang 
                WHERE l.id_lomba != $id AND l.id_bidang = {$lomba['id_bidang']} 
                ORDER BY RAND() LIMIT 3";
$related_result = mysqli_query($conn, $related_sql);
$related_lombas = mysqli_fetch_all($related_result, MYSQLI_ASSOC);

// Get requirements
$syarat_sql = "SELECT * FROM syarat WHERE id_lomba = $id";
$syarat_result = mysqli_query($conn, $syarat_sql);
$syarat_list = mysqli_fetch_all($syarat_result, MYSQLI_ASSOC);

// Get prizes
$hadiah_sql = "SELECT * FROM hadiah WHERE id_lomba = $id ORDER BY posisi";
$hadiah_result = mysqli_query($conn, $hadiah_sql);
$hadiah_list = mysqli_fetch_all($hadiah_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($lomba['nama_lomba']) ?> - InfoLomba</title>
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
        
        body {
            background: var(--primary-gradient);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        .detail-container {
            margin-top: 100px;
            margin-bottom: 50px;
        }
        
        .detail-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            overflow: hidden;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        .detail-image {
            height: 400px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .detail-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .detail-content {
            padding: 2rem;
            color: white;
        }
        
        .detail-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .detail-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .meta-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            border-radius: 15px;
            padding: 1rem;
            flex: 1;
            min-width: 200px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .meta-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
        }
        
        .meta-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #f093fb;
        }
        
        .meta-label {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 0.3rem;
        }
        
        .meta-value {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .detail-description {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            line-height: 1.8;
        }
        
        .detail-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .section-title {
            color: #f093fb;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .requirement-item, .prize-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 0.8rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn-primary-custom {
            background: var(--secondary-gradient);
            border: none;
            border-radius: 15px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            color: white;
        }
        
        .btn-outline-custom {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 10px 28px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-outline-custom:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-3px);
            color: white;
        }
        
        .related-section {
            margin-top: 3rem;
        }
        
        .related-title {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .related-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .related-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        
        .related-image {
            height: 150px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .related-content {
            padding: 1rem;
            color: white;
        }
        
        .related-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .back-button {
            position: fixed;
            top: 100px;
            left: 20px;
            z-index: 1000;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
            color: white;
        }
        
        @media (max-width: 768px) {
            .detail-title {
                font-size: 2rem;
            }
            
            .detail-meta {
                flex-direction: column;
            }
            
            .meta-card {
                min-width: auto;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-primary-custom,
            .btn-outline-custom {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Back Button -->
    <a href="index.php" class="back-button" title="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-glass fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php" style="color: white;">
                <i class="bi bi-trophy-fill"></i> InfoLomba
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php" style="color: rgba(255,255,255,0.9);">
                    <i class="bi bi-house"></i> Beranda
                </a>
            </div>
        </div>
    </nav>

    <div class="container detail-container">
        <div class="detail-card" data-aos="fade-up">
            <!-- Competition Image -->
            <div class="detail-image">
                <?php if ($lomba['gambar'] && file_exists($lomba['gambar'])): ?>
                    <img src="<?= htmlspecialchars($lomba['gambar']) ?>" alt="<?= htmlspecialchars($lomba['nama_lomba']) ?>">
                <?php else: ?>
                    <div class="text-center text-white">
                        <i class="bi bi-trophy-fill" style="font-size: 6rem; opacity: 0.7;"></i>
                        <div class="mt-3 fs-4"><?= htmlspecialchars($lomba['nama_lomba']) ?></div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Competition Content -->
            <div class="detail-content">
                <h1 class="detail-title"><?= htmlspecialchars($lomba['nama_lomba']) ?></h1>
                
                <!-- Meta Information -->
                <div class="detail-meta" data-aos="fade-up" data-aos-delay="200">
                    <div class="meta-card">
                        <div class="meta-icon">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="meta-label">Tanggal Lomba</div>
                        <div class="meta-value"><?= formatTanggalIndonesia($lomba['tgl_lomba']) ?></div>
                    </div>
                    
                    <div class="meta-card">
                        <div class="meta-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="meta-label">Penyelenggara</div>
                        <div class="meta-value"><?= htmlspecialchars($lomba['penyelenggara_lomba']) ?></div>
                    </div>
                    
                    <div class="meta-card">
                        <div class="meta-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="meta-label">Lokasi</div>
                        <div class="meta-value"><?= htmlspecialchars($lomba['lokasi']) ?></div>
                    </div>
                    
                    <?php if ($lomba['nama_bidang']): ?>
                    <div class="meta-card">
                        <div class="meta-icon">
                            <i class="bi bi-tags"></i>
                        </div>
                        <div class="meta-label">Bidang Lomba</div>
                        <div class="meta-value"><?= htmlspecialchars($lomba['nama_bidang']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Description -->
                <?php if ($lomba['deskripsi']): ?>
                <div class="detail-description" data-aos="fade-up" data-aos-delay="400">
                    <h4 style="color: #f093fb; margin-bottom: 1rem;">
                        <i class="bi bi-file-text"></i> Deskripsi Lomba
                    </h4>
                    <p><?= nl2br(htmlspecialchars($lomba['deskripsi'])) ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Requirements -->
                <?php if (!empty($syarat_list)): ?>
                <div class="detail-section" data-aos="fade-up" data-aos-delay="500">
                    <h4 class="section-title">
                        <i class="bi bi-list-check"></i> Persyaratan
                    </h4>
                    <?php foreach ($syarat_list as $syarat): ?>
                        <div class="requirement-item">
                            <i class="bi bi-check-circle text-success"></i>
                            <?= htmlspecialchars($syarat['persyaratan']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Prizes -->
                <?php if (!empty($hadiah_list)): ?>
                <div class="detail-section" data-aos="fade-up" data-aos-delay="600">
                    <h4 class="section-title">
                        <i class="bi bi-trophy"></i> Hadiah
                    </h4>
                    <?php foreach ($hadiah_list as $hadiah): ?>
                        <div class="prize-item">
                            <i class="bi bi-award text-warning"></i>
                            <strong><?= htmlspecialchars($hadiah['posisi']) ?>:</strong>
                            Rp <?= number_format($hadiah['nominal'], 0, ',', '.') ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div class="action-buttons" data-aos="fade-up" data-aos-delay="700">
                    <?php if ($lomba['link_daftar']): ?>
                        <a href="<?= htmlspecialchars($lomba['link_daftar']) ?>" target="_blank" class="btn-primary-custom">
                            <i class="bi bi-box-arrow-up-right"></i> Daftar Sekarang
                        </a>
                    <?php endif; ?>
                    
                    <a href="index.php" class="btn-outline-custom">
                        <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                    </a>
                    
                    <a href="admin/edit.php?id=<?= $lomba['id_lomba'] ?>" class="btn-outline-custom">
                        <i class="bi bi-pencil"></i> Edit Lomba
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Related Competitions -->
        <?php if (!empty($related_lombas)): ?>
        <div class="related-section" data-aos="fade-up" data-aos-delay="800">
            <h3 class="related-title">Lomba Serupa</h3>
            <div class="row">
                <?php foreach ($related_lombas as $related): ?>
                <div class="col-md-4 mb-3">
                    <div class="related-card">
                        <div class="related-image">
                            <?php if ($related['gambar'] && file_exists($related['gambar'])): ?>
                                <img src="<?= htmlspecialchars($related['gambar']) ?>" alt="<?= htmlspecialchars($related['nama_lomba']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <i class="bi bi-trophy-fill text-white" style="font-size: 3rem; opacity: 0.7;"></i>
                            <?php endif; ?>
                        </div>
                        <div class="related-content">
                            <h6 class="related-card-title"><?= htmlspecialchars($related['nama_lomba']) ?></h6>
                            <p class="mb-2" style="font-size: 0.9rem; opacity: 0.8;">
                                <i class="bi bi-calendar-event"></i> <?= formatTanggalIndonesia($related['tgl_lomba']) ?>
                            </p>
                            <a href="detail.php?id=<?= $related['id_lomba'] ?>" class="btn btn-sm btn-outline-light">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
        
        // Parallax effect
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const image = document.querySelector('.detail-image');
            if (image) {
                image.style.transform = `translateY(${scrolled * 0.3}px)`;
            }
        });
    </script>
</body>
</html>
