-- Database Seding untuk Modul Autentikasi
-- Sesuai SRS Bab 16.2 - Struktur tabel users

CREATE TABLE IF NOT EXISTS users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    nip VARCHAR(20) DEFAULT NULL,
    role ENUM('admin', 'waka') NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password default: admin123 dan waka123 (hash bcrypt cost 10)
-- Generate hash dengan: password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 10])

INSERT INTO users (username, password, nama_lengkap, nip, role, is_active) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator Sistem', 'ADM001', 'admin', 1),
('waka', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Waka Kurikulum', 'WKA001', 'waka', 1);

-- Catatan: 
-- Password hash di atas adalah contoh untuk 'password' 
-- Ganti dengan hash yang sesuai untuk production
-- Gunakan script PHP berikut untuk generate hash:
-- echo password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 10]);
