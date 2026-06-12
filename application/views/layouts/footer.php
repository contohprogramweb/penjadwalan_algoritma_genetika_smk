      </main>
    </div>
  </div>
  
  <!-- Loading Overlay -->
  <div id="loadingOverlay" class="loading-overlay">
    <div class="spinner-border text-primary" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
  
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Bootstrap 4.6 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- DataTables 1.10 -->
  <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Custom JS -->
  <script src="<?= base_url('assets/js/app.js') ?>"></script>
  
  <script>
    $(document).ready(function() {
      // Sidebar toggle
      $('#sidebarToggle, #topbarToggle, #sidebarToggleMobile').on('click', function() {
        $('body').toggleClass('sidebar-toggled');
        $('.sidebar').toggleClass('toggled');
      });
      
      // Fullscreen toggle
      $('#fullscreenBtn').on('click', function() {
        if (!document.fullscreenElement) {
          document.documentElement.requestFullscreen();
        } else {
          if (document.exitFullscreen) {
            document.exitFullscreen();
          }
        }
      });
      
      // Loading overlay helper functions
      window.showLoading = function() {
        $('#loadingOverlay').fadeIn();
      };
      
      window.hideLoading = function() {
        $('#loadingOverlay').fadeOut();
      };
      
      // Initialize DataTables with default config
      if ($.fn.DataTable.isDataTable('.datatable-default')) {
        $('.datatable-default').DataTable({
          responsive: true,
          language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json'
          },
          pageLength: 10,
          lengthMenu: [10, 25, 50, 100]
        });
      }
    });
    
    // Print function
    function printSection(elementId) {
      const printContent = document.getElementById(elementId).innerHTML;
      const originalContent = document.body.innerHTML;
      document.body.innerHTML = printContent;
      window.print();
      document.body.innerHTML = originalContent;
      location.reload();
    }
  </script>
  
  <!-- Page Specific JS -->
  <?php if (isset($js_files)): ?>
    <?php foreach ($js_files as $js): ?>
      <script src="<?= base_url($js) ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
  
  <!-- Inline Scripts -->
  <?php if (isset($inline_scripts)): ?>
    <script>
      <?= $inline_scripts ?>
    </script>
  <?php endif; ?>
</body>
</html>
