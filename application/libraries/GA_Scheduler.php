<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * GA_Scheduler - Algoritma Genetika untuk Penjadwalan
 * 
 * Library ini mengimplementasikan Algoritma Genetika (GA) untuk menghasilkan
 * jadwal pelajaran otomatis yang memenuhi hard constraints dan soft constraints.
 * 
 * Referensi: SRS Bab 16 - Pseudocode Fungsi Kritis
 * 
 * Representasi Kromosom: Array 2D [hari][slot] = id_penugasan
 * Hard Constraints (HC):
 *   HC-01: Guru tidak double-booked di slot sama
 *   HC-02: Kelas tidak double-booked di slot sama
 *   HC-03: Ruangan tidak double-booked di slot sama
 *   HC-04: Jam mengajar guru tidak melebihi jam maks/minggu
 *   HC-05: Kelas hanya mendapat mapel sesuai penugasan
 * Soft Constraints (SC):
 *   SC-01: Preferensi guru (hati-hati jika melanggar)
 *   SC-02: Penempatan mapel praktikum di ruangan sesuai tipe
 * 
 * Operator GA:
 *   - Selection: Tournament Selection
 *   - Crossover: Order-based Crossover
 *   - Mutation: Swap Slot Mutation
 * 
 * @author Waka Kurikulum System
 * @version 1.0
 */
class GA_Scheduler {

    // Konfigurasi GA
    private $populasi_size = 50;        // Jumlah individu dalam populasi
    private $generasi_maks = 500;       // Maksimum generasi
    private $tournament_size = 5;       // Ukuran turnamen untuk seleksi
    private $crossover_rate = 0.8;      // Probabilitas crossover
    private $mutation_rate = 0.1;       // Probabilitas mutasi
    private $elitism_count = 2;         // Jumlah elit yang dipertahankan

    // Parameter jadwal
    private $jumlah_hari = 6;           // Senin-Sabtu
    private $jumlah_slot_per_hari = 8;  // 8 jam pelajaran per hari
    
    // Data untuk penjadwalan
    private $penugasan_list = [];       // List penugasan guru
    private $jam_pelajaran = [];        // Data jam pelajaran
    private $kelas_list = [];           // List kelas
    private $ruangan_list = [];         // List ruangan
    private $guru_list = [];            // List guru
    private $mapel_list = [];           // List mata pelajaran

    // Populasi dan hasil
    private $populasi = [];             // Array kromosom
    private $fitness_scores = [];       // Array fitness score
    private $best_kromosom = null;      // Kromosom terbaik
    private $best_fitness = 0;          // Fitness score terbaik
    private $conflict_list = [];        // List konflik pada solusi terbaik

    // CI Instance
    private $CI;

    /**
     * Constructor - Inisialisasi library
     */
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('Penugasan_model');
        $this->CI->load->model('Jam_model');
        $this->CI->load->model('Kelas_model');
        $this->CI->load->model('Ruangan_model');
        $this->CI->load->model('Guru_model');
        $this->CI->load->model('Mapel_model');
    }

    // =========================================================================
    // METHOD PUBLIK UTAMA
    // =========================================================================

    /**
     * Generate jadwal menggunakan Algoritma Genetika
     * 
     * @param int $id_tahun_ajaran ID tahun ajaran untuk generate jadwal
     * @param int $generasi_maks Maksimum generasi (default: 500)
     * @return array Hasil penjadwalan dengan status dan data
     */
    public function generate($id_tahun_ajaran, $generasi_maks = 500) {
        // Set maksimum generasi dari parameter
        $this->generasi_maks = $generasi_maks;

        // Load semua data yang diperlukan
        $this->_load_data($id_tahun_ajaran);

        // Validasi data yang diperlukan
        if (empty($this->penugasan_list)) {
            return [
                'status' => 'error',
                'message' => 'Tidak ada data penugasan untuk tahun ajaran ini.',
                'schedule' => null
            ];
        }

        // Inisialisasi populasi awal
        $this->_inisialisasi_populasi();

        // Evaluasi fitness awal
        $this->_evaluasi_populasi();

        // Main loop algoritma genetika
        $generasi = 0;
        $konvergen = false;
        $fitness_sebelumnya = $this->best_fitness;
        $stuck_counter = 0;
        $max_stuck = 50; // Jika stuck lebih dari 50 generasi, hentikan

        while ($generasi < $this->generasi_maks && !$konvergen) {
            $generasi++;

            // Seleksi, crossover, dan mutasi
            $populasi_baru = $this->_seleksi_dan_reproduksi();
            
            // Ganti populasi lama dengan baru
            $this->populasi = $populasi_baru;
            
            // Evaluasi fitness populasi baru
            $this->_evaluasi_populasi();

            // Cek konvergensi
            if ($this->best_fitness == $fitness_sebelumnya) {
                $stuck_counter++;
                if ($stuck_counter >= $max_stuck) {
                    $konvergen = true;
                }
            } else {
                $stuck_counter = 0;
                $fitness_sebelumnya = $this->best_fitness;
            }

            // Optional: Logging progress setiap 50 generasi
            if ($generasi % 50 == 0) {
                log_message('debug', "GA Progress - Generasi: $generasi, Best Fitness: {$this->best_fitness}");
            }
        }

        // Ekstrak hasil terbaik dan daftar konflik
        $this->_ekstrak_hasil_terbaik();

        return [
            'status' => 'success',
            'message' => "Generate selesai setelah $generasi generasi.",
            'generasi' => $generasi,
            'best_fitness' => $this->best_fitness,
            'schedule' => $this->get_best_schedule(),
            'conflicts' => $this->get_conflict_list()
        ];
    }

    /**
     * Dapatkan fitness score terbaik
     * 
     * @return float Fitness score terbaik (0-100, semakin tinggi semakin baik)
     */
    public function get_best_fitness() {
        return $this->best_fitness;
    }

    /**
     * Dapatkan jadwal terbaik dalam format terstruktur
     * 
     * @return array Jadwal terbaik dengan struktur [hari][slot][detail]
     */
    public function get_best_schedule() {
        if ($this->best_kromosom === null) {
            return [];
        }

        $schedule = [];
        $nama_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        for ($h = 0; $h < $this->jumlah_hari; $h++) {
            $hari_name = $nama_hari[$h];
            $schedule[$hari_name] = [];

            for ($s = 0; $s < $this->jumlah_slot_per_hari; $s++) {
                $slot_key = "Slot_" . ($s + 1);
                $id_penugasan = $this->best_kromosom[$h][$s];

                if ($id_penugasan !== null && isset($this->penugasan_list[$id_penugasan])) {
                    $penugasan = $this->penugasan_list[$id_penugasan];
                    $schedule[$hari_name][$slot_key] = [
                        'id_penugasan' => $id_penugasan,
                        'guru' => $penugasan['nama_guru'] ?? 'N/A',
                        'mapel' => $penugasan['nama_mapel'] ?? 'N/A',
                        'kelas' => $penugasan['nama_kelas'] ?? 'N/A',
                        'ruangan' => $penugasan['nama_ruangan'] ?? 'N/A',
                        'jam_ke' => $s + 1
                    ];
                } else {
                    $schedule[$hari_name][$slot_key] = null;
                }
            }
        }

        return $schedule;
    }

    /**
     * Dapatkan daftar konflik pada jadwal terbaik
     * 
     * @return array List konflik dengan detail jenis dan entitas yang berkonflik
     */
    public function get_conflict_list() {
        return $this->conflict_list;
    }

    // =========================================================================
    // METHOD PRIVATE - LOADING DATA
    // =========================================================================

    /**
     * Load semua data yang diperlukan dari database
     * 
     * @param int $id_tahun_ajaran ID tahun ajaran
     */
    private function _load_data($id_tahun_ajaran) {
        // Load penugasan guru
        $this->CI->db->select('pg.*, g.nama as nama_guru, mp.nama_mapel, k.nama_kelas, r.nama_ruangan, r.jenis as jenis_ruangan, mp.jenis as jenis_mapel');
        $this->CI->db->from('penugasan_guru pg');
        $this->CI->db->join('guru g', 'pg.id_guru = g.id_guru', 'left');
        $this->CI->db->join('mata_pelajaran mp', 'pg.id_mapel = mp.id_mapel', 'left');
        $this->CI->db->join('kelas k', 'pg.id_kelas = k.id_kelas', 'left');
        $this->CI->db->join('ruangan r', 'pg.id_ruangan = r.id_ruangan', 'left');
        $this->CI->db->where('pg.id_tahun_ajaran', $id_tahun_ajaran);
        $query = $this->CI->db->get();
        
        foreach ($query->result_array() as $row) {
            $this->penugasan_list[$row['id_penugasan']] = $row;
        }

        // Load jam pelajaran
        $this->jam_pelajaran = $this->CI->Jam_model->get_all_ordered();

        // Load kelas
        $query = $this->CI->db->get('kelas');
        foreach ($query->result_array() as $row) {
            $this->kelas_list[$row['id_kelas']] = $row;
        }

        // Load ruangan
        $query = $this->CI->db->get('ruangan');
        foreach ($query->result_array() as $row) {
            $this->ruangan_list[$row['id_ruangan']] = $row;
        }

        // Load guru
        $query = $this->CI->db->get('guru');
        foreach ($query->result_array() as $row) {
            $this->guru_list[$row['id_guru']] = $row;
        }

        // Load mata pelajaran
        $query = $this->CI->db->get('mata_pelajaran');
        foreach ($query->result_array() as $row) {
            $this->mapel_list[$row['id_mapel']] = $row;
        }
    }

    // =========================================================================
    // METHOD PRIVATE - INISIALISASI POPULASI
    // =========================================================================

    /**
     * Inisialisasi populasi awal secara acak
     * Setiap kromosom adalah array 2D [hari][slot] = id_penugasan
     */
    private function _inisialisasi_populasi() {
        $this->populasi = [];
        $total_slot = $this->jumlah_hari * $this->jumlah_slot_per_hari;
        $id_penugasan_array = array_keys($this->penugasan_list);
        $jumlah_penugasan = count($id_penugasan_array);

        for ($i = 0; $i < $this->populasi_size; $i++) {
            $kromosom = $this->_buat_kromosom_acak($id_penugasan_array, $total_slot, $jumlah_penugasan);
            $this->populasi[] = $kromosom;
        }

        $this->best_fitness = 0;
        $this->best_kromosom = null;
    }

    /**
     * Buat kromosom acak dengan distribusi penugasan yang merata
     * 
     * @param array $id_penugasan_array Array ID penugasan
     * @param int $total_slot Total slot tersedia
     * @param int $jumlah_penugasan Jumlah penugasan
     * @return array Kromosom 2D
     */
    private function _buat_kromosom_acak($id_penugasan_array, $total_slot, $jumlah_penugasan) {
        $kromosom = [];

        // Inisialisasi kromosom dengan null
        for ($h = 0; $h < $this->jumlah_hari; $h++) {
            $kromosom[$h] = [];
            for ($s = 0; $s < $this->jumlah_slot_per_hari; $s++) {
                $kromosom[$h][$s] = null;
            }
        }

        // Hitung berapa kali setiap penugasan harus muncul (berdasarkan jam_per_minggu)
        $frekuensi = [];
        foreach ($id_penugasan_array as $id) {
            $jam = isset($this->penugasan_list[$id]['jam_per_minggu']) 
                   ? (int)$this->penugasan_list[$id]['jam_per_minggu'] 
                   : 2;
            $frekuensi[$id] = max(1, min($jam, $total_slot));
        }

        // Acak urutan penugasan
        shuffle($id_penugasan_array);

        // Tempatkan penugasan ke slot secara acak
        $slot_tersedia = [];
        for ($h = 0; $h < $this->jumlah_hari; $h++) {
            for ($s = 0; $s < $this->jumlah_slot_per_hari; $s++) {
                $slot_tersedia[] = ['hari' => $h, 'slot' => $s];
            }
        }
        shuffle($slot_tersedia);

        $slot_index = 0;
        foreach ($id_penugasan_array as $id) {
            $freq = $frekuensi[$id];
            
            for ($f = 0; $f < $freq && $slot_index < count($slot_tersedia); $f++) {
                $pos = $slot_tersedia[$slot_index];
                $kromosom[$pos['hari']][$pos['slot']] = $id;
                $slot_index++;
            }
        }

        return $kromosom;
    }

    // =========================================================================
    // METHOD PRIVATE - FITNESS FUNCTION
    // =========================================================================

    /**
     * Evaluasi fitness untuk seluruh populasi
     */
    private function _evaluasi_populasi() {
        $this->fitness_scores = [];

        foreach ($this->populasi as $index => $kromosom) {
            $fitness = $this->_hitung_fitness($kromosom);
            $this->fitness_scores[$index] = $fitness;

            // Update best solution
            if ($fitness > $this->best_fitness) {
                $this->best_fitness = $fitness;
                $this->best_kromosom = $this->_clone_kromosom($kromosom);
            }
        }
    }

    /**
     * Hitung fitness score untuk satu kromosom
     * Fitness = 100 - (total penalty)
     * Semakin kecil penalty, semakin baik fitness
     * 
     * @param array $kromosom Kromosom 2D
     * @return float Fitness score (0-100)
     */
    private function _hitung_fitness($kromosom) {
        $penalty = 0;
        $this->conflict_list = [];

        // Track penggunaan resources per slot
        // Format: [hari][slot][resource_type][resource_id] = count
        $guru_usage = [];
        $kelas_usage = [];
        $ruangan_usage = [];
        $guru_jam_mingguan = [];

        // =========================================================================
        // CEK HARD CONSTRAINTS
        // =========================================================================

        for ($h = 0; $h < $this->jumlah_hari; $h++) {
            for ($s = 0; $s < $this->jumlah_slot_per_hari; $s++) {
                $id_penugasan = $kromosom[$h][$s];

                if ($id_penugasan === null) {
                    continue;
                }

                if (!isset($this->penugasan_list[$id_penugasan])) {
                    continue;
                }

                $penugasan = $this->penugasan_list[$id_penugasan];
                $id_guru = $penugasan['id_guru'];
                $id_kelas = $penugasan['id_kelas'];
                $id_ruangan = $penugasan['id_ruangan'];

                // Initialize arrays untuk hari/slot ini
                if (!isset($guru_usage[$h][$s])) {
                    $guru_usage[$h][$s] = [];
                }
                if (!isset($kelas_usage[$h][$s])) {
                    $kelas_usage[$h][$s] = [];
                }
                if (!isset($ruangan_usage[$h][$s])) {
                    $ruangan_usage[$h][$s] = [];
                }
                if (!isset($guru_jam_mingguan[$id_guru])) {
                    $guru_jam_mingguan[$id_guru] = 0;
                }

                // HC-01: Guru tidak double-booked di slot sama
                if (isset($guru_usage[$h][$s][$id_guru])) {
                    $penalty += 50; // Penalty berat untuk HC
                    $this->conflict_list[] = [
                        'type' => 'HC-01',
                        'description' => 'Guru double-booked',
                        'hari' => $h,
                        'slot' => $s,
                        'entity' => $penugasan['nama_guru'] ?? 'Guru ID: ' . $id_guru
                    ];
                }
                $guru_usage[$h][$s][$id_guru] = true;

                // HC-02: Kelas tidak double-booked di slot sama
                if (isset($kelas_usage[$h][$s][$id_kelas])) {
                    $penalty += 50;
                    $this->conflict_list[] = [
                        'type' => 'HC-02',
                        'description' => 'Kelas double-booked',
                        'hari' => $h,
                        'slot' => $s,
                        'entity' => $penugasan['nama_kelas'] ?? 'Kelas ID: ' . $id_kelas
                    ];
                }
                $kelas_usage[$h][$s][$id_kelas] = true;

                // HC-03: Ruangan tidak double-booked di slot sama
                if (isset($ruangan_usage[$h][$s][$id_ruangan])) {
                    $penalty += 50;
                    $this->conflict_list[] = [
                        'type' => 'HC-03',
                        'description' => 'Ruangan double-booked',
                        'hari' => $h,
                        'slot' => $s,
                        'entity' => $penugasan['nama_ruangan'] ?? 'Ruangan ID: ' . $id_ruangan
                    ];
                }
                $ruangan_usage[$h][$s][$id_ruangan] = true;

                // Hitung jam mingguan guru
                $guru_jam_mingguan[$id_guru]++;
            }
        }

        // HC-04: Jam mengajar guru tidak melebihi jam maks/minggu
        foreach ($guru_jam_mingguan as $id_guru => $jam) {
            if (isset($this->guru_list[$id_guru])) {
                $jam_maks = (int)($this->guru_list[$id_guru]['jam_maks'] ?? 40);
                if ($jam > $jam_maks) {
                    $penalty += 30 * ($jam - $jam_maks);
                    $this->conflict_list[] = [
                        'type' => 'HC-04',
                        'description' => 'Guru melebihi jam maksimal',
                        'entity' => $this->guru_list[$id_guru]['nama'] ?? 'Guru ID: ' . $id_guru,
                        'jam_actual' => $jam,
                        'jam_maks' => $jam_maks
                    ];
                }
            }
        }

        // HC-05: Kelas hanya mendapat mapel sesuai penugasan
        // (Sudah terpenuhi karena kita hanya menggunakan data penugasan yang valid)
        // Tidak perlu pengecekan tambahan

        // =========================================================================
        // CEK SOFT CONSTRAINTS
        // =========================================================================

        for ($h = 0; $h < $this->jumlah_hari; $h++) {
            for ($s = 0; $s < $this->jumlah_slot_per_hari; $s++) {
                $id_penugasan = $kromosom[$h][$s];

                if ($id_penugasan === null || !isset($this->penugasan_list[$id_penugasan])) {
                    continue;
                }

                $penugasan = $this->penugasan_list[$id_penugasan];

                // SC-01: Preferensi guru (jika ada field preferensi di tabel)
                // Asumsi: jika ada kolom 'preferensi_slot', cek kesesuaian
                if (isset($penugasan['preferensi_slot']) && $penugasan['preferensi_slot'] !== null) {
                    $preferensi = json_decode($penugasan['preferensi_slot'], true);
                    if (is_array($preferensi)) {
                        $slot_str = "{$h}-{$s}";
                        if (!in_array($slot_str, $preferensi)) {
                            $penalty += 5; // Penalty ringan untuk SC
                        }
                    }
                }

                // SC-02: Penempatan mapel praktikum di ruangan sesuai tipe
                if (isset($penugasan['jenis_mapel']) && strtolower($penugasan['jenis_mapel']) === 'praktikum') {
                    // Mapel praktikum harus di ruangan lab/bengkel
                    $jenis_ruangan = strtolower($penugasan['jenis_ruangan'] ?? '');
                    if (!strpos($jenis_ruangan, 'lab') && !strpos($jenis_ruangan, 'bengkel')) {
                        $penalty += 10; // Penalty sedang untuk SC penting
                        $this->conflict_list[] = [
                            'type' => 'SC-02',
                            'description' => 'Mapel praktikum tidak di ruangan lab',
                            'hari' => $h,
                            'slot' => $s,
                            'entity' => $penugasan['nama_mapel'] ?? 'Mapel',
                            'ruangan' => $penugasan['nama_ruangan'] ?? 'N/A'
                        ];
                    }
                }
            }
        }

        // Normalisasi fitness: 100 - penalty (min 0)
        $fitness = max(0, 100 - $penalty);

        return $fitness;
    }

    // =========================================================================
    // METHOD PRIVATE - SELEKSI DAN REPRODUKSI
    // =========================================================================

    /**
     * Lakukan seleksi tournament dan reproduksi (crossover + mutation)
     * 
     * @return array Populasi baru
     */
    private function _seleksi_dan_reproduksi() {
        $populasi_baru = [];

        // Elitism: Pertahankan individu terbaik
        $sorted_indices = $this->_get_sorted_indices();
        for ($i = 0; $i < $this->elitism_count && $i < $this->populasi_size; $i++) {
            $best_index = $sorted_indices[$i];
            $populasi_baru[] = $this->_clone_kromosom($this->populasi[$best_index]);
        }

        // Isi sisa populasi dengan offspring dari crossover
        while (count($populasi_baru) < $this->populasi_size) {
            // Tournament selection untuk parent 1
            $parent1_index = $this->_tournament_selection();
            
            // Tournament selection untuk parent 2
            $parent2_index = $this->_tournament_selection();

            // Crossover
            if (mt_rand() / mt_getrandmax() < $this->crossover_rate) {
                $offspring = $this->_crossover(
                    $this->populasi[$parent1_index],
                    $this->populasi[$parent2_index]
                );
            } else {
                // Jika tidak crossover, pilih salah satu parent
                $offspring = $this->_clone_kromosom(
                    $this->fitness_scores[$parent1_index] >= $this->fitness_scores[$parent2_index]
                    ? $this->populasi[$parent1_index]
                    : $this->populasi[$parent2_index]
                );
            }

            // Mutation
            $offspring = $this->_mutasi($offspring);

            $populasi_baru[] = $offspring;
        }

        return $populasi_baru;
    }

    /**
     * Tournament selection - pilih individu terbaik dari tournament acak
     * 
     * @return int Index individu terpilih
     */
    private function _tournament_selection() {
        $best_index = null;
        $best_fitness = -1;

        for ($i = 0; $i < $this->tournament_size; $i++) {
            $random_index = mt_rand(0, $this->populasi_size - 1);
            if ($this->fitness_scores[$random_index] > $best_fitness) {
                $best_fitness = $this->fitness_scores[$random_index];
                $best_index = $random_index;
            }
        }

        return $best_index;
    }

    /**
     * Dapatkan index populasi terurut berdasarkan fitness (descending)
     * 
     * @return array Array index terurut
     */
    private function _get_sorted_indices() {
        $indices = range(0, $this->populasi_size - 1);
        usort($indices, function($a, $b) {
            return $this->fitness_scores[$b] <=> $this->fitness_scores[$a];
        });
        return $indices;
    }

    /**
     * Order-based crossover untuk kromosom 2D
     * Mengambil sebagian dari parent1 dan melengkapi dengan urutan dari parent2
     * 
     * @param array $parent1 Kromosom parent 1
     * @param array $parent2 Kromosom parent 2
     * @return array Offspring hasil crossover
     */
    private function _crossover($parent1, $parent2) {
        $offspring = [];
        
        // Flatten kromosom 2D menjadi 1D untuk crossover
        $flat_p1 = [];
        $flat_p2 = [];
        
        for ($h = 0; $h < $this->jumlah_hari; $h++) {
            for ($s = 0; $s < $this->jumlah_slot_per_hari; $s++) {
                $flat_p1[] = $parent1[$h][$s];
                $flat_p2[] = $parent2[$h][$s];
            }
        }

        $total_length = count($flat_p1);
        
        // Pilih dua titik crossover secara acak
        $point1 = mt_rand(0, $total_length - 1);
        $point2 = mt_rand($point1, $total_length - 1);

        // Copy segment dari parent1 ke offspring
        $offspring_flat = array_fill(0, $total_length, null);
        $used_values = [];

        for ($i = $point1; $i <= $point2; $i++) {
            $offspring_flat[$i] = $flat_p1[$i];
            if ($flat_p1[$i] !== null) {
                $used_values[$flat_p1[$i]] = true;
            }
        }

        // Isi posisi kosong dengan nilai dari parent2 (maintain order)
        $p2_index = 0;
        for ($i = 0; $i < $total_length; $i++) {
            if ($offspring_flat[$i] === null) {
                while ($p2_index < $total_length) {
                    $val = $flat_p2[$p2_index];
                    $p2_index++;
                    if ($val !== null && !isset($used_values[$val])) {
                        $offspring_flat[$i] = $val;
                        break;
                    }
                }
            }
        }

        // Convert kembali ke 2D
        $index = 0;
        for ($h = 0; $h < $this->jumlah_hari; $h++) {
            $offspring[$h] = [];
            for ($s = 0; $s < $this->jumlah_slot_per_hari; $s++) {
                $offspring[$h][$s] = $offspring_flat[$index];
                $index++;
            }
        }

        return $offspring;
    }

    /**
     * Swap slot mutation - tukar dua slot secara acak
     * 
     * @param array $kromosom Kromosom yang akan dimutasi
     * @return array Kromosom hasil mutasi
     */
    private function _mutasi($kromosom) {
        $result = $this->_clone_kromosom($kromosom);

        if (mt_rand() / mt_getrandmax() < $this->mutation_rate) {
            // Pilih dua slot acak untuk ditukar
            $h1 = mt_rand(0, $this->jumlah_hari - 1);
            $s1 = mt_rand(0, $this->jumlah_slot_per_hari - 1);
            $h2 = mt_rand(0, $this->jumlah_hari - 1);
            $s2 = mt_rand(0, $this->jumlah_slot_per_hari - 1);

            // Swap
            $temp = $result[$h1][$s1];
            $result[$h1][$s1] = $result[$h2][$s2];
            $result[$h2][$s2] = $temp;
        }

        return $result;
    }

    // =========================================================================
    // METHOD PRIVATE - UTILITY
    // =========================================================================

    /**
     * Clone kromosom (deep copy)
     * 
     * @param array $kromosom Kromosom yang akan di-clone
     * @return array Clone kromosom
     */
    private function _clone_kromosom($kromosom) {
        $clone = [];
        for ($h = 0; $h < $this->jumlah_hari; $h++) {
            $clone[$h] = [];
            for ($s = 0; $s < $this->jumlah_slot_per_hari; $s++) {
                $clone[$h][$s] = $kromosom[$h][$s];
            }
        }
        return $clone;
    }

    /**
     * Ekstrak hasil terbaik dan update conflict list
     */
    private function _ekstrak_hasil_terbaik() {
        if ($this->best_kromosom !== null) {
            // Hitung ulang fitness dan conflict list untuk best chromosome
            $this->_hitung_fitness($this->best_kromosom);
        }
    }
}
