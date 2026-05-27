<?php
include 'cek_login.php'; 
include '../config/db.php';

// Logika Tambah Orang Baru
if(isset($_POST['tambah'])){
    $nama = mysqli_real_escape_string($conn, strtoupper($_POST['nama']));
    $divisi = mysqli_real_escape_string($conn, $_POST['divisi']);
    mysqli_query($conn, "INSERT INTO m_karyawan (nama, divisi, status_aktif) VALUES ('$nama', '$divisi', 'Aktif')");
    header("Location: kelola_orang.php?pesan=berhasil");
    exit;
}

// Logika Hapus (Non-Aktifkan)
if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "UPDATE m_karyawan SET status_aktif='Non-Aktif' WHERE id_karyawan='$id'");
    header("Location: kelola_orang.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Personil - GSE POWER</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8fafc;
            --dark: #0f172a;
            --primary: #2563eb;
            --danger: #ef4444;
            --gray: #64748b;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg); 
            margin: 0; color: var(--dark);
            padding: 20px;
        }

        .container { max-width: 600px; margin: 0 auto; }

        .header { text-align: center; margin-bottom: 20px; }
        .btn-back { text-decoration: none; color: var(--primary); font-weight: 600; font-size: 0.9em; }

        /* Card Form */
        .card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .card h3 { margin-top: 0; font-size: 1.2em; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }

        input, select {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: inherit;
        }

        .btn-add {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 800;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-add:hover { background: #1d4ed8; }

        /* Table Style */
        .table-container { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        
        table { width: 100%; border-collapse: collapse; font-size: 0.9em; }
        
        th { background: #f1f5f9; padding: 15px; text-align: left; color: var(--gray); text-transform: uppercase; font-size: 0.75em; }
        
        td { padding: 15px; border-top: 1px solid #f1f5f9; }

        .badge-divisi {
            background: #e0e7ff;
            color: #4338ca;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8em;
            font-weight: 600;
        }

        .btn-delete { color: var(--danger); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <a href="index.php" class="btn-back">⬅️ Kembali ke Update Harian</a>
        <h2>Manajemen Personil</h2>
    </div>

    <div class="card">
        <h3>Tambah Personil Baru</h3>
        <form method="POST" autocomplete="off">
            <input type="text" name="nama" placeholder="Nama Lengkap (Contoh: ACEP JAELANI)" required>
            
            <select name="divisi" required>
                <option value="" disabled selected>-- Pilih Divisi (Rotasi) --</option>
                <option value="Group Leader">⭐ Group Leader</option>
                <option value="Operator ATT">Operator ATT</option>
                <option value="Operator BTT">Operator BTT</option>
                <option value="Operator Equipment">Operator Equipment</option>
                <option value="Operator LST & WST">Operator LST & WST</option>
                <option value="Wingman">Wingman</option>
            </select>
            
            <button type="submit" name="tambah" class="btn-add">TAMBAHKAN KE SISTEM</button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Divisi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q = mysqli_query($conn, "SELECT * FROM m_karyawan WHERE status_aktif='Aktif' ORDER BY divisi ASC, nama ASC");
                while($row = mysqli_fetch_assoc($q)){
                    ?>
                    <tr>
                        <td><strong><?php echo strtoupper($row['nama']); ?></strong></td>
                        <td><span class="badge-divisi"><?php echo $row['divisi']; ?></span></td>
                        <td>
                            <a href="?hapus=<?php echo $row['id_karyawan']; ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Karyawan akan dinonaktifkan dari daftar absen. Lanjutkan?')">Hapus</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>