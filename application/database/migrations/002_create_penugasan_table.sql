-- Database Schema untuk Modul Penugasan Guru
-- Sesuai SRS Bab 11.6 - Modul Penugasan (Waka Kurikulum)

-- Tabel penugasan_guru: relasi guru ↔ kelas ↔ mapel
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
    FOREIGN KEY (id_guru) REFERENCES guru(id_guru) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_mapel) REFERENCES mata_pelajaran(id_mapel) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_ruangan) REFERENCES ruangan(id_ruangan) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (id_tahun_ajaran) REFERENCES tahun_ajaran(id_tahun_ajaran) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Indexes untuk performa
    INDEX idx_guru (id_guru),
    INDEX idx_mapel (id_mapel),
    INDEX idx_kelas (id_kelas),
    INDEX idx_tahun_ajaran (id_tahun_ajaran),
    INDEX idx_semester (semester),
    
    -- Unique constraint: satu guru tidak boleh diassign mapel yang sama ke kelas yang sama
    UNIQUE KEY unique_penugasan (id_guru, id_mapel, id_kelas, id_tahun_ajaran, semester)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Catatan:
-- - is_praktikum = 1 jika mapel memerlukan ruangan khusus (lab, bengkel, dll)
-- - id_ruangan wajib diisi jika is_praktikum = 1
-- - jam_per_minggu diisi sesuai alokasi waktu mapel di kelas tersebut
-- - Unique constraint mencegah duplikasi penugasan guru-mapel-kelas
