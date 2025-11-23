<?php
session_start();
require_once "../config/koneksi.php";

if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['role'] != 'user') {
    header("location:../login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

$orders = mysqli_query($koneksi, "
    SELECT * FROM orders 
    WHERE id_user='$id_user' 
    ORDER BY tanggal_order DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Saya - Bambu Jaya</title>
    <style>
        body { font-family: Arial; background: #f4f6f9; margin: 0; }
        .navbar { background: #4CAF50; color: white; padding: 15px 20px; display: flex; justify-content: space-between; }
        .navbar a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        .navbar a:hover { background: rgba(255,255,255,0.3); }
        .container { max-width: 800px; margin: 30px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; }
        th { background: #4CAF50; color: white; }
        .status { font-weight: bold; }
    </style>
</head>
<body>
    <div class="navbar">
        <div><strong>Bambu Jaya</strong></div>
        <div>
            <a href="dashboard.php">Produk</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Pesanan Saya</h2>
        <table>
            <tr>
                <th>ID Order</th>
                <th>Tanggal</th>
                <th>Total Bayar</th>
                <th>Status</th>
            </tr>
            <?php while ($o = mysqli_fetch_assoc($orders)) : ?>
            <tr>
                <td>#<?= $o['id_order']; ?></td>
                <td><?= date('d-m-Y H:i', strtotime($o['tanggal_order'])); ?></td>
                <td>Rp <?= number_format($o['total_bayar'], 0, ',', '.'); ?></td>
                <td class="status"><?= ucfirst($o['status_order']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
