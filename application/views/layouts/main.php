<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Sistem Penjadwalan SMK</title>
  
  <!-- Meta Tags -->
  <meta name="description" content="Sistem Penjadwalan Guru SMK Berbasis Algoritma Genetika">
  <meta name="author" content="SMK Development Team">
  <meta name="csrf_token_name" content="<?= $this->security->get_csrf_token_name() ?>">
  <meta name="csrf_token_hash" content="<?= $this->security->get_csrf_hash() ?>">
  
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 4.6 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  
  <!-- Font Awesome 5 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  
  <!-- DataTables 1.10 -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/variables.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
  
  <!-- Page Specific CSS -->
  <?php if (isset($css_files)): ?>
    <?php foreach ($css_files as $css): ?>
      <link rel="stylesheet" href="<?= base_url($css) ?>">
    <?php endforeach; ?>
  <?php endif; ?>
  
  <!-- Inline Styles for Dynamic Data -->
  <style>
    :root {
      --current-year: <?= date('Y') ?>;
    }
  </style>
</head>
<body>
  <!-- Skip Link for Accessibility -->
  <a href="#main-content" class="skip-link sr-only">Langsung ke konten utama</a>
  
  <div class="wrapper">
    <!-- =========================================
         SIDEBAR
         ========================================= -->
    <aside class="sidebar" id="sidebar" role="navigation" aria-label="Sidebar Navigation">
      <!-- Sidebar Header -->
      <div class="sidebar-header">
        <a href="<?= site_url('dashboard') ?>" class="sidebar-brand">
          <i class="fas fa-calendar-alt mr-2"></i>
          SIJADWAL
          <small>Sistem Penjadwalan SMK</small>
        </a>
        <button class="sidebar-toggle d-lg-none" id="sidebarToggleMobile" aria-label="Toggle sidebar">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <!-- Sidebar Navigation -->
      <nav class="sidebar-nav">
        <!-- Dashboard -->
        <ul class="sidebar-menu">
          <li class="sidebar-menu-item">
            <a href="<?= site_url('dashboard') ?>" class="sidebar-menu-link <?php $this->uri->segment(1) == 'dashboard' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-tachometer-alt"></i>
              </span>
              <span class="sidebar-menu-text">Dashboard</span>
            </a>
          </li>
        </ul>
        
        <!-- Master Data (Admin & Waka) -->
        <?php if ($this->session->userdata('role') === 'admin' || $this->session->userdata('role') === 'waka'): ?>
        <div class="sidebar-nav-title">Master Data</div>
        <ul class="sidebar-menu">
          <li class="sidebar-menu-item">
            <a href="<?= site_url('admin/guru') ?>" class="sidebar-menu-link <?php $this->uri->segment(2) == 'guru' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-chalkboard-teacher"></i>
              </span>
              <span class="sidebar-menu-text">Data Guru</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="<?= site_url('admin/mata_pelajaran') ?>" class="sidebar-menu-link <?php $this->uri->segment(2) == 'mata_pelajaran' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-book"></i>
              </span>
              <span class="sidebar-menu-text">Mata Pelajaran</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="<?= site_url('admin/kelas') ?>" class="sidebar-menu-link <?php $this->uri->segment(2) == 'kelas' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-school"></i>
              </span>
              <span class="sidebar-menu-text">Data Kelas</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="<?= site_url('admin/ruangan') ?>" class="sidebar-menu-link <?php $this->uri->segment(2) == 'ruangan' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-door-open"></i>
              </span>
              <span class="sidebar-menu-text">Ruangan</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="<?= site_url('admin/jam_pelajaran') ?>" class="sidebar-menu-link <?php $this->uri->segment(2) == 'jam_pelajaran' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-clock"></i>
              </span>
              <span class="sidebar-menu-text">Jam Pelajaran</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="<?= site_url('admin/tahun_ajaran') ?>" class="sidebar-menu-link <?php $this->uri->segment(2) == 'tahun_ajaran' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-calendar"></i>
              </span>
              <span class="sidebar-menu-text">Tahun Ajaran</span>
            </a>
          </li>
        </ul>
        <?php endif; ?>
        
        <!-- Penugasan & Preferensi -->
        <?php if ($this->session->userdata('role') === 'admin' || $this->session->userdata('role') === 'waka'): ?>
        <div class="sidebar-nav-title">Penugasan</div>
        <ul class="sidebar-menu">
          <li class="sidebar-menu-item">
            <a href="<?= site_url('waka/penugasan') ?>" class="sidebar-menu-link <?php $this->uri->segment(2) == 'penugasan' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-clipboard-list"></i>
              </span>
              <span class="sidebar-menu-text">Penugasan Guru</span>
              <?php if (isset($pending_penugasan_count) && $pending_penugasan_count > 0): ?>
                <span class="sidebar-menu-badge"><?= $pending_penugasan_count ?></span>
              <?php endif; ?>
            </a>
          </li>
        </ul>
        <?php endif; ?>
        
        <!-- Jadwal -->
        <div class="sidebar-nav-title">Jadwal</div>
        <ul class="sidebar-menu">
          <li class="sidebar-menu-item">
            <a href="<?= site_url('waka/jadwal') ?>" class="sidebar-menu-link <?php $this->uri->segment(1) == 'waka' && $this->uri->segment(2) == 'jadwal' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-calendar-week"></i>
              </span>
              <span class="sidebar-menu-text">Grid Jadwal</span>
            </a>
          </li>
          <?php if ($this->session->userdata('role') === 'admin' || $this->session->userdata('role') === 'waka'): ?>
          <li class="sidebar-menu-item">
            <a href="<?= site_url('waka/generate') ?>" class="sidebar-menu-link <?php $this->uri->segment(2) == 'generate' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-magic"></i>
              </span>
              <span class="sidebar-menu-text">Generate Jadwal</span>
            </a>
          </li>
          <?php endif; ?>
        </ul>
        
        <!-- Laporan -->
        <?php if ($this->session->userdata('role') === 'admin' || $this->session->userdata('role') === 'waka'): ?>
        <div class="sidebar-nav-title">Laporan</div>
        <ul class="sidebar-menu">
          <li class="sidebar-menu-item">
            <a href="<?= site_url('laporan/jadwal') ?>" class="sidebar-menu-link <?php $this->uri->segment(2) == 'jadwal' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-file-pdf"></i>
              </span>
              <span class="sidebar-menu-text">Cetak Jadwal</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="<?= site_url('laporan/beban_guru') ?>" class="sidebar-menu-link <?php $this->uri->segment(2) == 'beban_guru' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-chart-bar"></i>
              </span>
              <span class="sidebar-menu-text">Beban Guru</span>
            </a>
          </li>
        </ul>
        <?php endif; ?>
        
        <!-- Settings (Admin Only) -->
        <?php if ($this->session->userdata('role') === 'admin'): ?>
        <div class="sidebar-nav-title">Pengaturan</div>
        <ul class="sidebar-menu">
          <li class="sidebar-menu-item">
            <a href="<?= site_url('admin/users') ?>" class="sidebar-menu-link <?php $this->uri->segment(2) == 'users' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-users-cog"></i>
              </span>
              <span class="sidebar-menu-text">Manajemen User</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="<?= site_url('admin/settings') ?>" class="sidebar-menu-link <?php $this->uri->segment(2) == 'settings' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-cog"></i>
              </span>
              <span class="sidebar-menu-text">Pengaturan</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="<?= site_url('admin/activity_log') ?>" class="sidebar-menu-link <?php $this->uri->segment(2) == 'activity_log' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-history"></i>
              </span>
              <span class="sidebar-menu-text">Activity Log</span>
            </a>
          </li>
        </ul>
        <?php endif; ?>
      </nav>
      
      <!-- Sidebar Footer (User Info) -->
      <div class="sidebar-footer">
        <div class="user-info">
          <div class="user-avatar">
            <?= strtoupper(substr($this->session->userdata('username'), 0, 1)) ?>
          </div>
          <div class="user-details">
            <div class="user-name"><?= htmlspecialchars($this->session->userdata('username')) ?></div>
            <div class="user-role"><?= htmlspecialchars($this->session->userdata('role')) ?></div>
          </div>
        </div>
      </div>
    </aside>
    
    <!-- =========================================
         MAIN CONTENT
         ========================================= -->
    <div class="main-content" id="main-content">
      <!-- Topbar -->
      <header class="topbar" role="banner">
        <div class="topbar-left">
          <button class="topbar-toggle" id="topbarToggle" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
          </button>
          
          <!-- Breadcrumb -->
          <nav aria-label="breadcrumb" class="d-none d-md-block">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
              <?php if (isset($breadcrumbs)): ?>
                <?php 
                $breadcrumb_keys = array_keys($breadcrumbs);
                $last_key = end($breadcrumb_keys);
                foreach ($breadcrumbs as $key => $value): 
                ?>
                  <?php if ($key !== $last_key): ?>
                    <li class="breadcrumb-item"><a href="<?= $value ?>"><?= ucfirst(str_replace('_', ' ', $key)) ?></a></li>
                  <?php else: ?>
                    <li class="breadcrumb-item active" aria-current="page"><?= ucfirst(str_replace('_', ' ', $key)) ?></li>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php else: ?>
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= isset($page_title) ? $page_title : 'Dashboard' ?></li>
              <?php endif; ?>
            </ol>
          </nav>
        </div>
        
        <div class="topbar-right">
          <!-- Notification -->
          <button class="topbar-action" id="notificationBtn" aria-label="Notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-bell"></i>
            <?php if (isset($notification_count) && $notification_count > 0): ?>
              <span class="topbar-action-badge"><?= $notification_count ?></span>
            <?php endif; ?>
          </button>
          
          <!-- Fullscreen -->
          <button class="topbar-action d-none d-md-inline-block" id="fullscreenBtn" aria-label="Toggle fullscreen">
            <i class="fas fa-expand"></i>
          </button>
          
          <div class="topbar-divider d-none d-md-block"></div>
          
          <!-- User Dropdown -->
          <div class="dropdown">
            <div class="dropdown-user" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <div class="dropdown-user-avatar">
                <?= strtoupper(substr($this->session->userdata('username'), 0, 1)) ?>
              </div>
              <span class="dropdown-user-name d-none d-md-inline"><?= htmlspecialchars($this->session->userdata('username')) ?></span>
              <i class="fas fa-chevron-down ml-2 text-muted"></i>
            </div>
            
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
              <a class="dropdown-item" href="<?= site_url('profile') ?>">
                <i class="fas fa-user mr-2"></i> Profil Saya
              </a>
              <a class="dropdown-item" href="<?= site_url('settings') ?>">
                <i class="fas fa-cog mr-2"></i> Pengaturan
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item text-danger" href="<?= site_url('auth/logout') ?>" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
              </a>
            </div>
          </div>
        </div>
      </header>
      
      <!-- Flash Messages -->
      <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
          <i class="fas fa-check-circle mr-2"></i>
          <?php $this->session->flashdata('success') ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      
      <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
          <i class="fas fa-exclamation-circle mr-2"></i>
          <?php $this->session->flashdata('error') ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      
      <?php if ($this->session->flashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show m-3" role="alert">
          <i class="fas fa-exclamation-triangle mr-2"></i>
          <?php $this->session->flashdata('warning') ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      
      <!-- Page Content -->
      <main class="page-content" role="main">
        <?= $content ?>
      </main>
      
      <!-- Footer -->
      <footer class="footer" role="contentinfo">
        <div class="footer-left">
          <span>&copy; <?= date('Y') ?> <strong>SIJADWAL SMK</strong>. All rights reserved.</span>
        </div>
        <div class="footer-right">
          <ul class="footer-links">
            <li><a href="#" data-toggle="modal" data-target="#aboutModal">Tentang</a></li>
            <li><a href="#" data-toggle="modal" data-target="#helpModal">Bantuan</a></li>
            <li><a href="#">Privasi</a></li>
          </ul>
        </div>
      </footer>
    </div>
  </div>
  
  <!-- Loading Overlay (Hidden by Default) -->
  <div class="loading-overlay" id="loadingOverlay" style="display: none;">
    <div class="text-center">
      <div class="loading-spinner"></div>
      <p class="loading-text">Memproses...</p>
    </div>
  </div>
  
  <!-- About Modal -->
  <div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="aboutModalLabel">Tentang SIJADWAL</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p><strong>Sistem Penjadwalan Guru SMK Berbasis Algoritma Genetika</strong></p>
          <p>Versi: 3.0 (Final)</p>
          <p>Framework: CodeIgniter 3.1.x</p>
          <p class="mb-0">© <?= date('Y') ?> SMK Development Team</p>
        </div>
      </div>
    </div>
  </div>
  
  <!-- jQuery 3.6 -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <!-- Bootstrap 4.6 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- DataTables 1.10 -->
  <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
  
  <!-- SweetAlert2 (Optional but Recommended) -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- Custom JS -->
  <script>
    // CSRF Token Setup for AJAX
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    
    // Sidebar Toggle
    $(document).ready(function() {
      const sidebar = $('#sidebar');
      const mainContent = $('.main-content');
      
      // Desktop Toggle
      $('#topbarToggle').on('click', function() {
        if ($(window).width() >= 992) {
          sidebar.toggleClass('collapsed');
          mainContent.toggleClass('expanded');
        } else {
          sidebar.toggleClass('show');
        }
      });
      
      // Mobile Toggle
      $('#sidebarToggleMobile').on('click', function() {
        sidebar.removeClass('show');
      });
      
      // Close sidebar on mobile when clicking outside
      $(document).on('click', function(e) {
        if ($(window).width() < 992) {
          if (!$(e.target).closest('#sidebar, #topbarToggle').length) {
            sidebar.removeClass('show');
          }
        }
      });
      
      // Fullscreen Toggle
      $('#fullscreenBtn').on('click', function() {
        if (!document.fullscreenElement) {
          document.documentElement.requestFullscreen();
          $(this).find('i').removeClass('fa-expand').addClass('fa-compress');
        } else {
          if (document.exitFullscreen) {
            document.exitFullscreen();
            $(this).find('i').removeClass('fa-compress').addClass('fa-expand');
          }
        }
      });
      
      // Auto-hide alerts after 5 seconds
      setTimeout(function() {
        $('.alert').alert('close');
      }, 5000);
      
      // Confirm delete actions
      $('.btn-delete').on('click', function(e) {
        if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
          e.preventDefault();
        }
      });
      
      // Loading overlay helper functions
      window.showLoading = function() {
        $('#loadingOverlay').fadeIn();
      };
      
      window.hideLoading = function() {
        $('#loadingOverlay').fadeOut();
      };
      
      // Initialize DataTables with default config
      if ($.fn.DataTable.isDataTable('.datatable-default')) {
        $('.datatable-default').DataTable({
          responsive: true,
          language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json'
          },
          pageLength: 10,
          lengthMenu: [10, 25, 50, 100]
        });
      }
    });
    
    // Print function
    function printSection(elementId) {
      const printContent = document.getElementById(elementId).innerHTML;
      const originalContent = document.body.innerHTML;
      document.body.innerHTML = printContent;
      window.print();
      document.body.innerHTML = originalContent;
      location.reload();
    }
  </script>
  
  <!-- Page Specific JS -->
  <?php if (isset($js_files)): ?>
    <?php foreach ($js_files as $js): ?>
      <script src="<?= base_url($js) ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
  
  <!-- Inline Scripts -->
  <?php if (isset($inline_scripts)): ?>
    <script>
      <?= $inline_scripts ?>
    </script>
  <?php endif; ?>
</body>
</html>
