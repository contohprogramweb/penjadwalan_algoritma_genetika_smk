# 🤝 Panduan Kontribusi

Terima kasih atas minat Anda untuk berkontribusi pada **Sistem Penjadwalan Guru SMK**! Berikut adalah panduan lengkap untuk berkontribusi.

## 📋 Daftar Isi

1. [Code of Conduct](#code-of-conduct)
2. [Cara Berkontribusi](#cara-berkontribusi)
3. [Standar Coding](#standar-coding)
4. [Pull Request Process](#pull-request-process)
5. [Bug Reporting](#bug-reporting)
6. [Feature Requests](#feature-requests)

---

## Code of Conduct

### Komitmen Kami

Kami berkomitmen untuk menciptakan lingkungan yang terbuka, ramah, dan inklusif. Semua kontributor diharapkan:

- Menggunakan bahasa yang sopan dan profesional
- Menghormati perbedaan pendapat dan perspektif
- Menerima kritik konstruktif dengan lapang dada
- Fokus pada apa yang terbaik untuk komunitas

### Standar Perilaku

**Diterapkan:**
- Empati dan rasa hormat terhadap orang lain
- Memberikan dan menerima umpan balik dengan konstruktif
- Mengakui kesalahan dan belajar darinya

**Tidak Diterapkan:**
- Komentar seksual atau pelecehan
- Trolling atau komentar merendahkan
- Advokasi atau dorongan perilaku berbahaya

---

## Cara Berkontribusi

### 1. Fork Repository

```bash
# Klik tombol "Fork" di GitHub
# Clone fork Anda
git clone https://github.com/YOUR_USERNAME/scheduling-system.git
cd scheduling-system
```

### 2. Buat Branch Baru

```bash
# Untuk fitur baru
git checkout -b feature/nama-fitur-baru

# Untuk bug fix
git checkout -b fix/nama-bug-yang-diperbaiki

# Untuk dokumentasi
git checkout -b docs/perbaikan-dokumentasi
```

### 3. Lakukan Perubahan

- Tulis kode dengan standar yang konsisten
- Tambahkan/konfirmasi testing jika diperlukan
- Update dokumentasi jika ada perubahan API

### 4. Commit Perubahan

```bash
# Gunakan commit message yang deskriptif
git add .
git commit -m "feat: tambah validasi input pada form guru

- Tambah validasi NIP unik
- Tambah validasi format email
- Tambah required field untuk nama lengkap

Closes #123"
```

**Format Commit Message:**
- `feat:` - Fitur baru
- `fix:` - Bug fix
- `docs:` - Dokumentasi
- `style:` - Formatting (tanpa perubahan logika)
- `refactor:` - Refactoring code
- `test:` - Menambah/memperbaiki test
- `chore:` - Maintenance tasks

### 5. Push dan Buat Pull Request

```bash
git push origin feature/nama-fitur-baru
```

Buka Pull Request di GitHub dengan:
- Judul yang jelas dan deskriptif
- Deskripsi perubahan yang detail
- Screenshot (jika ada perubahan UI)
- Referensi issue terkait (jika ada)

---

## Standar Coding

### PHP Standards

Ikuti [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard:

```php
<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class Guru extends Controller
{
    /**
     * Display list of teachers
     * 
     * @return void
     */
    public function index()
    {
        $data['gurus'] = $this->guruModel->findAll();
        
        return view('admin/guru/index', $data);
    }
}
```

**Aturan:**
- Indentasi: 4 spasi (bukan tab)
- Panjang baris maksimal: 120 karakter
- Nama variabel: camelCase
- Nama class: PascalCase
- Nama method: camelCase
- Nama konstanta: UPPER_CASE

### JavaScript Standards

```javascript
// Gunakan const/let, hindari var
const CONFIG = {
    pollingInterval: 2000,
    maxRetries: 3
};

// Arrow functions untuk callback
const handleSuccess = (response) => {
    if (response.status === 'success') {
        showNotification(response.message);
    }
};

// Async/await untuk async operations
async function fetchSchedule(id) {
    try {
        const response = await fetch(`/api/schedule/${id}`);
        return await response.json();
    } catch (error) {
        console.error('Error fetching schedule:', error);
        throw error;
    }
}
```

### Database Standards

```sql
-- Nama tabel: plural, snake_case
CREATE TABLE guru (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(20) NOT NULL UNIQUE,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Index untuk kolom yang sering di-query
CREATE INDEX idx_guru_nip ON guru(nip);
```

### HTML/CSS Standards

```html
<!-- Semantic HTML -->
<main class="container">
    <header class="page-header">
        <h1>Daftar Guru</h1>
    </header>
    
    <section class="content">
        <table class="table table-striped">
            <!-- Table content -->
        </table>
    </section>
</main>
```

```css
/* BEM Naming Convention */
.guru-list { }
.guru-list__item { }
.guru-list__item--active { }
.guru-list__action { }
```

---

## Pull Request Process

### Checklist PR

Sebelum submit PR, pastikan:

- [ ] Kode mengikuti standar coding
- [ ] Tidak ada syntax error
- [ ] Testing lolos (jika ada)
- [ ] Dokumentasi sudah diupdate
- [ ] Commit message deskriptif
- [ ] Branch up-to-date dengan main

### Review Process

1. **Automated Checks**: CI akan menjalankan linting dan testing
2. **Code Review**: Minimal 1 maintainer harus approve
3. **Testing**: QA testing untuk perubahan signifikan
4. **Merge**: Squash merge untuk menjaga history bersih

### Timeline

- Review biasanya selesai dalam 3-5 hari kerja
- Feedback akan diberikan sebagai comment di PR
- Jika tidak ada respons setelah 7 hari, silakan mention maintainer

---

## Bug Reporting

### Template Bug Report

```markdown
**Deskripsi Bug**
Jelaskan bug secara singkat dan jelas.

**Langkah Reproduksi**
1. Buka halaman '...'
2. Klik tombol '...'
3. Scroll ke '...'
4. Lihat error

**Hasil yang Diharapkan**
Jelaskan apa yang seharusnya terjadi.

**Hasil Aktual**
Jelaskan apa yang sebenarnya terjadi.

**Screenshots**
Jika ada, tambahkan screenshot.

**Environment:**
- OS: [e.g., Windows 10, macOS 11]
- Browser: [e.g., Chrome 91, Firefox 89]
- PHP Version: [e.g., 7.4.20]
- MySQL Version: [e.g., 8.0.25]

**Additional Context**
Informasi tambahan lainnya.
```

### Prioritas Bug

- 🔴 **Critical**: Sistem down, data corruption
- 🟠 **High**: Fitur utama tidak berfungsi
- 🟡 **Medium**: Fitur minor bermasalah
- 🟢 **Low**: Cosmetic issues, typos

---

## Feature Requests

### Template Feature Request

```markdown
**Is your feature request related to a problem?**
Jelaskan masalah yang ingin diselesaikan.

**Describe the solution you'd like**
Jelaskan solusi yang diinginkan.

**Describe alternatives you've considered**
Jelaskan alternatif solusi yang pernah dipertimbangkan.

**Additional context**
Screenshot, mockup, atau referensi lainnya.
```

### Feature Priority

Fitur akan diprioritaskan berdasarkan:
- Dampak terhadap pengguna
- Kompleksitas implementasi
- Ketersediaan resources
- Alignment dengan roadmap

---

## Development Setup

### Prerequisites

- PHP 7.4+
- Composer 2.x
- MySQL 5.7+ / MariaDB 10.3+
- Node.js 14+ (optional, untuk assets)

### Quick Start

```bash
# Clone repository
git clone https://github.com/original-repo/scheduling-system.git
cd scheduling-system

# Install dependencies
composer install

# Setup environment
cp .env.example .env

# Create database
mysql -u root -p -e "CREATE DATABASE scheduling_smk"

# Run migrations (jika ada)
php index.php migrate

# Start development server
php -S localhost:8000 -t public/
```

---

## Testing

### Running Tests

```bash
# Unit tests
vendor/bin/phpunit

# With coverage
vendor/bin/phpunit --coverage-html build/coverage

# Specific test file
vendor/bin/phpunit tests/GuruTest.php
```

### Writing Tests

```php
<?php
namespace Tests;

use CodeIgniter\Test\CIUnitTestCase;

class GuruTest extends CIUnitTestCase
{
    public function testCreateGuru()
    {
        $guruModel = new \App\Models\Guru();
        
        $data = [
            'nip' => '123456789',
            'nama_lengkap' => 'Test Guru'
        ];
        
        $result = $guruModel->insert($data);
        
        $this->seeInDatabase('guru', ['nip' => '123456789']);
    }
}
```

---

## Documentation

### Updating Docs

- README.md: Overview dan quick start
- INSTALL.md: Instalasi detail
- CONTRIBUTING.md: Panduan kontribusi (file ini)
- Inline comments: Untuk kompleks logic

### Documentation Standards

- Gunakan Bahasa Indonesia yang baik dan benar
- Sertakan contoh code snippet
- Update changelog untuk setiap versi
- Screenshot untuk UI changes

---

## Questions?

Jika ada pertanyaan, silakan:

1. Cek dokumentasi yang ada
2. Cari di Issues yang sudah ada
3. Buat Discussion baru di GitHub
4. Hubungi maintainer via email

---

## Recognition

Kontributor akan diakui di:
- README.md (Contributors section)
- Release notes
- Website (jika ada)

---

**Terima kasih atas kontribusi Anda!** 🎉

Versi: 1.0.0  
Last Updated: 2024
