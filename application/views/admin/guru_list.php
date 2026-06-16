<?php $this->load->view('layouts/header'); ?>

<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Data Guru</h5>
            <button class="btn btn-primary btn-sm" id="btnTambah">
                <i class="fas fa-plus mr-1"></i> Tambah Guru
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" role="grid" aria-label="Tabel data guru">
                    <caption class="sr-only">Daftar semua guru dengan informasi NIP, nama, email, nomor HP, status, jam mengajar, dan aksi</caption>
                    <thead>
                        <tr>
                            <th scope="col" aria-label="Nomor urut">No</th>
                            <th scope="col">NIP</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Jenis Kelamin</th>
                            <th scope="col">Tempat Lahir</th>
                            <th scope="col">Tanggal Lahir</th>
                            <th scope="col">Pendidikan</th>
                            <th scope="col">Status Kepegawaian</th>
                            <th scope="col">Status Aktif</th>
                            <th scope="col"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Guru -->
<div class="modal fade" id="modalGuru" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Guru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formGuru" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="id_guru" id="id_guru">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nip">NIP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nip" name="nip" maxlength="18" placeholder="18 digit" required>
                                <small class="text-muted">Harus 18 digit angka</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" maxlength="100" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" maxlength="50" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pendidikan_terakhir">Pendidikan Terakhir <span class="text-danger">*</span></label>
                                <select class="form-control" id="pendidikan_terakhir" name="pendidikan_terakhir" required>
                                    <option value="">Pilih Pendidikan</option>
                                    <option value="D3">D3</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                    <option value="D4">D4</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status_kepegawaian">Status Kepegawaian <span class="text-danger">*</span></label>
                                <select class="form-control" id="status_kepegawaian" name="status_kepegawaian" required>
                                    <option value="">Pilih Status</option>
                                    <option value="pns">PNS</option>
                                    <option value="honorer">Honorer</option>
                                    <option value="ttk">Tenaga Tidak Tetap</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="status_aktif">Status Aktif <span class="text-danger">*</span></label>
                                <select class="form-control" id="status_aktif" name="status_aktif" required>
                                    <option value="">Pilih Status</option>
                                    <option value="1">Aktif</option>
                                    <option value="0">Non Aktif</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
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


    
    <script>
        $(document).ready(function() {
            let editMode = false;
            
            // Initialize jValidate untuk form Guru (SRS Bab 13)
            jValidate.init('#formGuru', {
                nip: {
                    required: true,
                    exactLength: 18,
                    numeric: true,
                    messages: {
                        required: 'NIP wajib diisi.',
                        exactLength: 'NIP harus tepat 18 digit.',
                        numeric: 'NIP harus berupa angka.'
                    }
                },
                nama_lengkap: {
                    required: true,
                    minLength: 3,
                    maxLength: 100,
                    messages: {
                        required: 'Nama lengkap wajib diisi.',
                        minLength: 'Nama minimal 3 karakter.',
                        maxLength: 'Nama maksimal 100 karakter.'
                    }
                },
                jenis_kelamin: {
                    required: true,
                    messages: {
                        required: 'Jenis kelamin wajib dipilih.'
                    }
                },
                tempat_lahir: {
                    required: true,
                    maxLength: 50,
                    messages: {
                        required: 'Tempat lahir wajib diisi.',
                        maxLength: 'Tempat lahir maksimal 50 karakter.'
                    }
                },
                tanggal_lahir: {
                    required: true,
                    messages: {
                        required: 'Tanggal lahir wajib diisi.'
                    }
                },
                pendidikan_terakhir: {
                    required: true,
                    messages: {
                        required: 'Pendidikan terakhir wajib dipilih.'
                    }
                },
                status_kepegawaian: {
                    required: true,
                    messages: {
                        required: 'Status kepegawaian wajib dipilih.'
                    }
                },
                status_aktif: {
                    required: true,
                    messages: {
                        required: 'Status aktif wajib dipilih.'
                    }
                }
            });

            // Tambahkan data-label untuk setiap field untuk error message yang lebih baik
            $('#nip').data('label', 'NIP');
            $('#nama_lengkap').data('label', 'Nama Lengkap');
            $('#jenis_kelamin').data('label', 'Jenis Kelamin');
            $('#tempat_lahir').data('label', 'Tempat Lahir');
            $('#tanggal_lahir').data('label', 'Tanggal Lahir');
            $('#pendidikan_terakhir').data('label', 'Pendidikan Terakhir');
            $('#status_kepegawaian').data('label', 'Status Kepegawaian');
            $('#status_aktif').data('label', 'Status Aktif');
            
            // Initialize DataTable
            const table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?php echo site_url("datatables/guru"); ?>',
                    type: 'POST',
                    data: function(d) {
                        d[$('meta[name="csrf_token_name"]').attr('content')] = 
                            $('meta[name="csrf_token_hash"]').attr('content');
                    }
                },
                columns: [
                    {data: 'no', orderable: false},
                    {data: 'nip'},
                    {data: 'nama'},
                    {data: 'jenis_kelamin'},
                    {data: 'tempat_lahir'},
                    {data: 'tanggal_lahir'},
                    {data: 'pendidikan'},
                    {data: 'status_kepegawaian'},
                    {data: 'status_aktif'},
                    {data: 'aksi', orderable: false, searchable: false}
                ],
                order: [[1, 'desc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
                },
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100]
            });

            // Refresh CSRF after each request
            $(document).ajaxComplete(function(event, xhr, settings) {
                const response = xhr.responseJSON;
                if (response && response.csrf_token_name) {
                    $('meta[name="csrf_token_name"]').attr('content', response.csrf_token_name);
                    $('meta[name="csrf_token_hash"]').attr('content', response.csrf_hash);
                }
            });

            // Tombol Tambah
            $('#btnTambah').on('click', function() {
                editMode = false;
                $('#modalTitle').text('Tambah Guru');
                resetForm();
                $('#modalGuru').modal('show');
            });

            // Reset Form
            function resetForm() {
                $('#formGuru')[0].reset();
                $('#id_guru').val('');
                $('#formGuru').find('.is-invalid').removeClass('is-invalid');
                $('#formGuru').find('.invalid-feedback').text('');
            }

            // Edit Button (event delegation)
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                editMode = true;
                
                $.ajax({
                    url: '<?php echo site_url("admin/guru/get_detail/"); ?>' + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const data = response.data;
                            $('#modalTitle').text('Edit Guru');
                            $('#id_guru').val(data.id_guru);
                            $('#nip').val(data.nip);
                            $('#nama_lengkap').val(data.nama_lengkap);
                            $('#jenis_kelamin').val(data.jenis_kelamin);
                            $('#tempat_lahir').val(data.tempat_lahir);
                            $('#tanggal_lahir').val(data.tanggal_lahir);
                            $('#pendidikan_terakhir').val(data.pendidikan_terakhir);
                            $('#status_kepegawaian').val(data.status_kepegawaian);
                            $('#status_aktif').val(data.status_aktif);
                            
                            $('#modalGuru').modal('show');
                        }
                    }
                });
            });

            // Delete Button
            $(document).on('click', '.btn-hapus', function() {
                const id = $(this).data('id');
                
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data guru yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?php echo site_url("admin/guru/hapus/"); ?>' + id,
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
                            }
                        });
                    }
                });
            });

            // Submit Form
            $('#formGuru').on('submit', function(e) {
                e.preventDefault();
                
                const $form = $(this);
                const $btn = $('#btnSimpan');
                const url = editMode ? 
                    '<?php echo site_url("admin/guru/edit/"); ?>' + $('#id_guru').val() :
                    '<?php echo site_url("admin/guru/tambah"); ?>';

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
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            $('#modalGuru').modal('hide');
                            table.ajax.reload();
                        } else {
                            if (response.errors) {
                                showValidationErrors($form, response.errors);
                            } else {
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan pada server', 'error');
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $btn.find('.btn-text').removeClass('d-none');
                        $btn.find('.btn-loading').addClass('d-none');
                    }
                });
            });

            // Handle modal close
            $('#modalGuru').on('hidden.bs.modal', function() {
                resetForm();
            });
        });
    </script>


<?php $this->load->view('layouts/footer'); ?>