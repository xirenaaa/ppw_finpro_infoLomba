-- Database structure for ppwfinpro
-- Run this in PHPMyAdmin to create the required tables

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: `ppwfinpro`

-- --------------------------------------------------------

-- Table structure for table `bidang_lomba`
CREATE TABLE `bidang_lomba` (
  `id_bidang` int(11) NOT NULL,
  `nama_bidang` varchar(255) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data for table `bidang_lomba`
INSERT INTO `bidang_lomba` (`id_bidang`, `nama_bidang`, `deskripsi`, `created_at`) VALUES
(1, 'Teknologi Informasi', 'Lomba di bidang IT, programming, dan teknologi', '2024-01-01 00:00:00'),
(2, 'Seni dan Budaya', 'Lomba seni, musik, tari, dan budaya', '2024-01-01 00:00:00'),
(3, 'Olahraga', 'Lomba dan kompetisi olahraga', '2024-01-01 00:00:00'),
(4, 'Akademik', 'Lomba akademik dan ilmiah', '2024-01-01 00:00:00'),
(5, 'Kewirausahaan', 'Lomba bisnis dan kewirausahaan', '2024-01-01 00:00:00');

-- --------------------------------------------------------

-- Table structure for table `kategori`
CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data for table `kategori`
INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `deskripsi`, `created_at`) VALUES
(1, 'Mahasiswa', 'Kategori untuk mahasiswa', '2024-01-01 00:00:00'),
(2, 'Pelajar SMA', 'Kategori untuk pelajar SMA', '2024-01-01 00:00:00'),
(3, 'Umum', 'Kategori untuk umum', '2024-01-01 00:00:00'),
(4, 'Profesional', 'Kategori untuk profesional', '2024-01-01 00:00:00');

-- --------------------------------------------------------

-- Table structure for table `hadiah`
CREATE TABLE `hadiah` (
  `id_hadiah` int(11) NOT NULL,
  `nama_hadiah` varchar(255) NOT NULL,
  `nilai_hadiah` decimal(15,2) DEFAULT NULL,
  `deskripsi` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data for table `hadiah`
INSERT INTO `hadiah` (`id_hadiah`, `nama_hadiah`, `nilai_hadiah`, `deskripsi`, `created_at`) VALUES
(1, 'Juara 1', '5000000.00', 'Hadiah untuk juara pertama', '2024-01-01 00:00:00'),
(2, 'Juara 2', '3000000.00', 'Hadiah untuk juara kedua', '2024-01-01 00:00:00'),
(3, 'Juara 3', '2000000.00', 'Hadiah untuk juara ketiga', '2024-01-01 00:00:00'),
(4, 'Juara Harapan', '1000000.00', 'Hadiah untuk juara harapan', '2024-01-01 00:00:00'),
(5, 'Sertifikat', '0.00', 'Sertifikat penghargaan', '2024-01-01 00:00:00');

-- --------------------------------------------------------

-- Table structure for table `lomba`
CREATE TABLE `lomba` (
  `id_lomba` int(11) NOT NULL,
  `nama_lomba` varchar(255) NOT NULL,
  `deskripsi` text,
  `tgl_lomba` date DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `link_daftar` varchar(500) DEFAULT NULL,
  `gambar` varchar(500) DEFAULT NULL,
  `penyelenggara_lomba` varchar(255) DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `id_bidang` int(11) DEFAULT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `id_hadiah` int(11) DEFAULT NULL,
  `penyelenggara` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `peserta`
CREATE TABLE `peserta` (
  `id_peserta` int(11) NOT NULL,
  `id_lomba` int(11) NOT NULL,
  `nama_peserta` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `institusi` varchar(255) NOT NULL,
  `tanggal_daftar` date NOT NULL,
  `status_pendaftaran` enum('Menunggu','Diterima','Ditolak') DEFAULT 'Menunggu',
  `catatan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `jadwal_lomba`
CREATE TABLE `jadwal_lomba` (
  `id_jadwal` int(11) NOT NULL,
  `id_lomba` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `waktu_mulai` time DEFAULT NULL,
  `waktu_selesai` time DEFAULT NULL,
  `tempat` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `syarat`
CREATE TABLE `syarat` (
  `id_syarat` int(11) NOT NULL,
  `id_lomba` int(11) NOT NULL,
  `syarat_umum` text,
  `syarat_khusus` text,
  `dokumen_required` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Indexes for dumped tables

-- Indexes for table `bidang_lomba`
ALTER TABLE `bidang_lomba`
  ADD PRIMARY KEY (`id_bidang`);

-- Indexes for table `kategori`
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

-- Indexes for table `hadiah`
ALTER TABLE `hadiah`
  ADD PRIMARY KEY (`id_hadiah`);

-- Indexes for table `lomba`
ALTER TABLE `lomba`
  ADD PRIMARY KEY (`id_lomba`),
  ADD KEY `id_bidang` (`id_bidang`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_hadiah` (`id_hadiah`);

-- Indexes for table `peserta`
ALTER TABLE `peserta`
  ADD PRIMARY KEY (`id_peserta`),
  ADD KEY `id_lomba` (`id_lomba`);

-- Indexes for table `jadwal_lomba`
ALTER TABLE `jadwal_lomba`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_lomba` (`id_lomba`);

-- Indexes for table `syarat`
ALTER TABLE `syarat`
  ADD PRIMARY KEY (`id_syarat`),
  ADD KEY `id_lomba` (`id_lomba`);

-- AUTO_INCREMENT for dumped tables

-- AUTO_INCREMENT for table `bidang_lomba`
ALTER TABLE `bidang_lomba`
  MODIFY `id_bidang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

-- AUTO_INCREMENT for table `kategori`
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- AUTO_INCREMENT for table `hadiah`
ALTER TABLE `hadiah`
  MODIFY `id_hadiah` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

-- AUTO_INCREMENT for table `lomba`
ALTER TABLE `lomba`
  MODIFY `id_lomba` int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT for table `peserta`
ALTER TABLE `peserta`
  MODIFY `id_peserta` int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT for table `jadwal_lomba`
ALTER TABLE `jadwal_lomba`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT for table `syarat`
ALTER TABLE `syarat`
  MODIFY `id_syarat` int(11) NOT NULL AUTO_INCREMENT;

-- Constraints for dumped tables

-- Constraints for table `lomba`
ALTER TABLE `lomba`
  ADD CONSTRAINT `lomba_ibfk_1` FOREIGN KEY (`id_bidang`) REFERENCES `bidang_lomba` (`id_bidang`) ON DELETE SET NULL,
  ADD CONSTRAINT `lomba_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL,
  ADD CONSTRAINT `lomba_ibfk_3` FOREIGN KEY (`id_hadiah`) REFERENCES `hadiah` (`id_hadiah`) ON DELETE SET NULL;

-- Constraints for table `peserta`
ALTER TABLE `peserta`
  ADD CONSTRAINT `peserta_ibfk_1` FOREIGN KEY (`id_lomba`) REFERENCES `lomba` (`id_lomba`) ON DELETE CASCADE;

-- Constraints for table `jadwal_lomba`
ALTER TABLE `jadwal_lomba`
  ADD CONSTRAINT `jadwal_lomba_ibfk_1` FOREIGN KEY (`id_lomba`) REFERENCES `lomba` (`id_lomba`) ON DELETE CASCADE;

-- Constraints for table `syarat`
ALTER TABLE `syarat`
  ADD CONSTRAINT `syarat_ibfk_1` FOREIGN KEY (`id_lomba`) REFERENCES `lomba` (`id_lomba`) ON DELETE CASCADE;

COMMIT;
