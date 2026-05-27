<?php
include 'config/db.php'; // Sesuaikan path-nya karena ini di luar folder admin

$bulan_ini = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun_ini = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan_ini, $tahun_ini);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REKAP ABSENSI - USER VIEW</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0a0f18;
            --card-bg: #111827;
            --neon-blue: #2dd4bf;
            --text-main: #f3f4f6;
            --text-dim: #9ca3af;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            padding: 15px;
        }

        .header-rekap { text-align: center; margin-bottom: 25px; }
        .header-rekap h2 { color: var(--neon-blue); letter-spacing: 2px; margin-bottom: 5px; }

        /* Form Styling */
        form { text-align: center; margin-bottom: 20px; }
        select, button {
            background: var(--card-bg);
            color: white;
            border: 1px solid #374151;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
        }
        button { background: var(--neon-blue); color: #0a0f18; font-weight: bold; cursor: pointer; border: none; }

        /* Table Dark Styling */
        .table-wrapper {
            overflow-x: auto;
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid #1f2937;
        }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #1f2937; padding: 10px 5px; text-align: center; }
        th { background: #1f2937; color: var(--neon-blue); text-transform: uppercase; }

        /* Sticky Column Nama */
        .sticky-col {
            position: sticky;
            left: 0;
            background: #111827;
            text-align: left;
            padding-left: 10px;
            white-space: nowrap;
            border-right: 2px solid #1f2937;
        }

        /* Status Colors */
        .status-h { color: var(--neon-blue); font-weight: bold; }
        .status-not-h { color: #ef4444; font-weight: bold; }

        .btn-back {
            display: inline-block;
            margin-bottom: 15px;
            color: var(--text-dim);
            text-decoration: none;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

    <a href="index.php" class="btn-back">⬅️ Kembali ke Monitoring</a>

    <div class="header-rekap">
        <h2>REKAP ABSENSI</h2>
        <p style="color: var(--text-dim)"><?php echo date('F Y', strtotime("$tahun_ini-$bulan_ini-01")); ?></p>
    </div>

    <form method="GET">
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
        <button type="submit">CARI</button>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th rowspan="2" class="sticky-col">Nama Karyawan</th>
                    <th colspan="<?php echo $jumlah_hari; ?>">Tanggal</th>
                </tr>
                <tr>
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
                    echo "<td class='sticky-col'>" . strtoupper($k['nama']) . "</td>";
                    
                    for($d=1; $d<=$jumlah_hari; $d++){
                        $tgl_cek = "$tahun_ini-$bulan_ini-" . sprintf('%02d', $d);
                        $q_absen = mysqli_query($conn, "SELECT status_hadir FROM t_kehadiran WHERE id_karyawan='{$k['id_karyawan']}' AND tanggal='$tgl_cek'");
                        $row = mysqli_fetch_assoc($q_absen);
                        
                        if($row){
                            $status = $row['status_hadir'];
                            $inisial = ($status == 'Hadir') ? 'H' : substr($status, 0, 1);
                            $class = ($status == 'Hadir') ? 'status-h' : 'status-not-h';
                            echo "<td class='$class'>$inisial</td>";
                        } else {
                            echo "<td style='color:#374151'>-</td>";
                        }
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px; text-align:center; font-size:11px; color: var(--text-dim);">
        H (Hadir) | S (Sakit) | I (Izin) | A (Alpha) | O (Off)
    </div>

</body>
</html>