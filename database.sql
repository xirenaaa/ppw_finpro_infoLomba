-- Tabel bidang_lomba
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

-- Tabel kategori
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

-- Tabel hadiah
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

-- Tabel jadwal_lomba
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

-- Tabel syarat
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

-- Update tabel lomba untuk menambahkan kolom yang diperlukan
ALTER TABLE `lomba` 
ADD COLUMN IF NOT EXISTS `id_bidang` int(11),
ADD COLUMN IF NOT EXISTS `id_kategori` int(11),
ADD COLUMN IF NOT EXISTS `id_hadiah` int(11),
ADD COLUMN IF NOT EXISTS `penyelenggara` varchar(255),
ADD COLUMN IF NOT EXISTS `deskripsi` text,
ADD COLUMN IF NOT EXISTS `status` enum('available','unavailable') DEFAULT 'available',
ADD COLUMN IF NOT EXISTS `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Tambahkan foreign key constraints
ALTER TABLE `lomba`
ADD CONSTRAINT `fk_lomba_bidang` FOREIGN KEY (`id_bidang`) REFERENCES `bidang_lomba` (`id_bidang`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_lomba_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_lomba_hadiah` FOREIGN KEY (`id_hadiah`) REFERENCES `hadiah` (`id_hadiah`) ON DELETE SET NULL;

ALTER TABLE `jadwal_lomba`
ADD CONSTRAINT `fk_jadwal_lomba` FOREIGN KEY (`id_lomba`) REFERENCES `lomba` (`id_lomba`) ON DELETE CASCADE;

ALTER TABLE `syarat`
ADD CONSTRAINT `fk_syarat_lomba` FOREIGN KEY (`id_lomba`) REFERENCES `lomba` (`id_lomba`) ON DELETE CASCADE;