<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kelas - Admin</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/datatables.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/font-awesome.min.css') ?>">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --danger-color: #dc2626;
            --danger-hover: #b91c1c;
            --success-color: #16a34a;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-500: #6b7280;
            --gray-700: #374151;
        }
        
        body { background-color: var(--gray-100); font-family: 'Inter', sans-serif; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .card-header { background: white; border-bottom: 1px solid var(--gray-200); padding: 1.5rem; border-radius: 12px 12px 0 0 !important; }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background-color: var(--primary-hover); border-color: var(--primary-hover); }
        .btn-danger { background-color: var(--danger-color); border-color: var(--danger-color); }
        .btn-danger:hover { background-color: var(--danger-hover); border-color: var(--danger-hover); }
        .table thead th { background-color: var(--gray-50); color: var(--gray-700); font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
        .modal-header { border-bottom: 1px solid var(--gray-200); }
        .modal-footer { border-top: 1px solid var(--gray-200); }
        .form-label { font-weight: 500; color: var(--gray-700); margin-bottom: 0.5rem; }
        .is-invalid { border-color: var(--danger-color) !important; }
        .invalid-feedback { display: block; color: var(--danger-color); font-size: 0.875rem; margin-top: 0.25rem; }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa fa-chalkboard me-2"></i>Data Kelas</h5>
                        <button type="button" class="btn btn-primary" onclick="openModalTambah()">
                            <i class="fa fa-plus me-1"></i>Tambah Data
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-kelas" class="table table-hover" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kode</th>
                                        <th>Nama Kelas</th>
                                        <th>Tingkat</th>
                                        <th>Jurusan</th>
                                        <th>Kapasitas</th>
                                        <th>Tahun Ajaran</th>
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
                    <h5 class="modal-title" id="modalTitle">Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formKelas">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="kelasId">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode" class="form-label">Kode Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kode" name="kode" maxlength="20" placeholder="Contoh: X-RPL-1" required>
                                <div class="invalid-feedback" id="error-kode"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" maxlength="100" placeholder="Contoh: Kelas X RPL 1" required>
                                <div class="invalid-feedback" id="error-nama"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tingkat" class="form-label">Tingkat <span class="text-danger">*</span></label>
                                <select class="form-select" id="tingkat" name="tingkat" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    <option value="1">Tingkat 1 (X)</option>
                                    <option value="2">Tingkat 2 (XI)</option>
                                    <option value="3">Tingkat 3 (XII)</option>
                                    <option value="4">Tingkat 4 (XIII)</option>
                                    <option value="5">Tingkat 5 (XIV)</option>
                                    <option value="6">Tingkat 6 (XV)</option>
                                </select>
                                <div class="invalid-feedback" id="error-tingkat"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="kapasitas" class="form-label">Kapasitas Siswa <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="kapasitas" name="kapasitas" min="10" max="50" placeholder="10-50" required>
                                <div class="invalid-feedback" id="error-kapasitas"></div>
                                <small class="text-muted">Rentang: 10 - 50 siswa</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="jurusan" class="form-label">Jurusan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="jurusan" name="jurusan" maxlength="50" placeholder="Contoh: RPL" required>
                                <div class="invalid-feedback" id="error-jurusan"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tahun_ajaran" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" maxlength="9" placeholder="2024/2025" required>
                            <div class="invalid-feedback" id="error-tahun_ajaran"></div>
                            <small class="text-muted">Format: YYYY/YYYY (contoh: 2024/2025)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data kelas ini?</p>
                    <p class="mb-0 text-danger"><strong>Tindakan ini tidak dapat dibatalkan!</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
    <script>
        $(document).ready(function() {
            // DataTables
            const table = $('#table-kelas').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?= site_url("datatables/kelas") ?>',
                    type: 'POST'
                },
                columns: [
                    { data: 'no', orderable: false },
                    { data: 'kode' },
                    { data: 'nama' },
                    { data: 'tingkat' },
                    { data: 'jurusan' },
                    { data: 'kapasitas' },
                    { data: 'tahun_ajaran' },
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
                order: [[1, 'asc']]
            });

            let modalEdit = false;
            let deleteId = null;
            const modal = new bootstrap.Modal(document.getElementById('modalForm'));
            const modalHapus = new bootstrap.Modal(document.getElementById('modalHapus'));

            function resetForm() {
                $('#formKelas')[0].reset();
                $('#kelasId').val('');
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#btnSubmit').prop('disabled', false);
                $('#spinner').addClass('d-none');
                $('#btnText').text('Simpan');
            }

            window.openModalTambah = function() {
                modalEdit = false;
                resetForm();
                $('#modalTitle').text('Tambah Kelas');
                modal.show();
            }

            window.editData = function(id) {
                modalEdit = true;
                $('#btnSubmit').prop('disabled', true);
                
                $.ajax({
                    url: '<?= site_url("admin/kelas/get_detail") ?>/' + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const data = response.data;
                            $('#modalTitle').text('Edit Kelas');
                            $('#kelasId').val(data.id);
                            $('#kode').val(data.kode);
                            $('#nama').val(data.nama);
                            $('#tingkat').val(data.tingkat);
                            $('#kapasitas').val(data.kapasitas);
                            $('#jurusan').val(data.jurusan);
                            $('#tahun_ajaran').val(data.tahun_ajaran);
                            $('#btnSubmit').prop('disabled', false);
                            modal.show();
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

            window.hapusData = function(id) {
                deleteId = id;
                modalHapus.show();
            }

            $('#formKelas').submit(function(e) {
                e.preventDefault();
                
                const isEdit = modalEdit;
                const url = isEdit ? '<?= site_url("admin/kelas/edit") ?>/' + $('#kelasId').val() 
                                   : '<?= site_url("admin/kelas/tambah") ?>';
                
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
                            modal.hide();
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

                $.ajax({
                    url: '<?= site_url("admin/kelas/hapus") ?>/' + deleteId,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        modalHapus.hide();
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

            $('#modalHapus').on('hidden.bs.modal', function() {
                deleteId = null;
                $('#btnKonfirmasiHapus').prop('disabled', false);
                $('#spinnerHapus').addClass('d-none');
            });
        });
    </script>
</body>
</html>
