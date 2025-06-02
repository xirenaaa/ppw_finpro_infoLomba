-- Updated database structure with new features
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: `ppwfinpro`

-- Table structure for table `users`
CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `nama_lengkap` varchar(100) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: admin123)
INSERT INTO `users` (`username`, `email`, `password`, `role`, `nama_lengkap`) VALUES
('admin', 'admin@lomba.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator'),
('user1', 'user@lomba.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'User Demo');

-- Updated kategori table with new categories
DROP TABLE IF EXISTS `kategori`;
CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kategori` (`nama_kategori`, `deskripsi`) VALUES
('SD', 'Kategori untuk siswa Sekolah Dasar'),
('SMP', 'Kategori untuk siswa Sekolah Menengah Pertama'),
('SMA', 'Kategori untuk siswa Sekolah Menengah Atas'),
('Kuliah', 'Kategori untuk mahasiswa perguruan tinggi'),
('Umum', 'Kategori untuk peserta umum');

-- Updated bidang_lomba table
DROP TABLE IF EXISTS `bidang_lomba`;
CREATE TABLE `bidang_lomba` (
  `id_bidang` int(11) NOT NULL,
  `nama_bidang` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `bidang_lomba` (`nama_bidang`, `deskripsi`, `icon`) VALUES
('Saintek', 'Lomba di bidang Sains dan Teknologi', 'bi-flask'),
('IT', 'Lomba di bidang Teknologi Informasi dan Programming', 'bi-laptop'),
('Seni dan Budaya', 'Lomba seni, musik, tari, dan budaya', 'bi-palette'),
('Olahraga', 'Lomba dan kompetisi olahraga', 'bi-trophy'),
('Akademik', 'Lomba akademik dan ilmiah', 'bi-book'),
('Kewirausahaan', 'Lomba bisnis dan kewirausahaan', 'bi-briefcase');

-- Location types table
CREATE TABLE `lokasi_types` (
  `id_lokasi` int(11) NOT NULL,
  `nama_lokasi` varchar(50) NOT NULL,
  `deskripsi` text,
  `icon` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `lokasi_types` (`nama_lokasi`, `deskripsi`, `icon`) VALUES
('Online', 'Lomba dilaksanakan secara daring/online', 'bi-wifi'),
('Offline', 'Lomba dilaksanakan secara tatap muka/offline', 'bi-geo-alt');

-- Update lomba table structure
ALTER TABLE `lomba` 
ADD COLUMN `id_bidang` int(11) DEFAULT NULL,
ADD COLUMN `id_kategori` int(11) DEFAULT NULL,
ADD COLUMN `id_lokasi_type` int(11) DEFAULT NULL,
ADD COLUMN `deadline_daftar` date DEFAULT NULL,
ADD COLUMN `max_peserta` int(11) DEFAULT NULL,
ADD COLUMN `biaya_daftar` decimal(10,2) DEFAULT 0,
ADD COLUMN `created_at` timestamp DEFAULT CURRENT_TIMESTAMP;

-- Add indexes
ALTER TABLE `users` ADD PRIMARY KEY (`id_user`), ADD UNIQUE KEY `username` (`username`), ADD UNIQUE KEY `email` (`email`);
ALTER TABLE `kategori` ADD PRIMARY KEY (`id_kategori`);
ALTER TABLE `bidang_lomba` ADD PRIMARY KEY (`id_bidang`);
ALTER TABLE `lokasi_types` ADD PRIMARY KEY (`id_lokasi`);

-- Add foreign keys
ALTER TABLE `lomba` 
ADD CONSTRAINT `fk_lomba_bidang` FOREIGN KEY (`id_bidang`) REFERENCES `bidang_lomba` (`id_bidang`),
ADD CONSTRAINT `fk_lomba_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`),
ADD CONSTRAINT `fk_lomba_lokasi` FOREIGN KEY (`id_lokasi_type`) REFERENCES `lokasi_types` (`id_lokasi`);

-- Auto increment
ALTER TABLE `users` MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `kategori` MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `bidang_lomba` MODIFY `id_bidang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `lokasi_types` MODIFY `id_lokasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

COMMIT;
-- Update lomba table with new fields