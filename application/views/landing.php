<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Penjadwalan Mata Pelajaran SMK - Solusi cerdas untuk penjadwalan otomatis">
    <title>Sistem Penjadwalan SMK - Beranda</title>
    
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* Reset & Base */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Nunito', -apple-system, BlinkMacSystemFont, sans-serif; overflow-x: hidden; }
        
        /* Navbar Fixed Top */
        .navbar-fixed-top {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: white !important;
        }
        
        .btn-login {
            background: white;
            color: #667eea;
            font-weight: 600;
            border-radius: 25px;
            padding: 8px 25px;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,255,255,0.3);
            color: #764ba2;
        }
        
        /* Hero Section dengan Gradient */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding-top: 70px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .btn-hero {
            background: white;
            color: #667eea;
            font-weight: 700;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.1rem;
            transition: all 0.3s;
            border: none;
        }
        
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            color: #764ba2;
        }
        
        .hero-image {
            max-width: 100%;
            animation: floatImage 4s ease-in-out infinite;
        }
        
        @keyframes floatImage {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        
        /* Features Section */
        .features-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .section-title p {
            font-size: 1.1rem;
            color: #7f8c8d;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            height: 100%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .feature-icon i {
            font-size: 2rem;
            color: white;
        }
        
        .feature-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .feature-card p {
            color: #7f8c8d;
            line-height: 1.6;
            font-size: 0.95rem;
        }
        
        /* How It Works Section */
        .how-it-works-section {
            padding: 80px 0;
            background: white;
        }
        
        .step-card {
            text-align: center;
            padding: 30px 20px;
            position: relative;
        }
        
        .step-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            position: relative;
            z-index: 1;
        }
        
        .step-card:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 60px;
            right: -50%;
            width: 100%;
            height: 2px;
            background: linear-gradient(to right, #667eea, #764ba2);
            z-index: 0;
        }
        
        .step-card h4 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .step-card p {
            color: #7f8c8d;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        /* Footer */
        footer {
            background: #2c3e50;
            color: white;
            padding: 30px 0;
            text-align: center;
        }
        
        footer p {
            margin: 0;
            opacity: 0.9;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .step-card:not(:last-child)::after {
                display: none;
            }
            
            .navbar-collapse {
                background: rgba(102, 126, 234, 0.95);
                padding: 15px;
                border-radius: 10px;
                margin-top: 10px;
            }
        }
        
        /* Accessibility - Screen Reader Only */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        /* Focus visible untuk keyboard navigation */
        a:focus-visible, button:focus-visible {
            outline: 3px solid #667eea;
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <!-- Skip Link untuk Aksesibilitas -->
    <a href="#main-content" class="sr-only sr-only-focusable" style="position: absolute; left: -9999px;">
        Loncat ke konten utama
    </a>

    <!-- Navbar Fixed Top -->
    <nav class="navbar navbar-expand-lg navbar-fixed-top" role="navigation" aria-label="Navigasi utama">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url(); ?>">
                <i class="fas fa-calendar-alt mr-2" aria-hidden="true"></i>
                <span class="sr-only">Sistem Penjadwalan SMK</span>
                SiJadwal SMK
            </a>
            
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="background-image: url('data:image/svg+xml;charset=utf8,%3Csvg viewBox=\'0 0 32 32\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath stroke=\'rgba(255,255,255,1)\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-miterlimit=\'10\' d=\'M4 8h24M4 16h24M4 24h24\'/%3E%3C/svg%3E');"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#fitur">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#cara-kerja">Cara Kerja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">Tentang</a>
                    </li>
                    <li class="nav-item ml-lg-3">
                        <a class="btn btn-login" href="<?php echo site_url('auth/login'); ?>">
                            <i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i>
                            Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content">
        <!-- Hero Section -->
        <section class="hero-section" aria-labelledby="hero-title">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <h1 id="hero-title" class="hero-title">
                            Sistem Penjadwalan<br>Mata Pelajaran SMK
                        </h1>
                        <p class="hero-subtitle">
                            Solusi cerdas untuk penjadwalan otomatis yang efisien, adil, dan bebas konflik. 
                            Hemat waktu hingga 80% dalam pembuatan jadwal pelajaran.
                        </p>
                        <a href="<?php echo site_url('auth/login'); ?>" class="btn btn-hero">
                            <i class="fas fa-rocket mr-2" aria-hidden="true"></i>
                            Mulai Sekarang
                        </a>
                    </div>
                    <div class="col-lg-6 text-center">
                        <!-- Illustration placeholder -->
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 500 400'%3E%3Crect fill='%23ffffff20' width='500' height='400'/%3E%3Ccircle cx='250' cy='180' r='100' fill='%23ffffff40'/%3E%3Crect x='150' y='220' width='200' height='120' rx='10' fill='%23ffffff30'/%3E%3Ctext x='250' y='200' text-anchor='middle' fill='white' font-size='20' font-family='Arial'%3EIlustrasi%3C/text%3E%3C/svg%3E" 
                             alt="Ilustrasi sistem penjadwalan" 
                             class="hero-image img-fluid"
                             style="max-height: 400px;">
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section (6 Card Fitur) -->
        <section id="fitur" class="features-section" aria-labelledby="features-title">
            <div class="container">
                <div class="section-title">
                    <h2 id="features-title">Fitur Unggulan</h2>
                    <p>Solusi lengkap untuk kebutuhan penjadwalan di SMK Anda</p>
                </div>
                
                <div class="row">
                    <!-- Feature 1 -->
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-magic" aria-hidden="true"></i>
                            </div>
                            <h3>Penjadwalan Otomatis</h3>
                            <p>Algoritma cerdas menghasilkan jadwal optimal tanpa konflik dalam hitungan detik.</p>
                        </div>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-chalkboard-teacher" aria-hidden="true"></i>
                            </div>
                            <h3>Manajemen Guru</h3>
                            <p>Kelola data guru, beban mengajar, dan ketersediaan dengan mudah.</p>
                        </div>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-door-open" aria-hidden="true"></i>
                            </div>
                            <h3>Manajemen Ruangan</h3>
                            <p>Atur penggunaan ruangan agar tidak ada bentrok jadwal antar kelas.</p>
                        </div>
                    </div>
                    
                    <!-- Feature 4 -->
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-book" aria-hidden="true"></i>
                            </div>
                            <h3>Manajemen Mapel</h3>
                            <p>Katalog mata pelajaran lengkap dengan alokasi jam per minggu.</p>
                        </div>
                    </div>
                    
                    <!-- Feature 5 -->
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-print" aria-hidden="true"></i>
                            </div>
                            <h3>Cetak Laporan</h3>
                            <p>Export jadwal ke PDF dengan format profesional siap cetak.</p>
                        </div>
                    </div>
                    
                    <!-- Feature 6 -->
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-users-cog" aria-hidden="true"></i>
                            </div>
                            <h3>Multi Role Access</h3>
                            <p>Sistem akses berbasis role: Admin, Waka Kurikulum, dan Guru.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section (4 Langkah) -->
        <section id="cara-kerja" class="how-it-works-section" aria-labelledby="howitworks-title">
            <div class="container">
                <div class="section-title">
                    <h2 id="howitworks-title">Cara Kerja</h2>
                    <p>Empat langkah mudah untuk mendapatkan jadwal pelajaran Anda</p>
                </div>
                
                <div class="row">
                    <!-- Step 1 -->
                    <div class="col-md-3 col-sm-6">
                        <div class="step-card">
                            <div class="step-number">1</div>
                            <h4>Input Data Master</h4>
                            <p>Masukkan data guru, mata pelajaran, ruangan, dan kelas melalui dashboard admin.</p>
                        </div>
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="col-md-3 col-sm-6">
                        <div class="step-card">
                            <div class="step-number">2</div>
                            <h4>Setel Preferensi</h4>
                            <p>Tentukan preferensi seperti jam mengajar maksimal dan prioritas mapel.</p>
                        </div>
                    </div>
                    
                    <!-- Step 3 -->
                    <div class="col-md-3 col-sm-6">
                        <div class="step-card">
                            <div class="step-number">3</div>
                            <h4>Generate Jadwal</h4>
                            <p>Klik tombol generate dan biarkan sistem membuat jadwal optimal.</p>
                        </div>
                    </div>
                    
                    <!-- Step 4 -->
                    <div class="col-md-3 col-sm-6">
                        <div class="step-card">
                            <div class="step-number">4</div>
                            <h4>Export & Cetak</h4>
                            <p>Unduh jadwal dalam format PDF atau lihat langsung di dashboard.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer id="tentang" role="contentinfo">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Sistem Penjadwalan SMK. All rights reserved.</p>
            <p class="mt-2" style="font-size: 0.9rem;">Dibuat untuk meningkatkan efisiensi penjadwalan pendidikan vokasi.</p>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Smooth scroll untuk anchor links
        $(document).ready(function() {
            $('a[href^="#"]').on('click', function(e) {
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    e.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 70
                    }, 800);
                }
            });
            
            // Close mobile menu on link click
            $('.navbar-nav a').on('click', function() {
                $('.navbar-collapse').collapse('hide');
            });
            
            // Navbar background change on scroll
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('.navbar-fixed-top').css('box-shadow', '0 2px 10px rgba(0,0,0,0.2)');
                } else {
                    $('.navbar-fixed-top').css('box-shadow', '0 2px 10px rgba(0,0,0,0.1)');
                }
            });
        });
    </script>
</body>
</html>
