<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf_token_name" content="<?php echo $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf_token_hash" content="<?php echo $this->security->get_csrf_hash(); ?>">
    <title>Data Guru - Sistem Penjadwalan</title>
    
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fc; }
        .card { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); border: none; }
        .card-header { background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; }
        .btn-primary { background-color: #4e73df; border-color: #4e73df; }
        .btn-primary:hover { background-color: #2e59d9; border-color: #2653d4; }
    </style>
</head>
<body>
    <!-- Navbar placeholder -->
    <nav class="navbar navbar-expand navbar-dark bg-primary static-top mb-4">
        <a class="navbar-brand mr-1" href="#">Sistem Penjadwalan</a>
        <div class="navbar-nav ml-auto">
            <span class="mr-3 text-white"><?php echo $current_user['nama_lengkap']; ?> (<?php echo ucfirst($current_user['role']); ?>)</span>
            <a href="<?php echo site_url('auth/logout'); ?>" class="text-white"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

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
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>NUPTK</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No HP</th>
                                <th>Status</th>
                                <th>Jam Mengajar</th>
                                <th>Aksi</th>
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- App JS -->
    <script src="<?php echo base_url('assets/js/app.js'); ?>"></script>
    
    <script>
        $(document).ready(function() {
            let editMode = false;
            
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
</body>
</html>
