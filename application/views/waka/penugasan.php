<div class="container-fluid">
    <!-- Header Page -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800"><?= $page_title; ?></h1>
                <button class="btn btn-primary btn-icon-split" id="btnTambah">
                    <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                    <span class="text">Tambah Penugasan</span>
                </button>
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
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success mr-2 fa-lg"></i>
                                <div>
                                    <strong>Tahun Ajaran</strong>
                                    <div class="text-muted small"><?= htmlspecialchars($tahun_ajaran_aktif['tahun'] ?? '-'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chalkboard-teacher text-success mr-2 fa-lg"></i>
                                <div>
                                    <strong>Data Guru</strong>
                                    <div class="text-muted small">Sudah tersedia</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-book text-success mr-2 fa-lg"></i>
                                <div>
                                    <strong>Mata Pelajaran</strong>
                                    <div class="text-muted small">Sudah tersedia</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <?php if ($kelas_tanpa_penugasan_count > 0): ?>
                                    <i class="fas fa-exclamation-circle text-danger mr-2 fa-lg"></i>
                                    <div>
                                        <strong>Kelas Tanpa Penugasan</strong>
                                        <div class="text-danger small"><?= $kelas_tanpa_penugasan_count; ?> kelas belum ada penugasan</div>
                                    </div>
                                <?php else: ?>
                                    <i class="fas fa-check-circle text-success mr-2 fa-lg"></i>
                                    <div>
                                        <strong>Semua Kelas</strong>
                                        <div class="text-success small">Sudah memiliki penugasan</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($kelas_tanpa_penugasan_count > 0): ?>
                        <div class="alert alert-warning mt-3 mb-0" role="alert">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Perhatian:</strong> Ada <?= $kelas_tanpa_penugasan_count; ?> kelas yang belum memiliki penugasan guru.
                            <?php if ($kelas_tanpa_penugasan_count <= 5): ?>
                                Kelas: 
                                <?php foreach ($kelas_tanpa_penugasan as $k): ?>
                                    <span class="badge badge-warning"><?= htmlspecialchars($k['nama_kelas']); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                Silakan tambahkan penugasan untuk kelas-kelas tersebut.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-primary text-white shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Penugasan (<?= ucfirst($semester_aktif); ?>)</div>
                            <div class="h5 mb-0 font-weight-bold"><?= $total_penugasan; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-info text-white shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Kelas Tercover</div>
                            <div class="h5 mb-0 font-weight-bold">
                                <?= $total_penugasan > 0 ? '100%' : '0%; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Penugasan -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Penugasan Guru</h6>
                    <div class="dropdown no-arrow">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterSemester" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-filter mr-1"></i> Semester: <?= ucfirst($semester_aktif); ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="filterSemester">
                            <a class="dropdown-item" href="#" data-semester="ganjil">Ganjil</a>
                            <a class="dropdown-item" href="#" data-semester="genap">Genap</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTablePenugasan" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Guru</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelas</th>
                                    <th>Ruangan</th>
                                    <th>Semester</th>
                                    <th>Jam/Minggu</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Penugasan -->
<div class="modal fade" id="modalPenugasan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Penugasan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPenugasan" method="POST">
                <div class="modal-body">
                    <?php 
                    $csrf_token = $this->security->get_csrf_token_name();
                    $csrf_hash = $this->security->get_csrf_hash();
                    ?>
                    <input type="hidden" name="<?php echo $csrf_token; ?>" value="<?php echo $csrf_hash; ?>">
                    <input type="hidden" name="id_penugasan" id="id_penugasan">
                    <input type="hidden" name="id_tahun_ajaran" value="<?php echo $tahun_ajaran_aktif['id_tahun_ajaran']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_guru">Guru <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="id_guru" name="id_guru" required>
                                    <option value="">Pilih Guru</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_mapel">Mata Pelajaran <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="id_mapel" name="id_mapel" required>
                                    <option value="">Pilih Mata Pelajaran</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_kelas">Kelas <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="id_kelas" name="id_kelas" required>
                                    <option value="">Pilih Kelas</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="semester">Semester <span class="text-danger">*</span></label>
                                <select class="form-control" id="semester" name="semester" required>
                                    <option value="">Pilih</option>
                                    <option value="ganjil">Ganjil</option>
                                    <option value="genap">Genap</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="jam_per_minggu">Jam/Minggu <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="jam_per_minggu" name="jam_per_minggu" min="1" max="48" value="2" required>
                                <small class="text-muted">1-48</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_praktikum" name="is_praktikum" value="1">
                            <label class="custom-control-label" for="is_praktikum">
                                <strong>Mapel Praktikum</strong> (memerlukan ruangan khusus)
                            </label>
                        </div>
                    </div>

                    <div class="form-group d-none" id="ruanganGroup">
                        <label for="id_ruangan">Ruangan <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="id_ruangan" name="id_ruangan">
                            <option value="">Pilih Ruangan</option>
                        </select>
                        <small class="text-muted">Wajib diisi jika mapel praktikum</small>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpan">
                        <span class="btn-text"><i class="fas fa-save mr-1"></i> Simpan</span>
                        <span class="btn-loading d-none">
                            <span class="spinner-border spinner-border-sm mr-1"></span> Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus penugasan ini?</p>
                <p class="text-danger small"><i class="fas fa-exclamation-triangle mr-1"></i> Data yang dihapus tidak dapat dikembalikan!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnKonfirmasiHapus">
                    <i class="fas fa-trash mr-1"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

<script>
// CSRF Token setup
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    let editMode = false;
    let deleteId = null;
    const currentTahunAjaran = <?= json_encode($tahun_ajaran_aktif['id_tahun_ajaran']); ?>;
    let currentSemester = '<?= $semester_aktif; ?>';

    // Initialize DataTable
    const table = $('#dataTablePenugasan').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= site_url("waka/penugasan/get_data"); ?>',
            type: 'POST',
            data: function(d) {
                d.id_tahun_ajaran = currentTahunAjaran;
                d.semester = currentSemester;
                d[$('meta[name="csrf-token"]').attr('content')] = '';
            }
        },
        columns: [
            {data: null, orderable: false},
            {data: 'guru'},
            {data: 'mapel'},
            {data: 'kelas'},
            {data: 'ruangan'},
            {data: 'semester'},
            {data: 'jam_minggu'},
            {data: 'aksi', orderable: false, searchable: false}
        ],
        order: [[1, 'asc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
        },
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        drawCallback: function() {
            // Refresh CSRF after each request
            const api = this.api();
            const json = api.json();
            if (json.csrf_token_name) {
                $('meta[name="csrf-token"]').attr('content', json.csrf_hash);
            }
        }
    });

    // Load dropdown data
    loadDropdownData();

    function loadDropdownData() {
        // Load Guru
        $.ajax({
            url: '<?= site_url("waka/penugasan/get_dropdown_data"); ?>',
            data: {type: 'guru'},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">Pilih Guru</option>';
                    response.data.forEach(function(item) {
                        options += '<option value="' + item.id_guru + '">' + item.nama + '</option>';
                    });
                    $('#id_guru').html(options);
                }
            }
        });

        // Load Mapel
        $.ajax({
            url: '<?= site_url("waka/penugasan/get_dropdown_data"); ?>',
            data: {type: 'mapel'},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">Pilih Mata Pelajaran</option>';
                    response.data.forEach(function(item) {
                        options += '<option value="' + item.id_mapel + '">' + item.nama_mapel + '</option>';
                    });
                    $('#id_mapel').html(options);
                }
            }
        });

        // Load Kelas
        $.ajax({
            url: '<?= site_url("waka/penugasan/get_dropdown_data"); ?>',
            data: {type: 'kelas'},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">Pilih Kelas</option>';
                    response.data.forEach(function(item) {
                        options += '<option value="' + item.id_kelas + '">' + item.nama_kelas + '</option>';
                    });
                    $('#id_kelas').html(options);
                }
            }
        });

        // Load Ruangan
        $.ajax({
            url: '<?= site_url("waka/penugasan/get_dropdown_data"); ?>',
            data: {type: 'ruangan'},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">Pilih Ruangan</option>';
                    response.data.forEach(function(item) {
                        options += '<option value="' + item.id_ruangan + '">' + item.nama_ruangan + '</option>';
                    });
                    $('#id_ruangan').html(options);
                }
            }
        });
    }

    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Pilih...',
        allowClear: true
    });

    // Toggle Ruangan field based on praktikum checkbox
    $('#is_praktikum').on('change', function() {
        if ($(this).is(':checked')) {
            $('#ruanganGroup').removeClass('d-none');
            $('#id_ruangan').prop('required', true);
        } else {
            $('#ruanganGroup').addClass('d-none');
            $('#id_ruangan').prop('required', false).val('');
        }
    });

    // Tombol Tambah
    $('#btnTambah').on('click', function() {
        editMode = false;
        $('#modalTitle').text('Tambah Penugasan');
        resetForm();
        $('#semester').val(currentSemester);
        $('#modalPenugasan').modal('show');
    });

    // Reset Form
    function resetForm() {
        $('#formPenugasan')[0].reset();
        $('#id_penugasan').val('');
        $('#formPenugasan').find('.is-invalid').removeClass('is-invalid');
        $('#formPenugasan').find('.invalid-feedback').text('');
        $('#ruanganGroup').addClass('d-none');
        $('#id_ruangan').prop('required', false);
    }

    // Edit Button (event delegation)
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        editMode = true;
        
        $.ajax({
            url: '<?= site_url("waka/penugasan/get_detail/"); ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#modalTitle').text('Edit Penugasan');
                    $('#id_penugasan').val(data.id_penugasan);
                    $('#id_guru').val(data.id_guru).trigger('change');
                    $('#id_mapel').val(data.id_mapel).trigger('change');
                    $('#id_kelas').val(data.id_kelas).trigger('change');
                    $('#semester').val(data.semester);
                    $('#jam_per_minggu').val(data.jam_per_minggu);
                    
                    if (data.is_praktikum) {
                        $('#is_praktikum').prop('checked', true);
                        $('#ruanganGroup').removeClass('d-none');
                        $('#id_ruangan').prop('required', true);
                        $('#id_ruangan').val(data.id_ruangan).trigger('change');
                    } else {
                        $('#is_praktikum').prop('checked', false);
                        $('#ruanganGroup').addClass('d-none');
                        $('#id_ruangan').prop('required', false);
                    }
                    
                    $('#modalPenugasan').modal('show');
                }
            }
        });
    });

    // Delete Button
    $(document).on('click', '.btn-hapus', function() {
        deleteId = $(this).data('id');
        $('#modalHapus').modal('show');
    });

    // Konfirmasi Hapus
    $('#btnKonfirmasiHapus').on('click', function() {
        if (!deleteId) return;
        
        $.ajax({
            url: '<?= site_url("waka/penugasan/hapus/"); ?>' + deleteId,
            type: 'POST',
            dataType: 'json',
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': 
                    '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Terhapus!', response.message, 'success');
                    table.ajax.reload();
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
                $('#modalHapus').modal('hide');
            }
        });
    });

    // Filter Semester
    $('.dropdown-item[data-semester]').on('click', function(e) {
        e.preventDefault();
        currentSemester = $(this).data('semester');
        $('#filterSemester').html('<i class="fas fa-filter mr-1"></i> Semester: ' + ucfirst(currentSemester));
        table.ajax.reload();
    });

    // Submit Form
    $('#formPenugasan').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#btnSimpan');
        const url = editMode ? 
            '<?= site_url("waka/penugasan/edit/"); ?>' + $('#id_penugasan').val() :
            '<?= site_url("waka/penugasan/tambah"); ?>';

        // Clear previous errors
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');

        // Set button loading
        $btn.prop('disabled', true);
        $btn.find('.btn-text').addClass('d-none');
        $btn.find('.btn-loading').removeClass('d-none');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: $form.serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire('Berhasil!', response.message, 'success');
                    $('#modalPenugasan').modal('hide');
                    table.ajax.reload();
                } else {
                    if (response.errors) {
                        // Show validation errors
                        $.each(response.errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key).next('.invalid-feedback').text(value);
                        });
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error!', 'Terjadi kesalahan pada sistem', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false);
                $btn.find('.btn-text').removeClass('d-none');
                $btn.find('.btn-loading').addClass('d-none');
            }
        });
    });
});
</script>
