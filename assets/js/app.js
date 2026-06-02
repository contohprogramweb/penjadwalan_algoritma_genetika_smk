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
 * Toast Notification System
 * Referensi: SRS Bab 16.3
 * Posisi: top-right, auto-dismiss 3 detik, max 3 toast
 */
let toastQueue = [];
const MAX_TOASTS = 3;
const TOAST_DURATION = 3000;

function showToast(type = 'info', message = '') {
    const toastTypes = {
        'success': { icon: 'fa-check-circle', class: 'toast-success', bg: '#28a745' },
        'error': { icon: 'fa-exclamation-circle', class: 'toast-error', bg: '#dc3545' },
        'warning': { icon: 'fa-exclamation-triangle', class: 'toast-warning', bg: '#ffc107' },
        'info': { icon: 'fa-info-circle', class: 'toast-info', bg: '#17a2b8' }
    };

    const config = toastTypes[type] || toastTypes['info'];
    
    // Hapus toast lama jika sudah melebihi max
    const $toastContainer = $('#toast-container');
    if ($toastContainer.find('.toast-notification').length >= MAX_TOASTS) {
        $toastContainer.find('.toast-notification').first().remove();
    }

    const toastHtml = `
        <div class="toast-notification ${config.class}" role="alert" aria-live="assertive" aria-atomic="true" 
             style="min-width: 300px; margin-bottom: 10px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
            <div class="toast-body d-flex align-items-center" style="padding: 12px 16px; background: white; border-left: 4px solid ${config.bg};">
                <i class="fas ${config.icon} mr-3" style="color: ${config.bg}; font-size: 20px;" aria-hidden="true"></i>
                <span class="flex-grow-1" style="color: #333; font-weight: 500;">${escapeHtml(message)}</span>
                <button type="button" class="close ml-3" data-dismiss="toast" aria-label="Tutup" style="padding: 0; font-size: 18px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    `;

    if ($toastContainer.length === 0) {
        $('body').append('<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>');
    }

    $('#toast-container').append(toastHtml);
    
    const $newToast = $('#toast-container .toast-notification').last();
    
    // Auto dismiss setelah 3 detik
    setTimeout(() => {
        $newToast.fadeOut(300, function() {
            $(this).remove();
        });
    }, TOAST_DURATION);

    // Handler untuk tombol close manual
    $newToast.find('[data-dismiss="toast"]').on('click', function() {
        $newToast.fadeOut(300, function() {
            $(this).remove();
        });
    });
}

/**
 * Escape HTML untuk mencegah XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Global AJAX Error Handler
 * Handler untuk status: 0 (timeout/disconnect), 403 (forbidden), 500 (server error)
 */
$(document).ajaxError(function(event, xhr, settings, thrownError) {
    const status = xhr.status;
    
    // Refresh CSRF token jika ada di response header
    const newTokenName = xhr.getResponseHeader('X-CSRF-TOKEN-NAME');
    const newTokenHash = xhr.getResponseHeader('X-CSRF-TOKEN-HASH');
    if (newTokenName && newTokenHash) {
        $('meta[name="csrf_token_name"]').attr('content', newTokenName);
        $('meta[name="csrf_token_hash"]').attr('content', newTokenHash);
    }

    switch(status) {
        case 0:
            // Network error, timeout, atau disconnect
            showToast('error', 'Koneksi terputus. Periksa koneksi internet Anda.');
            console.error('AJAX Error: Connection lost or timeout');
            break;
        
        case 403:
            // Forbidden - akses ditolak
            showToast('error', 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.');
            console.warn('AJAX Error 403: Access forbidden');
            // Redirect ke halaman 403 jika perlu
            // window.location.href = base_url + 'errors/error_403';
            break;
        
        case 404:
            // Not found
            showToast('error', 'Data atau halaman tidak ditemukan.');
            console.warn('AJAX Error 404: Resource not found');
            break;
        
        case 500:
            // Internal server error
            showToast('error', 'Terjadi kesalahan pada server. Silakan coba lagi nanti.');
            console.error('AJAX Error 500: Internal server error');
            break;
        
        case 422:
            // Validation error (Laravel style)
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.errors) {
                    const messages = Object.values(response.errors).flat().join('<br>');
                    showToast('error', messages);
                } else {
                    showToast('error', response.message || 'Validasi gagal.');
                }
            } catch(e) {
                showToast('error', 'Validasi gagal.');
            }
            break;
        
        default:
            showToast('error', 'Terjadi kesalahan tak terduga (Status: ' + status + ')');
            console.error('AJAX Error ' + status + ':', thrownError || xhr.responseText);
    }
});

/**
 * Helper untuk menampilkan alert/notifikasi (legacy support)
 */
function showAlert(message, type = 'info') {
    // Gunakan showToast sebagai default
    showToast(type, message);
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

/**
 * jValidate - Validasi Real-time untuk Form
 * Referensi: SRS Bab 13
 * Fitur: validasi per-field, error message bahasa Indonesia, 
 *        class 'error' merah di bawah field, hilangkan error saat valid
 */
const jValidate = {
    /**
     * Inisialisasi validasi untuk form
     * @param {string|jQuery} formSelector - Selector form atau jQuery object
     * @param {object} rules - Aturan validasi per field
     */
    init: function(formSelector, rules = {}) {
        const $form = $(formSelector);
        if ($form.length === 0) {
            console.warn('jValidate: Form tidak ditemukan', formSelector);
            return;
        }

        // Simpan rules di form data
        $form.data('validationRules', rules);

        // Bind event handlers untuk real-time validation
        $form.on('keyup blur', 'input, select, textarea', function(e) {
            // Skip validasi jika enter pada input (untuk submit)
            if (e.type === 'keyup' && e.keyCode === 13) return;
            
            jValidate.validateField($(this));
        });

        // Validasi saat submit
        $form.on('submit', function(e) {
            const isValid = jValidate.validateForm($form);
            if (!isValid) {
                e.preventDefault();
                // Scroll ke error pertama
                const $firstError = $form.find('.error-message').first();
                if ($firstError.length > 0) {
                    $('html, body').animate({
                        scrollTop: $firstError.offset().top - 100
                    }, 300);
                }
                return false;
            }
        });

        // Hilangkan error saat user mulai mengetik lagi
        $form.on('focus', 'input, select, textarea', function() {
            const $field = $(this);
            const $errorEl = $field.siblings('.error-message');
            if ($errorEl.length > 0 && !$field.hasClass('invalid')) {
                $errorEl.remove();
            }
        });
    },

    /**
     * Validasi单个 field
     */
    validateField: function($field) {
        const $form = $field.closest('form');
        const rules = $form.data('validationRules') || {};
        const fieldName = $field.attr('name');
        
        if (!fieldName || !rules[fieldName]) {
            return true;
        }

        const value = $field.val();
        const fieldRules = rules[fieldName];
        const label = $field.data('label') || fieldName;
        
        let errors = [];

        // Cek required
        if (fieldRules.required && (!value || value.trim() === '')) {
            errors.push(fieldRules.messages?.required || `${label} wajib diisi.`);
        }

        // Jika kosong dan tidak required, skip validasi lainnya
        if (!value || value.trim() === '') {
            jValidate.showOrHideError($field, []);
            return errors.length === 0;
        }

        // Cek minLength
        if (fieldRules.minLength && value.length < fieldRules.minLength) {
            errors.push(fieldRules.messages?.minLength || 
                `${label} minimal ${fieldRules.minLength} karakter.`);
        }

        // Cek maxLength
        if (fieldRules.maxLength && value.length > fieldRules.maxLength) {
            errors.push(fieldRules.messages?.maxLength || 
                `${label} maksimal ${fieldRules.maxLength} karakter.`);
        }

        // Cek email
        if (fieldRules.email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                errors.push(fieldRules.messages?.email || 'Format email tidak valid.');
            }
        }

        // Cek numeric
        if (fieldRules.numeric && isNaN(parseFloat(value))) {
            errors.push(fieldRules.messages?.numeric || `${label} harus berupa angka.`);
        }

        // Cek exactLength
        if (fieldRules.exactLength && value.length !== fieldRules.exactLength) {
            errors.push(fieldRules.messages?.exactLength || 
                `${label} harus ${fieldRules.exactLength} karakter.`);
        }

        // Cek min
        if (fieldRules.min !== undefined && parseFloat(value) < fieldRules.min) {
            errors.push(fieldRules.messages?.min || 
                `${label} minimal ${fieldRules.min}.`);
        }

        // Cek max
        if (fieldRules.max !== undefined && parseFloat(value) > fieldRules.max) {
            errors.push(fieldRules.messages?.max || 
                `${label} maksimal ${fieldRules.max}.`);
        }

        // Cek pattern (regex)
        if (fieldRules.pattern) {
            const regex = new RegExp(fieldRules.pattern);
            if (!regex.test(value)) {
                errors.push(fieldRules.messages?.pattern || `Format ${label} tidak valid.`);
            }
        }

        // Cek custom validation function
        if (typeof fieldRules.custom === 'function') {
            const customResult = fieldRules.custom(value, $field);
            if (customResult !== true) {
                errors.push(customResult || `Validasi ${label} gagal.`);
            }
        }

        jValidate.showOrHideError($field, errors);
        return errors.length === 0;
    },

    /**
     * Tampilkan atau sembunyikan error message
     */
    showOrHideError: function($field, errors) {
        const fieldId = $field.attr('id');
        let $errorEl = $field.siblings('.error-message');

        if (errors.length > 0) {
            $field.addClass('invalid');
            $field.attr('aria-invalid', 'true');
            
            if ($errorEl.length === 0) {
                $errorEl = $('<div class="error-message" style="color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;"></div>');
                $field.after($errorEl);
                
                // Associate error dengan field untuk aksesibilitas
                if (fieldId) {
                    $field.attr('aria-describedby', fieldId + '-error');
                    $errorEl.attr('id', fieldId + '-error');
                }
            }
            
            $errorEl.text(errors[0]); // Tampilkan error pertama saja
        } else {
            $field.removeClass('invalid');
            $field.attr('aria-invalid', 'false');
            $errorEl.remove();
        }
    },

    /**
     * Validasi seluruh form
     */
    validateForm: function($form) {
        const rules = $form.data('validationRules') || {};
        let isValid = true;
        let firstInvalidField = null;

        $.each(rules, function(fieldName, fieldRules) {
            const $field = $form.find('[name="' + fieldName + '"]');
            if ($field.length > 0) {
                const fieldValid = jValidate.validateField($field);
                if (!fieldValid) {
                    isValid = false;
                    if (!firstInvalidField) {
                        firstInvalidField = $field;
                    }
                }
            }
        });

        return isValid;
    },

    /**
     * Reset validasi form
     */
    reset: function($form) {
        $form.find('.invalid').removeClass('invalid');
        $form.find('[aria-invalid="true"]').attr('aria-invalid', 'false');
        $form.find('.error-message').remove();
    }
};
