<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_gse_power"; // Pastikan namanya sama dengan yang kamu buat di phpMyAdmin

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>