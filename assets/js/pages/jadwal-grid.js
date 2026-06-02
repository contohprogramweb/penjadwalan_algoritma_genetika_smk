/**
 * Script: jadwal-grid.js
 * Deskripsi: Menangani interaksi pada halaman Grid Jadwal Mingguan.
 * Fitur: Edit slot, cek konflik AJAX, update DOM tanpa reload, approve jadwal.
 * Referensi SRS: Bab 11.8 (Use Case Review & Edit Jadwal)
 */

let currentEditData = {};
let editModal;

$(document).ready(function() {
    // Inisialisasi modal Bootstrap
    editModal = new bootstrap.Modal(document.getElementById('editSlotModal'));
    
    // Setup event listener untuk tombol Approve
    $('#btn-approve').on('click', function() {
        approveJadwal();
    });
    
    // Setup event listener untuk change dropdown penugasan
    $('#edit-id-penugasan').on('change', function() {
        checkConflictOnEdit();
    });
    
    // Setup event listener untuk change dropdown ruangan
    $('#edit-id-ruangan').on('change', function() {
        checkConflictOnEdit();
    });
    
    // Setup event listener untuk tombol simpan
    $('#btn-simpan-edit').on('click', function() {
        saveSlotEdit();
    });
});

/**
 * Fungsi: openEditModal(element)
 * Dipanggil saat user klik pada sel jadwal
 * @param {HTMLElement} el - TD element yang diklik
 */
function openEditModal(el) {
    // Ambil data dari attributes
    const idJadwalDetail = $(el).data('id');
    const namaKelas = $(el).data('nama-kelas');
    const hari = $(el).data('hari');
    const slot = $(el).data('slot');
    const idPenugasan = $(el).data('id-penugasan');
    const idRuangan = $(el).data('id-ruangan');
    const sifat = $(el).data('sifat');
    const idKelas = $(el).data('id-kelas');
    
    // Simpan data saat ini
    currentEditData = {
        idJadwalDetail: idJadwalDetail,
        idKelas: idKelas,
        hari: hari,
        slot: slot,
        sifat: sifat
    };
    
    // Populate form modal
    $('#edit-id-jadwal-detail').val(idJadwalDetail);
    $('#edit-nama-kelas').val(namaKelas);
    $('#edit-hari').val(hari);
    $('#edit-slot').val(slot);
    
    // Reset conflict alert
    hideConflictAlert();
    $('#btn-simpan-edit').prop('disabled', true);
    
    // Load dropdown penugasan via AJAX
    loadPenugasanDropdown(idKelas, idPenugasan);
    
    // Set nilai dropdown ruangan
    $('#edit-id-ruangan').val(idRuangan);
    
    // Tampilkan modal
    editModal.show();
}

/**
 * Fungsi: loadPenugasanDropdown(idKelas, selectedId)
 * Load daftar penugasan yang sesuai untuk kelas tertentu via AJAX
 * @param {number} idKelas 
 * @param {number} selectedId - ID penugasan yang sedang dipilih
 */
function loadPenugasanDropdown(idKelas, selectedId) {
    $('#edit-id-penugasan').html('<option value="">Loading...</option>');
    
    $.ajax({
        url: site_url + 'waka/jadwal/get_penugasan_by_kelas',
        type: 'POST',
        dataType: 'json',
        data: {
            id_kelas: idKelas
        },
        success: function(response) {
            let options = '<option value="">-- Pilih Penugasan --</option>';
            
            if (response.status === 'ok' && response.data.length > 0) {
                response.data.forEach(function(penugasan) {
                    const selected = (penugasan.id == selectedId) ? 'selected' : '';
                    options += `<option value="${penugasan.id}" ${selected}>
                        ${penugasan.nama_mapel} - ${penugasan.nama_guru} (${penugasan.sifat})
                    </option>`;
                });
            } else {
                options = '<option value="">Tidak ada penugasan untuk kelas ini</option>';
            }
            
            $('#edit-id-penugasan').html(options);
            
            // Trigger check conflict jika ada nilai terpilih
            if (selectedId) {
                checkConflictOnEdit();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading penugasan:', error);
            $('#edit-id-penugasan').html('<option value="">Gagal memuat data</option>');
            showToast('Gagal memuat daftar penugasan', 'danger');
        }
    });
}

/**
 * Fungsi: checkConflictOnEdit()
 * Cek konflik saat user mengubah pilihan penugasan atau ruangan
 */
function checkConflictOnEdit() {
    const idJadwalDetail = $('#edit-id-jadwal-detail').val();
    const idPenugasanBaru = $('#edit-id-penugasan').val();
    const idRuanganBaru = $('#edit-id-ruangan').val();
    
    // Validasi: kedua field harus terisi
    if (!idJadwalDetail || !idPenugasanBaru || !idRuanganBaru) {
        $('#btn-simpan-edit').prop('disabled', true);
        return;
    }
    
    // Disable tombol sementara
    $('#btn-simpan-edit').prop('disabled', true);
    
    // AJAX request untuk cek konflik
    $.ajax({
        url: site_url + 'jadwal/check_conflict',
        type: 'POST',
        dataType: 'json',
        data: {
            id_jadwal_detail: idJadwalDetail,
            id_penugasan_baru: idPenugasanBaru,
            id_ruangan_baru: idRuanganBaru
        },
        success: function(response) {
            if (response.status === 'ok') {
                hideConflictAlert();
                $('#btn-simpan-edit').prop('disabled', false);
            } else if (response.status === 'conflict') {
                showConflictAlert(response.konflik);
                $('#btn-simpan-edit').prop('disabled', true);
            } else {
                showToast('Gagal cek konflik', 'warning');
                $('#btn-simpan-edit').prop('disabled', true);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error checking conflict:', error);
            showToast('Gagal cek konflik', 'danger');
            $('#btn-simpan-edit').prop('disabled', true);
        }
    });
}

/**
 * Fungsi: showConflictAlert(konflikList)
 * Tampilkan alert konflik di modal
 * @param {Array} konflikList - Array objek {constraint, pesan}
 */
function showConflictAlert(konflikList) {
    let html = '<h6><i class="fas fa-exclamation-triangle"></i> Konflik Terdeteksi:</h6><ul class="mb-0">';
    
    konflikList.forEach(function(k) {
        html += `<li><strong>${k.constraint}:</strong> ${k.pesan}</li>`;
    });
    
    html += '</ul>';
    
    $('#conflict-list').html(html);
    $('#conflict-alert').slideDown();
}

/**
 * Fungsi: hideConflictAlert()
 * Sembunyikan alert konflik
 */
function hideConflictAlert() {
    $('#conflict-alert').slideUp();
    $('#conflict-list').html('');
}

/**
 * Fungsi: saveSlotEdit()
 * Simpan perubahan slot via AJAX
 */
function saveSlotEdit() {
    const idJadwalDetail = $('#edit-id-jadwal-detail').val();
    const idPenugasanBaru = $('#edit-id-penugasan').val();
    const idRuanganBaru = $('#edit-id-ruangan').val();
    
    if (!idJadwalDetail || !idPenugasanBaru || !idRuanganBaru) {
        showToast('Data tidak lengkap', 'warning');
        return;
    }
    
    // Show loading spinner
    const btnSimpan = $('#btn-simpan-edit');
    const spinner = btnSimpan.find('.loading-spinner');
    const icon = btnSimpan.find('i');
    
    spinner.show();
    icon.hide();
    btnSimpan.prop('disabled', true);
    
    // AJAX request untuk simpan
    $.ajax({
        url: site_url + 'waka/jadwal/edit_slot',
        type: 'POST',
        dataType: 'json',
        data: {
            id_jadwal_detail: idJadwalDetail,
            id_penugasan_baru: idPenugasanBaru,
            id_ruangan_baru: idRuanganBaru
        },
        success: function(response) {
            if (response.status === 'ok') {
                // Update sel di DOM tanpa reload
                updateSlotInDOM(idJadwalDetail, idPenugasanBaru, idRuanganBaru);
                
                // Tutup modal
                editModal.hide();
                
                // Tampilkan toast sukses
                showToast(response.message || 'Slot berhasil diperbarui', 'success');
            } else if (response.status === 'conflict') {
                showConflictAlert(response.konflik);
                showToast('Perubahan menyebabkan konflik', 'warning');
            } else {
                showToast(response.message || 'Gagal menyimpan perubahan', 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error saving edit:', error);
            showToast('Gagal menyimpan perubahan', 'danger');
        },
        complete: function() {
            // Hide loading spinner
            spinner.hide();
            icon.show();
            btnSimpan.prop('disabled', false);
        }
    });
}

/**
 * Fungsi: updateSlotInDOM(idJadwalDetail, idPenugasan, idRuangan)
 * Update tampilan sel jadwal di grid setelah edit berhasil
 * @param {number} idJadwalDetail 
 * @param {number} idPenugasan 
 * @param {number} idRuangan 
 */
function updateSlotInDOM(idJadwalDetail, idPenugasan, idRuangan) {
    // Cari data penugasan dan ruangan dari opsi yang dipilih
    const penugasanOption = $(`#edit-id-penugasan option[value="${idPenugasan}"]`);
    const ruanganOption = $(`#edit-id-ruangan option[value="${idRuangan}"]`);
    
    const mapelGuruText = penugasanOption.text().split(' - ');
    const namaMapel = mapelGuruText[0] || '';
    const guruInfo = mapelGuruText[1] || '';
    const sifatMatch = guruInfo.match(/\((teori|praktikum)\)/i);
    const sifat = sifatMatch ? sifatMatch[1].toLowerCase() : 'teori';
    
    const namaRuangan = ruanganOption.text().split(' (')[0] || '';
    
    // Cari sel berdasarkan data-id
    const sel = $(`td[data-id="${idJadwalDetail}"]`);
    
    if (sel.length > 0) {
        // Update classes
        sel.removeClass('jadwal-teori jadwal-praktikum');
        sel.addClass(sifat === 'praktikum' ? 'jadwal-praktikum' : 'jadwal-teori');
        
        // Update data attributes
        sel.attr('data-id-penugasan', idPenugasan);
        sel.attr('data-id-ruangan', idRuangan);
        sel.attr('data-mapel', namaMapel);
        sel.attr('data-guru', guruInfo.replace(/[()]/g, '').trim());
        sel.attr('data-nama-ruangan', namaRuangan);
        sel.attr('data-sifat', sifat);
        
        // Update content HTML
        sel.html(`
            <span class="slot-content">
                <strong>${namaMapel}</strong><br>
                <small class="slot-content-small">
                    <i class="fas fa-chalkboard-teacher"></i> ${guruInfo.replace(/[()]/g, '').trim()}<br>
                    <i class="fas fa-door-open"></i> ${namaRuangan}
                </small>
            </span>
        `);
    }
}

/**
 * Fungsi: approveJadwal()
 * Approve jadwal (ubah status draft → approved)
 */
function approveJadwal() {
    if (!confirm('Apakah Anda yakin ingin menyetujui jadwal ini? Pastikan tidak ada konflik.')) {
        return;
    }
    
    const btnApprove = $('#btn-approve');
    btnApprove.prop('disabled', true);
    btnApprove.html('<span class="spinner-border spinner-border-sm"></span> Processing...');
    
    $.ajax({
        url: site_url + 'waka/jadwal/approve',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'ok') {
                showToast(response.message, 'success');
                
                // Redirect setelah 2 detik atau update UI
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else {
                if (response.konflik && response.konflik.length > 0) {
                    let conflictMsg = 'Masih ada konflik:\n';
                    response.konflik.forEach(function(k) {
                        conflictMsg += `- ${k.constraint}: ${k.pesan}\n`;
                    });
                    alert(conflictMsg);
                }
                showToast(response.message || 'Gagal approve jadwal', 'danger');
                btnApprove.prop('disabled', false);
                btnApprove.html('<i class="fas fa-check-circle"></i> Approve Jadwal');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error approving jadwal:', error);
            showToast('Gagal approve jadwal', 'danger');
            btnApprove.prop('disabled', false);
            btnApprove.html('<i class="fas fa-check-circle"></i> Approve Jadwal');
        }
    });
}

/**
 * Fungsi: showToast(message, type)
 * Tampilkan notifikasi toast
 * @param {string} message 
 * @param {string} type - success, danger, warning, info
 */
function showToast(message, type) {
    const toastEl = document.getElementById('liveToast');
    const toastBody = toastEl.querySelector('.toast-body');
    const toastHeader = toastEl.querySelector('.toast-header i');
    
    toastBody.textContent = message;
    
    // Set icon berdasarkan type
    toastHeader.className = '';
    switch(type) {
        case 'success':
            toastHeader.classList.add('fas', 'fa-check-circle', 'text-success', 'me-2');
            break;
        case 'danger':
            toastHeader.classList.add('fas', 'fa-exclamation-circle', 'text-danger', 'me-2');
            break;
        case 'warning':
            toastHeader.classList.add('fas', 'fa-exclamation-triangle', 'text-warning', 'me-2');
            break;
        default:
            toastHeader.classList.add('fas', 'fa-info-circle', 'text-info', 'me-2');
    }
    
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

// Helper: site_url (jika belum didefinisikan di view)
const site_url = typeof base_url !== 'undefined' ? base_url : '/';
