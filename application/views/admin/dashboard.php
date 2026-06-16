<?php  $this->load->view('layouts/header'); ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" onclick="location.reload()">
            <i class="fas fa-sync fa-sm text-white-50"></i> Refresh
        </a>
    </div>

    <!-- Info Cards Row -->
    <div class="row">

        <!-- Total Guru Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Guru</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalGuru"><?= $total_guru ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Kelas Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Kelas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalKelas"><?= $total_kelas ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Mapel Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Mata Pelajaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalMapel"><?= $total_mapel ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Ruangan Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Ruangan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalRuangan"><?= $total_ruangan ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-door-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row">
        <!-- Total Tahun Ajaran Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Total Tahun Ajaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalTahunAjaran"><?= $total_tahun_ajaran ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Jam Mengajar Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Jam Mengajar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalJam"><?= $total_jam ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Info Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Pengguna Aktif</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?php $this->session->userdata('nama_lengkap') ?></div>
                            <small class="text-muted">Role: <?php ucfirst($this->session->userdata('role')) ?></small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?= site_url('admin/guru') ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-chalkboard-teacher mr-2"></i>Kelola Guru
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?= site_url('admin/kelas') ?>" class="btn btn-success btn-block">
                                <i class="fas fa-school mr-2"></i>Kelola Kelas
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?= site_url('admin/mapel') ?>" class="btn btn-info btn-block">
                                <i class="fas fa-book mr-2"></i>Kelola Mapel
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?= site_url('admin/ruangan') ?>" class="btn btn-warning btn-block">
                                <i class="fas fa-door-open mr-2"></i>Kelola Ruangan
                            </a>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?= site_url('admin/tahun_ajaran') ?>" class="btn btn-secondary btn-block">
                                <i class="fas fa-calendar-alt mr-2"></i>Kelola Tahun Ajaran
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?= site_url('admin/jam') ?>" class="btn btn-danger btn-block">
                                <i class="fas fa-clock mr-2"></i>Kelola Jam Mengajar
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?= site_url('auth/logout') ?>" class="btn btn-outline-dark btn-block">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?= site_url('admin/onboarding') ?>" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-question-circle mr-2"></i>Panduan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<script>
$(document).ready(function() {
    // Load dashboard statistics
    loadDashboardStats();

    function loadDashboardStats() {
        // Fetch stats from API or controller
        // For now, using placeholder values
        // In production, this should fetch from actual data

        // Example: $.ajax({ url: '<?php site_url('admin/dashboard/stats') ?>', ... })

        // Placeholder values (remove when real API is implemented)
        $('#totalGuru').text('0');
        $('#totalKelas').text('0');
        $('#totalMapel').text('0');
        $('#totalRuangan').text('0');
        $('#totalTahunAjaran').text('0');
        $('#totalJam').text('0');
    }
});
</script>

<?php $this->load->view('layouts/footer'); ?>