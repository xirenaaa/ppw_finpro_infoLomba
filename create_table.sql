-- Jalankan script ini di PHPMyAdmin untuk membuat tabel yang hilang
-- Pilih database ppwfinpro, lalu jalankan di tab SQL

-- 1. Buat tabel bidang_lomba
CREATE TABLE IF NOT EXISTS `bidang_lomba` (
  `id_bidang` int(11) NOT NULL AUTO_INCREMENT,
  `nama_bidang` varchar(255) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_bidang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data sample bidang lomba
INSERT INTO `bidang_lomba` (`nama_bidang`, `deskripsi`) VALUES
('Teknologi Informasi', 'Lomba di bidang IT, programming, dan teknologi'),
('Seni dan Budaya', 'Lomba seni, musik, tari, dan budaya'),
('Olahraga', 'Lomba dan kompetisi olahraga'),
('Akademik', 'Lomba akademik dan ilmiah'),
('Kewirausahaan', 'Lomba bisnis dan kewirausahaan');

-- 2. Buat tabel kategori
CREATE TABLE IF NOT EXISTS `kategori` (
  `id_kategori` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(255) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data sample kategori
INSERT INTO `kategori` (`nama_kategori`, `deskripsi`) VALUES
('Mahasiswa', 'Kategori untuk mahasiswa'),
('Pelajar SMA', 'Kategori untuk pelajar SMA'),
('Umum', 'Kategori untuk umum'),
('Profesional', 'Kategori untuk profesional');

-- 3. Buat tabel hadiah
CREATE TABLE IF NOT EXISTS `hadiah` (
  `id_hadiah` int(11) NOT NULL AUTO_INCREMENT,
  `nama_hadiah` varchar(255) NOT NULL,
  `nilai_hadiah` decimal(15,2),
  `deskripsi` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_hadiah`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data sample hadiah
INSERT INTO `hadiah` (`nama_hadiah`, `nilai_hadiah`, `deskripsi`) VALUES
('Juara 1', 5000000.00, 'Hadiah untuk juara pertama'),
('Juara 2', 3000000.00, 'Hadiah untuk juara kedua'),
('Juara 3', 2000000.00, 'Hadiah untuk juara ketiga'),
('Juara Harapan', 1000000.00, 'Hadiah untuk juara harapan'),
('Sertifikat', 0.00, 'Sertifikat penghargaan');

-- 4. Buat tabel jadwal_lomba
CREATE TABLE IF NOT EXISTS `jadwal_lomba` (
  `id_jadwal` int(11) NOT NULL AUTO_INCREMENT,
  `id_lomba` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date,
  `waktu_mulai` time,
  `waktu_selesai` time,
  `tempat` varchar(255),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_jadwal`),
  KEY `id_lomba` (`id_lomba`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Buat tabel syarat
CREATE TABLE IF NOT EXISTS `syarat` (
  `id_syarat` int(11) NOT NULL AUTO_INCREMENT,
  `id_lomba` int(11) NOT NULL,
  `syarat_umum` text,
  `syarat_khusus` text,
  `dokumen_required` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_syarat`),
  KEY `id_lomba` (`id_lomba`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Update tabel lomba yang sudah ada
-- Tambahkan kolom yang diperlukan jika belum ada
ALTER TABLE `lomba` 
ADD COLUMN `id_bidang` int(11) DEFAULT NULL,
ADD COLUMN `id_kategori` int(11) DEFAULT NULL,
ADD COLUMN `id_hadiah` int(11) DEFAULT NULL,
ADD COLUMN `penyelenggara` varchar(255) DEFAULT NULL,
ADD COLUMN `status` enum('available','unavailable') DEFAULT 'available';

-- Cek apakah kolom created_at sudah ada
ALTER TABLE `lomba` 
ADD COLUMN `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
