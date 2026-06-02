# 📦 Panduan Instalasi & Konfigurasi

## Prasyarat Sistem

- **PHP**: Versi 7.4 atau lebih tinggi
- **Web Server**: Apache dengan mod_rewrite atau Nginx
- **Database**: MySQL 5.7+ atau MariaDB 10.3+
- **Composer**: Versi 2.x (untuk dependency management)
- **Node.js** (opsional): Untuk minifikasi assets
- **Extensions PHP**: mysqli, mbstring, json, xml, gd, zip

---

## 🚀 Langkah Instalasi

### 1. Clone Repository

```bash
git clone <repository-url> scheduling-system
cd scheduling-system
```

### 2. Install Dependencies via Composer

```bash
composer install --no-dev --optimize-autoloader
```

Untuk development:

```bash
composer install --optimize-autoloader
```

### 3. Setup Environment Variables

```bash
# Copy file contoh
cp .env.example .env

# Edit file .env sesuai kebutuhan
nano .env
```

### 4. Konfigurasi Database

```bash
# Buat database baru
mysql -u root -p
CREATE DATABASE scheduling_smk CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
EXIT;

# Import schema (jika ada file SQL)
mysql -u root -p scheduling_smk < database/schema.sql
```

### 5. Konfigurasi CodeIgniter

Edit `application/config/config.php`:

```php
$config['base_url'] = 'http://localhost/scheduling-system/';
$config['encryption_key'] = 'YourRandomSecretKey_32CharactersMinimum!';
```

Edit `application/config/database.php` (copy dari database.php.example):

```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => 'your_password',
    'database' => 'scheduling_smk',
);
```

### 6. Set Permissions

```bash
# Linux/Mac
chmod -R 755 ./
chmod -R 777 application/logs/
chmod -R 777 application/cache/
chmod -R 777 application/sessions/

# Windows (PowerShell)
icacls application\logs /grant Everyone:F
icacls application\cache /grant Everyone:F
icacls application\sessions /grant Everyone:F
```

### 7. Enable Apache mod_rewrite (Jika Menggunakan Apache)

```bash
# Ubuntu/Debian
sudo a2enmod rewrite
sudo systemctl restart apache2

# Edit Apache VirtualHost
<Directory "/var/www/html/scheduling-system">
    AllowOverride All
    Require all granted
</Directory>
```

### 8. Akses Aplikasi

Buka browser dan akses:

```
http://localhost/scheduling-system
```

Login default:
- **Username**: admin
- **Password**: admin123

---

## 🔧 Troubleshooting

### Error: "The encryption key is missing"

**Solusi**: Pastikan `$config['encryption_key']` di `config.php` sudah diisi dengan minimal 32 karakter.

### Error: "Unable to connect to the database"

**Solusi**: 
1. Periksa kredensial di `application/config/database.php`
2. Pastikan MySQL service berjalan
3. Verifikasi database sudah dibuat

### Error: "CSRF token mismatch"

**Solusi**:
1. Clear browser cookies
2. Pastikan `$config['csrf_protection'] = TRUE`
3. Cek meta tag CSRF di `application/views/layouts/main.php`

### Error: "Permission denied" pada logs/cache

**Solusi**: Jalankan perintah `chmod` seperti di langkah 6.

---

## 📝 Update Aplikasi

```bash
# Pull perubahan terbaru
git pull origin main

# Update dependencies
composer update --no-dev --optimize-autoloader

# Clear cache
rm -rf application/cache/*

# Migrate database (jika ada)
php index.php migrate
```

---

## 🧪 Testing (Development Only)

```bash
# Install dev dependencies
composer install

# Run unit tests
composer test

# Run linting
composer lint
```

---

## 📞 Support

Jika mengalami masalah, silakan:

1. Cek log file di `application/logs/`
2. Baca dokumentasi di README.md
3. Buka issue di repository GitHub

---

**Versi Dokumentasi**: 1.0.0  
**Terakhir Diupdate**: 2024
