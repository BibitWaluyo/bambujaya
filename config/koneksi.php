<?php
// Pengaturan Koneksi
$host = "localhost:3307";
$user = "root";
$pass = "PASSWORD_YANG_BENAR";     // <--- GANTI 'PASSWORD_YANG_BENAR' DENGAN PASSWORD MYSQL LO
$db   = "db_bambujaya"; 

$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek Koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>