/**
 * Generate Jadwal - JavaScript Handler
 * 
 * Sesuai SRS Bab 15.3:
 * - Polling setiap 2 detik ke /jadwal/progress
 * - Update progress bar, info panel, timer
 * - Redirect otomatis ke /waka/jadwal setelah sukses (3 detik)
 * - Tombol Generate disabled saat proses untuk mencegah double-click
 * 
 * @author Waka Kurikulum System
 * @version 1.0
 */

(function($) {
    'use strict';

    // State management
    var isGenerating = false;
    var pollingInterval = null;
    var startTime = null;

    /**
     * Inisialisasi halaman generate
     */
    function init() {
        console.log('Generate page initialized');
        
        // Bind event tombol Generate
        $('#btnGenerate').on('click', handleGenerateClick);
        
        // Jika data sudah ready, tampilkan state yang sesuai
        if (typeof INITIAL_READY !== 'undefined' && INITIAL_READY) {
            console.log('Data ready for generation');
        }
    }

    /**
     * Handle click tombol Generate
     * Mencegah double-click dengan disable button
     */
    function handleGenerateClick() {
        if (isGenerating) {
            console.warn('Generate already in progress');
            return;
        }

        // Disable tombol untuk mencegah double-click
        setButtonState(false);

        // Trigger proses generate
        triggerGenerate();
    }

    /**
     * Set state tombol Generate
     * @param {boolean} enabled - true untuk enable, false untuk disable
     */
    function setButtonState(enabled) {
        var $btn = $('#btnGenerate');
        if (enabled) {
            $btn.prop('disabled', false);
            $btn.html('<i class="fas fa-cogs mr-2"></i>Generate Jadwal Pelajaran');
        } else {
            $btn.prop('disabled', true);
            $btn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');
        }
    }

    /**
     * Trigger proses generate via AJAX POST
     */
    function triggerGenerate() {
        console.log('Triggering generate process...');

        $.ajax({
            url: GENERATE_CONFIG.triggerUrl,
            type: 'POST',
            dataType: 'json',
            timeout: 30000, // 30 detik timeout
            success: function(response) {
                console.log('Trigger response:', response);
                
                if (response.status === 'started') {
                    // Mulai polling progress
                    startPolling();
                    
                    // Switch ke state berjalan
                    switchToStateBerjalan();
                } else if (response.status === 'error') {
                    // Error saat trigger
                    showError(response.message || 'Gagal memulai proses generate.');
                    setButtonState(true);
                } else {
                    showError('Response tidak dikenali.');
                    setButtonState(true);
                }
            },
            error: function(xhr, status, error) {
                console.error('Trigger error:', xhr, status, error);
                
                var message = 'Terjadi kesalahan saat memulai generate.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                showError(message);
                setButtonState(true);
            }
        });
    }

    /**
     * Mulai polling progress ke server
     */
    function startPolling() {
        console.log('Starting progress polling...');
        startTime = Date.now();
        
        // Polling pertama langsung
        pollProgress();
        
        // Set interval polling setiap 2 detik
        pollingInterval = setInterval(pollProgress, GENERATE_CONFIG.pollingInterval);
    }

    /**
     * Stop polling progress
     */
    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    }

    /**
     * Poll progress dari server
     */
    function pollProgress() {
        $.ajax({
            url: GENERATE_CONFIG.progressUrl,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(progress) {
                console.log('Progress update:', progress);
                
                updateProgressUI(progress);
                
                // Cek apakah proses sudah selesai
                if (!progress.is_generating) {
                    handleProcessComplete(progress);
                }
            },
            error: function(xhr, status, error) {
                console.error('Polling error:', xhr, status, error);
                // Jangan stop polling pada error transient
            }
        });
    }

    /**
     * Update UI progress berdasarkan data dari server
     * @param {Object} progress - Data progress dari server
     */
    function updateProgressUI(progress) {
        // Update progress bar
        var persen = progress.persen || 0;
        $('#progressBar')
            .css('width', persen + '%')
            .attr('aria-valuenow', persen)
            .find('#progressText').text(persen + '%');
        
        // Update info panel
        $('#infoGenerasi').text((progress.generasi || 0) + ' / ' + (progress.generasi_maks || 500));
        $('#infoFitness').text(progress.fitness_terbaik || 0);
        $('#infoStatus').text(capitalizeFirst(progress.status || 'unknown'));
        $('#pesanProgress').text(progress.pesan || 'Processing...');
        
        // Update waktu berjalan
        if (progress.waktu_berjalan) {
            $('#infoWaktu').text(formatTime(progress.waktu_berjalan));
        } else if (startTime) {
            var elapsed = (Date.now() - startTime) / 1000;
            $('#infoWaktu').text(formatTime(elapsed));
        }
        
        // Update status color
        var $statusEl = $('#infoStatus');
        $statusEl.removeClass('text-warning text-success text-danger text-info');
        
        switch (progress.status) {
            case 'initializing':
            case 'running':
                $statusEl.addClass('text-warning');
                break;
            case 'completed':
                $statusEl.addClass('text-success');
                break;
            case 'failed':
            case 'error':
                $statusEl.addClass('text-danger');
                break;
            default:
                $statusEl.addClass('text-info');
        }
    }

    /**
     * Handle ketika proses generate selesai
     * @param {Object} progress - Data progress final
     */
    function handleProcessComplete(progress) {
        console.log('Process complete:', progress);
        
        stopPolling();
        
        if (progress.status === 'completed') {
            handleSuccess(progress);
        } else {
            handleFailure(progress);
        }
    }

    /**
     * Handle sukses generate
     * @param {Object} progress - Data progress
     */
    function handleSuccess(progress) {
        console.log('Generate successful!');
        
        // Switch ke state selesai
        switchToStateSelesai();
        
        // Isi data hasil
        $('#hasilGenerasi').text(progress.generasi || '-');
        $('#hasilFitness').text(progress.fitness_terbaik || '-');
        $('#hasilWaktu').text(formatTime(progress.waktu_berjalan || 0));
        $('#hasilKonflik').text(progress.conflicts_count || 0);
        $('#pesanSukses').text(progress.pesan || 'Generate berhasil!');
        
        // Tampilkan alert sukses
        $('#alertSukses').removeClass('d-none');
        
        // Auto redirect setelah 3 detik
        setTimeout(function() {
            console.log('Redirecting to review page...');
            window.location.href = GENERATE_CONFIG.reviewUrl;
        }, GENERATE_CONFIG.redirectDelay);
    }

    /**
     * Handle gagal generate
     * @param {Object} progress - Data progress
     */
    function handleFailure(progress) {
        console.log('Generate failed:', progress);
        
        // Switch ke state selesai
        switchToStateSelesai();
        
        // Tampilkan alert gagal
        $('#pesanGagal').text(progress.pesan || 'Generate gagal. Silakan coba lagi.');
        $('#alertGagal').removeClass('d-none');
        
        // Enable kembali tombol
        setButtonState(true);
    }

    /**
     * Tampilkan error message
     * @param {string} message - Pesan error
     */
    function showError(message) {
        console.error('Error:', message);
        
        // Bisa tambahkan toast/notification di sini
        alert('Error: ' + message);
    }

    /**
     * Switch ke state berjalan
     */
    function switchToStateBerjalan() {
        $('#state-sebelum').addClass('d-none');
        $('#state-selesai').addClass('d-none');
        $('#state-berjalan').removeClass('d-none');
        
        isGenerating = true;
    }

    /**
     * Switch ke state selesai
     */
    function switchToStateSelesai() {
        $('#state-berjalan').addClass('d-none');
        $('#state-sebelum').addClass('d-none');
        $('#state-selesai').removeClass('d-none');
        
        isGenerating = false;
    }

    /**
     * Format waktu dalam format MM:SS
     * @param {number} seconds - Waktu dalam detik
     * @returns {string} Formatted time
     */
    function formatTime(seconds) {
        seconds = Math.floor(seconds || 0);
        var mins = Math.floor(seconds / 60);
        var secs = seconds % 60;
        
        return (mins < 10 ? '0' : '') + mins + ':' + 
               (secs < 10 ? '0' : '') + secs;
    }

    /**
     * Capitalize first letter
     * @param {string} str - Input string
     * @returns {string} Capitalized string
     */
    function capitalizeFirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    /**
     * Reset progress di server
     */
    function resetProgress() {
        $.ajax({
            url: GENERATE_CONFIG.resetUrl,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log('Progress reset:', response);
            },
            error: function(xhr, status, error) {
                console.error('Reset error:', xhr, status, error);
            }
        });
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        init();
    });

    // Cleanup on page unload
    $(window).on('beforeunload', function() {
        if (isGenerating) {
            return 'Proses generate sedang berjalan. Jika Anda keluar, proses akan terhenti.';
        }
    });

})(jQuery);
