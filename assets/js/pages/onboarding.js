/**
 * Onboarding Wizard JavaScript
 * 
 * Sesuai SRS Bab 11.9 - Navigasi dan validasi per step wizard
 */

(function($) {
    'use strict';

    // Config dari PHP view
    const config = window.onboardingConfig || {};
    
    // State management
    let currentStep = config.currentStep || 1;
    let stepStatus = config.stepStatus || {};

    /**
     * Initialize onboarding wizard
     */
    function init() {
        console.log('Onboarding wizard initialized', config);
        
        // Bind event handlers
        bindEvents();
        
        // Load initial status
        refreshStepStatus();
        
        // Update UI berdasarkan status awal
        updateUI();
    }

    /**
     * Bind semua event handlers
     */
    function bindEvents() {
        // Skip wizard button
        $('#btnSkipWizard').on('click', handleSkipWizard);
        
        // Confirm skip wizard in modal
        $('#confirmSkipWizard').on('click', confirmSkipWizard);
        
        // Auto-refresh status ketika kembali dari halaman master data
        $(window).on('focus', function() {
            setTimeout(refreshStepStatus, 500);
        });
        
        // Prevent back navigation jika step belum lengkap (optional)
        // history.pushState(null, null, location.href);
        // window.onpopstate = function() {
        //     history.pushState(null, null, location.href);
        //     alert('Silakan lengkapi step ini terlebih dahulu!');
        // };
    }

    /**
     * Refresh status dari server
     */
    function refreshStepStatus() {
        $.ajax({
            url: config.apiUrls.checkStatus,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    stepStatus = response.data;
                    updateUI();
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to refresh status:', error);
            }
        });
    }

    /**
     * Update UI berdasarkan status
     */
    function updateUI() {
        // Update setiap step indicator
        for (let step = 1; step <= config.totalSteps; step++) {
            const status = stepStatus[step] || {};
            const isCompleted = status.completed === true;
            const $stepWrapper = $(`.step-wrapper[data-step="${step}"]`).closest('.nav-item').find('.step-wrapper');
            
            if (isCompleted) {
                $stepWrapper.addClass('completed');
                
                // Update icon ke checkmark
                $stepWrapper.find('.step-indicator').html('<i class="fas fa-check"></i>');
                
                // Show badge "Selesai"
                if ($stepWrapper.find('.step-badge').length === 0) {
                    $stepWrapper.append('<span class="step-badge badge badge-success">Selesai</span>');
                }
            } else {
                $stepWrapper.removeClass('completed');
                
                // Keep number icon
                if (!$stepWrapper.find('.step-indicator i').length) {
                    $stepWrapper.find('.step-indicator').html(`<span>${step}</span>`);
                }
                
                // Remove badge jika ada
                $stepWrapper.find('.step-badge').remove();
            }
            
            // Update count display di setiap step panel
            updateCountDisplay(step, status);
        }
        
        // Update tombol skip
        updateSkipButton();
        
        // Simpan progress jika ada perubahan
        saveProgress();
    }

    /**
     * Update tampilan jumlah data di setiap step
     */
    function updateCountDisplay(step, status) {
        const $panel = $(`.step-panel[data-step="${step}"]`);
        
        // Update badge status
        const $badge = $panel.find(`#statusBadge${step}`);
        if (status.completed) {
            $badge.removeClass('badge-warning').addClass('badge-success');
            $badge.html('<i class="fas fa-check mr-1"></i>Siap');
        } else {
            $badge.removeClass('badge-success').addClass('badge-warning');
            $badge.html('<i class="fas fa-exclamation-triangle mr-1"></i>Belum Lengkap');
        }
        
        // Update count numbers
        if (status.count !== undefined) {
            $panel.find(`#count${step}`).text(status.count);
        }
        
        if (status.kelas_count !== undefined) {
            $panel.find('#countKelas').text(status.kelas_count);
        }
        
        if (status.ruangan_count !== undefined) {
            $panel.find('#countRuangan').text(status.ruangan_count);
        }
        
        // Show/hide sample data section jika ada data
        const hasData = status.count > 0 || status.kelas_count > 0 || status.ruangan_count > 0;
        const $sampleData = $panel.find('.sample-data');
        
        if (hasData && $sampleData.length) {
            $sampleData.show();
            loadSampleData(step);
        } else {
            $sampleData.hide();
        }
    }

    /**
     * Load sample data untuk ditampilkan di step
     */
    function loadSampleData(step) {
        // Ini bisa di-enhance dengan AJAX call untuk mengambil sample data
        // Untuk sekarang hanya placeholder
        const $list = $(`#sampleList${step}`);
        
        if ($list.length && step <= 3) {
            // Contoh: Tampilkan loading atau pesan
            if ($list.children().length === 0) {
                $list.html('<li class="list-group-item text-muted">Data tersedia, silakan kunjungi halaman pengelolaan untuk melihat detail.</li>');
            }
        }
    }

    /**
     * Update visibilitas tombol skip wizard
     */
    function updateSkipButton() {
        const $btn = $('#btnSkipWizard');
        
        // Tombol skip hanya muncul jika step 1-3 sudah selesai
        const canSkip = stepStatus[1]?.completed && 
                       stepStatus[2]?.completed && 
                       stepStatus[3]?.completed;
        
        if (canSkip) {
            $btn.show();
        } else {
            $btn.hide();
        }
    }

    /**
     * Handle klik tombol skip wizard
     */
    function handleSkipWizard() {
        // Show confirmation modal
        $('#skipWizardModal').modal('show');
    }

    /**
     * Confirm skip wizard dan redirect
     */
    function confirmSkipWizard() {
        // Disable button untuk prevent double-click
        const $btn = $('#confirmSkipWizard');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...');
        
        $.ajax({
            url: config.apiUrls.skipWizard,
            method: 'POST',
            data: {
                [csrfTokenName]: csrfTokenHash
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Redirect ke dashboard
                    window.location.href = response.redirect_url || '/admin/dashboard';
                } else {
                    // Show error
                    alert('Gagal melewati wizard. Silakan coba lagi.');
                    $btn.prop('disabled', false).html('<i class="fas fa-forward mr-2"></i>Ya, Lewati Wizard');
                    $('#skipWizardModal').modal('hide');
                }
            },
            error: function(xhr, status, error) {
                console.error('Skip wizard failed:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
                $btn.prop('disabled', false).html('<i class="fas fa-forward mr-2"></i>Ya, Lewati Wizard');
            }
        });
    }

    /**
     * Save progress wizard ke server
     */
    function saveProgress() {
        // Cari step yang completed
        const completedSteps = [];
        
        for (let step = 1; step <= config.totalSteps; step++) {
            if (stepStatus[step]?.completed) {
                completedSteps.push(step);
            }
        }
        
        // Kirim ke server (throttled - hanya jika ada perubahan)
        if (completedSteps.length > 0) {
            // Debounce agar tidak terlalu sering request
            clearTimeout(window.saveProgressTimeout);
            window.saveProgressTimeout = setTimeout(() => {
                const lastCompletedStep = Math.max(...completedSteps);
                
                $.ajax({
                    url: config.apiUrls.saveProgress,
                    method: 'POST',
                    data: {
                        step: lastCompletedStep,
                        completed: true,
                        [csrfTokenName]: csrfTokenHash
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Progress saved:', response);
                        
                        // Jika semua step selesai, tampilkan notifikasi
                        if (response.all_completed) {
                            showCompletionNotification();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Save progress failed:', error);
                    }
                });
            }, 1000); // Delay 1 detik
        }
    }

    /**
     * Show notification ketika semua step selesai
     */
    function showCompletionNotification() {
        // Cek apakah sudah pernah ditampilkan
        if (sessionStorage.getItem('onboardingCompleted')) {
            return;
        }
        
        sessionStorage.setItem('onboardingCompleted', 'true');
        
        // Show toast atau alert
        const notification = `
            <div class="alert alert-success alert-dismissible fade show fixed-top m-3" style="max-width: 400px; z-index: 9999;">
                <i class="fas fa-check-circle mr-2"></i>
                <strong>Selamat!</strong> Setup sistem Anda sudah selesai.
                <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $('body').append(notification);
        
        // Auto-dismiss setelah 5 detik
        setTimeout(() => {
            $('.alert.fixed-top').alert('close');
        }, 5000);
    }

    /**
     * Navigate to specific step
     */
    function navigateToStep(step) {
        if (step < 1 || step > config.totalSteps) {
            return;
        }
        
        // Validate current step before navigating forward
        if (step > currentStep) {
            const currentStatus = stepStatus[currentStep];
            if (currentStatus && !currentStatus.completed && currentStatus.required) {
                // Optional: Show warning jika ingin lanjut tapi belum lengkap
                // Untuk sekarang biarkan user lanjut saja
            }
        }
        
        currentStep = step;
        window.location.href = `/admin/onboarding/${step}`;
    }

    /**
     * Helper: Get CSRF token dari meta tag
     */
    function getCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content') || '';
    }

    // Set global CSRF tokens untuk AJAX (diambil dari meta tag)
    const csrfTokenName = '<?= $this->security->get_csrf_token_name() ?>';
    const csrfTokenHash = getCsrfToken();
    
    // Setup AJAX defaults
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfTokenHash
        }
    });

    // Initialize when DOM is ready
    $(document).ready(init);

})(jQuery);
