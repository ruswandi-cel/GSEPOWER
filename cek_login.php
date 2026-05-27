<?php 
session_start();
// Cek apakah ada session status login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    // Kalau tidak ada, tendang balik ke login.php
    header("location:login.php");
    exit;
}
?>