<?php
include 'cek_login.php'; 
include '../config/db.php';

// Ambil ID dan proteksi input
$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM m_karyawan WHERE id_karyawan = '$id'");
$data = mysqli_fetch_assoc($query);

if(isset($_POST['update'])){
    // Nama otomatis jadi HURUF BESAR biar rapi di laporan
    $nama = mysqli_real_escape_string($conn, strtoupper($_POST['nama']));
    $divisi = mysqli_real_escape_string($conn, $_POST['divisi']);
    
    mysqli_query($conn, "UPDATE m_karyawan SET nama='$nama', divisi='$divisi' WHERE id_karyawan='$id'");
    header("Location: kelola_orang.php?pesan=update_berhasil");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Personil - GSE POWER</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; padding: 20px; display: flex; justify-content: center; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { margin-top: 0; font-size: 1.3em; font-weight: 800; color: #0f172a; text-align: center; margin-bottom: 20px; }
        label { font-size: 0.8em; font-weight: 700; color: #64748b; text-transform: uppercase; }
        input, select { 
            width: 100%; padding: 12px; margin: 8px 0 20px 0; 
            border: 1px solid #e2e8f0; border-radius: 10px; 
            background: #f1f5f9; font-weight: 600; font-family: inherit;
        }
        .btn-update { 
            background: #f59e0b; color: white; border: none; width: 100%; 
            padding: 15px; border-radius: 12px; font-weight: 800; 
            cursor: pointer; transition: 0.3s;
        }
        .btn-update:hover { background: #d97706; transform: translateY(-2px); }
        .btn-back { display: block; text-align: center; margin-top: 15px; color: #94a3b8; text-decoration: none; font-size: 0.9em; font-weight: 600; }
    </style>
</head>
<body>

<div class="card">
    <h2>Rotasi / Edit Nama</h2>
    <form method="POST">
        <label>Nama Karyawan</label>
        <input type="text" name="nama" value="<?php echo $data['nama']; ?>" required>
        
        <label>Divisi (Ganti untuk Rotasi)</label>
        <select name="divisi">
            <?php 
            // REVISI: Tambahkan Group Leader ke dalam list pilihan
            $divisiArr = ['Group Leader', 'Operator ATT', 'Operator BTT', 'Operator Equipment', 'Operator LST & WST', 'Wingman'];
            foreach($divisiArr as $d){
                // Sekarang 'Group Leader' akan ketemu kecocokannya di sini
                $sel = ($data['divisi'] == $d) ? "selected" : "";
                echo "<option value='$d' $sel>$d</option>";
            }
            ?>
        </select>
        
        <button type="submit" name="update" class="btn-update">UPDATE DATA</button>
        <a href="kelola_orang.php" class="btn-back">Batal / Kembali</a>
    </form>
</div>

</body>
</html>