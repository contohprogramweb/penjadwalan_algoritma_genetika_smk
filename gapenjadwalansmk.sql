-- =====================================================
-- GAPENJADWALAN SMK - Complete Database Schema & Data
-- REVISI: Disesuaikan dengan kode Model yang ada
-- - Tabel jadwal (header) + jadwal_detail (detail)
-- - Kolom slot (bukan slot_ke di jadwal_detail)
-- - Konsisten dengan Jadwal_model.php
-- Tidak menggunakan DEFAULT CHARSET (menggunakan setting server)
-- =====================================================

-- Disable foreign key checks untuk keamanan saat create tables
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- BAGIAN 1: TABEL MASTER (Tanpa Foreign Key)
-- =====================================================

-- Tabel: guru
CREATE TABLE IF NOT EXISTS guru (
    id_guru INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(18) NOT NULL UNIQUE COMMENT '18 digit NIP',
    nuptk VARCHAR(16) DEFAULT NULL COMMENT '16 digit NUPTK (opsional)',
    nama_lengkap VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL DEFAULT 'L',
    tempat_lahir VARCHAR(50) DEFAULT NULL,
    tanggal_lahir DATE DEFAULT NULL,
    pendidikan_terakhir ENUM('S1', 'S2', 'S3', 'D3', 'D4') DEFAULT 'S1',
    jam_maks_minggu TINYINT(2) DEFAULT 24 COMMENT 'Maksimal 24 jam/minggu',
    jam_min_minggu TINYINT(2) DEFAULT 12 COMMENT 'Minimal 12 jam/minggu',
    status_aktif TINYINT(1) DEFAULT 1 COMMENT '1=aktif, 0=nonaktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_nip (nip),
    INDEX idx_nuptk (nuptk),
    INDEX idx_status (status_aktif)
) ENGINE=InnoDB;

-- Tabel: mata_pelajaran
CREATE TABLE IF NOT EXISTS mata_pelajaran (
    id_mapel INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_mapel VARCHAR(10) NOT NULL UNIQUE COMMENT 'Max 10 alphanumeric+strip',
    nama_mapel VARCHAR(100) NOT NULL,
    kelompok ENUM('A', 'B', 'C', 'D') NOT NULL DEFAULT 'A' COMMENT 'Kelompok mapel',
    tipe ENUM('teori', 'praktikum') NOT NULL DEFAULT 'teori',
    jam_per_minggu TINYINT(2) NOT NULL DEFAULT 2 COMMENT '1-40 jam/minggu',
    requires_bloc TINYINT(1) DEFAULT 0 COMMENT 'Memerlukan blok waktu',
    bloc_duration TINYINT(2) DEFAULT 0 COMMENT 'Durasi blok dalam jam',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_kode (kode_mapel),
    INDEX idx_kelompok (kelompok),
    INDEX idx_tipe (tipe)
) ENGINE=InnoDB;

-- Tabel: kelas
CREATE TABLE IF NOT EXISTS kelas (
    id_kelas INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_kelas VARCHAR(20) NOT NULL UNIQUE COMMENT 'Max 20 char, unique',
    nama_kelas VARCHAR(50) NOT NULL,
    jurusan VARCHAR(50) NOT NULL,
    tingkat ENUM('X', 'XI', 'XII') NOT NULL,
    kapasitas_siswa SMALLINT(3) DEFAULT 32 COMMENT '10-50 siswa',
    id_kurikulum INT(11) UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_kode (kode_kelas),
    INDEX idx_tingkat (tingkat),
    INDEX idx_jurusan (jurusan)
) ENGINE=InnoDB;

-- Tabel: ruangan
CREATE TABLE IF NOT EXISTS ruangan (
    id_ruangan INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_ruangan VARCHAR(20) NOT NULL UNIQUE,
    nama_ruangan VARCHAR(100) NOT NULL,
    tipe ENUM('kelas', 'lab', 'bengkel', 'lapangan', 'aula') NOT NULL DEFAULT 'kelas',
    kapasitas SMALLINT(3) DEFAULT 32,
    lantai VARCHAR(10) DEFAULT NULL,
    fasilitas TEXT DEFAULT NULL,
    status_aktif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_kode (kode_ruangan),
    INDEX idx_tipe (tipe),
    INDEX idx_status (status_aktif)
) ENGINE=InnoDB;

-- Tabel: jam_pelajaran
CREATE TABLE IF NOT EXISTS jam_pelajaran (
    id_jam INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slot TINYINT(2) NOT NULL UNIQUE COMMENT 'Urutan slot 1-14',
    waktu_mulai TIME NOT NULL,
    waktu_selesai TIME NOT NULL,
    durasi_menit SMALLINT(3) DEFAULT 45,
    is_istirahat TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_slot (slot),
    INDEX idx_istirahat (is_istirahat),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- Tabel: tahun_ajaran
CREATE TABLE IF NOT EXISTS tahun_ajaran (
    id_tahun_ajaran INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tahun_mulai YEAR NOT NULL,
    tahun_selesai YEAR NOT NULL,
    semester ENUM('ganjil', 'genap') NOT NULL,
    is_aktif TINYINT(1) DEFAULT 0,
    tanggal_mulai DATE DEFAULT NULL,
    tanggal_selesai DATE DEFAULT NULL,
    status ENUM('draft', 'active', 'closed') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_tahun (tahun_mulai, tahun_selesai),
    INDEX idx_aktif (is_aktif),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Tabel: users (untuk autentikasi) - tanpa FK dulu
CREATE TABLE IF NOT EXISTS users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    role ENUM('admin', 'waka', 'guru', 'wali_kelas') NOT NULL,
    id_guru INT(11) UNSIGNED DEFAULT NULL COMMENT 'FK ke guru jika role=guru',
    id_kelas INT(11) UNSIGNED DEFAULT NULL COMMENT 'FK ke kelas jika role=wali_kelas',
    nip VARCHAR(18) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- Tabel: preferensi (soft constraints untuk GA)
CREATE TABLE IF NOT EXISTS preferensi (
    id_preferensi INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_guru INT(11) UNSIGNED NOT NULL,
    hari ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu') DEFAULT NULL,
    slot TINYINT(2) DEFAULT NULL,
    tipe_preferensi ENUM('suka', 'hindari') NOT NULL,
    prioritas TINYINT(2) DEFAULT 5 COMMENT '1-10',
    keterangan VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_guru (id_guru),
    INDEX idx_tipe (tipe_preferensi)
) ENGINE=InnoDB;

-- =====================================================
-- BAGIAN 2: TABEL DENGAN FOREIGN KEY
-- =====================================================

-- Tabel: penugasan_guru (relasi guru ↔ kelas ↔ mapel)
CREATE TABLE IF NOT EXISTS penugasan_guru (
    id_penugasan INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_guru INT(11) UNSIGNED NOT NULL,
    id_mapel INT(11) UNSIGNED NOT NULL,
    id_kelas INT(11) UNSIGNED NOT NULL,
    id_ruangan INT(11) UNSIGNED DEFAULT NULL,
    id_tahun_ajaran INT(11) UNSIGNED NOT NULL,
    semester ENUM('ganjil', 'genap') NOT NULL,
    jam_per_minggu TINYINT(2) DEFAULT 0,
    is_praktikum TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign keys
    CONSTRAINT fk_penugasan_guru FOREIGN KEY (id_guru) REFERENCES guru(id_guru) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_penugasan_mapel FOREIGN KEY (id_mapel) REFERENCES mata_pelajaran(id_mapel) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_penugasan_kelas FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_penugasan_ruangan FOREIGN KEY (id_ruangan) REFERENCES ruangan(id_ruangan) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_penugasan_tahun FOREIGN KEY (id_tahun_ajaran) REFERENCES tahun_ajaran(id_tahun_ajaran) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Indexes untuk performa
    INDEX idx_guru (id_guru),
    INDEX idx_mapel (id_mapel),
    INDEX idx_kelas (id_kelas),
    INDEX idx_tahun_ajaran (id_tahun_ajaran),
    INDEX idx_semester (semester),
    
    -- Unique constraint: satu guru tidak boleh diassign mapel yang sama ke kelas yang sama
    UNIQUE KEY unique_penugasan (id_guru, id_mapel, id_kelas, id_tahun_ajaran, semester)
) ENGINE=InnoDB;

-- Tabel: jadwal (HEADER - metadata jadwal per tahun ajaran)
CREATE TABLE IF NOT EXISTS jadwal (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_tahun_ajaran INT(11) UNSIGNED NOT NULL,
    status ENUM('draft', 'approved', 'revised') DEFAULT 'draft',
    generated_at TIMESTAMP NULL DEFAULT NULL,
    generated_by INT(11) UNSIGNED DEFAULT NULL,
    approved_at TIMESTAMP NULL DEFAULT NULL,
    approved_by INT(11) UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_jadwal_tahun FOREIGN KEY (id_tahun_ajaran) REFERENCES tahun_ajaran(id_tahun_ajaran) ON DELETE CASCADE,
    CONSTRAINT fk_jadwal_generated_by FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_jadwal_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_tahun (id_tahun_ajaran),
    INDEX idx_status (status),
    UNIQUE KEY unique_jadwal_tahun (id_tahun_ajaran)
) ENGINE=InnoDB;

-- Tabel: jadwal_detail (DETAIL - setiap entry jadwal)
CREATE TABLE IF NOT EXISTS jadwal_detail (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_jadwal INT(11) UNSIGNED NOT NULL,
    hari ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu') NOT NULL,
    slot TINYINT(2) NOT NULL,
    id_kelas INT(11) UNSIGNED NOT NULL,
    id_penugasan INT(11) UNSIGNED NOT NULL,
    id_ruangan INT(11) UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_jadwal_detail_jadwal FOREIGN KEY (id_jadwal) REFERENCES jadwal(id) ON DELETE CASCADE,
    CONSTRAINT fk_jadwal_detail_kelas FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas) ON DELETE CASCADE,
    CONSTRAINT fk_jadwal_detail_penugasan FOREIGN KEY (id_penugasan) REFERENCES penugasan_guru(id_penugasan) ON DELETE CASCADE,
    CONSTRAINT fk_jadwal_detail_ruangan FOREIGN KEY (id_ruangan) REFERENCES ruangan(id_ruangan) ON DELETE SET NULL,
    
    INDEX idx_jadwal (id_jadwal),
    INDEX idx_kelas (id_kelas),
    INDEX idx_hari_slot (hari, slot),
    INDEX idx_penugasan (id_penugasan),
    UNIQUE KEY unique_jadwal_detail (id_jadwal, hari, slot)
) ENGINE=InnoDB;

-- Tabel: activity_log (audit trail)
CREATE TABLE IF NOT EXISTS activity_log (
    id_log BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) UNSIGNED DEFAULT NULL,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(50) DEFAULT NULL,
    record_id INT(11) DEFAULT NULL,
    old_value TEXT DEFAULT NULL COMMENT 'JSON data sebelum perubahan',
    new_value TEXT DEFAULT NULL COMMENT 'JSON data setelah perubahan',
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- =====================================================
-- BAGIAN 3: TAMBAHKAN FOREIGN KEY KE TABEL users
-- =====================================================

ALTER TABLE users
    ADD CONSTRAINT fk_users_guru FOREIGN KEY (id_guru) REFERENCES guru(id_guru) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT fk_users_kelas FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas) ON DELETE SET NULL ON UPDATE CASCADE;

-- =====================================================
-- BAGIAN 4: INSERT DATA DUMMY
-- =====================================================

-- Data jam_pelajaran (12 slot: 10 belajar + 2 istirahat)
INSERT INTO jam_pelajaran (slot, waktu_mulai, waktu_selesai, durasi_menit, is_istirahat, is_active) VALUES
(1, '07:00:00', '07:45:00', 45, 0, 1),
(2, '07:45:00', '08:30:00', 45, 0, 1),
(3, '08:30:00', '09:15:00', 45, 0, 1),
(4, '09:15:00', '10:00:00', 45, 0, 1),
(5, '10:00:00', '10:15:00', 15, 1, 0),
(6, '10:15:00', '11:00:00', 45, 0, 1),
(7, '11:00:00', '11:45:00', 45, 0, 1),
(8, '11:45:00', '12:30:00', 45, 0, 1),
(9, '12:30:00', '13:15:00', 45, 0, 1),
(10, '13:15:00', '13:30:00', 15, 1, 0),
(11, '13:30:00', '14:15:00', 45, 0, 1),
(12, '14:15:00', '15:00:00', 45, 0, 1);

-- Data tahun_ajaran
INSERT INTO tahun_ajaran (tahun_mulai, tahun_selesai, semester, is_aktif, tanggal_mulai, tanggal_selesai, status) VALUES
(2024, 2025, 'ganjil', 1, '2024-07-15', '2024-12-20', 'active'),
(2024, 2025, 'genap', 0, '2025-01-06', '2025-06-20', 'draft');

-- Data guru (10 guru)
INSERT INTO guru (nip, nuptk, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, pendidikan_terakhir, jam_maks_minggu, jam_min_minggu, status_aktif) VALUES
('198501152010011001', '1234567890123456', 'Ahmad Fauzi, S.Pd', 'L', 'Bandung', '1985-01-15', 'S1', 24, 12, 1),
('198703202011012002', '2345678901234567', 'Siti Nurhaliza, S.Pd', 'P', 'Jakarta', '1987-03-20', 'S1', 24, 12, 1),
('199005102012011003', '3456789012345678', 'Budi Santoso, S.Kom', 'L', 'Surabaya', '1990-05-10', 'S1', 24, 12, 1),
('198808152013012004', '4567890123456789', 'Dewi Lestari, M.Pd', 'P', 'Yogyakarta', '1988-08-15', 'S2', 24, 12, 1),
('199202202014011005', '5678901234567890', 'Eko Prasetyo, S.T', 'L', 'Semarang', '1992-02-20', 'S1', 24, 12, 1),
('198906102015012006', '6789012345678901', 'Fitri Handayani, S.Pd', 'P', 'Malang', '1989-06-10', 'S1', 24, 12, 1),
('199109152016011007', '7890123456789012', 'Gunawan Wibowo, S.Pd', 'L', 'Medan', '1991-09-15', 'S1', 24, 12, 1),
('199304202017012008', '8901234567890123', 'Hana Pertiwi, S.Kom', 'P', 'Denpasar', '1993-04-20', 'S1', 24, 12, 1),
('198607102018011009', '9012345678901234', 'Indra Kusuma, M.T', 'L', 'Makassar', '1986-07-10', 'S2', 24, 12, 1),
('199408152019012010', '0123456789012345', 'Juliarti Sari, S.Pd', 'P', 'Palembang', '1994-08-15', 'S1', 24, 12, 1);

-- Data mata_pelajaran (15 mapel)
INSERT INTO mata_pelajaran (kode_mapel, nama_mapel, kelompok, tipe, jam_per_minggu, requires_bloc, bloc_duration) VALUES
('MTK-01', 'Matematika', 'A', 'teori', 4, 0, 0),
('BIN-01', 'Bahasa Indonesia', 'A', 'teori', 3, 0, 0),
('BIG-01', 'Bahasa Inggris', 'A', 'teori', 3, 0, 0),
('FIS-01', 'Fisika', 'B', 'teori', 3, 0, 0),
('FIS-02', 'Praktikum Fisika', 'B', 'praktikum', 2, 1, 2),
('KIM-01', 'Kimia', 'B', 'teori', 3, 0, 0),
('KIM-02', 'Praktikum Kimia', 'B', 'praktikum', 2, 1, 2),
('BIO-01', 'Biologi', 'B', 'teori', 3, 0, 0),
('PKN-01', 'Pendidikan Kewarganegaraan', 'A', 'teori', 2, 0, 0),
('AGM-01', 'Pendidikan Agama', 'A', 'teori', 2, 0, 0),
('PJOK-01', 'Penjas Orkes', 'B', 'praktikum', 2, 0, 0),
('PKK-01', 'Produk Kreatif & Kewirausahaan', 'C', 'teori', 2, 0, 0),
('RPL-01', 'Rekayasa Perangkat Lunak', 'C', 'praktikum', 6, 1, 3),
('BDG-01', 'Basis Data', 'C', 'praktikum', 4, 1, 2),
('JRK-01', 'Jaringan Komputer', 'C', 'praktikum', 6, 1, 3);

-- Data jurusan dan kelas (3 jurusan x 3 tingkat x 2 paralel = 18 kelas)
INSERT INTO kelas (kode_kelas, nama_kelas, jurusan, tingkat, kapasitas_siswa) VALUES
('X-RPL-1', 'X RPL 1', 'RPL', 'X', 32),
('X-RPL-2', 'X RPL 2', 'RPL', 'X', 32),
('XI-RPL-1', 'XI RPL 1', 'RPL', 'XI', 32),
('XI-RPL-2', 'XI RPL 2', 'RPL', 'XI', 32),
('XII-RPL-1', 'XII RPL 1', 'RPL', 'XII', 32),
('XII-RPL-2', 'XII RPL 2', 'RPL', 'XII', 32),
('X-TKI-1', 'X TKJ 1', 'TKJ', 'X', 32),
('X-TKI-2', 'X TKJ 2', 'TKJ', 'X', 32),
('XI-TKI-1', 'XI TKJ 1', 'TKJ', 'XI', 32),
('XI-TKI-2', 'XI TKJ 2', 'TKJ', 'XI', 32),
('XII-TKI-1', 'XII TKJ 1', 'TKJ', 'XII', 32),
('XII-TKI-2', 'XII TKJ 2', 'TKJ', 'XII', 32),
('X-OTKP-1', 'X OTKP 1', 'OTKP', 'X', 32),
('X-OTKP-2', 'X OTKP 2', 'OTKP', 'X', 32),
('XI-OTKP-1', 'XI OTKP 1', 'OTKP', 'XI', 32),
('XI-OTKP-2', 'XI OTKP 2', 'OTKP', 'XI', 32),
('XII-OTKP-1', 'XII OTKP 1', 'OTKP', 'XII', 32),
('XII-OTKP-2', 'XII OTKP 2', 'OTKP', 'XII', 32);

-- Data ruangan (20 ruangan)
INSERT INTO ruangan (kode_ruangan, nama_ruangan, tipe, kapasitas, lantai, fasilitas, status_aktif) VALUES
('R-001', 'Ruang Kelas 1', 'kelas', 32, '1', 'AC, Proyektor', 1),
('R-002', 'Ruang Kelas 2', 'kelas', 32, '1', 'AC, Proyektor', 1),
('R-003', 'Ruang Kelas 3', 'kelas', 32, '1', 'Proyektor', 1),
('R-004', 'Ruang Kelas 4', 'kelas', 32, '2', 'Proyektor', 1),
('R-005', 'Ruang Kelas 5', 'kelas', 32, '2', 'Proyektor', 1),
('R-006', 'Ruang Kelas 6', 'kelas', 32, '2', 'Proyektor', 1),
('LAB-RPL', 'Lab RPL', 'lab', 30, '3', '30 PC, Server, AC', 1),
('LAB-JRK', 'Lab Jaringan', 'lab', 30, '3', '30 PC, Router, Switch', 1),
('LAB-BDG', 'Lab Basis Data', 'lab', 30, '3', '30 PC, Server DB', 1),
('LAB-FIS', 'Lab Fisika', 'lab', 28, '2', 'Alat Praktikum Fisika', 1),
('LAB-KIM', 'Lab Kimia', 'lab', 28, '2', 'Alat Praktikum Kimia', 1),
('LAB-BIO', 'Lab Biologi', 'lab', 28, '2', 'Alat Praktikum Biologi', 1),
('BENG-01', 'Bengkel Kerja 1', 'bengkel', 25, '1', 'Mesin Bubut, dll', 1),
('BENG-02', 'Bengkel Kerja 2', 'bengkel', 25, '1', 'Mesin Frais, dll', 1),
('AULA-01', 'Aula Utama', 'aula', 200, '1', 'Sound System, AC', 1),
('LPNG-01', 'Lapangan Olahraga', 'lapangan', 500, '-', 'Tribun', 1),
('R-007', 'Ruang Kelas 7', 'kelas', 32, '3', 'Proyektor', 1),
('R-008', 'Ruang Kelas 8', 'kelas', 32, '3', 'Proyektor', 1),
('R-009', 'Ruang Kelas 9', 'kelas', 32, '3', 'Proyektor', 1),
('R-010', 'Ruang Kelas 10', 'kelas', 32, '3', 'Proyektor', 1);

-- Data users (password: admin123, waka123, guru123)
-- Hash bcrypt cost 10 untuk 'admin123', 'waka123', 'guru123'
INSERT INTO users (username, password, nama_lengkap, email, role, id_guru, id_kelas, nip, is_active) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator Sistem', 'admin@smk.sch.id', 'admin', NULL, NULL, 'ADM001', 1),
('waka', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Waka Kurikulum', 'waka@smk.sch.id', 'waka', NULL, NULL, 'WKA001', 1),
('guru1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ahmad Fauzi, S.Pd', 'ahmad@smk.sch.id', 'guru', 1, NULL, '198501152010011001', 1),
('guru2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siti Nurhaliza, S.Pd', 'siti@smk.sch.id', 'guru', 2, NULL, '198703202011012002', 1),
('guru3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Santoso, S.Kom', 'budi@smk.sch.id', 'guru', 3, NULL, '199005102012011003', 1),
('guru4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dewi Lestari, M.Pd', 'dewi@smk.sch.id', 'guru', 4, NULL, '198808152013012004', 1),
('guru5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Eko Prasetyo, S.T', 'eko@smk.sch.id', 'guru', 5, NULL, '199202202014011005', 1),
('walikelas1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fitri Handayani, S.Pd', 'fitri@smk.sch.id', 'wali_kelas', 6, 1, '198906102015012006', 1),
('walikelas2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gunawan Wibowo, S.Pd', 'gunawan@smk.sch.id', 'wali_kelas', 7, 7, '199109152016011007', 1),
('walikelas3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hana Pertiwi, S.Kom', 'hana@smk.sch.id', 'wali_kelas', 8, 13, '199304202017012008', 1);

-- Data penugasan_guru (contoh penugasan untuk semester ganjil 2024/2025)
INSERT INTO penugasan_guru (id_guru, id_mapel, id_kelas, id_ruangan, id_tahun_ajaran, semester, jam_per_minggu, is_praktikum) VALUES
-- Guru 1 (Ahmad Fauzi) - Matematika
(1, 1, 1, NULL, 1, 'ganjil', 4, 0),
(1, 1, 2, NULL, 1, 'ganjil', 4, 0),
(1, 1, 7, NULL, 1, 'ganjil', 4, 0),
-- Guru 2 (Siti Nurhaliza) - Bahasa Indonesia
(2, 2, 1, NULL, 1, 'ganjil', 3, 0),
(2, 2, 2, NULL, 1, 'ganjil', 3, 0),
(2, 2, 3, NULL, 1, 'ganjil', 3, 0),
-- Guru 3 (Budi Santoso) - RPL & Basis Data
(3, 13, 1, 7, 1, 'ganjil', 4, 1),
(3, 13, 2, 7, 1, 'ganjil', 4, 1),
(3, 14, 3, 9, 1, 'ganjil', 4, 1),
-- Guru 4 (Dewi Lestari) - Bahasa Inggris
(4, 3, 1, NULL, 1, 'ganjil', 3, 0),
(4, 3, 2, NULL, 1, 'ganjil', 3, 0),
(4, 3, 7, NULL, 1, 'ganjil', 3, 0),
-- Guru 5 (Eko Prasetyo) - Jaringan Komputer
(5, 15, 7, 8, 1, 'ganjil', 4, 1),
(5, 15, 8, 8, 1, 'ganjil', 4, 1),
(5, 15, 9, 8, 1, 'ganjil', 4, 1),
-- Guru 6 (Fitri Handayani) - Fisika
(6, 4, 1, NULL, 1, 'ganjil', 3, 0),
(6, 4, 2, NULL, 1, 'ganjil', 3, 0),
(6, 5, 3, 10, 1, 'ganjil', 2, 1),
-- Guru 7 (Gunawan Wibowo) - Kimia
(7, 6, 1, NULL, 1, 'ganjil', 3, 0),
(7, 6, 7, NULL, 1, 'ganjil', 3, 0),
(7, 7, 2, 11, 1, 'ganjil', 2, 1),
-- Guru 8 (Hana Pertiwi) - Biologi & PJOK
(8, 8, 3, NULL, 1, 'ganjil', 3, 0),
(8, 8, 4, NULL, 1, 'ganjil', 3, 0),
(8, 11, 1, 16, 1, 'ganjil', 2, 1),
-- Guru 9 (Indra Kusuma) - PKK & PKN
(9, 12, 1, NULL, 1, 'ganjil', 2, 0),
(9, 12, 2, NULL, 1, 'ganjil', 2, 0),
(9, 9, 3, NULL, 1, 'ganjil', 2, 0),
-- Guru 10 (Juliarti Sari) - Agama
(10, 10, 1, NULL, 1, 'ganjil', 2, 0),
(10, 10, 2, NULL, 1, 'ganjil', 2, 0),
(10, 10, 3, NULL, 1, 'ganjil', 2, 0);

-- Data preferensi (contoh preferensi guru)
INSERT INTO preferensi (id_guru, hari, slot, tipe_preferensi, prioritas, keterangan) VALUES
(1, 'Senin', 1, 'hindari', 8, 'Ada rapat pagi Senin'),
(1, 'Jumat', NULL, 'suka', 5, 'Lebih suka mengajar Jumat'),
(2, 'Selasa', 2, 'suka', 7, 'Preferensi waktu optimal'),
(3, 'Rabu', NULL, 'hindari', 6, 'Hari pengembangan diri'),
(4, 'Kamis', 1, 'hindari', 9, 'Keperluan keluarga'),
(5, 'Senin', 6, 'suka', 6, 'Energi maksimal siang'),
(6, NULL, 1, 'hindari', 7, 'Tidak suka pagi terlalu awal'),
(7, 'Jumat', NULL, 'hindari', 8, 'Sholat Jumat'),
(8, 'Selasa', 3, 'suka', 5, 'Waktu favorit'),
(9, 'Rabu', 2, 'suka', 6, 'Kondisi optimal');

-- Enable foreign key checks kembali
SET FOREIGN_KEY_CHECKS = 1;

-- Selesai! Database siap digunakan.
