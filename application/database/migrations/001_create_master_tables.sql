-- Migration 001: Create Master Data Tables
-- Sesuai SRS Bab 9 (Struktur Database) dan Bab 13 (Validasi)

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
    status_kepegawaian ENUM('pns', 'honorer', 'ttk') DEFAULT 'honorer' COMMENT 'Status kepegawaian',
    jam_maks_minggu TINYINT(2) DEFAULT 24 COMMENT 'Maksimal 24 jam/minggu',
    jam_min_minggu TINYINT(2) DEFAULT 12 COMMENT 'Minimal 12 jam/minggu',
    status_aktif TINYINT(1) DEFAULT 1 COMMENT '1=aktif, 0=nonaktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_nip (nip),
    INDEX idx_nuptk (nuptk),
    INDEX idx_status (status_aktif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel: jam_pelajaran
CREATE TABLE IF NOT EXISTS jam_pelajaran (
    id_jam INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slot_ke TINYINT(2) NOT NULL UNIQUE COMMENT 'Urutan slot 1-14',
    waktu_mulai TIME NOT NULL,
    waktu_selesai TIME NOT NULL,
    durasi_menit SMALLINT(3) DEFAULT 45,
    is_istirahat TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_slot (slot_ke),
    INDEX idx_istirahat (is_istirahat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel: tahun_ajaran
CREATE TABLE IF NOT EXISTS tahun_ajaran (
    id_tahun_ajaran INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tahun_mulai YEAR NOT NULL,
    tahun_selesai YEAR NOT NULL,
    semester Ganjil ENUM('ganjil', 'genap') NOT NULL,
    is_aktif TINYINT(1) DEFAULT 0,
    tanggal_mulai DATE DEFAULT NULL,
    tanggal_selesai DATE DEFAULT NULL,
    status ENUM('draft', 'active', 'closed') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_tahun (tahun_mulai, tahun_selesai),
    INDEX idx_aktif (is_aktif),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel: users (untuk autentikasi)
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
    
    FOREIGN KEY (id_guru) REFERENCES guru(id_guru) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel: schedule (jadwal yang sudah di-generate)
CREATE TABLE IF NOT EXISTS schedule (
    id_jadwal INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_tahun_ajaran INT(11) UNSIGNED NOT NULL,
    id_kelas INT(11) UNSIGNED NOT NULL,
    id_penugasan INT(11) UNSIGNED NOT NULL,
    id_ruangan INT(11) UNSIGNED DEFAULT NULL,
    hari ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu') NOT NULL,
    slot_ke TINYINT(2) NOT NULL,
    status ENUM('draft', 'approved', 'revised') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_tahun_ajaran) REFERENCES tahun_ajaran(id_tahun_ajaran) ON DELETE CASCADE,
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas) ON DELETE CASCADE,
    FOREIGN KEY (id_penugasan) REFERENCES penugasan_guru(id_penugasan) ON DELETE CASCADE,
    FOREIGN KEY (id_ruangan) REFERENCES ruangan(id_ruangan) ON DELETE SET NULL,
    
    INDEX idx_tahun (id_tahun_ajaran),
    INDEX idx_kelas (id_kelas),
    INDEX idx_hari_slot (hari, slot_ke),
    UNIQUE KEY unique_schedule (id_tahun_ajaran, id_kelas, hari, slot_ke)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel: preferensi (soft constraints untuk GA)
CREATE TABLE IF NOT EXISTS preferensi (
    id_preferensi INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_guru INT(11) UNSIGNED NOT NULL,
    hari ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu') DEFAULT NULL,
    slot_ke TINYINT(2) DEFAULT NULL,
    tipe_preferensi ENUM('suka', 'hindari') NOT NULL,
    prioritas TINYINT(2) DEFAULT 5 COMMENT '1-10',
    keterangan VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_guru) REFERENCES guru(id_guru) ON DELETE CASCADE,
    INDEX idx_guru (id_guru),
    INDEX idx_tipe (tipe_preferensi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default data jam pelajaran (contoh)
INSERT INTO jam_pelajaran (slot_ke, waktu_mulai, waktu_selesai, durasi_menit, is_istirahat) VALUES
(1, '07:00:00', '07:45:00', 45, 0),
(2, '07:45:00', '08:30:00', 45, 0),
(3, '08:30:00', '09:15:00', 45, 0),
(4, '09:15:00', '10:00:00', 45, 0),
(5, '10:00:00', '10:15:00', 15, 1),
(6, '10:15:00', '11:00:00', 45, 0),
(7, '11:00:00', '11:45:00', 45, 0),
(8, '11:45:00', '12:30:00', 45, 0),
(9, '12:30:00', '13:15:00', 45, 0),
(10, '13:15:00', '13:30:00', 15, 1);
