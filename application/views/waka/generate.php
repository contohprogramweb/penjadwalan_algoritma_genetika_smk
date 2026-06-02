<div class="container-fluid">
    <!-- Header Page -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800"><?= $page_title; ?></h1>
            </div>
        </div>
    </div>

    <?php if (isset($error_message) && $error_message): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= htmlspecialchars($error_message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- STATE 1: Sebelum Generate - Ringkasan Data & Checklist -->
    <div id="state-sebelum" class="<?= isset($ready) && !$ready ? 'd-block' : 'd-none'; ?>">
        <!-- Ringkasan Data -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Ringkasan Data Tahun Ajaran <?= htmlspecialchars($tahun_ajaran['tahun'] ?? '-'); ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-2 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-chalkboard-teacher text-primary fa-2x mb-2"></i>
                                    <h4 class="mb-0"><?= number_format($summary['total_penugasan'] ?? 0); ?></h4>
                                    <small class="text-muted">Penugasan</small>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-user-tie text-success fa-2x mb-2"></i>
                                    <h4 class="mb-0"><?= number_format($summary['total_guru'] ?? 0); ?></h4>
                                    <small class="text-muted">Guru</small>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-users text-info fa-2x mb-2"></i>
                                    <h4 class="mb-0"><?= number_format($summary['total_kelas'] ?? 0); ?></h4>
                                    <small class="text-muted">Kelas</small>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-door-open text-warning fa-2x mb-2"></i>
                                    <h4 class="mb-0"><?= number_format($summary['total_ruangan'] ?? 0); ?></h4>
                                    <small class="text-muted">Ruangan</small>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-clock text-secondary fa-2x mb-2"></i>
                                    <h4 class="mb-0"><?= number_format($summary['total_jam'] ?? 0); ?></h4>
                                    <small class="text-muted">Jam Pelajaran</small>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-calendar-check text-danger fa-2x mb-2"></i>
                                    <h4 class="mb-0">6 Hari</h4>
                                    <small class="text-muted">Periode</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checklist Kesiapan Data (SRS Bab 11.6.2) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-left-warning">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Checklist Kesiapan Data
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-<?= isset($checklist['tahun_ajaran']) && $checklist['tahun_ajaran'] ? 'check-circle text-success' : 'times-circle text-danger'; ?> mr-3 fa-lg"></i>
                                    <div>
                                        <strong>Tahun Ajaran Aktif</strong>
                                        <div class="text-muted small">Harus ada tahun ajaran aktif</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-<?= isset($checklist['penugasan']) && $checklist['penugasan'] ? 'check-circle text-success' : 'times-circle text-danger'; ?> mr-3 fa-lg"></i>
                                    <div>
                                        <strong>Data Penugasan Guru</strong>
                                        <div class="text-muted small">Penugasan harus sudah diisi</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-<?= isset($checklist['guru']) && $checklist['guru'] ? 'check-circle text-success' : 'times-circle text-danger'; ?> mr-3 fa-lg"></i>
                                    <div>
                                        <strong>Data Guru</strong>
                                        <div class="text-muted small">Data guru harus tersedia</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-<?= isset($checklist['kelas']) && $checklist['kelas'] ? 'check-circle text-success' : 'times-circle text-danger'; ?> mr-3 fa-lg"></i>
                                    <div>
                                        <strong>Data Kelas</strong>
                                        <div class="text-muted small">Data kelas harus tersedia</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-<?= isset($checklist['ruangan']) && $checklist['ruangan'] ? 'check-circle text-success' : 'times-circle text-danger'; ?> mr-3 fa-lg"></i>
                                    <div>
                                        <strong>Data Ruangan</strong>
                                        <div class="text-muted small">Data ruangan harus tersedia</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-<?= isset($checklist['jam_pelajaran']) && $checklist['jam_pelajaran'] ? 'check-circle text-success' : 'times-circle text-danger'; ?> mr-3 fa-lg"></i>
                                    <div>
                                        <strong>Jam Pelajaran</strong>
                                        <div class="text-muted small">Konfigurasi jam harus ada</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Informasi:</strong> Pastikan semua checklist di atas sudah terpenuhi sebelum melakukan generate jadwal. 
                    Proses generate menggunakan Algoritma Genetika yang mungkin memerlukan waktu beberapa menit tergantung jumlah data.
                </div>
            </div>
        </div>

        <!-- Tombol Generate -->
        <div class="row">
            <div class="col-12 text-center">
                <button id="btnGenerate" class="btn btn-primary btn-lg btn-generate" 
                        <?= isset($ready) && $ready ? '' : 'disabled'; ?>>
                    <i class="fas fa-cogs mr-2"></i>
                    Generate Jadwal Pelajaran
                </button>
                <?php if (!isset($ready) || !$ready): ?>
                <p class="text-muted mt-2">
                    <i class="fas fa-lock mr-1"></i>
                    Tombol generate akan aktif setelah semua checklist terpenuhi.
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- STATE 2: Sedang Berjalan - Progress Bar -->
    <div id="state-berjalan" class="d-none">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-left-primary">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Proses Generate Sedang Berjalan
                        </h6>
                    </div>
                    <div class="card-body text-center py-5">
                        <!-- Warning: Jangan tutup halaman -->
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Jangan tutup halaman ini!</strong> Proses sedang berjalan, harap tunggu hingga selesai.
                        </div>

                        <!-- Progress Bar (tinggi 25px sesuai SRS) -->
                        <div class="mb-4" style="max-width: 600px; margin: 0 auto;">
                            <div class="progress" style="height: 25px;">
                                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                     role="progressbar" style="width: 0%;" 
                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <span id="progressText">0%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Info Panel -->
                        <div class="row mt-4">
                            <div class="col-md-3 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-dna text-primary fa-2x mb-2"></i>
                                    <h5 id="infoGenerasi">0 / 500</h5>
                                    <small class="text-muted">Generasi</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-chart-line text-success fa-2x mb-2"></i>
                                    <h5 id="infoFitness">0</h5>
                                    <small class="text-muted">Fitness Terbaik</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-stopwatch text-info fa-2x mb-2"></i>
                                    <h5 id="infoWaktu">00:00</h5>
                                    <small class="text-muted">Waktu Berjalan</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-info-circle text-warning fa-2x mb-2"></i>
                                    <h5 id="infoStatus" class="text-warning">Initializing...</h5>
                                    <small class="text-muted">Status</small>
                                </div>
                            </div>
                        </div>

                        <!-- Pesan Progress -->
                        <p id="pesanProgress" class="text-muted mt-3 mb-0">
                            Menyiapkan data dan memulai algoritma genetika...
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STATE 3: Selesai - Alert Sukses/Gagal -->
    <div id="state-selesai" class="d-none">
        <div class="row">
            <div class="col-12">
                <!-- Alert Sukses -->
                <div id="alertSukses" class="alert alert-success alert-dismissible fade show d-none" role="alert">
                    <h5><i class="fas fa-check-circle mr-2"></i>Generate Berhasil!</h5>
                    <p id="pesanSukses">Jadwal pelajaran telah berhasil dibuat.</p>
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Generasi:</strong> <span id="hasilGenerasi">-</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Fitness:</strong> <span id="hasilFitness">-</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Waktu:</strong> <span id="hasilWaktu">-</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Konflik:</strong> <span id="hasilKonflik">-</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="<?= site_url('waka/jadwal'); ?>" class="btn btn-success">
                            <i class="fas fa-eye mr-2"></i>Review Jadwal
                        </a>
                        <button type="button" class="btn btn-outline-secondary ml-2" onclick="location.reload()">
                            <i class="fas fa-redo mr-2"></i>Generate Ulang
                        </button>
                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Alert Gagal -->
                <div id="alertGagal" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
                    <h5><i class="fas fa-times-circle mr-2"></i>Generate Gagal</h5>
                    <p id="pesanGagal">Terjadi kesalahan saat generate jadwal.</p>
                    <button type="button" class="btn btn-outline-danger" onclick="location.reload()">
                        <i class="fas fa-redo mr-2"></i>Coba Lagi
                    </button>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden data untuk JS -->
<script>
var GENERATE_CONFIG = {
    triggerUrl: '<?= site_url("waka/generate/trigger"); ?>',
    progressUrl: '<?= site_url("waka/generate/progress"); ?>',
    resetUrl: '<?= site_url("waka/generate/reset_progress"); ?>',
    reviewUrl: '<?= site_url("waka/jadwal"); ?>',
    pollingInterval: 2000, // 2 detik
    redirectDelay: 3000, // 3 detik setelah selesai
    csrfName: '<?= $this->security->get_csrf_token_name() ?>',
    csrfHash: '<?= $this->security->get_csrf_hash() ?>'
};
var INITIAL_READY = <?= isset($ready) && $ready ? 'true' : 'false'; ?>;
</script>
