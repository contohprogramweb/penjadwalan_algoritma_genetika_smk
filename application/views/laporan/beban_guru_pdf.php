<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Beban Mengajar - <?php echo $guru->nama_guru; ?></title>
    <style>
        @page {
            margin: 15mm;
            size: A4 portrait;
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
            font-size: 13pt;
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
            width: 150px;
        }
        
        .beban-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .beban-table th,
        .beban-table td {
            border: 1px solid #333;
            padding: 6px 4px;
            text-align: left;
            font-size: 10pt;
        }
        
        .beban-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .beban-table .center {
            text-align: center;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
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
        
        .summary-box {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 10px;
            margin-top: 15px;
            border-radius: 4px;
        }
        
        .summary-box strong {
            font-size: 12pt;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN BEBAN MENGAJAR GURU</h1>
        <h2><?php echo strtoupper($guru->nama_guru); ?></h2>
    </div>
    
    <!-- Info Table -->
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Guru</td>
            <td>: <?php echo $guru->nama_guru; ?></td>
        </tr>
        <tr>
            <td class="info-label">NIP/NIY</td>
            <td>: <?php echo isset($guru->nip) ? $guru->nip : '-'; ?></td>
        </tr>
        <tr>
            <td class="info-label">Semester</td>
            <td>: <?php echo $semester; ?></td>
            <td class="info-label">Tahun Ajaran</td>
            <td>: <?php echo $tahun_ajaran; ?></td>
        </tr>
    </table>
    
    <!-- Beban Mengajar Table -->
    <?php if (empty($penugasan_list)): ?>
        <div class="no-data">Belum ada penugasan mengajar untuk guru ini.</div>
    <?php else: ?>
        <table class="beban-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="25%">Mata Pelajaran</th>
                    <th width="20%">Kelas</th>
                    <th width="15%">Semester</th>
                    <th width="10%" class="center">Jam/Minggu</th>
                    <th width="25%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach ($penugasan_list as $p): 
                ?>
                <tr>
                    <td class="center"><?php echo $no++; ?></td>
                    <td><?php echo $p->nama_mapel; ?></td>
                    <td><?php echo $p->nama_kelas; ?></td>
                    <td><?php echo $p->semester; ?></td>
                    <td class="center"><?php echo $p->jam_mengajar; ?></td>
                    <td><?php echo isset($p->keterangan) ? $p->keterangan : '-'; ?></td>
                </tr>
                <?php endforeach; ?>
                
                <!-- Total Row -->
                <tr class="total-row">
                    <td colspan="4" class="center"><strong>Total Jam Mengajar per Minggu</strong></td>
                    <td class="center"><strong><?php echo $total_jam; ?></strong></td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Summary Box -->
        <div class="summary-box">
            <strong>Kesimpulan:</strong><br>
            Guru <strong><?php echo $guru->nama_guru; ?></strong> memiliki beban mengajar sebanyak 
            <strong><?php echo $total_jam; ?> jam pelajaran per minggu</strong> pada semester 
            <strong><?php echo $semester; ?></strong> tahun ajaran <strong><?php echo $tahun_ajaran; ?></strong>.
        </div>
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
