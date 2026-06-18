<?php $this->load->view('layouts/header'); ?>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa fa-door-open me-2"></i>Data Ruangan</h5>
                        <button type="button" class="btn btn-primary" onclick="openModalTambah()">
                            <i class="fa fa-plus me-1"></i>Tambah Data
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-ruangan" class="table table-hover" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kode</th>
                                        <th>Nama Ruangan</th>
                                        <th>Kapasitas</th>
                                        <th>Lokasi</th>
                                        <th>Fasilitas</th>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Ruangan</h5>
                    <button type="button" class="close btn-close-white" data-dismiss="modal"></button>
                </div>
                <form id="formRuangan" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="ruanganId">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="kode_ruangan" class="form-label">Kode Ruangan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kode_ruangan" name="kode_ruangan" maxlength="10" placeholder="Contoh: R.001" required>
                                <div class="invalid-feedback" id="error-kode"></div>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label for="nama_ruangan" class="form-label">Nama Ruangan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_ruangan" name="nama_ruangan" maxlength="100" placeholder="Contoh: Ruang Teori 1" required>
                                <div class="invalid-feedback" id="error-nama"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="kapasitas" class="form-label">Kapasitas <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="kapasitas" name="kapasitas" min="10" max="100" placeholder="10-100" required>
                                <div class="invalid-feedback" id="error-kapasitas"></div>
                                <small class="text-muted">Rentang: 10 - 100 orang</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tipe" class="form-label">Jenis Ruangan <span class="text-danger">*</span></label>
                                <select class="form-control" id="tipe" name="tipe" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="kelas">Kelas</option>
                                    <option value="lab">Laboratorium</option>
                                    <option value="bengkel">Bengkel</option>
                                    <option value="lapangan">Lapangan</option>
                                    <option value="aula">Aula</option>
                                </select>
                                <div class="invalid-feedback" id="error-tipe"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="lantai" class="form-label">Lantai</label>
                                <input type="text" class="form-control" id="lantai" name="lantai" maxlength="10" placeholder="Contoh: 1, 2, Dasar">
                                <div class="invalid-feedback" id="error-lantai"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="fasilitas" class="form-label">Fasilitas</label>
                            <textarea class="form-control" id="fasilitas" name="fasilitas" rows="3" placeholder="Contoh: AC, Proyektor, Sound System"></textarea>
                            <div class="invalid-feedback" id="error-fasilitas"></div>
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
                    <p>Apakah Anda yakin ingin menghapus data ruangan ini?</p>
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
    <script src="<?= base_url('assets/js/datatables.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    <script src="<?= base_url('assets/js/sweetalert2.all.min.js') ?>"></script>
    <script>
        $(document).ready(function() {
            // Get CSRF token from meta tag or initial load
            let csrfTokenName = $('meta[name="csrf_token_name"]').length ?
                $('meta[name="csrf_token_name"]').attr('content') : '';
            let csrfTokenHash = $('meta[name="csrf_token_hash"]').length ?
                $('meta[name="csrf_token_hash"]').attr('content') : '';

            const table = $('#table-ruangan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?= site_url("datatables/ruangan") ?>',
                    type: 'POST',
                    data: function(d) {
                        if (csrfTokenName && csrfTokenHash) {
                            d[csrfTokenName] = csrfTokenHash;
                        }
                    },
                    dataSrc: function(json) {
                        // Update CSRF tokens from response
                        if (json.csrf_token_name && json.csrf_hash) {
                            csrfTokenName = json.csrf_token_name;
                            csrfTokenHash = json.csrf_hash;
                        }
                        return json.data;
                    }
                },
                columns: [
                    { data: 'no', orderable: false },
                    { data: 'kode' },
                    { data: 'nama' },
                    { data: 'kapasitas' },
                    { data: 'lokasi' },
                    {
                        data: 'fasilitas',
                        render: function(data) {
                            return data ? data.substring(0, 50) + (data.length > 50 ? '...' : '') : '-';
                        }
                    },
                    {
                        data: 'aksi',
                        orderable: false,
                        render: function(data, type, row) {
                            return `<div class="btn-group btn-group-sm">
                                <button class="btn btn-info" onclick="editData(${row.id})"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-danger" onclick="hapusData(${row.id})"><i class="fa fa-trash"></i></button>
                            </div>`;
                        }
                    }
                ],
                order: [[1, 'asc']]
            });

            let modalEdit = false;
            let deleteId = null;
            const modalEl = $('#modalForm');
            const modalHapusEl = $('#modalHapus');

            function resetForm() {
                $('#formRuangan')[0].reset();
                $('#ruanganId').val('');
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#btnSubmit').prop('disabled', false);
                $('#spinner').addClass('d-none');
                $('#btnText').text('Simpan');
            }

            window.openModalTambah = function() {
                modalEdit = false;
                resetForm();
                $('#modalTitle').text('Tambah Ruangan');
                modalEl.modal('show');
            }

            window.editData = function(id) {
                modalEdit = true;
                $('#btnSubmit').prop('disabled', true);

                $.ajax({
                    url: '<?= site_url("admin/ruangan/get_detail") ?>/' + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const data = response.data;
                            $('#modalTitle').text('Edit Ruangan');
                            $('#ruanganId').val(data.id_ruangan || data.id);
                            $('#kode_ruangan').val(data.kode_ruangan);
                            $('#nama_ruangan').val(data.nama_ruangan);
                            $('#kapasitas').val(data.kapasitas);
							$('#tipe').val(data.tipe || '');
                            $('#lantai').val(data.lantai || '');
                            $('#fasilitas').val(data.fasilitas || '');
                            $('#btnSubmit').prop('disabled', false);
                            modalEl.modal('show');
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Data tidak ditemukan' });
                        }
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat mengambil data' });
                        $('#btnSubmit').prop('disabled', false);
                    }
                });
            }

            window.hapusData = function(id) {
                deleteId = id;
                modalHapusEl.modal('show');
            }

            $('#formRuangan').submit(function(e) {
                e.preventDefault();

                const isEdit = modalEdit;
                const url = isEdit ? '<?= site_url("admin/ruangan/edit") ?>/' + $('#ruanganId').val()
                                   : '<?= site_url("admin/ruangan/tambah") ?>';

                const $form = $(this);
                const $btn = $('#btnSubmit');

                // Update CSRF token before submit
                const csrfData = {};
                csrfData[csrfTokenName] = csrfTokenHash;
                $form.find('input[name="' + csrfTokenName + '"]').val(csrfTokenHash);

                $('#btnSubmit').prop('disabled', true);
                $('#spinner').removeClass('d-none');
                $('#btnText').text(isEdit ? 'Menyimpan...' : 'Menambahkan...');
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            modalEl.modal('hide');
                            table.ajax.reload();
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message });
                        } else {
                            if (response.errors) {
                                Object.keys(response.errors).forEach(key => {
                                    $('#' + key).addClass('is-invalid');
                                    $('#error-' + key).text(response.errors[key]);
                                });
                            } else {
                                Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                            }
                            $('#btnSubmit').prop('disabled', false);
                            $('#spinner').addClass('d-none');
                            $('#btnText').text(isEdit ? 'Simpan' : 'Tambah');
                        }
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan pada sistem' });
                        $('#btnSubmit').prop('disabled', false);
                        $('#spinner').addClass('d-none');
                        $('#btnText').text(isEdit ? 'Simpan' : 'Tambah');
                    }
                });
            });

            $('#btnKonfirmasiHapus').click(function() {
                if (!deleteId) return;

                $(this).prop('disabled', true);
                $('#spinnerHapus').removeClass('d-none');

                const csrfData = {};
                csrfData[csrfTokenName] = csrfTokenHash;

                $.ajax({
                    url: '<?= site_url("admin/ruangan/hapus") ?>/' + deleteId,
                    type: 'POST',
                    dataType: 'json',
                    data: csrfData,
                    success: function(response) {
                        modalHapusEl.modal('hide');
                        if (response.success) {
                            table.ajax.reload();
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                        }
                        deleteId = null;
                        $('#btnKonfirmasiHapus').prop('disabled', false);
                        $('#spinnerHapus').addClass('d-none');
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan pada sistem' });
                        deleteId = null;
                        $('#btnKonfirmasiHapus').prop('disabled', false);
                        $('#spinnerHapus').addClass('d-none');
                    }
                });
            });

            modalEl.on('hidden.bs.modal', function() {
                resetForm();
            });

            modalHapusEl.on('hidden.bs.modal', function() {
                deleteId = null;
                $('#btnKonfirmasiHapus').prop('disabled', false);
                $('#spinnerHapus').addClass('d-none');
            });
        });
    </script>

<?php $this->load->view('layouts/footer'); ?>
