# 🏫 Sistem Penjadwalan Guru SMK Berbasis Algoritma Genetika

[![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://www.php.net/)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-3.1-red)](https://codeigniter.com/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-4.6-purple)](https://getbootstrap.com/)
[![SRS](https://img.shields.io/badge/SRS-v3-green)](./SRS_Penjadwalan_SMK_v3_UIUX_Dev.docx)

Sistem informasi berbasis web untuk menghasilkan jadwal pelajaran otomatis di SMK menggunakan **Algoritma Genetika (Genetic Algorithm)**. Dirancang khusus untuk membantu Waka Kurikulum dalam menyusun jadwal yang memenuhi semua constraint (hard & soft constraints) secara efisien.

---

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Teknologi](#-teknologi)
- [Arsitektur Sistem](#-arsitektur-sistem)
- [Instalasi](#-instalasi)
- [Konfigurasi Database](#-konfigurasi-database)
- [Penggunaan](#-penggunaan)
- [Struktur Direktori](#-struktur-direktori)
- [API Endpoints](#-api-endpoints)
- [Algoritma Genetika](#-algoritma-genetika)
- [Constraint Validation](#-constraint-validation)
- [User Roles](#-user-roles)
- [Testing](#-testing)
- [Troubleshooting](#-troubleshooting)
- [Lisensi](#-lisensi)

---

## ✨ Fitur Utama

### 🔐 Autentikasi & Keamanan
- Login multi-role (Admin & Waka Kurikulum)
- CSRF Protection pada semua POST request
- Password hashing dengan bcrypt (cost 10)
- Session-based authentication
- Role-based access control (RBAC)

### 👤 Modul Admin (Master Data)
- **Manajemen Guru**: CRUD guru dengan NIP, mata pelajaran, dan jam maksimal
- **Manajemen Mata Pelajaran**: CRUD mapel teori dan praktikum
- **Manajemen Kelas**: CRUD kelas dengan kapasitas siswa
- **Manajemen Ruangan**: CRUD ruangan dengan tipe (regular/lab)
- **Manajemen Tahun Ajaran**: Setup tahun ajaran aktif
- **Manajemen Jam Pelajaran**: Definisi slot waktu per hari
- **Onboarding Wizard**: Panduan setup data awal bertahap

### 📅 Modul Waka Kurikulum
- **Penugasan Guru**: Assign guru ke kelas + mapel + alokasi jam/minggu
- **Generate Jadwal**: Eksekusi Algoritma Genetika dengan progress real-time
- **Jadwal Interaktif**: Grid jadwal dengan filter kelas/guru/ruangan
- **Review & Validasi**: Cek konflik sebelum publikasi
- **Publish Jadwal**: Aktivasi jadwal untuk tahun ajaran tertentu

### 📊 Laporan & Export
- Export jadwal ke PDF (menggunakan Dompdf)
- Export jadwal ke Excel/CSV
- Cetak jadwal per kelas, guru, atau ruangan
- Statistik penggunaan ruangan dan beban mengajar guru

### 🎯 Algoritma Genetika (GA)
- **Representasi Kromosom**: Array 2D [hari][slot] = id_penugasan
- **Seleksi**: Tournament Selection (size=5)
- **Crossover**: Order-based Crossover (rate=0.8)
- **Mutasi**: Swap Slot Mutation (rate=0.1)
- **Elitisme**: Mempertahankan 2 individu terbaik
- **Populasi**: 50 individu per generasi
- **Maks Generasi**: 500 (dengan early stopping jika konvergen)

---

## 🛠️ Teknologi

| Komponen | Versi | Keterangan |
|----------|-------|------------|
| **Backend** | PHP 7.4+ | Server-side scripting |
| **Framework** | CodeIgniter 3.1.13 | MVC framework (legacy but stable) |
| **Database** | MySQL 5.7+ / MariaDB 10.3+ | Relational database |
| **Frontend** | HTML5, CSS3, JavaScript | Responsive UI |
| **CSS Framework** | Bootstrap 4.6.1 | UI components & grid system |
| **JavaScript Library** | jQuery 3.6.0 | DOM manipulation & AJAX |
| **DataTables** | 1.11.5 | Server-side processing tables |
| **Chart Library** | Chart.js 3.9.1 | Dashboard visualizations |
| **PDF Engine** | Dompdf 1.2.2 | PDF generation |
| **Icons** | Font Awesome 5.15.4 | Icon library |
| **SweetAlert2** | 11.4.0 | Beautiful alerts |

---

## 🏗️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────┐
│                      USER INTERFACE                         │
│  (Bootstrap 4 + jQuery + DataTables + Chart.js)             │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    CONTROLLER LAYER                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Admin/*    │  │   Waka/*     │  │   Auth.php   │      │
│  │  (CRUD)      │  │  (Schedule)  │  │   (Login)    │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                   BUSINESS LOGIC LAYER                      │
│  ┌──────────────────────────────────────────────────────┐  │
│  │           GA_Scheduler.php (Algoritma Genetika)      │  │
│  │  - Inisialisasi Populasi                             │  │
│  │  - Fitness Calculation (Hard & Soft Constraints)     │  │
│  │  - Tournament Selection                              │  │
│  │  - Order-based Crossover                             │  │
│  │  - Swap Slot Mutation                                │  │
│  │  - Elitism & Convergence Detection                   │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    DATA ACCESS LAYER                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Models     │  │  Libraries   │  │   Helpers    │      │
│  │  (9 models)  │  │  (GA, PDF)   │  │  (Auth, etc) │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                      DATABASE (MySQL)                       │
│  users, guru, mapel, kelas, ruangan, tahun_ajaran,         │
│  jam_pelajaran, penugasan, jadwal                          │
└─────────────────────────────────────────────────────────────┘
```

---

## 📦 Instalasi

### Prasyarat
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau MariaDB 10.3+
- Composer (opsional, untuk dependency management)
- Web server (Apache/Nginx) dengan mod_rewrite enabled
- Extension PHP: `mysqli`, `json`, `mbstring`, `openssl`, `dom`

### Langkah Instalasi

#### 1. Clone Repository
```bash
git clone <repository-url> penjadwalan-smk
cd penjadwalan-smk
```

#### 2. Setup Database
```bash
# Buat database baru
mysql -u root -p -e "CREATE DATABASE penjadwalan_smk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import struktur tabel dan data seeding
mysql -u root -p penjadwalan_smk < database_seeding.sql
```

#### 3. Konfigurasi CodeIgniter

Buat file `application/config/database.php`:
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
    'dsn'   => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => 'your_password',
    'database' => 'penjadwalan_smk',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_unicode_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);
```

#### 4. Set Base URL

Edit `application/config/config.php`:
```php
$config['base_url'] = 'http://localhost/penjadwalan-smk/';
$config['index_page'] = ''; // Hilangkan index.php dari URL
```

#### 5. Set Permissions
```bash
# Linux/Mac
chmod -R 755 /path/to/penjadwalan-smk
chmod -R 777 /path/to/penjadwalan-smk/application/logs
chmod -R 777 /path/to/penjadwalan-smk/application/cache

# Windows (via GUI): Klik kanan folder > Properties > Security > Edit
```

#### 6. Enable Apache mod_rewrite (Optional)
Aktifkan file `.htaccess` di root directory untuk clean URLs:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
```

#### 7. Akses Aplikasi
Buka browser dan akses:
```
http://localhost/penjadwalan-smk
```

**Login Default:**
| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin123` |
| Waka | `waka` | `waka123` |

⚠️ **PENTING**: Ganti password default segera setelah instalasi pertama!

---

## 🗄️ Konfigurasi Database

### Tabel Database

| Tabel | Deskripsi | Jumlah Record Default |
|-------|-----------|----------------------|
| `users` | Data pengguna sistem | 2 (admin, waka) |
| `guru` | Data guru | 0 (input via UI) |
| `mapel` | Mata pelajaran | 0 (input via UI) |
| `kelas` | Data kelas | 0 (input via UI) |
| `ruangan` | Data ruangan | 0 (input via UI) |
| `tahun_ajaran` | Tahun ajaran | 0 (input via UI) |
| `jam_pelajaran` | Slot waktu | 0 (input via UI) |
| `penugasan` | Assignment guru ke kelas+mapel | 0 (input via UI) |
| `jadwal` | Hasil generate jadwal | 0 (auto-generated) |

### ERD (Entity Relationship Diagram)

```
users (1) ──────< (N) Tidak ada relasi langsung

guru (1) ──────< (N) penugasan
mapel (1) ──────< (N) penugasan
kelas (1) ──────< (N) penugasan
tahun_ajaran (1) ──────< (N) penugasan

penugasan (1) ──────< (N) jadwal
jam_pelajaran (1) ──────< (N) jadwal
ruangan (1) ──────< (N) jadwal
```

---

## 🚀 Penggunaan

### Flow Kerja Sistem

```
Login Admin → Setup Master Data → Setup Tahun Ajaran → Setup Jam Pelajaran
                ↓
Login Waka → Input Penugasan → Generate Jadwal (GA) → Review → Publish
```

### 1. Onboarding (Admin)
Gunakan wizard onboarding untuk setup data awal secara bertahap:
1. Setup tahun ajaran aktif
2. Input data guru
3. Input mata pelajaran
4. Input data kelas
5. Input data ruangan
6. Setup jam pelajaran per hari

### 2. Penugasan Guru (Waka)
Assign guru untuk mengajar:
- Pilih kelas, mata pelajaran, dan guru
- Tentukan alokasi jam per minggu
- Set preferensi (opsional): hari tidak tersedia, jam preferensi

### 3. Generate Jadwal (Waka)
Proses generate otomatis:
1. Buka menu **Generate Jadwal**
2. Pastikan semua checklist hijau (data lengkap)
3. Klik tombol **Generate Jadwal**
4. Tunggu progress bar (real-time polling setiap 2 detik)
5. Lihat hasil: jumlah generasi, fitness score, waktu eksekusi

### 4. Review & Publish (Waka)
Setelah generate selesai:
- Review jadwal di grid interaktif
- Filter berdasarkan kelas, guru, atau ruangan
- Cek konflik (jika ada)
- Publish jadwal untuk digunakan

---

## 📁 Struktur Direktori

```
penjadwalan-smk/
├── application/
│   ├── config/
│   │   ├── config.php              # Konfigurasi aplikasi & Dompdf
│   │   └── database.php            # Konfigurasi database (buat manual)
│   ├── controllers/
│   │   ├── Admin/
│   │   │   ├── Dashboard.php       # Dashboard admin
│   │   │   ├── Guru.php            # CRUD guru
│   │   │   ├── Mapel.php           # CRUD mapel
│   │   │   ├── Kelas.php           # CRUD kelas
│   │   │   ├── Ruangan.php         # CRUD ruangan
│   │   │   ├── Tahun_ajaran.php    # CRUD tahun ajaran
│   │   │   ├── Jam.php             # CRUD jam pelajaran
│   │   │   └── Onboarding.php      # Wizard onboarding
│   │   ├── Waka/
│   │   │   ├── Dashboard.php       # Dashboard waka
│   │   │   ├── Penugasan.php       # Manajemen penugasan
│   │   │   ├── Generate.php        # Generate jadwal (GA)
│   │   │   └── Jadwal.php          # Review & publish jadwal
│   │   ├── Auth.php                # Login/logout
│   │   ├── Datatables.php          # Server-side DataTables handler
│   │   ├── Jadwal.php              # Public view jadwal
│   │   └── Laporan.php             # Export PDF/Excel
│   ├── core/
│   │   └── MY_Controller.php       # Base controller dengan auth check
│   ├── libraries/
│   │   └── GA_Scheduler.php        # Library Algoritma Genetika (27KB)
│   ├── models/
│   │   ├── Guru_model.php          # Model guru
│   │   ├── Mapel_model.php         # Model mapel
│   │   ├── Kelas_model.php         # Model kelas
│   │   ├── Ruangan_model.php       # Model ruangan
│   │   ├── Tahun_ajaran_model.php  # Model tahun ajaran
│   │   ├── Jam_model.php           # Model jam pelajaran
│   │   ├── Penugasan_model.php     # Model penugasan
│   │   ├── Jadwal_model.php        # Model jadwal
│   │   └── User_model.php          # Model user
│   ├── views/
│   │   ├── layouts/
│   │   │   └── main.php            # Layout utama
│   │   ├── admin/                  # Views modul admin
│   │   ├── waka/                   # Views modul waka
│   │   ├── errors/                 # Custom error pages (403, 404, 500)
│   │   └── auth/                   # Login page
│   └── helpers/
│       └── auth_helper.php         # Helper fungsi autentikasi
├── assets/
│   ├── css/
│   │   └── style.css               # Custom styles
│   └── js/
│       ├── app.js                  # Global JS (AJAX setup, utilities)
│       └── pages/
│           ├── generate.js         # Logic generate jadwal
│           ├── jadwal-grid.js      # Grid jadwal interaktif
│           └── onboarding.js       # Wizard onboarding
├── database_seeding.sql            # SQL dump database
├── .htaccess                       # Apache rewrite rules
├── index.php                       # Entry point CodeIgniter
└── README.md                       # Dokumentasi ini
```

---

## 🌐 API Endpoints

### Authentication
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/auth/login` | Login user |
| POST | `/auth/logout` | Logout user |

### Admin Module
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/admin/guru` | List guru (DataTables) |
| POST | `/admin/guru/save` | Simpan guru (create/update) |
| DELETE | `/admin/guru/delete/{id}` | Hapus guru |
| GET | `/admin/mapel` | List mata pelajaran |
| POST | `/admin/mapel/save` | Simpan mapel |
| GET | `/admin/kelas` | List kelas |
| POST | `/admin/kelas/save` | Simpan kelas |
| GET | `/admin/ruangan` | List ruangan |
| POST | `/admin/ruangan/save` | Simpan ruangan |
| GET | `/admin/tahun_ajaran` | List tahun ajaran |
| POST | `/admin/tahun_ajaran/save` | Simpan tahun ajaran |
| GET | `/admin/jam` | List jam pelajaran |
| POST | `/admin/jam/save` | Simpan jam pelajaran |

### Waka Module
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/waka/penugasan` | List penugasan |
| POST | `/waka/penugasan/save` | Simpan penugasan |
| POST | `/waka/generate/trigger` | **Start GA process** |
| GET | `/waka/generate/progress` | **Get GA progress (polling)** |
| GET | `/waka/jadwal` | Review grid jadwal |
| POST | `/waka/jadwal/publish` | Publish jadwal |

### Response Format (JSON)
```json
{
  "status": "success",
  "message": "Operation completed",
  "data": { ... }
}
```

**Generate Progress Response:**
```json
{
  "is_generating": true,
  "persen": 45,
  "generasi": 225,
  "generasi_maks": 500,
  "fitness_terbaik": 0.87,
  "waktu_berjalan": "00:02:15",
  "status": "running",
  "pesan": "Sedang melakukan crossover..."
}
```

---

## 🧬 Algoritma Genetika

### Overview
Library `GA_Scheduler.php` mengimplementasikan Algoritma Genetika untuk optimasi penjadwalan dengan detail sebagai berikut:

### Representasi Kromosom
```php
// Kromosom: Array 2D [hari][slot] = id_penugasan
[
  [1, 5, 3, 8, 2, 7, 4, 6],  // Senin (8 slot)
  [3, 1, 7, 2, 8, 4, 6, 5],  // Selasa
  [5, 2, 8, 1, 4, 6, 3, 7],  // Rabu
  ...
]
```

### Hard Constraints (WC - Wajib Dipenuhi)
| Kode | Constraint | Penalty |
|------|-----------|---------|
| HC-01 | Guru tidak double-booked di slot sama | ∞ (invalid) |
| HC-02 | Kelas tidak double-booked di slot sama | ∞ (invalid) |
| HC-03 | Ruangan tidak double-booked di slot sama | ∞ (invalid) |
| HC-04 | Jam mengajar guru ≤ jam maks/minggu | ∞ (invalid) |
| HC-05 | Kelas hanya mendapat mapel sesuai penugasan | ∞ (invalid) |

### Soft Constraints (SC - Preferensi)
| Kode | Constraint | Weight |
|------|-----------|--------|
| SC-01 | Hindari violating preferensi guru | 10 |
| SC-02 | Penempatan mapel praktikum di lab | 5 |
| SC-03 | Distribusi jam merata dalam seminggu | 3 |
| SC-04 | Minimalkan gap antar jam guru | 2 |

### Fitness Function
```php
fitness = (Σ soft_constraints_fulfilled × weight) / max_possible_score
// Range: 0.0 - 1.0 (1.0 = perfect schedule)
```

### Operator Genetika

#### 1. Seleksi: Tournament Selection
```php
// Pilih 5 individu acak, ambil yang fitness tertinggi
private function _tournament_selection() {
    $candidates = array_rand($this->populasi, $this->tournament_size);
    // Return index dengan fitness terbaik
}
```

#### 2. Crossover: Order-based Crossover (OX)
```php
// Pilih 2 cut points, salin segment dari parent1, isi sisa dari parent2
private function _order_crossover($parent1, $parent2) {
    // Implementasi OX untuk maintain validitas kromosom
}
```

#### 3. Mutasi: Swap Slot Mutation
```php
// Tukar 2 slot acak dalam kromosom
private function _swap_mutation(&$kromosom) {
    $pos1 = rand(0, total_slots-1);
    $pos2 = rand(0, total_slots-1);
    // Swap values
}
```

### Parameter Default
```php
$populasi_size = 50;        // Individu per generasi
$generasi_maks = 500;       // Maksimum iterasi
$tournament_size = 5;       // Peserta turnamen
$crossover_rate = 0.8;      // 80% kemungkinan crossover
$mutation_rate = 0.1;       // 10% kemungkinan mutasi
$elitism_count = 2;         // Top 2 dipertahankan
```

### Early Stopping
Algoritma berhenti jika:
1. Mencapai `generasi_maks` (500), ATAU
2. Fitness tidak membaik selama 50 generasi berturut-turut (konvergensi)

---

## ✅ Constraint Validation

### Validasi Input (Client-Side)
Semua form menggunakan validasi Bootstrap + custom JavaScript:
- Required fields validation
- Numeric range validation
- Unique constraint checking (AJAX)
- Real-time feedback dengan SweetAlert2

### Validasi Business Logic (Server-Side)
Setiap operasi CRUD divalidasi di Controller:
```php
// Contoh: Validasi penugasan ganda
if ($this->Penugasan_model->check_duplicate($kelas, $mapel, $guru)) {
    return $this->output->set_json([
        'status' => 'error',
        'message' => 'Penugasan ganda terdeteksi!'
    ]);
}
```

### Validasi Jadwal (Post-Generate)
Setelah GA selesai, sistem melakukan validasi akhir:
- Cek semua hard constraints terpenuhi
- Hitung persentase soft constraints fulfilled
- Generate conflict report (jika ada)

---

## 👥 User Roles

### Administrator (Admin)
**Akses Penuh ke Master Data:**
- ✅ Dashboard statistik
- ✅ CRUD Guru, Mapel, Kelas, Ruangan
- ✅ Setup Tahun Ajaran & Jam Pelajaran
- ✅ Onboarding Wizard
- ❌ Tidak dapat generate jadwal (hanya Waka)

### Wakil Kepala Kurikulum (Waka)
**Fokus pada Penjadwalan:**
- ✅ Dashboard ringkasan penjadwalan
- ✅ Manajemen Penugasan Guru
- ✅ **Generate Jadwal (Algoritma Genetika)**
- ✅ Review & Publish Jadwal
- ✅ Export/Cetak Jadwal
- ❌ Tidak dapat akses master data (hanya Admin)

---

## 🧪 Testing

### Manual Testing Checklist

#### Authentication
- [ ] Login dengan kredensial benar → redirect sesuai role
- [ ] Login dengan kredensial salah → alert error
- [ ] Logout → redirect ke login page
- [ ] Akses halaman tanpa login → redirect ke login

#### Admin Module
- [ ] CRUD Guru (create, read, update, delete)
- [ ] CRUD Mapel (termasuk tipe teori/praktikum)
- [ ] CRUD Kelas (dengan kapasitas)
- [ ] CRUD Ruangan (dengan tipe regular/lab)
- [ ] CRUD Tahun Ajaran (set active/inactive)
- [ ] CRUD Jam Pelajaran (setup slot per hari)
- [ ] Onboarding wizard (step-by-step)

#### Waka Module
- [ ] Input penugasan guru
- [ ] Validasi penugasan ganda
- [ ] Generate jadwal (tombol disabled jika data kurang)
- [ ] Progress bar real-time (polling 2 detik)
- [ ] Alert sukses/gagal setelah generate
- [ ] Redirect otomatis ke review page (3 detik)
- [ ] Grid jadwal interaktif (filter kelas/guru/ruangan)
- [ ] Export PDF jadwal
- [ ] Publish jadwal

#### Algoritma Genetika
- [ ] Generate dengan data minimal (1 kelas, 1 guru)
- [ ] Generate dengan data lengkap (10+ kelas)
- [ ] Konvergensi terdeteksi (early stopping)
- [ ] Fitness score meningkat per generasi
- [ ] Tidak ada infinite loop

### Automated Testing (Future Enhancement)
```bash
# PHPUnit integration (belum diimplementasikan)
vendor/bin/phpunit tests/
```

---

## 🔧 Troubleshooting

### Error Umum & Solusi

#### 1. "Database Connection Error"
**Penyebab:** File `database.php` belum dibuat atau konfigurasi salah.
**Solusi:**
```php
// Pastikan file application/config/database.php ada
// Cek username, password, dan nama database
```

#### 2. "CSRF Token Mismatch"
**Penyebab:** Meta tag CSRF tidak match dengan yang diharapkan app.js.
**Solusi:** Sudah diperbaiki di audit. Pastikan meta tag:
```html
<meta name="csrf_token_name" content="<?= $this->security->get_csrf_token_name(); ?>">
<meta name="csrf_token_hash" content="<?= $this->security->get_csrf_token_hash(); ?>">
```

#### 3. "Generate Jadwal Timeout"
**Penyebab:** Data terlalu besar atau server lambat.
**Solusi:**
- Tingkatkan `max_execution_time` di php.ini
- Kurangi `$generasi_maks` di GA_Scheduler.php
- Optimasi query database dengan indexing

#### 4. "Class 'GA_Scheduler' not found"
**Penyebab:** Library belum di-load.
**Solusi:**
```php
$this->load->library('GA_Scheduler');
```

#### 5. "Mod Rewrite Not Working"
**Penyebab:** Apache mod_rewrite belum aktif.
**Solusi:**
```bash
# Ubuntu/Debian
sudo a2enmod rewrite
sudo systemctl restart apache2

# Atau hapus index.php dari config.php
$config['index_page'] = 'index.php';
```

### Debug Mode
Untuk development, aktifkan debug mode:
```php
// application/config/config.php
$config['environment'] = 'development';
$config['log_threshold'] = 4; // Log semua message

// application/config/database.php
$db['default']['db_debug'] = TRUE;
```

### Log Files
Cek log file untuk debugging:
```bash
tail -f application/logs/log-YYYY-MM-DD.php
```

---

## 📝 Changelog

### Version 1.0.0 (2025) - Initial Release
- ✅ Implementasi SRS v3 (100% fitur)
- ✅ Modul Admin (7 CRUD + Onboarding)
- ✅ Modul Waka (Penugasan, Generate, Jadwal)
- ✅ Algoritma Genetika lengkap
- ✅ Export PDF dengan Dompdf
- ✅ Responsive UI dengan Bootstrap 4
- ✅ CSRF Protection & Security fixes
- ✅ Polling real-time untuk generate progress
- ✅ Audit compliance (all critical & medium issues fixed)

---

## 🤝 Kontribusi

Jika ingin berkontribusi:
1. Fork repository ini
2. Buat branch fitur (`git checkout -b feature/amazing-feature`)
3. Commit perubahan (`git commit -m 'Add amazing feature'`)
4. Push ke branch (`git push origin feature/amazing-feature`)
5. Buat Pull Request

### Coding Standards
- PSR-12 untuk PHP code style
- Gunakan type hinting untuk parameter function
- Comment semua method dengan PHPDoc
- Write unit tests untuk fitur baru

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

## 📞 Kontak & Support

**Developer:** Waka Kurikulum System  
**Email:** support@smk-system.sch.id  
**Documentation:** [SRS v3 Document](./SRS_Penjadwalan_SMK_v3_UIUX_Dev.docx)

---

## 🙏 Acknowledgments

- CodeIgniter Framework
- Bootstrap Team
- Dompdf Project
- All contributors to this project

---

<div align="center">

**Dibuat dengan ❤️ untuk Pendidikan Indonesia**

© 2025 Sistem Penjadwalan Guru SMK

</div>
