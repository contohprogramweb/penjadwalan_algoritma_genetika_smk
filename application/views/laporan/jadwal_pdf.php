<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Jadwal Pelajaran - <?php echo $kelas->nama_kelas; ?></title>
    <style>
        @page {
            margin: 15mm;
            size: A4 landscape;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 14pt;
            font-weight: normal;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        
        .jadwal-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .jadwal-table th,
        .jadwal-table td {
            border: 1px solid #333;
            padding: 6px 4px;
            text-align: center;
            font-size: 10pt;
        }
        
        .jadwal-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .jadwal-table .hari-column {
            width: 80px;
            font-weight: bold;
            background-color: #e8e8e8;
        }
        
        .jadwal-table .slot-column {
            width: 60px;
        }
        
        .mapel-cell {
            min-width: 120px;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            margin: 0 15mm;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            font-size: 9pt;
            color: #666;
        }
        
        .footer-content {
            display: table;
            width: 100%;
        }
        
        .footer-left,
        .footer-right {
            display: table-cell;
        }
        
        .footer-right {
            text-align: right;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>JADWAL PELAJARAN</h1>
        <h2><?php echo strtoupper($kelas->nama_kelas); ?></h2>
    </div>
    
    <!-- Info Table -->
    <table class="info-table">
        <tr>
            <td class="info-label">Semester</td>
            <td>: <?php echo $semester; ?></td>
            <td class="info-label">Tahun Ajaran</td>
            <td>: <?php echo $tahun_ajaran; ?></td>
        </tr>
        <tr>
            <td class="info-label">Kelas</td>
            <td>: <?php echo $kelas->nama_kelas; ?></td>
            <td class="info-label">Kompetensi Keahlian</td>
            <td>: <?php echo isset($kelas->kompetensi_keahlian) ? $kelas->kompetensi_keahlian : '-'; ?></td>
        </tr>
    </table>
    
    <!-- Jadwal Table -->
    <?php if (empty($jadwal_list)): ?>
        <div class="no-data">Belum ada jadwal untuk kelas ini.</div>
    <?php else: ?>
        <table class="jadwal-table">
            <thead>
                <tr>
                    <th class="hari-column">Hari</th>
                    <th class="slot-column">Jam Ke-</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>Ruangan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $current_hari = '';
                foreach ($jadwal_list as $j): 
                    // Group by hari
                    if ($current_hari !== $j->hari):
                        $current_hari = $j->hari;
                    endif;
                ?>
                <tr>
                    <td class="hari-column"><?php echo ucfirst($j->hari); ?></td>
                    <td class="slot-column"><?php echo $j->slot; ?></td>
                    <td class="mapel-cell"><?php echo $j->nama_mapel; ?></td>
                    <td><?php echo $j->nama_guru; ?></td>
                    <td><?php echo $j->nama_ruangan; ?></td>
                    <td><?php echo isset($j->keterangan) ? $j->keterangan : '-'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <!-- Footer -->
    <div class="footer">
        <div class="footer-content">
            <div class="footer-left">
                Dicetak pada: <?php echo $tanggal_cetak; ?>
            </div>
            <div class="footer-right">
                Halaman <?php echo $page_number; ?>
            </div>
        </div>
    </div>
</body>
</html>
