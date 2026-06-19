<div class="content-wrapper">
  <div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex align-items-center justify-content-between">
          <h1 class="page-title mb-0">
            <i class="fas fa-user-circle mr-2"></i>Profil Saya
          </h1>
          <nav aria-label="breadcrumb" class="d-none d-md-block">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
              <li class="breadcrumb-item"><a href="<?= site_url($this->session->userdata('role') . '/dashboard') ?>">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Profil</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>

    <!-- Profile Form Card -->
    <div class="row">
      <div class="col-lg-8">
        <div class="card shadow-sm">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-edit mr-2"></i>Edit Profil</h5>
          </div>
          <div class="card-body p-4">
            <form id="profileForm" method="POST" action="<?= site_url('profile/update') ?>">
              <?= csrf_field() ?>
              
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($user->username) ?>" disabled>
                <small class="form-text text-muted">Username tidak dapat diubah.</small>
              </div>

              <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($user->nama_lengkap) ?>" required>
              </div>

              <div class="form-group">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user->email ?? '') ?>" required>
              </div>

              <?php if (isset($user->nip)): ?>
              <div class="form-group">
                <label for="nip">NIP</label>
                <input type="text" class="form-control" id="nip" value="<?= htmlspecialchars($user->nip) ?>" disabled>
              </div>
              <?php endif; ?>

              <div class="form-group">
                <label for="role">Role</label>
                <input type="text" class="form-control" id="role" value="<?= ucfirst(htmlspecialchars($user->role)) ?>" disabled>
              </div>

              <hr class="my-4">

              <h6 class="mb-3"><i class="fas fa-key mr-2"></i>Ubah Password</h6>
              <p class="text-muted small">Kosongkan jika tidak ingin mengubah password.</p>

              <div class="form-group">
                <label for="password">Password Baru</label>
                <input type="password" class="form-control" id="password" name="password" minlength="6">
                <small class="form-text text-muted">Minimal 6 karakter.</small>
              </div>

              <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6">
              </div>

              <div class="form-group mb-0">
                <button type="submit" class="btn btn-primary px-4">
                  <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
                <button type="reset" class="btn btn-secondary px-4">
                  <i class="fas fa-undo mr-2"></i>Reset
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- User Info Sidebar -->
      <div class="col-lg-4">
        <div class="card shadow-sm">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Informasi Akun</h5>
          </div>
          <div class="card-body p-4">
            <div class="text-center mb-4">
              <div class="user-avatar-lg mx-auto mb-3">
                <?= strtoupper(substr($user->nama_lengkap, 0, 1)) ?>
              </div>
              <h5 class="mb-1"><?= htmlspecialchars($user->nama_lengkap) ?></h5>
              <p class="text-muted mb-0"><?= ucfirst($user->role) ?></p>
            </div>

            <ul class="list-unstyled mb-0">
              <li class="py-2 border-bottom">
                <small class="text-muted"><i class="fas fa-envelope mr-2"></i>Email</small>
                <div class="font-weight-medium"><?= htmlspecialchars($user->email ?? '-') ?></div>
              </li>
              <?php if (isset($user->nip)): ?>
              <li class="py-2 border-bottom">
                <small class="text-muted"><i class="fas fa-id-card mr-2"></i>NIP</small>
                <div class="font-weight-medium"><?= htmlspecialchars($user->nip) ?></div>
              </li>
              <?php endif; ?>
              <li class="py-2 border-bottom">
                <small class="text-muted"><i class="fas fa-calendar mr-2"></i>Last Login</small>
                <div class="font-weight-medium"><?= $user->last_login ? date('d M Y, H:i', strtotime($user->last_login)) : '-' ?></div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.user-avatar-lg {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: var(--primary-color);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  font-weight: 700;
}
.page-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--dark-color);
}
.card {
  border: none;
  border-radius: 0.5rem;
}
.form-control:disabled {
  background-color: #f8f9fa;
}
</style>

<script>
$(document).ready(function() {
  // Ambil nama token CSRF sekali di awal
  const csrfName = '<?= $this->security->get_csrf_token_name() ?>';
  
  $('#profileForm').on('submit', function(e) {
    e.preventDefault();
    
    // Ambil nilai token CSRF terbaru dari input hidden
    const csrfToken = $('input[name="' + csrfName + '"]').val();
    
    // Serialize form data dan tambahkan CSRF token
    const formData = $(this).serialize() + '&' + csrfName + '=' + csrfToken;
    
    Swal.fire({
      title: 'Memproses...',
      text: 'Sedang memperbarui profil Anda.',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function(response) {
        // Update token CSRF jika server mengembalikan token baru (rotasi token)
        if (response.csrf_hash) {
          $('input[name="' + csrfName + '"]').val(response.csrf_hash);
        }
        
        if (response.success || response.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: response.message || 'Profil berhasil diperbarui.',
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            html: response.errors || response.message || 'Terjadi kesalahan saat memperbarui profil.'
          });
        }
      },
      error: function(xhr, status, error) {
        let message = 'Terjadi kesalahan pada server.';
        
        // Handle CSRF error (403 Forbidden)
        if (xhr.status === 403) {
          message = 'Sesi keamanan kadaluarsa. Halaman akan dimuat ulang otomatis.';
          Swal.fire({
            icon: 'warning',
            title: 'Sesi Kadaluarsa',
            text: message,
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            location.reload();
          });
          return;
        }
        
        if (xhr.responseJSON && xhr.responseJSON.message) {
          message = xhr.responseJSON.message;
        }
        
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: message
        });
      }
    });
  });
});
</script>
