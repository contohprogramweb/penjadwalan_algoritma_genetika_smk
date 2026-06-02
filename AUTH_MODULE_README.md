# Modul Autentikasi - CodeIgniter 3

## Ringkasan Implementasi

Modul autentikasi lengkap sesuai SRS V3.0 Bab 11.4, 16.1, 16.2, dan 16.4.

## Struktur File

```
application/
├── core/
│   └── MY_Controller.php          # Base controller dengan auth & role check
├── controllers/
│   ├── Auth.php                   # Login, proses_login, logout
│   ├── Admin/
│   │   └── Dashboard.php          # Dashboard admin (role: admin)
│   └── Waka/
│       └── Dashboard.php          # Dashboard waka (role: waka)
├── helpers/
│   └── auth_helper.php            # Helper functions: is_logged_in(), current_role(), redirect_by_role()
├── models/
│   └── User_model.php             # Model untuk operasi user
└── views/
    └── auth/
        └── login.php              # Halaman login (UI sesuai SRS 11.4)
```

## Fitur Utama

### 1. MY_Controller (Bab 16.1)
- ✅ Cek session `logged_in`
- ✅ Properti `protected $allowed_roles = []`
- ✅ Auto redirect ke login jika belum authenticated
- ✅ Show error 403 jika role tidak sesuai

### 2. Auth Controller (Bab 11.4, 16.2)
- ✅ Method `login()` - Tampilkan form login
- ✅ Method `proses_login()` - Validasi & set session
  - CSRF validation (Bab 16.4)
  - Server-side validation
  - Password verification dengan bcrypt
  - Session regeneration
  - Session payload lengkap sesuai Bab 16.2
- ✅ Method `logout()` - Destroy session

### 3. Auth Helper (Bab 16.3)
- ✅ `redirect_by_role()` - Redirect berdasarkan role
- ✅ `is_logged_in()` - Cek status login
- ✅ `current_role()` - Dapatkan role user
- ✅ `current_user()` - Dapatkan data user lengkap
- ✅ `has_role()` - Cek role spesifik

### 4. Login View (Bab 11.4)
- ✅ Card width 420px
- ✅ Toggle password visibility
- ✅ CSRF token hidden field
- ✅ Spinner pada tombol saat submitting
- ✅ State management:
  - **Default**: Button enabled, no spinner
  - **Submitting**: Button disabled + spinner visible
  - **Error**: Button enabled + alert merah
- ✅ Auto-hide error saat user mengetik
- ✅ Responsive design
- ✅ Accessibility (ARIA labels)

### 5. Dashboard Controllers
- ✅ `Admin/Dashboard.php` - `allowed_roles = ['admin']`
- ✅ `Waka/Dashboard.php` - `allowed_roles = ['waka']`

## Session Payload (Bab 16.2)

```php
$session_data = [
    'logged_in' => TRUE,
    'user_id' => $user->id,
    'username' => $user->username,
    'nama_lengkap' => $user->nama_lengkap,
    'role' => $user->role,      // 'admin' atau 'waka'
    'nip' => $user->nip,
    'login_time' => date('Y-m-d H:i:s'),
    'last_activity' => time()
];
```

## Database Schema

File: `database_seeding.sql`

```sql
CREATE TABLE users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    nip VARCHAR(20) DEFAULT NULL,
    role ENUM('admin', 'waka') NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Default Users (Development)

| Username | Password  | Role  | Nama Lengkap         |
|----------|-----------|-------|----------------------|
| admin    | admin123  | admin | Administrator Sistem |
| waka     | waka123   | waka  | Waka Kurikulum       |

**PENTING**: Ganti password default sebelum production!

## Cara Menggunakan

### 1. Setup Database
```bash
mysql -u root -p database_name < database_seeding.sql
```

### 2. Generate Password Hash (Production)
```php
echo password_hash('your_password', PASSWORD_BCRYPT, ['cost' => 10]);
```

### 3. Akses Halaman Login
```
http://localhost/your_app/auth/login
```

### 4. Extend MY_Controller di Controller Baru
```php
require_once APPPATH . 'core/MY_Controller.php';

class Some_controller extends MY_Controller
{
    protected $allowed_roles = ['admin']; // atau ['waka'], ['admin', 'waka']
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        // Hanya bisa diakses oleh role yang ditentukan
    }
}
```

### 5. Gunakan Helper di View
```php
<?php if (is_logged_in()): ?>
    <p>Halo, <?= current_user()['nama_lengkap'] ?></p>
    <?php if (has_role('admin')): ?>
        <!-- Konten khusus admin -->
    <?php endif; ?>
<?php else: ?>
    <a href="<?= site_url('auth/login') ?>">Login</a>
<?php endif; ?>
```

## API Response Format

### Login Success
```json
{
    "success": true,
    "message": "Login berhasil",
    "redirect_url": "http://localhost/app/admin/dashboard"
}
```

### Login Failed
```json
{
    "success": false,
    "message": "Username atau password salah"
}
```

### Validation Error
```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": "Username harus diisiPassword harus diisi"
}
```

## Keamanan

- ✅ CSRF Protection (Bab 16.4)
- ✅ Password hashing dengan bcrypt
- ✅ Session regeneration setelah login
- ✅ Role-based access control
- ✅ Server-side validation
- ✅ Prepared statements (via CI3 Query Builder)

## Testing Checklist

- [ ] Login dengan credentials valid → redirect sesuai role
- [ ] Login dengan username salah → error message
- [ ] Login dengan password salah → error message
- [ ] Login dengan akun nonaktif → error message
- [ ] Akses dashboard tanpa login → redirect ke login
- [ ] Admin akses waka/dashboard → 403 Forbidden
- [ ] Waka akses admin/dashboard → 403 Forbidden
- [ ] Logout → destroy session, redirect ke login
- [ ] Token CSRF kadaluarsa → error 403
- [ ] Toggle password visibility berfungsi
- [ ] Spinner muncul saat submit
- [ ] Error alert auto-hide saat mengetik

## Catatan Development

1. **User Model**: Saat ini menggunakan hardcoded query. Ganti dengan model sebenarnya sesuai struktur database final.

2. **Password Hash**: Hash di `database_seeding.sql` adalah contoh. Generate hash baru untuk production.

3. **Session Config**: Pastikan konfigurasi session di `config/config.php` sudah benar:
   ```php
   $config['sess_driver'] = 'database'; // atau 'files'
   $config['sess_cookie_name'] = 'ci_session';
   $config['sess_expiration'] = 7200;
   $config['sess_match_ip'] = FALSE;
   $config['sess_time_to_update'] = 300;
   ```

4. **Base URL**: Set `base_url` di `config/config.php` sesuai environment.

## Referensi SRS

- **Bab 11.4**: Spesifikasi halaman login
- **Bab 16.1**: MY_Controller pseudocode
- **Bab 16.2**: Session payload
- **Bab 16.4**: CSRF implementation
