# Aplikasi Manajemen Lomba

Aplikasi web untuk mengelola lomba dan peserta menggunakan PHP dan Bootstrap.

## Fitur
- CRUD Lomba (Create, Read, Update, Delete)
- Manajemen Peserta
- Status Management
- Upload Gambar
- Dashboard Statistik
- Responsive Design

## Teknologi
- PHP 7.4+
- MySQL/MariaDB
- Bootstrap 5
- Bootstrap Icons

## Instalasi Lokal

### Prasyarat
- XAMPP/WAMP/LAMP
- PHP 7.4 atau lebih baru
- MySQL/MariaDB

### Langkah Instalasi
1. Clone repository ini
2. Letakkan di folder `htdocs` (untuk XAMPP)
3. Buat database `ppwfinpro` di PHPMyAdmin
4. Import struktur database dari `database.sql`
5. Sesuaikan konfigurasi database di `config.php`
6. Akses aplikasi di `http://localhost/ppw_finpro_infoLomba`

## Struktur Database

### Tabel Utama
- `lomba` - Data lomba
- `peserta` - Data peserta lomba
- `bidang_lomba` - Kategori bidang lomba
- `kategori` - Kategori peserta
- `hadiah` - Data hadiah lomba
- `jadwal_lomba` - Jadwal lomba
- `syarat` - Syarat lomba

## Deployment

### Hosting PHP Tradisional
1. Upload semua file ke hosting
2. Buat database MySQL
3. Import struktur database
4. Sesuaikan `config.php`

### Platform Modern (Vercel, Netlify, dll)
Aplikasi ini dirancang untuk hosting PHP tradisional. Untuk platform modern, pertimbangkan:
- Railway (mendukung PHP)
- Heroku (dengan buildpack PHP)
- DigitalOcean App Platform
- AWS Elastic Beanstalk

## Konfigurasi

Edit file `config.php`:
\`\`\`php
$host = "localhost";        // Host database
$username = "root";         // Username database  
$password = "";             // Password database
$database = "ppwfinpro";    // Nama database
\`\`\`

## Kontribusi
1. Fork repository
2. Buat branch fitur (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -am 'Tambah fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

## Lisensi
MIT License
