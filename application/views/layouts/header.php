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
        <a href="<?= site_url('admin/dashboard') ?>" class="sidebar-brand">
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
            <a href="<?= site_url('admin/dashboard') ?>" class="sidebar-menu-link <?= $this->uri->segment(1) == 'admin' && $this->uri->segment(2) == 'dashboard' ? 'active' : '' ?>">
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
            <a href="<?= site_url('admin/guru') ?>" class="sidebar-menu-link <?= $this->uri->segment(2) == 'guru' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-chalkboard-teacher"></i>
              </span>
              <span class="sidebar-menu-text">Data Guru</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="<?= site_url('admin/mapel') ?>" class="sidebar-menu-link <?= $this->uri->segment(2) == 'mapel' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-book"></i>
              </span>
              <span class="sidebar-menu-text">Mata Pelajaran</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="<?= site_url('admin/kelas') ?>" class="sidebar-menu-link <?= $this->uri->segment(2) == 'kelas' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-school"></i>
              </span>
              <span class="sidebar-menu-text">Data Kelas</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="<?= site_url('admin/ruangan') ?>" class="sidebar-menu-link <?= $this->uri->segment(2) == 'ruangan' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-door-open"></i>
              </span>
              <span class="sidebar-menu-text">Ruangan</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="<?= site_url('admin/jam') ?>" class="sidebar-menu-link <?= $this->uri->segment(2) == 'jam' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-clock"></i>
              </span>
              <span class="sidebar-menu-text">Jam Pelajaran</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="<?= site_url('admin/tahun_ajaran') ?>" class="sidebar-menu-link <?= $this->uri->segment(2) == 'tahun_ajaran' ? 'active' : '' ?>">
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
            <a href="<?= site_url('waka/penugasan') ?>" class="sidebar-menu-link <?= $this->uri->segment(2) == 'penugasan' ? 'active' : '' ?>">
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
            <a href="<?= site_url('jadwal') ?>" class="sidebar-menu-link <?= $this->uri->segment(1) == 'jadwal' && $this->uri->segment(2) == '' ? 'active' : '' ?>">
              <span class="sidebar-menu-icon">
                <i class="fas fa-calendar-week"></i>
              </span>
              <span class="sidebar-menu-text">Lihat Jadwal</span>
            </a>
          </li>
          <?php if ($this->session->userdata('role') === 'admin' || $this->session->userdata('role') === 'waka'): ?>
          <li class="sidebar-menu-item">
            <a href="<?= site_url('waka/generate') ?>" class="sidebar-menu-link <?= $this->uri->segment(2) == 'generate' ? 'active' : '' ?>">
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
          <li class="sidebar-menu-item dropdown">
            <a href="#" class="sidebar-menu-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="sidebar-menu-icon">
                <i class="fas fa-file-pdf"></i>
              </span>
              <span class="sidebar-menu-text">Cetak Jadwal</span>
              <i class="fas fa-chevron-right ml-auto dropdown-arrow"></i>
            </a>
            <div class="dropdown-menu">
              <?php 
              $this->load->model('Kelas_model');
              $kelas_list = $this->Kelas_model->get_all();
              foreach ($kelas_list as $k): 
              ?>
              <a class="dropdown-item" href="<?= site_url('laporan/pdf_jadwal/' . $k->id_kelas) ?>" target="_blank">
                <i class="fas fa-print mr-2"></i><?= htmlspecialchars($k->nama_kelas) ?>
              </a>
              <?php endforeach; ?>
            </div>
          </li>
          <li class="sidebar-menu-item dropdown">
            <a href="#" class="sidebar-menu-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="sidebar-menu-icon">
                <i class="fas fa-chart-bar"></i>
              </span>
              <span class="sidebar-menu-text">Beban Guru</span>
              <i class="fas fa-chevron-right ml-auto dropdown-arrow"></i>
            </a>
            <div class="dropdown-menu">
              <?php 
              $this->load->model('Guru_model');
              $guru_list = $this->Guru_model->get_all();
              foreach ($guru_list as $g): 
              ?>
              <a class="dropdown-item" href="<?= site_url('laporan/pdf_beban_guru/' . $g->id_guru) ?>" target="_blank">
                <i class="fas fa-print mr-2"></i><?= htmlspecialchars($g->nama_guru) ?>
              </a>
              <?php endforeach; ?>
            </div>
          </li>
        </ul>
        <?php endif; ?>
        
        <!-- Settings (Admin Only) -->
        <?php if ($this->session->userdata('role') === 'admin'): ?>
        <div class="sidebar-nav-title">Pengaturan</div>
        <ul class="sidebar-menu">
          <li class="sidebar-menu-item">
            <a href="<?= site_url('auth/logout') ?>" class="sidebar-menu-link" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
              <span class="sidebar-menu-icon">
                <i class="fas fa-sign-out-alt"></i>
              </span>
              <span class="sidebar-menu-text">Logout</span>
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
                <?php foreach ($breadcrumbs as $key => $value): ?>
                  <?php if ($key !== end(array_keys($breadcrumbs))): ?>
                    <li class="breadcrumb-item"><a href="<?= $value ?>"><?= ucfirst(str_replace('_', ' ', $key)) ?></a></li>
                  <?php else: ?>
                    <li class="breadcrumb-item active" aria-current="page"><?= ucfirst(str_replace('_', ' ', $key)) ?></li>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php else: ?>
                <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Home</a></li>
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
          <?= $this->session->flashdata('success') ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      
      <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
          <i class="fas fa-exclamation-circle mr-2"></i>
          <?= $this->session->flashdata('error') ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      
      <!-- Main Content Area -->
      <main id="main-content-area" class="content-wrapper">
