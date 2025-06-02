<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = escape_string($_POST['username']);
    $email = escape_string($_POST['email']);
    $nama_lengkap = escape_string($_POST['nama_lengkap']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Cek username sudah ada
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' OR email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = 'Username atau email sudah digunakan!';
    } else {
        $sql = "INSERT INTO users (username, email, nama_lengkap, password, role) VALUES ('$username', '$email', '$nama_lengkap', '$password', 'user')";
        if (mysqli_query($conn, $sql)) {
            $success = 'Registrasi berhasil! Silakan login.';
        } else {
            $error = 'Gagal mendaftar: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - InfoLomba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .register-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 2rem;
            width: 100%;
            max-width: 450px;
        }
        
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            backdrop-filter: blur(5px);
        }
        
        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .btn-register {
            background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .text-white {
            color: white !important;
        }
        
        .alert-glass {
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
        }
        
        .alert-danger.alert-glass {
            background: rgba(220, 53, 69, 0.1);
            border-color: rgba(220, 53, 69, 0.3);
        }
        
        .alert-success.alert-glass {
            background: rgba(25, 135, 84, 0.1);
            border-color: rgba(25, 135, 84, 0.3);
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="text-center mb-4">
            <i class="bi bi-person-plus-fill text-white" style="font-size: 3rem;"></i>
            <h2 class="text-white mt-2">Daftar Akun</h2>
            <p class="text-white opacity-75">Buat akun baru untuk mengakses InfoLomba</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-glass alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?= $error ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-glass alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?= $success ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-white">
                    <i class="bi bi-person"></i> Username
                </label>
                <input type="text" class="form-control" name="username" placeholder="Masukkan username" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label text-white">
                    <i class="bi bi-envelope"></i> Email
                </label>
                <input type="email" class="form-control" name="email" placeholder="Masukkan email" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label text-white">
                    <i class="bi bi-person-badge"></i> Nama Lengkap
                </label>
                <input type="text" class="form-control" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label text-white">
                    <i class="bi bi-lock"></i> Password
                </label>
                <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
            </div>
            
            <button type="submit" class="btn btn-register text-white w-100">
                <i class="bi bi-person-plus"></i> Daftar
            </button>
        </form>
        
        <div class="text-center mt-3">
            <a href="login.php" class="text-white text-decoration-none">
                <i class="bi bi-box-arrow-in-right"></i> Sudah punya akun? Masuk
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
