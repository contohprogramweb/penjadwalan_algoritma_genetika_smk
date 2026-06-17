<?php $this->load->view('layouts/header'); ?>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa fa-book me-2"></i>Data Mata Pelajaran</h5>
                        <button type="button" class="btn btn-primary" onclick="openModalTambah()">
                            <i class="fa fa-plus me-1"></i>Tambah Data
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-mapel" class="table table-hover" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kode</th>
                                        <th>Nama Mapel</th>
                                        <th>Tipe</th>
                                        <th>JP/Minggu</th>
                                        <th>Semester</th>
                                        <th width="15%">Aksi</th>
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

    <!-- Modal Tambah/Edit -->
    <div class="modal fade" id="modalForm" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Mapel</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <form id="formMapel">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="mapelId">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="kode_mapel" class="form-label">Kode Mapel <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kode_mapel" name="kode_mapel" maxlength="10" placeholder="Contoh: MTK-01" required>
                            <div class="invalid-feedback" id="error-kode"></div>
                            <small class="text-muted">Maksimal 10 karakter, huruf/angka/strip</small>
                        </div>

                        <div class="mb-3">
                            <label for="nama_mapel" class="form-label">Nama Mapel <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_mapel" name="nama_mapel" maxlength="100" placeholder="Contoh: Matematika Wajib" required>
                            <div class="invalid-feedback" id="error-nama"></div>
                        </div>

                        <div class="mb-3">
                            <label for="tipe" class="form-label">Tipe Mapel <span class="text-danger">*</span></label>
                            <select class="form-select" id="tipe" name="tipe" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="teori">Teori</option>
                                <option value="praktikum">Praktikum</option>
                            </select>
                            <div class="invalid-feedback" id="error-tipe"></div>
                        </div>

                        <div class="mb-3">
                            <label for="jam_per_minggu" class="form-label">JP Per Minggu <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="jam_per_minggu" name="jam_per_minggu" min="1" max="48" placeholder="1-48" required>
                            <div class="invalid-feedback" id="error-jp_per_minggu"></div>
                            <small class="text-muted">Rentang: 1 - 48 jam pelajaran</small>
                        </div>

                        <div class="mb-3">
                            <label for="kelompok" class="form-label">Kelompok Mapel <span class="text-danger">*</span></label>
                            <select class="form-control" id="kelompok" name="kelompok" required>
                                <option value="">-- Pilih Kelompok --</option>
                                <option value="A">Kelompok A (Normatif)</option>
                                <option value="B">Kelompok B (Adaptif)</option>
                                <option value="C">Kelompok C (Produktif)</option>
                                <option value="D">Kelompok D (Muatan Lokal)</option>
                            </select>
                            <div class="invalid-feedback" id="error-kelompok"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <span class="spinner-border spinner-border-sm me-1 d-none" id="spinner"></span>
                            <span id="btnText">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div class="modal fade" id="modalHapus" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fa fa-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                    <button type="button" class="close btn-close-white" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data mapel ini?</p>
                    <p class="mb-0 text-danger"><strong>Tindakan ini tidak dapat dibatalkan!</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btnKonfirmasiHapus">
                        <span class="spinner-border spinner-border-sm me-1 d-none" id="spinnerHapus"></span>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    
	 <!-- DataTables JS - Menggunakan CDN -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
	
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    <script>
        $(document).ready(function() {

            // Initialize jValidate untuk form Mapel (SRS Bab 13)
            jValidate.init('#formMapel', {
                kode: {
                    required: true,
                    maxLength: 10,
                    pattern: /^[a-zA-Z0-9\\-]+$/,
                    messages: {
                        required: 'Kode mapel wajib diisi.',
                        maxLength: 'Kode mapel maksimal 10 karakter.',
                        pattern: 'Kode hanya boleh berisi huruf, angka, dan tanda strip (-).'
                    }
                },
                nama: {
                    required: true,
                    minLength: 3,
                    maxLength: 100,
                    messages: {
                        required: 'Nama mata pelajaran wajib diisi.',
                        minLength: 'Nama minimal 3 karakter.',
                        maxLength: 'Nama maksimal 100 karakter.'
                    }
                },
                tipe: {
                    required: true,
                    messages: {
                        required: 'Tipe mapel wajib dipilih.'
                    }
                },
                jp_per_minggu: {
                    required: true,
                    min: 1,
                    max: 48,
                    messages: {
                        required: 'JP per minggu wajib diisi.',
                        min: 'Minimal 1 jam pelajaran.',
                        max: 'Maksimal 48 jam pelajaran.'
                    }
                },
                kelompok: {
                    required: true,
                    messages: {
                        required: 'Semester wajib dipilih.'
                    }
                }
            });

            // Tambahkan data-label untuk setiap field
            $('#kode_mapel').data('label', 'Kode Mapel');
            $('#nama_mapel').data('label', 'Nama Mata Pelajaran');
            $('#tipe').data('label', 'Tipe Mapel');
            $('#jam_per_minggu').data('label', 'JP Per Minggu');
            $('#kelompok').data('label', 'Kelompok');

            // Setup CSRF dari app.js
            var CSRF_NAME  = $('meta[name="csrf_token_name"]').attr('content');
            var CSRF_HASH  = $('meta[name="csrf_token_hash"]').attr('content');
            
            $.ajaxSetup({
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: function(d) {
                    d[CSRF_NAME] = CSRF_HASH;
                }
            });

            // DataTables
            const table = $('#table-mapel').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?= site_url("datatables/mapel") ?>',
                    type: 'POST'
                },
                columns: [
                    { data: 'no', orderable: false },
                    { data: 'kode' },
                    { data: 'nama' },
                    { 
                        data: 'tipe',
                        render: function(data) {
                            const badgeClass = data === 'teori' ? 'badge-info' : 'badge-warning';
                            return `<span class="badge ${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                        }
                    },
                    { 
                        data: 'jp_per_minggu',
                        render: function(data) {
                            return data + ' JP';
                        }
                    },
                    { data: 'semester' },
                    { 
                        data: 'kelompok',
                        render: function(data) {
                            return 'Kelompok ' + data;
                        }
                    },
                    { 
                        data: 'aksi',
                        orderable: false,
                        render: function(data) {
                            return `<div class="btn-group btn-group-sm">
                                <button class="btn btn-info" onclick="editData(${data.id})"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-danger" onclick="hapusData(${data.id})"><i class="fa fa-trash"></i></button>
                            </div>`;
                        }
                    }
                ],
                order: [[1, 'asc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json',
                    processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
                }
            });

            let modalEdit = false;
            let deleteId = null;
            const modal = $('#modalForm').modal({show: false});
            const modalHapus = $('#modalHapus').modal({show: false});

            // Reset form
            function resetForm() {
                $('#formMapel')[0].reset();
                $('#mapelId').val('');
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#btnSubmit').prop('disabled', false);
                $('#spinner').addClass('d-none');
                $('#btnText').text('Simpan');
            }

            // Open modal tambah
            window.openModalTambah = function() {
                modalEdit = false;
                resetForm();
                $('#modalTitle').text('Tambah Mapel');
                $('#modalForm').modal('show');
            }

            // Edit data
            window.editData = function(id) {
                modalEdit = true;
                $('#btnSubmit').prop('disabled', true);
                
                $.ajax({
                    url: '<?= site_url("admin/mapel/get_detail") ?>/' + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const data = response.data;
                            $('#modalTitle').text('Edit Mapel');
                            $('#mapelId').val(data.id);
                            $('#kode_mapel').val(data.kode_mapel);
                            $('#nama_mapel').val(data.nama_mapel);
                            $('#tipe').val(data.tipe);
                            $('#jam_per_minggu').val(data.jam_per_minggu);
                            $('#kelompok').val(data.kelompok);
                            $('#btnSubmit').prop('disabled', false);
                            $('#modalForm').modal('show');
                        } else {
                            alert('Data tidak ditemukan');
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat mengambil data');
                        $('#btnSubmit').prop('disabled', false);
                    }
                });
            }

            // Hapus data
            window.hapusData = function(id) {
                deleteId = id;
                $('#modalHapus').modal('show');
            }

            // Submit form
            $('#formMapel').submit(function(e) {
                e.preventDefault();
                
                const isEdit = modalEdit;
                const url = isEdit ? '<?= site_url("admin/mapel/edit") ?>/' + $('#mapelId').val() 
                                   : '<?= site_url("admin/mapel/tambah") ?>';
                
                $('#btnSubmit').prop('disabled', true);
                $('#spinner').removeClass('d-none');
                $('#btnText').text(isEdit ? 'Menyimpan...' : 'Menambahkan...');
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#modalForm').modal('hide');
                            table.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                        } else {
                            if (response.errors) {
                                Object.keys(response.errors).forEach(key => {
                                    $('#' + key).addClass('is-invalid');
                                    $('#error-' + key).text(response.errors[key]);
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message
                                });
                            }
                            $('#btnSubmit').prop('disabled', false);
                            $('#spinner').addClass('d-none');
                            $('#btnText').text(isEdit ? 'Simpan' : 'Tambah');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan pada sistem'
                        });
                        $('#btnSubmit').prop('disabled', false);
                        $('#spinner').addClass('d-none');
                        $('#btnText').text(isEdit ? 'Simpan' : 'Tambah');
                    }
                });
            });

            // Konfirmasi hapus
            $('#btnKonfirmasiHapus').click(function() {
                if (!deleteId) return;
                
                $(this).prop('disabled', true);
                $('#spinnerHapus').removeClass('d-none');

                $.ajax({
                    url: '<?= site_url("admin/mapel/hapus") ?>/' + deleteId,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        $('#modalHapus').modal('hide');
                        if (response.success) {
                            table.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            });
                        }
                        deleteId = null;
                        $('#btnKonfirmasiHapus').prop('disabled', false);
                        $('#spinnerHapus').addClass('d-none');
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan pada sistem'
                        });
                        deleteId = null;
                        $('#btnKonfirmasiHapus').prop('disabled', false);
                        $('#spinnerHapus').addClass('d-none');
                    }
                });
            });

            // Reset modal when hidden
            $('#modalForm').on('hidden.bs.modal', function() {
                resetForm();
            });

            $('#modalHapus').on('hidden.bs.modal', function() {
                deleteId = null;
                $('#btnKonfirmasiHapus').prop('disabled', false);
                $('#spinnerHapus').addClass('d-none');
            });
        });
    </script>

<?php $this->load->view('layouts/footer'); ?>
