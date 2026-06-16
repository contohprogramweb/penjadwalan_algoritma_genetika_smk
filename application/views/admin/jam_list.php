<?php $this->load->view('layouts/header'); ?>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-clock mr-2"></i>Data Jam Pelajaran</h5>
                        <button type="button" class="btn btn-primary" onclick="openModalTambah()">
                            <i class="fas fa-plus mr-1"></i>Tambah Data
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-jam" class="table table-hover w-100">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Jam Ke-</th>
                                        <th>Waktu Mulai</th>
                                        <th>Waktu Selesai</th>
                                        <th>Durasi (menit)</th>
                                        <th>Keterangan</th>
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

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Jam Pelajaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formTambah">
                        <div class="form-group">
                            <label>Jam Ke- <span class="text-danger">*</span></label>
                            <input type="number" name="slot" id="slot" class="form-control" min="1" max="20" required>
                            <small class="text-muted">Nomor urut jam pelajaran (1-20)</small>
                        </div>
                        <div class="form-group">
                            <label>Waktu Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="waktu_mulai" id="waktu_mulai" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Waktu Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="waktu_selesai" id="waktu_selesai" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <select name="is_istirahat" id="is_istirahat" class="form-control">
                                <option value="0">Jam Reguler</option>
                                <option value="1">Jam Istirahat</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpan" onclick="simpanData()">
                        <span id="spinnerSimpan" class="spinner-border spinner-border-sm d-none mr-1" role="status"></span>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Jam Pelajaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formEdit">
                        <input type="hidden" name="id_jam" id="edit_id_jam">
                        <div class="form-group">
                            <label>Jam Ke- <span class="text-danger">*</span></label>
                            <input type="number" name="slot" id="edit_slot" class="form-control" min="1" max="20" required>
                        </div>
                        <div class="form-group">
                            <label>Waktu Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="waktu_mulai" id="edit_waktu_mulai" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Waktu Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="waktu_selesai" id="edit_waktu_selesai" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <select name="is_istirahat" id="edit_is_istirahat" class="form-control">
                                <option value="0">Jam Reguler</option>
                                <option value="1">Jam Istirahat</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnUpdate" onclick="updateData()">
                        <span id="spinnerUpdate" class="spinner-border spinner-border-sm d-none mr-1" role="status"></span>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-labelledby="modalHapusLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalHapusLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus jam pelajaran ini? Tindakan ini tidak dapat dibatalkan.</p>
                    <input type="hidden" id="hapus_id_jam">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btnKonfirmasiHapus" onclick="hapusData()">
                        <span id="spinnerHapus" class="spinner-border spinner-border-sm d-none mr-1" role="status"></span>
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>
var CSRF_NAME  = $('meta[name="csrf_token_name"]').attr('content');
var CSRF_HASH  = $('meta[name="csrf_token_hash"]').attr('content');
var BASE_URL   = '<?= site_url() ?>';

$(document).ready(function() {
    var table = $('#table-jam').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: BASE_URL + 'datatables/jam',
            type: 'POST',
            data: function(d) {
                d[CSRF_NAME] = CSRF_HASH;
            }
        },
        columns: [
            { data: null, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, orderable: false },
            { data: 'slot' },
            { data: 'waktu_mulai' },
            { data: 'waktu_selesai' },
            { data: 'durasi_menit', defaultContent: '-' },
            { data: 'keterangan' },
            { data: null, render: function(data, type, row) {
                return '<button class="btn btn-sm btn-warning mr-1" onclick="editData(' + row.id_jam + ')"><i class="fas fa-edit"></i></button>' +
                       '<button class="btn btn-sm btn-danger" onclick="konfirmasiHapus(' + row.id_jam + ')"><i class="fas fa-trash"></i></button>';
            }, orderable: false }
        ],
        language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json' },
        order: [[1, 'asc']]
    });

    // Refresh CSRF on each draw
    table.on('xhr', function() {
        CSRF_HASH = $('meta[name="csrf_token_hash"]').attr('content');
    });
});

function openModalTambah() {
    $('#formTambah')[0].reset();
    $('#modalTambah').modal('show');
}

function simpanData() {
    var data = {
        slot: $('#slot').val(),
        waktu_mulai: $('#waktu_mulai').val(),
        waktu_selesai: $('#waktu_selesai').val(),
        is_istirahat: $('#is_istirahat').val()
    };
    data[CSRF_NAME] = CSRF_HASH;

    $('#btnSimpan').prop('disabled', true);
    $('#spinnerSimpan').removeClass('d-none');

    $.ajax({
        url: BASE_URL + 'admin/jam/tambah',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(res) {
            CSRF_HASH = res.csrf_hash || CSRF_HASH;
            if (res.success) {
                $('#modalTambah').modal('hide');
                $('#table-jam').DataTable().ajax.reload();
                Swal.fire('Berhasil', res.message, 'success');
            } else {
                Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
            }
        },
        error: function() { Swal.fire('Error', 'Gagal menghubungi server.', 'error'); },
        complete: function() {
            $('#btnSimpan').prop('disabled', false);
            $('#spinnerSimpan').addClass('d-none');
        }
    });
}

function editData(id) {
    $.get(BASE_URL + 'admin/jam/get_detail/' + id, function(res) {
        if (res.success) {
            var d = res.data;
            $('#edit_id_jam').val(d.id_jam);
            $('#edit_slot').val(d.slot);
            $('#edit_waktu_mulai').val(d.waktu_mulai);
            $('#edit_waktu_selesai').val(d.waktu_selesai);
            $('#edit_is_istirahat').val(d.is_istirahat);
            $('#modalEdit').modal('show');
        } else {
            Swal.fire('Error', 'Data tidak ditemukan.', 'error');
        }
    }, 'json');
}

function updateData() {
    var data = {
        id_jam: $('#edit_id_jam').val(),
        slot: $('#edit_slot').val(),
        waktu_mulai: $('#edit_waktu_mulai').val(),
        waktu_selesai: $('#edit_waktu_selesai').val(),
        is_istirahat: $('#edit_is_istirahat').val()
    };
    data[CSRF_NAME] = CSRF_HASH;

    $('#btnUpdate').prop('disabled', true);
    $('#spinnerUpdate').removeClass('d-none');

    $.ajax({
        url: BASE_URL + 'admin/jam/edit/' + data.id_jam,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(res) {
            CSRF_HASH = res.csrf_hash || CSRF_HASH;
            if (res.success) {
                $('#modalEdit').modal('hide');
                $('#table-jam').DataTable().ajax.reload();
                Swal.fire('Berhasil', res.message, 'success');
            } else {
                Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
            }
        },
        error: function() { Swal.fire('Error', 'Gagal menghubungi server.', 'error'); },
        complete: function() {
            $('#btnUpdate').prop('disabled', false);
            $('#spinnerUpdate').addClass('d-none');
        }
    });
}

function konfirmasiHapus(id) {
    $('#hapus_id_jam').val(id);
    $('#modalHapus').modal('show');
}

function hapusData() {
    var id = $('#hapus_id_jam').val();
    var data = {};
    data[CSRF_NAME] = CSRF_HASH;

    $('#btnKonfirmasiHapus').prop('disabled', true);
    $('#spinnerHapus').removeClass('d-none');

    $.ajax({
        url: BASE_URL + 'admin/jam/hapus/' + id,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(res) {
            CSRF_HASH = res.csrf_hash || CSRF_HASH;
            if (res.success) {
                $('#modalHapus').modal('hide');
                $('#table-jam').DataTable().ajax.reload();
                Swal.fire('Berhasil', res.message, 'success');
            } else {
                Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
            }
        },
        error: function() { Swal.fire('Error', 'Gagal menghubungi server.', 'error'); },
        complete: function() {
            $('#btnKonfirmasiHapus').prop('disabled', false);
            $('#spinnerHapus').addClass('d-none');
        }
    });
}
</script>

<?php $this->load->view('layouts/footer'); ?>
