<?= $this->load->view('layouts/header'); ?>

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
                    <caption class="sr-only">Daftar semua guru dengan informasi NIP, NUPTK, nama, email, nomor HP, status, jam mengajar, dan aksi</caption>
                    <thead>
                        <tr>
                            <th scope="col" aria-label="Nomor urut">No</th>
                            <th scope="col">NIP</th>
                            <th scope="col">NUPTK</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Email</th>
                            <th scope="col">No HP</th>
                            <th scope="col">Status</th>
                            <th scope="col">Jam Mengajar</th>
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
                                <label for="nuptk">NUPTK <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nuptk" name="nuptk" maxlength="16" placeholder="16 digit" required>
                                <small class="text-muted">Harus 16 digit angka</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama" maxlength="100" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="100" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_hp">Nomor HP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" maxlength="15" placeholder="08xxxxxxxxxx" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="jam_min">Jam Minimal <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="jam_min" name="jam_min" min="1" max="48" value="1" required>
                                <small class="text-muted">1-48</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="jam_maks">Jam Maksimal <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="jam_maks" name="jam_maks" min="1" max="48" value="24" required>
                                <small class="text-muted">1-48, harus >= jam minimal</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status Kepegawaian <span class="text-danger">*</span></label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="pns">PNS</option>
                                    <option value="honorer">Honorer</option>
                                    <option value="ttk">Tenaga Tidak Tetap</option>
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

<?= $this->load->view('layouts/footer'); ?>
    
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
                nuptk: {
                    required: true,
                    exactLength: 16,
                    numeric: true,
                    messages: {
                        required: 'NUPTK wajib diisi.',
                        exactLength: 'NUPTK harus tepat 16 digit.',
                        numeric: 'NUPTK harus berupa angka.'
                    }
                },
                nama: {
                    required: true,
                    minLength: 3,
                    maxLength: 100,
                    messages: {
                        required: 'Nama lengkap wajib diisi.',
                        minLength: 'Nama minimal 3 karakter.',
                        maxLength: 'Nama maksimal 100 karakter.'
                    }
                },
                email: {
                    required: true,
                    email: true,
                    maxLength: 100,
                    messages: {
                        required: 'Email wajib diisi.',
                        email: 'Format email tidak valid.',
                        maxLength: 'Email maksimal 100 karakter.'
                    }
                },
                no_hp: {
                    required: true,
                    minLength: 10,
                    maxLength: 15,
                    numeric: true,
                    messages: {
                        required: 'Nomor HP wajib diisi.',
                        minLength: 'Nomor HP minimal 10 digit.',
                        maxLength: 'Nomor HP maksimal 15 digit.',
                        numeric: 'Nomor HP harus berupa angka.'
                    }
                },
                jam_min: {
                    required: true,
                    min: 1,
                    max: 48,
                    messages: {
                        required: 'Jam minimal wajib diisi.',
                        min: 'Jam minimal minimal 1.',
                        max: 'Jam maksimal maksimal 48.'
                    }
                },
                jam_maks: {
                    required: true,
                    min: 1,
                    max: 48,
                    custom: function(value, $field) {
                        const jamMin = parseInt($('#jam_min').val()) || 0;
                        if (parseInt(value) < jamMin) {
                            return 'Jam maksimal harus lebih besar atau sama dengan jam minimal.';
                        }
                        return true;
                    },
                    messages: {
                        required: 'Jam maksimal wajib diisi.',
                        min: 'Jam maksimal minimal 1.',
                        max: 'Jam maksimal maksimal 48.'
                    }
                },
                status: {
                    required: true,
                    messages: {
                        required: 'Status kepegawaian wajib dipilih.'
                    }
                }
            });

            // Tambahkan data-label untuk setiap field untuk error message yang lebih baik
            $('#nip').data('label', 'NIP');
            $('#nuptk').data('label', 'NUPTK');
            $('#nama').data('label', 'Nama Lengkap');
            $('#email').data('label', 'Email');
            $('#no_hp').data('label', 'Nomor HP');
            $('#jam_min').data('label', 'Jam Minimal');
            $('#jam_maks').data('label', 'Jam Maksimal');
            $('#status').data('label', 'Status Kepegawaian');
            
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
                    {data: null, orderable: false},
                    {data: 'nip'},
                    {data: 'nuptk'},
                    {data: 'nama'},
                    {data: 'email'},
                    {data: 'no_hp'},
                    {data: 'status'},
                    {data: 'jam_mengajar'},
                    {data: 'aksi', orderable: false, searchable: false}
                ],
                order: [[3, 'asc']],
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
                            $('#nuptk').val(data.nuptk);
                            $('#nama').val(data.nama);
                            $('#email').val(data.email);
                            $('#no_hp').val(data.no_hp);
                            $('#jam_min').val(data.jam_min);
                            $('#jam_maks').val(data.jam_maks);
                            $('#status').val(data.status);
                            
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

