-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Bulan Mei 2025 pada 18.05
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ppwfinpro`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bidang_lomba`
--

CREATE TABLE `bidang_lomba` (
  `id_bidang` int(11) NOT NULL,
  `nama_bidang` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bidang_lomba`
--

INSERT INTO `bidang_lomba` (`id_bidang`, `nama_bidang`, `deskripsi`, `created_at`) VALUES
(1, 'Teknologi Informasi', 'Lomba di bidang IT, programming, dan teknologi', '2025-05-28 13:45:02'),
(2, 'Seni dan Budaya', 'Lomba seni, musik, tari, dan budaya', '2025-05-28 13:45:02'),
(3, 'Olahraga', 'Lomba dan kompetisi olahraga', '2025-05-28 13:45:02'),
(4, 'Akademik', 'Lomba akademik dan ilmiah', '2025-05-28 13:45:02'),
(5, 'Kewirausahaan', 'Lomba bisnis dan kewirausahaan', '2025-05-28 13:45:02'),
(6, 'Teknologi Informasi', 'Lomba di bidang IT, programming, dan teknologi', '2025-05-28 13:45:39'),
(7, 'Seni dan Budaya', 'Lomba seni, musik, tari, dan budaya', '2025-05-28 13:45:39'),
(8, 'Olahraga', 'Lomba dan kompetisi olahraga', '2025-05-28 13:45:39'),
(9, 'Akademik', 'Lomba akademik dan ilmiah', '2025-05-28 13:45:39'),
(10, 'Kewirausahaan', 'Lomba bisnis dan kewirausahaan', '2025-05-28 13:45:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hadiah`
--

CREATE TABLE `hadiah` (
  `id_hadiah` int(11) NOT NULL,
  `id_lomba` int(11) NOT NULL,
  `posisi` varchar(50) NOT NULL,
  `nominal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal lomba`
--

CREATE TABLE `jadwal lomba` (
  `id_jadwal` int(11) NOT NULL,
  `id_lomba` int(11) NOT NULL,
  `tanggal kegiatan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `lomba`
--

CREATE TABLE `lomba` (
  `id_lomba` int(11) NOT NULL,
  `nama_lomba` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `tgl_lomba` date NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `link_daftar` varchar(500) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `penyelenggara_lomba` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('available','unavailable') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `lomba`
--

INSERT INTO `lomba` (`id_lomba`, `nama_lomba`, `deskripsi`, `tgl_lomba`, `lokasi`, `link_daftar`, `gambar`, `penyelenggara_lomba`, `updated_at`, `status`) VALUES
(1, 'FindIT', 'Lomba IT', '2025-05-07', 'FT UGM', 'https', 'pengumuman seleksi berkas.png', 'DTETI UGM', '2025-05-28 12:52:00', 'available'),
(4, 'GELATIK', 'Dalam rangka mendorong pengembangan talenta mahasiswa di bidang sains data, inovasi digital, dan teknologi informasi, Balai Pengembangan Talenta Indonesia menyelenggarakan Pagelaran Sains Data, Inovasi Digital, dan TIK (GELATIK) tahun 2025. Kegiatan ini mengintegrasikan tiga kompetisi utama, yaitu Satria Data, LIDM, dan GEMASTIK, sebagai upaya penguatan ekosistem teknologi dan inovasi di perguruan tinggi.', '2025-07-23', 'Universitas Negeri Semarang', 'https://himasadaukh.my.canva.site/', 'uploads/1748533459_images (2).png', 'Kemdikbudristek', '2025-05-29 15:44:19', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `peserta`
--

CREATE TABLE `peserta` (
  `id_peserta` int(11) NOT NULL,
  `id_lomba` int(11) NOT NULL,
  `nama_peserta` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `institusi` varchar(255) NOT NULL,
  `tanggal_daftar` date NOT NULL,
  `status_pendaftaran` enum('Menunggu','Diterima','Ditolak') DEFAULT 'Menunggu',
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `syarat`
--

CREATE TABLE `syarat` (
  `id_syarat` int(11) NOT NULL,
  `id_lomba` int(11) NOT NULL,
  `persyaratan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bidang_lomba`
--
ALTER TABLE `bidang_lomba`
  ADD PRIMARY KEY (`id_bidang`);

--
-- Indeks untuk tabel `hadiah`
--
ALTER TABLE `hadiah`
  ADD PRIMARY KEY (`id_hadiah`);

--
-- Indeks untuk tabel `jadwal lomba`
--
ALTER TABLE `jadwal lomba`
  ADD PRIMARY KEY (`id_jadwal`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `lomba`
--
ALTER TABLE `lomba`
  ADD PRIMARY KEY (`id_lomba`);

--
-- Indeks untuk tabel `peserta`
--
ALTER TABLE `peserta`
  ADD PRIMARY KEY (`id_peserta`);

--
-- Indeks untuk tabel `syarat`
--
ALTER TABLE `syarat`
  ADD PRIMARY KEY (`id_syarat`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bidang_lomba`
--
ALTER TABLE `bidang_lomba`
  MODIFY `id_bidang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `hadiah`
--
ALTER TABLE `hadiah`
  MODIFY `id_hadiah` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jadwal lomba`
--
ALTER TABLE `jadwal lomba`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `lomba`
--
ALTER TABLE `lomba`
  MODIFY `id_lomba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `peserta`
--
ALTER TABLE `peserta`
  MODIFY `id_peserta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `syarat`
--
ALTER TABLE `syarat`
  MODIFY `id_syarat` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
