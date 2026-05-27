<?php
include '../config/db.php';

// Ambil bulan dan tahun dari filter, defaultnya bulan sekarang
$bulan_ini = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun_ini = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Hitung jumlah hari dalam bulan tersebut
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan_ini, $tahun_ini);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rekap Bulanan GSE Power</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .table-rekap { font-size: 10px; border-collapse: collapse; width: 100%; background: white; }
        .table-rekap th, .table-rekap td { border: 1px solid #ddd; padding: 5px; text-align: center; }
        .bg-hadir { background: #d4edda; color: #155724; }
        .bg-tidak-hadir { background: #f8d7da; color: #721c24; font-weight: bold; }
        @media print { .no-print { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="no-print" style="background:#333; padding:10px; text-align:center;">
        <a href="index.php" style="color:white; text-decoration:none;">⬅️ Kembali ke Update</a>
    </div>

    <h2 style="text-align:center;">REKAP ABSENSI - <?php echo date('F Y', strtotime("$tahun_ini-$bulan_ini-01")); ?></h2>

    <form method="GET" class="no-print" style="text-align:center; margin-bottom:20px;">
        <select name="bulan">
            <?php for($m=1; $m<=12; $m++): ?>
                <option value="<?php echo sprintf('%02d', $m); ?>" <?php if($bulan_ini == $m) echo 'selected'; ?>>
                    <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                </option>
            <?php endfor; ?>
        </select>
        <select name="tahun">
            <?php for($y=2024; $y<=2026; $y++): ?>
                <option value="<?php echo $y; ?>" <?php if($tahun_ini == $y) echo 'selected'; ?>><?php echo $y; ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit">Tampilkan</button>
        <button onclick="window.print()" style="background:orange; color:white;">Cetak/PDF</button>
    </form>

    <div style="overflow-x:auto;">
        <table class="table-rekap">
            <thead>
                <tr style="background:#f2f2f2;">
                    <th rowspan="2">Nama Karyawan</th>
                    <th colspan="<?php echo $jumlah_hari; ?>">Tanggal</th>
                </tr>
                <tr style="background:#f2f2f2;">
                    <?php for($d=1; $d<=$jumlah_hari; $d++): ?>
                        <th><?php echo $d; ?></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $q_karyawan = mysqli_query($conn, "SELECT id_karyawan, nama FROM m_karyawan WHERE status_aktif='Aktif' ORDER BY divisi, nama");
                while($k = mysqli_fetch_assoc($q_karyawan)){
                    echo "<tr>";
                    echo "<td style='text-align:left; white-space:nowrap;'>{$k['nama']}</td>";
                    
                    for($d=1; $d<=$jumlah_hari; $d++){
                        $tgl_cek = "$tahun_ini-$bulan_ini-" . sprintf('%02d', $d);
                        $q_absen = mysqli_query($conn, "SELECT status_hadir FROM t_kehadiran WHERE id_karyawan='{$k['id_karyawan']}' AND tanggal='$tgl_cek'");
                        $row = mysqli_fetch_assoc($q_absen);
                        
                        if($row){
                            $status = $row['status_hadir'];
                            $kelas = ($status == 'Hadir') ? 'bg-hadir' : 'bg-tidak-hadir';
                            $inisial = ($status == 'Hadir') ? 'H' : substr($status, 0, 1);
                            echo "<td class='$kelas'>$inisial</td>";
                        } else {
                            echo "<td>-</td>"; // Jika belum diinput
                        }
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px; font-size:12px;">
        <strong>Keterangan:</strong> H (Hadir), S (Sakit), I (Izin), A (Alpha), O (Off)
    </div>
</body>
</html>