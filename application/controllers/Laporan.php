<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Laporan
 * Deskripsi: Generate laporan PDF untuk jadwal dan beban guru
 * Referensi SRS: Bab 11.10 (PDF Engine)
 */
require_once APPPATH . 'core/MY_Controller.php';

class Laporan extends MY_Controller {
    
    protected $allowed_roles = ['waka', 'admin'];

    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('Jadwal_model');
        $this->load->model('Penugasan_model');
        $this->load->model('Guru_model');
        $this->load->model('Kelas_model');
        $this->load->model('Tahun_ajaran_model');
    }

    /**
     * Method: pdf_jadwal($id_kelas)
     * Deskripsi: Generate PDF jadwal untuk kelas tertentu
     * Route: /laporan/pdf_jadwal/<id_kelas>
     */
    public function pdf_jadwal($id_kelas)
    {
        // Validasi ID kelas
        $kelas = $this->Kelas_model->get_by_id($id_kelas);
        if (!$kelas) {
            show_error('Data kelas tidak ditemukan', 404);
        }

        // Ambil tahun ajaran aktif
        $tahun_ajaran = $this->Tahun_ajaran_model->get_active();
        $semester = $tahun_ajaran ? $tahun_ajaran->semester : 'Ganjil';
        $tahun = $tahun_ajaran ? $tahun_ajaran->nama_tahun : date('Y');

        // Ambil jadwal untuk kelas ini
        $jadwal_list = $this->Jadwal_model->get_jadwal_by_kelas($id_kelas);

        // Format data untuk view
        $data = [
            'kelas' => $kelas,
            'semester' => $semester,
            'tahun_ajaran' => $tahun,
            'jadwal_list' => $jadwal_list,
            'tanggal_cetak' => date('d F Y H:i:s'),
            'page_number' => '{PAGENO}'
        ];

        // Load Dompdf
        require_once APPPATH . '../vendor/autoload.php';
        
        // Instantiate Dompdf
        $dompdf = new Dompdf\Dompdf();
        
        // Load HTML dari view
        $html = $this->load->view('laporan/jadwal_pdf', $data, TRUE);
        
        // Load HTML ke Dompdf
        $dompdf->loadHtml($html);
        
        // Set paper size A4 landscape
        $dompdf->setPaper('A4', 'landscape');
        
        // Render PDF
        $dompdf->render();
        
        // Output: Download atau stream
        $dompdf->stream('Jadwal_Kelas_' . $kelas['nama_kelas'] . '.pdf', ['Attachment' => 1]);
    }

    /**
     * Method: pdf_beban_guru($id_guru)
     * Deskripsi: Generate PDF laporan beban mengajar guru
     * Route: /laporan/pdf_beban_guru/<id_guru>
     */
    public function pdf_beban_guru($id_guru)
    {
        // Validasi ID guru
        $guru = $this->Guru_model->get_by_id($id_guru);
        if (!$guru) {
            show_error('Data guru tidak ditemukan', 404);
        }

        // Ambil tahun ajaran aktif
        $tahun_ajaran = $this->Tahun_ajaran_model->get_active();
        $semester = $tahun_ajaran ? $tahun_ajaran->semester : 'Ganjil';
        $tahun = $tahun_ajaran ? $tahun_ajaran->nama_tahun : date('Y');

        // Ambil penugasan guru
        $penugasan_list = $this->Penugasan_model->get_by_guru($id_guru);
        
        // Hitung total jam
        $total_jam = 0;
        foreach ($penugasan_list as $p) {
            $total_jam += $p->jam_mengajar ?? 0;
        }

        // Format data untuk view
        $data = [
            'guru' => $guru,
            'semester' => $semester,
            'tahun_ajaran' => $tahun,
            'penugasan_list' => $penugasan_list,
            'total_jam' => $total_jam,
            'tanggal_cetak' => date('d F Y H:i:s'),
            'page_number' => '{PAGENO}'
        ];

        // Load Dompdf
        require_once APPPATH . '../vendor/autoload.php';
        
        // Instantiate Dompdf
        $dompdf = new Dompdf\Dompdf();
        
        // Load HTML dari view
        $html = $this->load->view('laporan/beban_guru_pdf', $data, TRUE);
        
        // Load HTML ke Dompdf
        $dompdf->loadHtml($html);
        
        // Set paper size A4 portrait
        $dompdf->setPaper('A4', 'portrait');
        
        // Render PDF
        $dompdf->render();
        
        // Output: Download atau stream
        $dompdf->stream('Beban_Mengajar_' . $guru['nama_lengkap'] . '.pdf', ['Attachment' => 1]);
    }
}
