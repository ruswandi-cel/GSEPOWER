<?php
include 'cek_login.php'; 
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = date('Y-m-d');
    
    if (isset($_POST['status']) && is_array($_POST['status'])) {
        $status_array = $_POST['status'];
        $keterangan_array = $_POST['keterangan'] ?? [];

        foreach ($status_array as $id_karyawan => $status) {
            $id_karyawan = mysqli_real_escape_string($conn, $id_karyawan);
            $status = mysqli_real_escape_string($conn, $status);
            $ket = mysqli_real_escape_string($conn, $keterangan_array[$id_karyawan] ?? '');

            $cek = mysqli_query($conn, "SELECT id_absensi FROM t_kehadiran WHERE id_karyawan = '$id_karyawan' AND tanggal = '$tanggal'");
            
            if (mysqli_num_rows($cek) > 0) {
                mysqli_query($conn, "UPDATE t_kehadiran SET status_hadir = '$status', keterangan = '$ket' WHERE id_karyawan = '$id_karyawan' AND tanggal = '$tanggal'");
            } else {
                mysqli_query($conn, "INSERT INTO t_kehadiran (id_karyawan, tanggal, status_hadir, keterangan) VALUES ('$id_karyawan', '$tanggal', '$status', '$ket')");
            }
        }

        // --- JURUS PEMBERSIH REMARKS HANTU ---
        // Hapus data absensi hari ini yang id_karyawan-nya sudah berstatus 'Non-Aktif'
        mysqli_query($conn, "DELETE FROM t_kehadiran WHERE tanggal = '$tanggal' AND id_karyawan IN (SELECT id_karyawan FROM m_karyawan WHERE status_aktif = 'Non-Aktif')");

        echo "<script>alert('BERHASIL! Data diperbarui & Remarks hantu dibersihkan.'); window.location='index.php';</script>";
    }
}
?>