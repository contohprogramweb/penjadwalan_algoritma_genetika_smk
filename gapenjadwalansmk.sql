-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- =============================================
-- 1. Tabel guru (tidak bergantung pada tabel lain)
-- =============================================
CREATE TABLE IF NOT EXISTS `guru` (
  `id_guru` int unsigned NOT NULL AUTO_INCREMENT,
  `nip` varchar(18) NOT NULL COMMENT '18 digit NIP',
  `nuptk` varchar(16) DEFAULT NULL COMMENT '16 digit NUPTK (opsional)',
  `nama_lengkap` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL DEFAULT 'L',
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `pendidikan_terakhir` enum('S1','S2','S3','D3','D4') DEFAULT 'S1',
  `status_kepegawaian` enum('pns','honorer','ttk') DEFAULT 'honorer',
  `jam_maks_minggu` tinyint DEFAULT '24' COMMENT 'Maksimal 24 jam/minggu',
  `jam_min_minggu` tinyint DEFAULT '12' COMMENT 'Minimal 12 jam/minggu',
  `status_aktif` tinyint(1) DEFAULT '1' COMMENT '1=aktif, 0=nonaktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_guru`),
  UNIQUE KEY `nip` (`nip`),
  KEY `idx_nip` (`nip`),
  KEY `idx_nuptk` (`nuptk`),
  KEY `idx_status` (`status_aktif`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

INSERT INTO `guru` (`id_guru`, `nip`, `nuptk`, `nama_lengkap`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `pendidikan_terakhir`, `status_kepegawaian`, `jam_maks_minggu`, `jam_min_minggu`, `status_aktif`, `created_at`, `updated_at`) VALUES
	(1, '198501152010011001', '1234567890123456', 'Ahmad Fauzi, S.Pd', 'L', 'Bandung', '1985-01-15', 'S1', 'pns', 24, 12, 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(2, '198703202011012002', '2345678901234567', 'Siti Nurhaliza, S.Pd', 'P', 'Jakarta', '1987-03-20', 'S1', 'pns', 24, 12, 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(3, '199005102012011003', '3456789012345678', 'Budi Santoso, S.Kom', 'L', 'Surabaya', '1990-05-10', 'S1', 'honorer', 24, 12, 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(4, '198808152013012004', '4567890123456789', 'Dewi Lestari, M.Pd', 'P', 'Yogyakarta', '1988-08-15', 'S2', 'pns', 24, 12, 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(5, '199202202014011005', '5678901234567890', 'Eko Prasetyo, S.T', 'L', 'Semarang', '1992-02-20', 'S1', 'honorer', 24, 12, 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(6, '198906102015012006', '6789012345678901', 'Fitri Handayani, S.Pd', 'P', 'Malang', '1989-06-10', 'S1', 'ttk', 24, 12, 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(7, '199109152016011007', '7890123456789012', 'Gunawan Wibowo, S.Pd', 'L', 'Medan', '1991-09-15', 'S1', 'honorer', 24, 12, 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(8, '199304202017012008', '8901234567890123', 'Hana Pertiwi, S.Kom', 'P', 'Denpasar', '1993-04-20', 'S1', 'ttk', 24, 12, 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(9, '198607102018011009', '9012345678901234', 'Indra Kusuma, M.T', 'L', 'Makassar', '1986-07-10', 'S2', 'pns', 24, 12, 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(10, '199408152019012010', '0123456789012345', 'Juliarti Sari, S.Pd', 'P', 'Palembang', '1994-08-15', 'S1', 'honorer', 24, 12, 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03');

-- =============================================
-- 2. Tabel jam_pelajaran (mandiri)
-- =============================================
CREATE TABLE IF NOT EXISTS `jam_pelajaran` (
  `id_jam` int unsigned NOT NULL AUTO_INCREMENT,
  `slot` tinyint NOT NULL COMMENT 'Urutan slot 1-14',
  `waktu_mulai` time NOT NULL,
  `waktu_selesai` time NOT NULL,
  `durasi_menit` smallint DEFAULT '45',
  `is_istirahat` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_jam`),
  UNIQUE KEY `slot` (`slot`),
  KEY `idx_slot` (`slot`),
  KEY `idx_istirahat` (`is_istirahat`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

INSERT INTO `jam_pelajaran` (`id_jam`, `slot`, `waktu_mulai`, `waktu_selesai`, `durasi_menit`, `is_istirahat`, `is_active`, `created_at`) VALUES
	(1, 1, '07:00:00', '07:45:00', 45, 0, 1, '2026-06-11 23:34:03'),
	(2, 2, '07:45:00', '08:30:00', 45, 0, 1, '2026-06-11 23:34:03'),
	(3, 3, '08:30:00', '09:15:00', 45, 0, 1, '2026-06-11 23:34:03'),
	(4, 4, '09:15:00', '10:00:00', 45, 0, 1, '2026-06-11 23:34:03'),
	(5, 5, '10:00:00', '10:15:00', 15, 1, 0, '2026-06-11 23:34:03'),
	(6, 6, '10:15:00', '11:00:00', 45, 0, 1, '2026-06-11 23:34:03'),
	(7, 7, '11:00:00', '11:45:00', 45, 0, 1, '2026-06-11 23:34:03'),
	(8, 8, '11:45:00', '12:30:00', 45, 0, 1, '2026-06-11 23:34:03'),
	(9, 9, '12:30:00', '13:15:00', 45, 0, 1, '2026-06-11 23:34:03'),
	(10, 10, '13:15:00', '13:30:00', 15, 1, 0, '2026-06-11 23:34:03'),
	(11, 11, '13:30:00', '14:15:00', 45, 0, 1, '2026-06-11 23:34:03'),
	(12, 12, '14:15:00', '15:00:00', 45, 0, 1, '2026-06-11 23:34:03');

-- =============================================
-- 3. Tabel kelas (mandiri, abaikan id_kurikulum)
-- =============================================
CREATE TABLE IF NOT EXISTS `kelas` (
  `id_kelas` int unsigned NOT NULL AUTO_INCREMENT,
  `kode_kelas` varchar(20) NOT NULL COMMENT 'Max 20 char, unique',
  `nama_kelas` varchar(50) NOT NULL,
  `jurusan` varchar(50) NOT NULL,
  `tingkat` enum('X','XI','XII') NOT NULL,
  `kapasitas_siswa` smallint DEFAULT '32' COMMENT '10-50 siswa',
  `id_kurikulum` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kelas`),
  UNIQUE KEY `kode_kelas` (`kode_kelas`),
  KEY `idx_kode` (`kode_kelas`),
  KEY `idx_tingkat` (`tingkat`),
  KEY `idx_jurusan` (`jurusan`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

INSERT INTO `kelas` (`id_kelas`, `kode_kelas`, `nama_kelas`, `jurusan`, `tingkat`, `kapasitas_siswa`, `id_kurikulum`, `created_at`, `updated_at`) VALUES
	(1, 'X-RPL-1', 'X RPL 1', 'RPL', 'X', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(2, 'X-RPL-2', 'X RPL 2', 'RPL', 'X', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(3, 'XI-RPL-1', 'XI RPL 1', 'RPL', 'XI', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(4, 'XI-RPL-2', 'XI RPL 2', 'RPL', 'XI', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(5, 'XII-RPL-1', 'XII RPL 1', 'RPL', 'XII', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(6, 'XII-RPL-2', 'XII RPL 2', 'RPL', 'XII', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(7, 'X-TKI-1', 'X TKJ 1', 'TKJ', 'X', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(8, 'X-TKI-2', 'X TKJ 2', 'TKJ', 'X', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(9, 'XI-TKI-1', 'XI TKJ 1', 'TKJ', 'XI', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(10, 'XI-TKI-2', 'XI TKJ 2', 'TKJ', 'XI', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(11, 'XII-TKI-1', 'XII TKJ 1', 'TKJ', 'XII', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(12, 'XII-TKI-2', 'XII TKJ 2', 'TKJ', 'XII', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(13, 'X-OTKP-1', 'X OTKP 1', 'OTKP', 'X', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(14, 'X-OTKP-2', 'X OTKP 2', 'OTKP', 'X', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(15, 'XI-OTKP-1', 'XI OTKP 1', 'OTKP', 'XI', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(16, 'XI-OTKP-2', 'XI OTKP 2', 'OTKP', 'XI', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(17, 'XII-OTKP-1', 'XII OTKP 1', 'OTKP', 'XII', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(18, 'XII-OTKP-2', 'XII OTKP 2', 'OTKP', 'XII', 32, NULL, '2026-06-11 23:34:03', '2026-06-11 23:34:03');

-- =============================================
-- 4. Tabel mata_pelajaran (mandiri)
-- =============================================
CREATE TABLE IF NOT EXISTS `mata_pelajaran` (
  `id_mapel` int unsigned NOT NULL AUTO_INCREMENT,
  `kode_mapel` varchar(10) NOT NULL COMMENT 'Max 10 alphanumeric+strip',
  `nama_mapel` varchar(100) NOT NULL,
  `kelompok` enum('A','B','C','D') NOT NULL DEFAULT 'A' COMMENT 'Kelompok mapel',
  `tipe` enum('teori','praktikum') NOT NULL DEFAULT 'teori',
  `jam_per_minggu` tinyint NOT NULL DEFAULT '2' COMMENT '1-40 jam/minggu',
  `requires_bloc` tinyint(1) DEFAULT '0' COMMENT 'Memerlukan blok waktu',
  `bloc_duration` tinyint DEFAULT '0' COMMENT 'Durasi blok dalam jam',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mapel`),
  UNIQUE KEY `kode_mapel` (`kode_mapel`),
  KEY `idx_kode` (`kode_mapel`),
  KEY `idx_kelompok` (`kelompok`),
  KEY `idx_tipe` (`tipe`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

INSERT INTO `mata_pelajaran` (`id_mapel`, `kode_mapel`, `nama_mapel`, `kelompok`, `tipe`, `jam_per_minggu`, `requires_bloc`, `bloc_duration`, `created_at`, `updated_at`) VALUES
	(1, 'MTK-01', 'Matematika', 'A', 'teori', 4, 0, 0, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(2, 'BIN-01', 'Bahasa Indonesia', 'A', 'teori', 3, 0, 0, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(3, 'BIG-01', 'Bahasa Inggris', 'A', 'teori', 3, 0, 0, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(4, 'FIS-01', 'Fisika', 'B', 'teori', 3, 0, 0, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(5, 'FIS-02', 'Praktikum Fisika', 'B', 'praktikum', 2, 1, 2, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(6, 'KIM-01', 'Kimia', 'B', 'teori', 3, 0, 0, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(7, 'KIM-02', 'Praktikum Kimia', 'B', 'praktikum', 2, 1, 2, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(8, 'BIO-01', 'Biologi', 'B', 'teori', 3, 0, 0, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(9, 'PKN-01', 'Pendidikan Kewarganegaraan', 'A', 'teori', 2, 0, 0, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(10, 'AGM-01', 'Pendidikan Agama', 'A', 'teori', 2, 0, 0, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(11, 'PJOK-01', 'Penjas Orkes', 'B', 'praktikum', 2, 0, 0, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(12, 'PKK-01', 'Produk Kreatif & Kewirausahaan', 'C', 'teori', 2, 0, 0, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(13, 'RPL-01', 'Rekayasa Perangkat Lunak', 'C', 'praktikum', 6, 1, 3, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(14, 'BDG-01', 'Basis Data', 'C', 'praktikum', 4, 1, 2, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(15, 'JRK-01', 'Jaringan Komputer', 'C', 'praktikum', 6, 1, 3, '2026-06-11 23:34:03', '2026-06-11 23:34:03');

-- =============================================
-- 5. Tabel ruangan (mandiri)
-- =============================================
CREATE TABLE IF NOT EXISTS `ruangan` (
  `id_ruangan` int unsigned NOT NULL AUTO_INCREMENT,
  `kode_ruangan` varchar(20) NOT NULL,
  `nama_ruangan` varchar(100) NOT NULL,
  `tipe` enum('kelas','lab','bengkel','lapangan','aula') NOT NULL DEFAULT 'kelas',
  `kapasitas` smallint DEFAULT '32',
  `lantai` varchar(10) DEFAULT NULL,
  `fasilitas` text,
  `status_aktif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_ruangan`),
  UNIQUE KEY `kode_ruangan` (`kode_ruangan`),
  KEY `idx_kode` (`kode_ruangan`),
  KEY `idx_tipe` (`tipe`),
  KEY `idx_status` (`status_aktif`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

INSERT INTO `ruangan` (`id_ruangan`, `kode_ruangan`, `nama_ruangan`, `tipe`, `kapasitas`, `lantai`, `fasilitas`, `status_aktif`, `created_at`, `updated_at`) VALUES
	(1, 'R-001', 'Ruang Kelas 1', 'kelas', 32, '1', 'AC, Proyektor', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(2, 'R-002', 'Ruang Kelas 2', 'kelas', 32, '1', 'AC, Proyektor', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(3, 'R-003', 'Ruang Kelas 3', 'kelas', 32, '1', 'Proyektor', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(4, 'R-004', 'Ruang Kelas 4', 'kelas', 32, '2', 'Proyektor', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(5, 'R-005', 'Ruang Kelas 5', 'kelas', 32, '2', 'Proyektor', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(6, 'R-006', 'Ruang Kelas 6', 'kelas', 32, '2', 'Proyektor', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(7, 'LAB-RPL', 'Lab RPL', 'lab', 30, '3', '30 PC, Server, AC', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(8, 'LAB-JRK', 'Lab Jaringan', 'lab', 30, '3', '30 PC, Router, Switch', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(9, 'LAB-BDG', 'Lab Basis Data', 'lab', 30, '3', '30 PC, Server DB', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(10, 'LAB-FIS', 'Lab Fisika', 'lab', 28, '2', 'Alat Praktikum Fisika', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(11, 'LAB-KIM', 'Lab Kimia', 'lab', 28, '2', 'Alat Praktikum Kimia', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(12, 'LAB-BIO', 'Lab Biologi', 'lab', 28, '2', 'Alat Praktikum Biologi', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(13, 'BENG-01', 'Bengkel Kerja 1', 'bengkel', 25, '1', 'Mesin Bubut, dll', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(14, 'BENG-02', 'Bengkel Kerja 2', 'bengkel', 25, '1', 'Mesin Frais, dll', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(15, 'AULA-01', 'Aula Utama', 'aula', 200, '1', 'Sound System, AC', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(16, 'LPNG-01', 'Lapangan Olahraga', 'lapangan', 500, '-', 'Tribun', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(17, 'R-007', 'Ruang Kelas 7', 'kelas', 32, '3', 'Proyektor', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(18, 'R-008', 'Ruang Kelas 8', 'kelas', 32, '3', 'Proyektor', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(19, 'R-009', 'Ruang Kelas 9', 'kelas', 32, '3', 'Proyektor', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(20, 'R-010', 'Ruang Kelas 10', 'kelas', 32, '3', 'Proyektor', 1, '2026-06-11 23:34:03', '2026-06-11 23:34:03');

-- =============================================
-- 6. Tabel tahun_ajaran (mandiri)
-- =============================================
CREATE TABLE IF NOT EXISTS `tahun_ajaran` (
  `id_tahun_ajaran` int unsigned NOT NULL AUTO_INCREMENT,
  `tahun_mulai` year NOT NULL,
  `tahun_selesai` year NOT NULL,
  `semester` enum('ganjil','genap') NOT NULL,
  `is_aktif` tinyint(1) DEFAULT '0',
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('draft','active','closed') DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_tahun_ajaran`),
  KEY `idx_tahun` (`tahun_mulai`,`tahun_selesai`),
  KEY `idx_aktif` (`is_aktif`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

INSERT INTO `tahun_ajaran` (`id_tahun_ajaran`, `tahun_mulai`, `tahun_selesai`, `semester`, `is_aktif`, `tanggal_mulai`, `tanggal_selesai`, `status`, `created_at`, `updated_at`) VALUES
	(1, '2024', '2025', 'ganjil', 1, '2024-07-15', '2024-12-20', 'active', '2026-06-11 23:34:03', '2026-06-11 23:34:03'),
	(2, '2024', '2025', 'genap', 0, '2025-01-06', '2025-06-20', 'draft', '2026-06-11 23:34:03', '2026-06-11 23:34:03');

-- =============================================
-- 7. Tabel users (FK ke guru, kelas) -> guru & kelas sudah ada
-- =============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','waka','guru','wali_kelas') NOT NULL,
  `id_guru` int unsigned DEFAULT NULL COMMENT 'FK ke guru jika role=guru',
  `id_kelas` int unsigned DEFAULT NULL COMMENT 'FK ke kelas jika role=wali_kelas',
  `nip` varchar(18) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_username` (`username`),
  KEY `idx_role` (`role`),
  KEY `idx_active` (`is_active`),
  KEY `fk_users_guru` (`id_guru`),
  KEY `fk_users_kelas` (`id_kelas`),
  CONSTRAINT `fk_users_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_users_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `id_guru`, `id_kelas`, `nip`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
	(21, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator Sistem', 'admin@smk.sch.id', 'admin', NULL, NULL, 'ADM001', 1, NULL, '2026-06-12 00:03:09', '2026-06-12 00:03:09'),
	(22, 'waka', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Waka Kurikulum', 'waka@smk.sch.id', 'waka', NULL, NULL, 'WKA001', 1, NULL, '2026-06-12 00:03:09', '2026-06-12 00:03:09'),
	(23, 'guru1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ahmad Fauzi, S.Pd', 'ahmad@smk.sch.id', 'guru', 1, NULL, '198501152010011001', 1, NULL, '2026-06-12 00:03:09', '2026-06-12 00:03:09'),
	(24, 'guru2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siti Nurhaliza, S.Pd', 'siti@smk.sch.id', 'guru', 2, NULL, '198703202011012002', 1, NULL, '2026-06-12 00:03:09', '2026-06-12 00:03:09'),
	(25, 'guru3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Santoso, S.Kom', 'budi@smk.sch.id', 'guru', 3, NULL, '199005102012011003', 1, NULL, '2026-06-12 00:03:09', '2026-06-12 00:03:09'),
	(26, 'guru4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dewi Lestari, M.Pd', 'dewi@smk.sch.id', 'guru', 4, NULL, '198808152013012004', 1, NULL, '2026-06-12 00:03:09', '2026-06-12 00:03:09'),
	(27, 'guru5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Eko Prasetyo, S.T', 'eko@smk.sch.id', 'guru', 5, NULL, '199202202014011005', 1, NULL, '2026-06-12 00:03:09', '2026-06-12 00:03:09'),
	(28, 'walikelas1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fitri Handayani, S.Pd', 'fitri@smk.sch.id', 'wali_kelas', 6, 1, '198906102015012006', 1, NULL, '2026-06-12 00:03:09', '2026-06-12 00:03:09'),
	(29, 'walikelas2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gunawan Wibowo, S.Pd', 'gunawan@smk.sch.id', 'wali_kelas', 7, 7, '199109152016011007', 1, NULL, '2026-06-12 00:03:09', '2026-06-12 00:03:09'),
	(30, 'walikelas3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hana Pertiwi, S.Kom', 'hana@smk.sch.id', 'wali_kelas', 8, 13, '199304202017012008', 1, NULL, '2026-06-12 00:03:09', '2026-06-12 00:03:09');

-- =============================================
-- 8. Tabel activity_log (FK ke users) -> users sudah ada
-- =============================================
CREATE TABLE IF NOT EXISTS `activity_log` (
  `id_log` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int DEFAULT NULL,
  `old_value` text COMMENT 'JSON data sebelum perubahan',
  `new_value` text COMMENT 'JSON data setelah perubahan',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `idx_user` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- (data activity_log kosong)

-- =============================================
-- 9. Tabel jadwal (FK ke tahun_ajaran, users) -> keduanya sudah ada
-- =============================================
CREATE TABLE IF NOT EXISTS `jadwal` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_tahun_ajaran` int unsigned NOT NULL,
  `status` enum('draft','approved','revised') DEFAULT 'draft',
  `generated_at` timestamp NULL DEFAULT NULL,
  `generated_by` int unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_jadwal_tahun` (`id_tahun_ajaran`),
  KEY `fk_jadwal_generated_by` (`generated_by`),
  KEY `fk_jadwal_approved_by` (`approved_by`),
  KEY `idx_tahun` (`id_tahun_ajaran`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_jadwal_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_jadwal_generated_by` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_jadwal_tahun` FOREIGN KEY (`id_tahun_ajaran`) REFERENCES `tahun_ajaran` (`id_tahun_ajaran`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- (data jadwal kosong)

-- =============================================
-- 10. Tabel penugasan_guru (FK ke guru, mapel, kelas, ruangan, tahun_ajaran) -> semua sudah ada
-- =============================================
CREATE TABLE IF NOT EXISTS `penugasan_guru` (
  `id_penugasan` int unsigned NOT NULL AUTO_INCREMENT,
  `id_guru` int unsigned NOT NULL,
  `id_mapel` int unsigned NOT NULL,
  `id_kelas` int unsigned NOT NULL,
  `id_ruangan` int unsigned DEFAULT NULL,
  `id_tahun_ajaran` int unsigned NOT NULL,
  `semester` enum('ganjil','genap') NOT NULL,
  `jam_per_minggu` tinyint DEFAULT '0',
  `is_praktikum` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_penugasan`),
  UNIQUE KEY `unique_penugasan` (`id_guru`,`id_mapel`,`id_kelas`,`id_tahun_ajaran`,`semester`),
  KEY `fk_penugasan_ruangan` (`id_ruangan`),
  KEY `idx_guru` (`id_guru`),
  KEY `idx_mapel` (`id_mapel`),
  KEY `idx_kelas` (`id_kelas`),
  KEY `idx_tahun_ajaran` (`id_tahun_ajaran`),
  KEY `idx_semester` (`semester`),
  CONSTRAINT `fk_penugasan_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_penugasan_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_penugasan_mapel` FOREIGN KEY (`id_mapel`) REFERENCES `mata_pelajaran` (`id_mapel`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_penugasan_ruangan` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_penugasan_tahun` FOREIGN KEY (`id_tahun_ajaran`) REFERENCES `tahun_ajaran` (`id_tahun_ajaran`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

INSERT INTO `penugasan_guru` (`id_penugasan`, `id_guru`, `id_mapel`, `id_kelas`, `id_ruangan`, `id_tahun_ajaran`, `semester`, `jam_per_minggu`, `is_praktikum`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, NULL, 1, 'ganjil', 4, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(2, 1, 1, 2, NULL, 1, 'ganjil', 4, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(3, 1, 1, 7, NULL, 1, 'ganjil', 4, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(4, 2, 2, 1, NULL, 1, 'ganjil', 3, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(5, 2, 2, 2, NULL, 1, 'ganjil', 3, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(6, 2, 2, 3, NULL, 1, 'ganjil', 3, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(7, 3, 13, 1, 7, 1, 'ganjil', 4, 1, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(8, 3, 13, 2, 7, 1, 'ganjil', 4, 1, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(9, 3, 14, 3, 9, 1, 'ganjil', 4, 1, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(10, 4, 3, 1, NULL, 1, 'ganjil', 3, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(11, 4, 3, 2, NULL, 1, 'ganjil', 3, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(12, 4, 3, 7, NULL, 1, 'ganjil', 3, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(13, 5, 15, 7, 8, 1, 'ganjil', 4, 1, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(14, 5, 15, 8, 8, 1, 'ganjil', 4, 1, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(15, 5, 15, 9, 8, 1, 'ganjil', 4, 1, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(16, 6, 4, 1, NULL, 1, 'ganjil', 3, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(17, 6, 4, 2, NULL, 1, 'ganjil', 3, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(18, 6, 5, 3, 10, 1, 'ganjil', 2, 1, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(19, 7, 6, 1, NULL, 1, 'ganjil', 3, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(20, 7, 6, 7, NULL, 1, 'ganjil', 3, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(21, 7, 7, 2, 11, 1, 'ganjil', 2, 1, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(22, 8, 8, 3, NULL, 1, 'ganjil', 3, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(23, 8, 8, 4, NULL, 1, 'ganjil', 3, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(24, 8, 11, 1, 16, 1, 'ganjil', 2, 1, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(25, 9, 12, 1, NULL, 1, 'ganjil', 2, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(26, 9, 12, 2, NULL, 1, 'ganjil', 2, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(27, 9, 9, 3, NULL, 1, 'ganjil', 2, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(28, 10, 10, 1, NULL, 1, 'ganjil', 2, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(29, 10, 10, 2, NULL, 1, 'ganjil', 2, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04'),
	(30, 10, 10, 3, NULL, 1, 'ganjil', 2, 0, '2026-06-11 23:34:04', '2026-06-11 23:34:04');

-- =============================================
-- 11. Tabel jadwal_detail (FK ke jadwal, kelas, penugasan_guru, ruangan) -> semua sudah ada
-- =============================================
CREATE TABLE IF NOT EXISTS `jadwal_detail` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_jadwal` int unsigned NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') NOT NULL,
  `slot` tinyint NOT NULL,
  `id_kelas` int unsigned NOT NULL,
  `id_penugasan` int unsigned NOT NULL,
  `id_ruangan` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_jadwal_detail` (`id_jadwal`,`hari`,`slot`),
  KEY `fk_jadwal_detail_ruangan` (`id_ruangan`),
  KEY `idx_jadwal` (`id_jadwal`),
  KEY `idx_kelas` (`id_kelas`),
  KEY `idx_hari_slot` (`hari`,`slot`),
  KEY `idx_penugasan` (`id_penugasan`),
  CONSTRAINT `fk_jadwal_detail_jadwal` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_jadwal_detail_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  CONSTRAINT `fk_jadwal_detail_penugasan` FOREIGN KEY (`id_penugasan`) REFERENCES `penugasan_guru` (`id_penugasan`) ON DELETE CASCADE,
  CONSTRAINT `fk_jadwal_detail_ruangan` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

-- (data jadwal_detail kosong)

-- =============================================
-- 12. Tabel preferensi (FK ke guru, tanpa formal constraint di dump)
-- =============================================
CREATE TABLE IF NOT EXISTS `preferensi` (
  `id_preferensi` int unsigned NOT NULL AUTO_INCREMENT,
  `id_guru` int unsigned NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') DEFAULT NULL,
  `slot` tinyint DEFAULT NULL,
  `tipe_preferensi` enum('suka','hindari') NOT NULL,
  `prioritas` tinyint DEFAULT '5' COMMENT '1-10',
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_preferensi`),
  KEY `idx_guru` (`id_guru`),
  KEY `idx_tipe` (`tipe_preferensi`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

INSERT INTO `preferensi` (`id_preferensi`, `id_guru`, `hari`, `slot`, `tipe_preferensi`, `prioritas`, `keterangan`, `created_at`) VALUES
	(1, 1, 'Senin', 1, 'hindari', 8, 'Ada rapat pagi Senin', '2026-06-11 23:34:04'),
	(2, 1, 'Jumat', NULL, 'suka', 5, 'Lebih suka mengajar Jumat', '2026-06-11 23:34:04'),
	(3, 2, 'Selasa', 2, 'suka', 7, 'Preferensi waktu optimal', '2026-06-11 23:34:04'),
	(4, 3, 'Rabu', NULL, 'hindari', 6, 'Hari pengembangan diri', '2026-06-11 23:34:04'),
	(5, 4, 'Kamis', 1, 'hindari', 9, 'Keperluan keluarga', '2026-06-11 23:34:04'),
	(6, 5, 'Senin', 6, 'suka', 6, 'Energi maksimal siang', '2026-06-11 23:34:04'),
	(7, 6, NULL, 1, 'hindari', 7, 'Tidak suka pagi terlalu awal', '2026-06-11 23:34:04'),
	(8, 7, 'Jumat', NULL, 'hindari', 8, 'Sholat Jumat', '2026-06-11 23:34:04'),
	(9, 8, 'Selasa', 3, 'suka', 5, 'Waktu favorit', '2026-06-11 23:34:04'),
	(10, 9, 'Rabu', 2, 'suka', 6, 'Kondisi optimal', '2026-06-11 23:34:04');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;