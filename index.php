<?php
// Proteksi Halaman Admin
if (file_exists(__DIR__ . '/cek_login.php')) {
    include __DIR__ . '/cek_login.php';
} else {
    session_start();
    if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
        header("location:login.php");
        exit;
    }
}

// Koneksi Database
include '../config/db.php';
$tanggal_hari_ini = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSE POWER - Admin Control</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8fafc;
            --dark: #0f172a;
            --primary: #2563eb;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray: #64748b;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg); 
            margin: 0; color: var(--dark);
        }

        /* Navigasi */
        .admin-nav {
            background: var(--dark);
            padding: 12px;
            display: flex;
            justify-content: center;
            gap: 10px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .admin-nav a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 11px;
            font-weight: 700;
            padding: 10px 15px;
            border-radius: 8px;
            transition: 0.3s;
            text-transform: uppercase;
        }
        .admin-nav a.active { background: var(--primary); color: white; }
        .admin-nav a.logout { color: var(--danger); }

        .container { padding: 20px; max-width: 500px; margin: 0 auto; }

        .header-box { text-align: center; margin-bottom: 30px; }
        .header-box h2 { margin: 0; font-size: 1.6em; font-weight: 800; }
        .header-box p { color: var(--primary); margin: 5px 0; font-weight: 600; }

        .filter-info {
            background: #fff;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.8em;
            color: var(--gray);
            border: 1px solid #e2e8f0;
        }

        /* Card System */
        .karyawan-list { display: grid; gap: 12px; margin-bottom: 120px; }
        
        .karyawan-card {
            background: white;
            padding: 15px;
            border-radius: 15px;
            border: 1px solid #e2e8f0;
            transition: 0.3s;
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            position: relative;
        }

        .status-Hadir { border-left: 6px solid var(--success); }
        .status-Izin { border-left: 6px solid var(--warning); }
        .status-Sakit { border-left: 6px solid #fb923c; }
        .status-Off { border-left: 6px solid var(--gray); }
        .status-Alpha { border-left: 6px solid var(--danger); }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .card-info strong { font-size: 1.1em; color: var(--dark); display: block; }
        .card-info small { color: var(--gray); font-size: 0.75em; text-transform: uppercase; font-weight: 700; }

        .btn-edit-fast {
            text-decoration: none;
            background: #f1f5f9;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 0.9em;
            transition: 0.2s;
        }
        .btn-edit-fast:hover { background: #e2e8f0; transform: scale(1.1); }

        select {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #f1f5f9;
            font-weight: 700;
            font-size: 0.9em;
            cursor: pointer;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-sizing: border-box;
            font-size: 0.85em;
        }

        /* Floating Button */
        .bottom-action {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 500px;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            padding: 20px;
            box-sizing: border-box;
            border-top: 1px solid #e2e8f0;
            border-radius: 20px 20px 0 0;
            z-index: 999;
        }

        .btn-update {
            background: var(--dark);
            color: white;
            border: none;
            width: 100%;
            padding: 18px;
            border-radius: 12px;
            font-size: 1em;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <nav class="admin-nav">
        <a href="index.php" class="active">Update</a>
        <a href="rekap.php">Rekap</a>
        <a href="kelola_orang.php">Karyawan</a>
        <a href="logout.php" class="logout">Keluar</a>
    </nav>

    <div class="container">
        <div class="header-box">
            <p><?php echo date('l, d F Y'); ?></p>
            <h2>Input Kekuatan</h2>
        </div>

        <div class="filter-info">
            Menampilkan <strong>
            <?php 
                $res = mysqli_query($conn, "SELECT COUNT(*) as jml FROM m_karyawan WHERE status_aktif='Aktif'");
                $count = mysqli_fetch_assoc($res);
                echo $count['jml'] ?? 0;
            ?></strong> Personil Aktif
        </div>

        <form action="proses_update.php" method="POST">
            <div class="karyawan-list">
                <?php
                // Urutan Group Leader tetap di atas karena ORDER BY divisi ASC
                $query = mysqli_query($conn, "SELECT * FROM m_karyawan WHERE status_aktif='Aktif' ORDER BY divisi ASC, nama ASC");
                while($row = mysqli_fetch_assoc($query)){
                    $id_k = $row['id_karyawan'];
                    $cek = mysqli_query($conn, "SELECT status_hadir, keterangan FROM t_kehadiran WHERE id_karyawan = '$id_k' AND tanggal = '$tanggal_hari_ini'");
                    $data_lama = mysqli_fetch_assoc($cek);
                    $status_sekarang = $data_lama['status_hadir'] ?? 'Hadir';
                    $ket_sekarang = $data_lama['keterangan'] ?? '';
                ?>
                    <div class="karyawan-card status-<?php echo $status_sekarang; ?>" id="card-<?php echo $id_k; ?>">
                        <div class="card-header">
                            <div class="card-info">
                                <strong><?php echo strtoupper($row['nama']); ?></strong>
                                <small><?php echo $row['divisi']; ?></small>
                            </div>
                            <a href="edit_orang.php?id=<?php echo $id_k; ?>" class="btn-edit-fast" title="Edit / Rotasi">✏️</a>
                        </div>
                        
                        <select name="status[<?php echo $id_k; ?>]" onchange="updateCardColor(this, '<?php echo $id_k; ?>')">
                            <option value="Hadir" <?php echo ($status_sekarang == 'Hadir' ? 'selected' : ''); ?>>✅ HADIR</option>
                            <option value="Izin" <?php echo ($status_sekarang == 'Izin' ? 'selected' : ''); ?>>📝 IZIN</option>
                            <option value="Sakit" <?php echo ($status_sekarang == 'Sakit' ? 'selected' : ''); ?>>🤒 SAKIT</option>
                            <option value="Off" <?php echo ($status_sekarang == 'Off' ? 'selected' : ''); ?>>😴 OFF</option>
                            <option value="Alpha" <?php echo ($status_sekarang == 'Alpha' ? 'selected' : ''); ?>>😡 ALPHA</option>
                        </select>

                        <input type="text" name="keterangan[<?php echo $id_k; ?>]" placeholder="Tambahkan Remarks..." value="<?php echo $ket_sekarang; ?>">
                    </div>
                <?php } ?>
            </div>

            <div class="bottom-action">
                <button type="submit" class="btn-update">
                    PUSH DATA KEKUATAN
                </button>
            </div>
        </form>
    </div>

    <script>
        function updateCardColor(selectElement, id) {
            const card = document.getElementById('card-' + id);
            card.className = 'karyawan-card status-' + selectElement.value;
        }
    </script>

</body>
</html>