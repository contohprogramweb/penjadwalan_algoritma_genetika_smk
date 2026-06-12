<?php $this->load->view('layouts/header'); ?>

<!-- Onboarding Wizard - SRS Bab 11.9 -->
<div class="onboarding-wrapper">
  
  <!-- Header Section -->
  <div class="onboarding-header text-center mb-4">
    <h2 class="onboarding-title">
      <i class="fas fa-rocket mr-2"></i>Setup Awal Sistem
    </h2>
    <p class="onboarding-subtitle">
      Selamat datang! Mari setup sistem penjadwalan Anda dalam 6 langkah mudah.
    </p>
  </div>

  <!-- Progress Steps (Bootstrap Nav Pills) -->
  <div class="onboarding-progress mb-5">
    <ul class="nav nav-pills justify-content-center" id="onboardingSteps" role="tablist">
      <?php foreach ($steps as $step_num => $step_config): ?>
        <?php 
          $status = isset($step_status[$step_num]) ? $step_status[$step_num] : [];
          $is_completed = isset($status['completed']) && $status['completed'];
          $is_current = $step_num == $current_step;
          $is_past = $step_num < $current_step;
        ?>
        <li class="nav-item" data-step="<?= $step_num ?>">
          <div class="step-wrapper <?= $is_completed ? 'completed' : '' ?> <?= $is_current ? 'active' : '' ?> <?= $is_past ? 'past' : '' ?>">
            <div class="step-indicator">
              <?php if ($is_completed): ?>
                <i class="fas fa-check"></i>
              <?php else: ?>
                <span><?= $step_num ?></span>
              <?php endif; ?>
            </div>
            <div class="step-info">
              <div class="step-title"><?= $step_config['title'] ?></div>
              <div class="step-desc d-none d-md-block"><?= $step_config['description'] ?></div>
            </div>
            <?php if ($is_completed): ?>
              <span class="step-badge badge badge-success">Selesai</span>
            <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <!-- Main Content Area -->
  <div class="onboarding-content">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        
        <!-- Step Content Container -->
        <div id="stepContentContainer">
          
          <!-- Step 1: Tahun Ajaran -->
          <div class="step-panel" data-step="1" style="display: <?= $current_step == 1 ? 'block' : 'none' ?>;">
            <div class="step-header mb-4">
              <h4 class="step-title"><i class="fas fa-calendar mr-2"></i>Tahun Ajaran</h4>
              <p class="text-muted">Setup tahun ajaran aktif untuk periode penjadwalan</p>
            </div>
            
            <div class="step-body">
              <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Yang perlu dilakukan:</strong> Tambahkan minimal 1 tahun ajaran dan set sebagai aktif.
              </div>
              
              <div class="data-status mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <span>Status:</span>
                  <span class="badge <?= isset($step_status[1]['completed']) && $step_status[1]['completed'] ? 'badge-success' : 'badge-warning' ?>" 
                        id="statusBadge1">
                    <?= isset($step_status[1]['completed']) && $step_status[1]['completed'] ? '<i class="fas fa-check mr-1"></i>Siap' : '<i class="fas fa-exclamation-triangle mr-1"></i>Belum Lengkap' ?>
                  </span>
                </div>
                <div class="mt-2">
                  <small class="text-muted">
                    Jumlah tahun ajaran: <strong id="count1"><?= isset($step_status[1]['count']) ? $step_status[1]['count'] : 0 ?></strong>
                  </small>
                </div>
              </div>

              <div class="quick-action mt-3">
                <a href="<?= site_url('admin/tahun_ajaran') ?>" class="btn btn-primary btn-block">
                  <i class="fas fa-plus mr-2"></i>Kelola Tahun Ajaran
                </a>
              </div>

              <div class="sample-data mt-3" id="sampleData1" style="display: none;">
                <h6 class="mb-2">Data yang sudah diinput:</h6>
                <ul class="list-group list-group-flush" id="sampleList1"></ul>
              </div>
            </div>
          </div>

          <!-- Step 2: Data Guru -->
          <div class="step-panel" data-step="2" style="display: <?= $current_step == 2 ? 'block' : 'none' ?>;">
            <div class="step-header mb-4">
              <h4 class="step-title"><i class="fas fa-chalkboard-teacher mr-2"></i>Data Guru</h4>
              <p class="text-muted">Input data guru yang akan mengajar di sekolah Anda</p>
            </div>
            
            <div class="step-body">
              <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Yang perlu dilakukan:</strong> Tambahkan minimal 1 data guru dengan NIP dan mata pelajaran yang diampu.
              </div>
              
              <div class="data-status mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <span>Status:</span>
                  <span class="badge <?= isset($step_status[2]['completed']) && $step_status[2]['completed'] ? 'badge-success' : 'badge-warning' ?>" 
                        id="statusBadge2">
                    <?= isset($step_status[2]['completed']) && $step_status[2]['completed'] ? '<i class="fas fa-check mr-1"></i>Siap' : '<i class="fas fa-exclamation-triangle mr-1"></i>Belum Lengkap' ?>
                  </span>
                </div>
                <div class="mt-2">
                  <small class="text-muted">
                    Jumlah guru: <strong id="count2"><?= isset($step_status[2]['count']) ? $step_status[2]['count'] : 0 ?></strong>
                    <span class="ml-2">(minimal 1)</span>
                  </small>
                </div>
              </div>

              <div class="quick-action mt-3">
                <a href="<?= site_url('admin/guru') ?>" class="btn btn-primary btn-block">
                  <i class="fas fa-plus mr-2"></i>Kelola Data Guru
                </a>
              </div>

              <div class="sample-data mt-3" id="sampleData2" style="display: none;">
                <h6 class="mb-2">Guru yang sudah terdaftar:</h6>
                <ul class="list-group list-group-flush" id="sampleList2"></ul>
              </div>
            </div>
          </div>

          <!-- Step 3: Mata Pelajaran -->
          <div class="step-panel" data-step="3" style="display: <?= $current_step == 3 ? 'block' : 'none' ?>;">
            <div class="step-header mb-4">
              <h4 class="step-title"><i class="fas fa-book mr-2"></i>Mata Pelajaran</h4>
              <p class="text-muted">Daftar mata pelajaran yang tersedia untuk diajarkan</p>
            </div>
            
            <div class="step-body">
              <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Yang perlu dilakukan:</strong> Tambahkan minimal 1 mata pelajaran dengan alokasi jam per minggu.
              </div>
              
              <div class="data-status mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <span>Status:</span>
                  <span class="badge <?= isset($step_status[3]['completed']) && $step_status[3]['completed'] ? 'badge-success' : 'badge-warning' ?>" 
                        id="statusBadge3">
                    <?= isset($step_status[3]['completed']) && $step_status[3]['completed'] ? '<i class="fas fa-check mr-1"></i>Siap' : '<i class="fas fa-exclamation-triangle mr-1"></i>Belum Lengkap' ?>
                  </span>
                </div>
                <div class="mt-2">
                  <small class="text-muted">
                    Jumlah mapel: <strong id="count3"><?= isset($step_status[3]['count']) ? $step_status[3]['count'] : 0 ?></strong>
                    <span class="ml-2">(minimal 1)</span>
                  </small>
                </div>
              </div>

              <div class="quick-action mt-3">
                <a href="<?= site_url('admin/mata_pelajaran') ?>" class="btn btn-primary btn-block">
                  <i class="fas fa-plus mr-2"></i>Kelola Mata Pelajaran
                </a>
              </div>

              <div class="sample-data mt-3" id="sampleData3" style="display: none;">
                <h6 class="mb-2">Mapel yang sudah terdaftar:</h6>
                <ul class="list-group list-group-flush" id="sampleList3"></ul>
              </div>
            </div>
          </div>

          <!-- Step 4: Kelas & Ruangan -->
          <div class="step-panel" data-step="4" style="display: <?= $current_step == 4 ? 'block' : 'none' ?>;">
            <div class="step-header mb-4">
              <h4 class="step-title"><i class="fas fa-school mr-2"></i>Kelas & Ruangan</h4>
              <p class="text-muted">Setup kelas dan ruangan untuk kegiatan pembelajaran</p>
            </div>
            
            <div class="step-body">
              <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Yang perlu dilakukan:</strong> Tambahkan minimal 1 kelas dan 1 ruangan.
              </div>
              
              <div class="data-status mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <span>Status:</span>
                  <span class="badge <?= isset($step_status[4]['completed']) && $step_status[4]['completed'] ? 'badge-success' : 'badge-warning' ?>" 
                        id="statusBadge4">
                    <?= isset($step_status[4]['completed']) && $step_status[4]['completed'] ? '<i class="fas fa-check mr-1"></i>Siap' : '<i class="fas fa-exclamation-triangle mr-1"></i>Belum Lengkap' ?>
                  </span>
                </div>
                <div class="mt-2">
                  <small class="text-muted">
                    <div><strong>Kelas:</strong> <span id="countKelas"><?= isset($step_status[4]['kelas_count']) ? $step_status[4]['kelas_count'] : 0 ?></span> (minimal 1)</div>
                    <div><strong>Ruangan:</strong> <span id="countRuangan"><?= isset($step_status[4]['ruangan_count']) ? $step_status[4]['ruangan_count'] : 0 ?></span> (minimal 1)</div>
                  </small>
                </div>
              </div>

              <div class="quick-actions mt-3">
                <div class="row">
                  <div class="col-md-6 mb-2">
                    <a href="<?= site_url('admin/kelas') ?>" class="btn btn-primary btn-block">
                      <i class="fas fa-plus mr-2"></i>Kelola Kelas
                    </a>
                  </div>
                  <div class="col-md-6 mb-2">
                    <a href="<?= site_url('admin/ruangan') ?>" class="btn btn-secondary btn-block">
                      <i class="fas fa-plus mr-2"></i>Kelola Ruangan
                    </a>
                  </div>
                </div>
              </div>

              <div class="sample-data mt-3" id="sampleData4" style="display: none;">
                <div class="row">
                  <div class="col-md-6">
                    <h6 class="mb-2">Kelas terdaftar:</h6>
                    <ul class="list-group list-group-flush" id="sampleListKelas"></ul>
                  </div>
                  <div class="col-md-6">
                    <h6 class="mb-2">Ruangan terdaftar:</h6>
                    <ul class="list-group list-group-flush" id="sampleListRuangan"></ul>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 5: Jam Pelajaran -->
          <div class="step-panel" data-step="5" style="display: <?= $current_step == 5 ? 'block' : 'none' ?>;">
            <div class="step-header mb-4">
              <h4 class="step-title"><i class="fas fa-clock mr-2"></i>Jam Pelajaran</h4>
              <p class="text-muted">Konfigurasi jam pelajaran untuk setiap hari dalam seminggu</p>
            </div>
            
            <div class="step-body">
              <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Yang perlu dilakukan:</strong> Setup jam pelajaran (misal: 07:00-07:45, 07:45-08:30, dst).
              </div>
              
              <div class="data-status mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <span>Status:</span>
                  <span class="badge <?= isset($step_status[5]['completed']) && $step_status[5]['completed'] ? 'badge-success' : 'badge-warning' ?>" 
                        id="statusBadge5">
                    <?= isset($step_status[5]['completed']) && $step_status[5]['completed'] ? '<i class="fas fa-check mr-1"></i>Siap' : '<i class="fas fa-exclamation-triangle mr-1"></i>Belum Lengkap' ?>
                  </span>
                </div>
                <div class="mt-2">
                  <small class="text-muted">
                    Jumlah slot jam: <strong id="count5"><?= isset($step_status[5]['count']) ? $step_status[5]['count'] : 0 ?></strong>
                    <span class="ml-2">(minimal 1)</span>
                  </small>
                </div>
              </div>

              <div class="quick-action mt-3">
                <a href="<?= site_url('admin/jam_pelajaran') ?>" class="btn btn-primary btn-block">
                  <i class="fas fa-plus mr-2"></i>Kelola Jam Pelajaran
                </a>
              </div>

              <div class="sample-data mt-3" id="sampleData5" style="display: none;">
                <h6 class="mb-2">Jam pelajaran yang sudah diset:</h6>
                <ul class="list-group list-group-flush" id="sampleList5"></ul>
              </div>
            </div>
          </div>

          <!-- Step 6: Penugasan Guru -->
          <div class="step-panel" data-step="6" style="display: <?= $current_step == 6 ? 'block' : 'none' ?>;">
            <div class="step-header mb-4">
              <h4 class="step-title"><i class="fas fa-clipboard-list mr-2"></i>Penugasan Guru</h4>
              <p class="text-muted">Assign guru ke mata pelajaran dan kelas yang akan diajar</p>
            </div>
            
            <div class="step-body">
              <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Langkah terakhir!</strong> Tentukan guru mana yang mengajar mapel apa di kelas mana.
                <br><small class="text-muted">Step ini bersifat opsional, bisa diisi nanti.</small>
              </div>
              
              <div class="data-status mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <span>Status:</span>
                  <span class="badge <?= isset($step_status[6]['completed']) && $step_status[6]['completed'] ? 'badge-success' : 'badge-secondary' ?>" 
                        id="statusBadge6">
                    <?= isset($step_status[6]['completed']) && $step_status[6]['completed'] ? '<i class="fas fa-check mr-1"></i>Siap' : '<i class="fas fa-circle mr-1"></i>Opsional' ?>
                  </span>
                </div>
                <div class="mt-2">
                  <small class="text-muted">
                    Jumlah penugasan: <strong id="count6"><?= isset($step_status[6]['count']) ? $step_status[6]['count'] : 0 ?></strong>
                  </small>
                </div>
              </div>

              <div class="quick-action mt-3">
                <a href="<?= site_url('admin/penugasan') ?>" class="btn btn-primary btn-block">
                  <i class="fas fa-plus mr-2"></i>Kelola Penugasan
                </a>
              </div>

              <div class="sample-data mt-3" id="sampleData6" style="display: none;">
                <h6 class="mb-2">Penugasan yang sudah dibuat:</h6>
                <ul class="list-group list-group-flush" id="sampleList6"></ul>
              </div>

              <!-- Completion Message -->
              <?php if (isset($step_status[6]) && $step_status[6]['completed']): ?>
              <div class="completion-message mt-4 p-4 bg-success text-white rounded">
                <div class="text-center">
                  <i class="fas fa-check-circle fa-3x mb-3"></i>
                  <h5>Selamat! Setup selesai!</h5>
                  <p>Sistem Anda sudah siap digunakan untuk generate jadwal.</p>
                  <a href="<?= site_url('jadwal/generate') ?>" class="btn btn-light btn-lg mt-2">
                    <i class="fas fa-magic mr-2"></i>Generate Jadwal Sekarang
                  </a>
                </div>
              </div>
              <?php endif; ?>
            </div>
          </div>

        </div>

        <!-- Navigation Buttons -->
        <div class="onboarding-footer mt-5 pt-4 border-top">
          <div class="row align-items-center">
            <div class="col-md-4">
              <?php if ($can_skip): ?>
                <button type="button" class="btn btn-outline-secondary" id="btnSkipWizard">
                  <i class="fas fa-forward mr-2"></i>Lewati Wizard
                </button>
              <?php endif; ?>
            </div>
            
            <div class="col-md-4 text-center">
              <span class="text-muted">
                Langkah <strong id="currentStepDisplay"><?= $current_step ?></strong> dari <strong><?= $total_steps ?></strong>
              </span>
              <div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar" role="progressbar" 
                     style="width: <?= ($current_step / $total_steps) * 100 ?>%" 
                     aria-valuenow="<?= ($current_step / $total_steps) * 100 ?>" 
                     aria-valuemin="0" aria-valuemax="100">
                </div>
              </div>
            </div>
            
            <div class="col-md-4 text-right">
              <?php if ($current_step > 1): ?>
                <a href="<?= site_url('admin/onboarding/' . ($current_step - 1)) ?>" class="btn btn-secondary">
                  <i class="fas fa-arrow-left mr-2"></i>Sebelumnya
                </a>
              <?php endif; ?>
              
              <?php if ($current_step < $total_steps): ?>
                <a href="<?= site_url('admin/onboarding/' . ($current_step + 1)) ?>" class="btn btn-primary ml-2" id="btnNext">
                  Selanjutnya <i class="fas fa-arrow-right ml-2"></i>
                </a>
              <?php else: ?>
                <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-success ml-2" id="btnFinish">
                  <i class="fas fa-flag-checkered mr-2"></i>Selesai
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

</div>

<!-- Skip Wizard Confirmation Modal -->
<div class="modal fade" id="skipWizardModal" tabindex="-1" role="dialog" aria-labelledby="skipWizardModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title" id="skipWizardModalLabel">
          <i class="fas fa-exclamation-triangle mr-2"></i>Konfirmasi Lewati Wizard
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin melewati wizard setup?</p>
        <p class="text-muted small">
          Anda dapat melengkapi data master nanti melalui menu di sidebar. 
          Namun sistem belum bisa digunakan untuk generate jadwal sampai semua data wajib terpenuhi.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-warning" id="confirmSkipWizard">
          <i class="fas fa-forward mr-2"></i>Ya, Lewati Wizard
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Custom CSS for Onboarding -->
<style>
.onboarding-wrapper {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.onboarding-header {
  margin-bottom: 2rem;
}

.onboarding-title {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary-color, #4e73df);
  margin-bottom: 0.5rem;
}

.onboarding-subtitle {
  font-size: 1.1rem;
  color: #6c757d;
}

/* Progress Steps Styling */
.onboarding-progress .nav-pills {
  flex-wrap: nowrap;
  overflow-x: auto;
  padding-bottom: 1rem;
}

.onboarding-progress .nav-item {
  flex: 0 0 auto;
  margin: 0 0.5rem;
}

.step-wrapper {
  display: flex;
  align-items: center;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 10px;
  transition: all 0.3s ease;
  position: relative;
  min-width: 200px;
}

.step-wrapper.active {
  background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(78, 115, 223, 0.4);
  transform: scale(1.05);
}

.step-wrapper.completed {
  background: #e8f5e9;
  border: 2px solid #28a745;
}

.step-wrapper.past {
  background: #f8f9fa;
  opacity: 0.7;
}

.step-indicator {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #6c757d;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  margin-right: 1rem;
  flex-shrink: 0;
}

.step-wrapper.active .step-indicator {
  background: white;
  color: #4e73df;
}

.step-wrapper.completed .step-indicator {
  background: #28a745;
}

.step-info {
  flex: 1;
}

.step-title {
  font-weight: 600;
  font-size: 0.95rem;
  margin-bottom: 0.25rem;
}

.step-desc {
  font-size: 0.8rem;
  opacity: 0.8;
}

.step-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  font-size: 0.7rem;
}

/* Step Panel */
.step-panel {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.step-header {
  border-bottom: 2px solid #e9ecef;
  padding-bottom: 1rem;
}

.step-header .step-title {
  font-size: 1.5rem;
  color: #4e73df;
}

.data-status {
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 8px;
  border-left: 4px solid #4e73df;
}

.quick-action .btn, .quick-actions .btn {
  padding: 0.75rem 1.5rem;
  font-weight: 500;
}

.sample-data {
  background: #fff;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 1rem;
}

.sample-data .list-group-item {
  border: none;
  padding: 0.5rem 0;
  font-size: 0.9rem;
}

/* Footer Navigation */
.onboarding-footer {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 1.5rem !important;
}

.onboarding-footer .btn {
  padding: 0.6rem 1.5rem;
  font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
  .onboarding-progress .nav-pills {
    flex-wrap: wrap;
  }
  
  .step-wrapper {
    min-width: 100%;
    margin-bottom: 0.5rem;
  }
  
  .step-desc {
    display: none !important;
  }
  
  .onboarding-footer .row {
    text-align: center !important;
  }
  
  .onboarding-footer .col-md-4 {
    margin-bottom: 1rem;
  }
  
  .onboarding-footer .text-right {
    text-align: center !important;
  }
}

/* Loading State */
.step-wrapper.loading {
  pointer-events: none;
  opacity: 0.7;
}

.step-wrapper.loading::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 20px;
  height: 20px;
  margin: -10px 0 0 -10px;
  border: 2px solid #f3f3f3;
  border-top: 2px solid #4e73df;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

<script>
// Config untuk JavaScript
window.onboardingConfig = {
  currentStep: <?= $current_step ?>,
  totalSteps: <?= $total_steps ?>,
  canSkip: <?= $can_skip ? 'true' : 'false' ?>,
  stepStatus: <?= json_encode($step_status) ?>,
  apiUrls: {
    checkStatus: '<?= site_url('admin/onboarding/api_step_status') ?>',
    saveProgress: '<?= site_url('admin/onboarding/api_save_progress') ?>',
    skipWizard: '<?= site_url('admin/onboarding/api_skip_wizard') ?>'
  }
};
</script>


<?php $this->load->view('layouts/footer'); ?>