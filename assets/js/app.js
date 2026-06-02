/**
 * Global CSRF Setup untuk jQuery AJAX
 * Referensi: SRS Bab 16.3
 */

// Setup CSRF token untuk semua AJAX requests
$(document).ajaxComplete(function() {
    // Token akan di-refresh setiap request selesai
});

// Helper function untuk mendapatkan CSRF token
function getCSRFToken() {
    return $('meta[name="csrf_token_name"]').attr('content');
}

function getCSRFHash() {
    return $('meta[name="csrf_token_hash"]').attr('content');
}

// Setup default AJAX settings untuk menyertakan CSRF token
$.ajaxSetup({
    beforeSend: function(xhr, settings) {
        if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
            const csrfName = getCSRFToken();
            const csrfHash = getCSRFHash();
            
            if (csrfName && csrfHash) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfHash);
                
                // Untuk form data
                if (settings.data instanceof FormData) {
                    // FormData sudah handle sendiri
                } else if (typeof settings.data === 'string') {
                    // Append ke query string atau body
                    settings.data += '&' + csrfName + '=' + csrfHash;
                } else if (typeof settings.data === 'object') {
                    settings.data[csrfName] = csrfHash;
                }
            }
        }
    }
});

/**
 * Helper untuk menampilkan alert/notifikasi
 */
function showAlert(message, type = 'info') {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };

    const alertHtml = `
        <div class="alert ${alertClass[type] || 'alert-info'} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

    // Cari container alert atau buat baru
    let $alertContainer = $('#alert-container');
    if ($alertContainer.length === 0) {
        $alertContainer = $('<div id="alert-container" class="mb-3"></div>');
        $('.content-wrapper').prepend($alertContainer);
    }

    $alertContainer.html(alertHtml);

    // Auto dismiss setelah 5 detik untuk success
    if (type === 'success') {
        setTimeout(() => {
            $alertContainer.find('.alert').alert('close');
        }, 5000);
    }
}

/**
 * Helper untuk konfirmasi hapus
 */
function confirmDelete(callback) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
}

/**
 * Helper untuk reset form dan hide errors
 */
function resetForm($form) {
    $form[0].reset();
    $form.find('.is-invalid').removeClass('is-invalid');
    $form.find('.invalid-feedback').remove();
    $form.find('.alert').remove();
}

/**
 * Helper untuk menampilkan error validasi per field
 */
function showValidationErrors($form, errors) {
    // Clear previous errors
    $form.find('.is-invalid').removeClass('is-invalid');
    $form.find('.invalid-feedback').remove();

    // Show new errors
    $.each(errors, function(field, message) {
        const $field = $form.find('[name="' + field + '"]');
        if ($field.length > 0) {
            $field.addClass('is-invalid');
            $field.after('<div class="invalid-feedback">' + message + '</div>');
        }
    });
}

/**
 * Helper untuk toggle button state (loading spinner)
 */
function setButtonLoading($button, isLoading = true, defaultText = '') {
    if (isLoading) {
        $button.prop('disabled', true);
        const originalText = $button.text().trim();
        $button.data('original-text', originalText);
        
        $button.html(`
            <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
            Memproses...
        `);
    } else {
        $button.prop('disabled', false);
        const originalText = $button.data('original-text') || defaultText;
        $button.html(originalText);
    }
}

/**
 * Format currency IDR
 */
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

/**
 * Format date to Indonesian locale
 */
function formatDateIndonesia(dateString) {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

/**
 * Initialize DataTables with common settings
 */
function initDataTable(tableSelector, ajaxUrl, columns, orderColumn = 0) {
    return $(tableSelector).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: ajaxUrl,
            type: 'POST',
            data: function(d) {
                // Add CSRF token
                d[$('meta[name="csrf_token_name"]').attr('content')] = 
                    $('meta[name="csrf_token_hash"]').attr('content');
            }
        },
        columns: columns,
        order: [[orderColumn, 'asc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
        },
        responsive: true,
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    });
}
