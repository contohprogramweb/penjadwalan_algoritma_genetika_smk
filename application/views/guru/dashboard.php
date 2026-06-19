<?= $this->load->view('layouts/header'); ?>

<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex align-items-center justify-content-between">
          <h1 class="page-title"><?= isset($page_title) ? $page_title : 'Dashboard Guru' ?></h1>
        </div>
      </div>
    </div>

    <!-- Dashboard Content -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Selamat Datang, <?= htmlspecialchars($guru_data['nama_guru'] ?? 'Guru') ?></h5>
            <p class="text-muted">Ini adalah dashboard guru. Anda dapat melihat informasi pribadi dan jadwal mengajar Anda di sini.</p>
            
            <?php if ($guru_data): ?>
              <div class="row mt-4">
                <div class="col-md-6">
                  <div class="info-box">
                    <h6>Informasi Guru</h6>
                    <table class="table table-sm table-borderless">
                      <tbody>
                        <tr>
                          <td width="30%"><strong>NIP/NIY:</strong></td>
                          <td><?= htmlspecialchars($guru_data['nip_niy'] ?? '-') ?></td>
                        </tr>
                        <tr>
                          <td><strong>Nama:</strong></td>
                          <td><?= htmlspecialchars($guru_data['nama_guru'] ?? '-') ?></td>
                        </tr>
                        <tr>
                          <td><strong>Email:</strong></td>
                          <td><?= htmlspecialchars($guru_data['email'] ?? '-') ?></td>
                        </tr>
                        <tr>
                          <td><strong>No. HP:</strong></td>
                          <td><?= htmlspecialchars($guru_data['no_hp'] ?? '-') ?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->load->view('layouts/footer'); ?>
