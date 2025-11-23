<?php
session_start();
require_once "../config/koneksi.php";

// ====================================================================
// 1. CEK SESSION ADMIN
// ====================================================================
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['role'] != 'admin') {
    header("location:../index.php");
    exit();
}

// ====================================================================
// 2. AMBIL DATA STATISTIK UNTUK DASHBOARD
// ====================================================================
$total_produk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM products"))['total'];
$total_user   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users"))['total'];
$total_order  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM orders"))['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Bambu Jaya</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 6px;
            margin-left: 10px;
        }
        .navbar a:hover { background: rgba(255,255,255,0.3); }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #4CAF50;
            margin-bottom: 10px;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        .card {
            flex: 1;
            min-width: 220px;
            background-color: #f8fff8;
            border-left: 6px solid #4CAF50;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            text-align: center;
        }
        .card h2 {
            margin: 0;
            font-size: 2em;
            color: #333;
        }
        .card p {
            margin: 5px 0 0;
            color: #555;
        }
        .actions {
            margin-top: 40px;
            display: flex;
            gap: 20px;
        }
        .actions a {
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 6px;
            transition: 0.3s;
        }
        .actions a:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <div class="navbar">
        <div><strong>Bambu Jaya Admin Panel</strong></div>
        <div>
            <a href="products.php">Produk</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h1>Selamat datang, <?php echo $_SESSION['nama']; ?>!</h1>
        <p>Berikut ringkasan toko hari ini:</p>

        <div class="stats">
            <div class="card">
                <h2><?php echo $total_produk; ?></h2>
                <p>Total Produk</p>
            </div>
            <div class="card">
                <h2><?php echo $total_user; ?></h2>
                <p>Total Pengguna</p>
            </div>
            <div class="card">
                <h2><?php echo $total_order; ?></h2>
                <p>Total Pesanan</p>
            </div>
        </div>

        <div class="actions">
            <a href="products.php">Kelola Produk</a>
            <a href="orders.php">Kelola Pesanan</a>
        </div>
    </div>
</body>
</html>
