<?php 
// Memulai session
session_start();

// Menghapus semua session
session_destroy();

// Mengalihkan halaman ke login.php sambil mengirim pesan logout
header("location:login.php?pesan=logout");
exit;
?>