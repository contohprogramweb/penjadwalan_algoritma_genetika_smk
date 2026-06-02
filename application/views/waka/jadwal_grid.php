<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - SIPEJAR</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/fontawesome.min.css') ?>">
    <style>
        .jadwal-grid {
            font-size: 12px;
        }
        .jadwal-grid thead th {
            vertical-align: middle;
            text-align: center;
            background-color: #343a40;
            color: white;
            padding: 10px 5px;
        }
        .jadwal-grid tbody td {
            vertical-align: middle;
            padding: 8px 5px;
            height: 70px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .jadwal-grid tbody td:hover {
            box-shadow: inset 0 0 0 2px #007bff;
        }
        .jadwal-kosong {
            background-color: #F5F5F5 !important;
            color: #999;
            font-style: italic;
        }
        .jadwal-teori {
            background-color: #E8F5E9;
            border-left: 3px solid #4CAF50;
        }
        .jadwal-praktikum {
            background-color: #E3F2FD;
            border-left: 3px solid #2196F3;
        }
        .jadwal-konflik {
            background-color: #f44336 !important;
            color: white !important;
        }
        .slot-content {
            max-width: 140px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }
        .slot-content-small {
            font-size: 11px;
        }
        .jam-istirahat {
            background-color: #f8f9fa;
            text-align: center;
            font-weight: bold;
            color: #666;
        }
        .btn-approve {
            margin-top: 20px;
        }
        #conflict-alert {
            display: none;
            margin-top: 15px;
        }
        .loading-spinner {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Header akan di-load dari template -->
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2><i class="fas fa-calendar-alt"></i> <?= $title ?></h2>
                    <?php if ($meta_jadwal && $meta_jadwal->status === 'draft'): ?>
                        <button id="btn-approve" class="btn btn-success btn-approve">
                            <i class="fas fa-check-circle"></i> Approve Jadwal
                        </button>
                    <?php elseif ($meta_jadwal && $meta_jadwal->status === 'approved'): ?>
                        <span class="badge badge-success p-2"><i class="fas fa-check"></i> Jadwal Approved</span>
                    <?php endif; ?>
                </div>

                <?php if (!$jadwal_data || empty($jadwal_data)): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Belum ada jadwal yang di-generate untuk tahun ajaran <?= $tahun_ajaran->nama ?>.
                        Silakan generate jadwal terlebih dahulu.
                    </div>
                    <a href="<?= site_url('waka/generate') ?>" class="btn btn-primary">
                        <i class="fas fa-magic"></i> Generate Jadwal
                    </a>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-table"></i> Grid Jadwal Mingguan - <?= $tahun_ajaran->nama ?>
                            <span class="float-right badge badge-light text-dark">Status: <?= strtoupper($meta_jadwal->status ?? 'DRAFT') ?></span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered jadwal-grid mb-0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="width: 80px;">Jam</th>
                                            <?php foreach ($hari_list as $hari): ?>
                                                <th><?= $hari ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Group jadwal by slot
                                        $jadwal_by_slot = [];
                                        foreach ($jadwal_data as $j) {
                                            $jadwal_by_slot[$j->slot][] = $j;
                                        }
                                        
                                        // Render per slot/jam
                                        $istirahat_slots = [5, 10]; // Contoh: slot 5 dan 10 adalah istirahat
                                        foreach ($jam_mapel as $jam):
                                            $slot = $jam->slot;
                                            
                                            // Cek apakah slot istirahat
                                            if (in_array($slot, $istirahat_slots)):
                                        ?>
                                            <tr>
                                                <td colspan="7" class="jam-istirahat">
                                                    <i class="fas fa-coffee"></i> ISTIRAHAT (<?= $jam->jam_mulai ?> - <?= $jam->jam_selesai ?>)
                                                </td>
                                            </tr>
                                        <?php 
                                            continue;
                                            endif;
                                        ?>
                                        <tr>
                                            <td class="text-center font-weight-bold">
                                                <?= $jam->jam_mulai ?><br>
                                                <small class="text-muted"><?= $jam->jam_selesai ?></small>
                                            </td>
                                            <?php foreach ($hari_list as $hari): ?>
                                                <?php
                                                // Cari jadwal untuk hari dan slot ini
                                                $current_jadwal = null;
                                                foreach ($jadwal_by_slot[$slot] ?? [] as $j) {
                                                    if ($j->hari === $hari) {
                                                        $current_jadwal = $j;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <?php if ($current_jadwal): ?>
                                                    <?php
                                                    $kelas_bg = ($current_jadwal->mapel_sifat === 'praktikum') ? 'jadwal-praktikum' : 'jadwal-teori';
                                                    $konflik_class = (!empty($current_jadwal->konflik)) ? 'jadwal-konflik' : '';
                                                    ?>
                                                    <td class="<?= $kelas_bg ?> <?= $konflik_class ?>" 
                                                        data-id="<?= $current_jadwal->id_jadwal_detail ?>"
                                                        data-hari="<?= $hari ?>"
                                                        data-slot="<?= $slot ?>"
                                                        data-id-kelas="<?= $current_jadwal->id_kelas ?>"
                                                        data-nama-kelas="<?= $current_jadwal->nama_kelas ?>"
                                                        data-id-penugasan="<?= $current_jadwal->id_penugasan ?>"
                                                        data-mapel="<?= $current_jadwal->nama_mapel ?>"
                                                        data-guru="<?= $current_jadwal->nama_guru ?>"
                                                        data-id-ruangan="<?= $current_jadwal->id_ruangan ?>"
                                                        data-nama-ruangan="<?= $current_jadwal->nama_ruangan ?>"
                                                        data-sifat="<?= $current_jadwal->mapel_sifat ?>"
                                                        onclick="openEditModal(this)">
                                                        <span class="slot-content">
                                                            <strong><?= $current_jadwal->nama_mapel ?></strong><br>
                                                            <small class="slot-content-small">
                                                                <i class="fas fa-chalkboard-teacher"></i> <?= $current_jadwal->nama_guru ?><br>
                                                                <i class="fas fa-door-open"></i> <?= $current_jadwal->nama_ruangan ?>
                                                            </small>
                                                        </span>
                                                        <?php if (!empty($current_jadwal->konflik)): ?>
                                                            <span class="badge badge-danger float-right">!</span>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php else: ?>
                                                    <td class="jadwal-kosong" onclick="showToast('Slot kosong', 'info')">
                                                        <small>-</small>
                                                    </td>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <span class="badge" style="background-color: #E8F5E9; color: #333;">Teori</span>
                            <span class="badge" style="background-color: #E3F2FD; color: #333;">Praktikum</span>
                            <span class="badge bg-danger text-white">Konflik</span>
                            <span class="badge" style="background-color: #F5F5F5; color: #999;">Kosong</span>
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Edit Slot -->
    <div class="modal fade" id="editSlotModal" tabindex="-1" role="dialog" aria-labelledby="editSlotModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editSlotModalLabel">
                        <i class="fas fa-edit"></i> Edit Slot Jadwal
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-edit-slot">
                        <input type="hidden" id="edit-id-jadwal-detail">
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="font-weight-bold">Kelas</label>
                                <input type="text" class="form-control" id="edit-nama-kelas" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="font-weight-bold">Hari</label>
                                <input type="text" class="form-control" id="edit-hari" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="font-weight-bold">Jam Ke-</label>
                                <input type="text" class="form-control" id="edit-slot" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit-id-penugasan" class="font-weight-bold">Penugasan Guru & Mapel</label>
                            <select class="form-control" id="edit-id-penugasan" required>
                                <option value="">-- Pilih Penugasan --</option>
                                <!-- Akan di-load via AJAX -->
                            </select>
                            <small class="text-muted">Pilih penugasan yang sesuai dengan kelas ini.</small>
                        </div>

                        <div class="form-group">
                            <label for="edit-id-ruangan" class="font-weight-bold">Ruangan</label>
                            <select class="form-control" id="edit-id-ruangan" required>
                                <option value="">-- Pilih Ruangan --</option>
                                <?php foreach ($this->db->get('ruangan')->result() as $r): ?>
                                    <option value="<?= $r->id ?>"><?= $r->nama ?> (<?= strtoupper($r->tipe) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="conflict-alert" class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Konflik Terdeteksi:</h6>
                            <ul id="conflict-list" class="mb-0"></ul>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" id="btn-simpan-edit" class="btn btn-primary" disabled>
                        <span class="loading-spinner spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-info-circle me-2"></i>
                <strong class="me-auto">Notifikasi</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/pages/jadwal-grid.js') ?>"></script>
</body>
</html>
