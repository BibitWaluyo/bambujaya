<?php
session_start();
require_once "../config/koneksi.php";

if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['role'] != 'admin') {
    header("location:../login.php");
    exit();
}

// =============================================================
// UPDATE STATUS PESANAN
// =============================================================
if (isset($_POST['update_status'])) {
    $id_order = mysqli_real_escape_string($koneksi, $_POST['id_order']); 
    $status = mysqli_real_escape_string($koneksi, $_POST['status_order']); 
    mysqli_query($koneksi, "UPDATE orders SET status_order='$status' WHERE id_order='$id_order'");
    header("Location: orders.php");
    exit;
}

// =============================================================
// AMBIL DATA PESANAN (FIX: Menggunakan u.nama_user)
// =============================================================
// Menghapus komentar PHP dari dalam string SQL
$query = mysqli_query($koneksi, "
    SELECT 
        o.*, 
        u.nama_user 
    FROM orders o
    JOIN users u ON o.id_user = u.id_user
    ORDER BY o.tanggal_order DESC
");

// Cek apakah query gagal
if (!$query) {
    die("Query error: " . mysqli_error($koneksi) . ". Cek apakah kolom nama_user sudah benar.");
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pesanan - Bambu Jaya</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background-color: #f4f6f9; margin: 0; }
        .navbar { background-color: #2e8b57; /* Darker Green */ color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .navbar a { color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 6px; transition: background 0.3s; }
        .navbar a:hover { background: rgba(255,255,255,0.4); }
        .container { max-width: 1000px; margin: 30px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #2e8b57; margin-bottom: 20px; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 15px; border-radius: 8px; overflow: hidden; }
        th, td { border: 1px solid #e0e0e0; padding: 12px; text-align: left; }
        th { background: #4CAF50; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        select { padding: 8px; border-radius: 5px; border: 1px solid #ccc; }
        button[type="submit"] { 
            padding: 8px 12px; 
            background-color: #2e8b57; 
            color: white; 
            border: none; 
            cursor: pointer; 
            border-radius: 5px; 
            transition: background-color 0.3s;
            margin-left: 5px;
        }
        button[type="submit"]:hover { background-color: #246d45; }
        form { display: flex; align-items: center; }
        /* Style for Order Status Colors */
        .status-pending { color: orange; font-weight: bold; }
        .status-diproses { color: blue; font-weight: bold; }
        .status-dikirim { color: purple; font-weight: bold; }
        .status-selesai { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <div class="navbar">
        <div><strong>Bambu Jaya Admin Panel</strong></div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="orders.php">Pesanan</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Daftar Pesanan</h2>
        <table>
            <tr>
                <th>ID Order</th>
                <th>Nama User</th>
                <th>Tanggal</th>
                <th>Total Bayar</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            <?php 
            if (mysqli_num_rows($query) > 0) :
                while ($o = mysqli_fetch_assoc($query)) : 
            ?>
            <tr>
                <td>#<?= htmlspecialchars($o['id_order']); ?></td>
                
                <!-- FIX: Menampilkan nama_user -->
                <td><?= htmlspecialchars($o['nama_user']); ?></td> 
                
                <td><?= date('d-m-Y H:i', strtotime($o['tanggal_order'])); ?></td>
                <td>Rp <?= number_format($o['total_bayar'], 0, ',', '.'); ?></td>
                <td class="status-<?= strtolower($o['status_order']); ?>"><?= ucfirst($o['status_order']); ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="id_order" value="<?= htmlspecialchars($o['id_order']); ?>">
                        <select name="status_order" required>
                            <option value="pending" <?= ($o['status_order'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="diproses" <?= ($o['status_order'] == 'diproses') ? 'selected' : ''; ?>>Diproses</option>
                            <option value="dikirim" <?= ($o['status_order'] == 'dikirim') ? 'selected' : ''; ?>>Dikirim</option>
                            <option value="selesai" <?= ($o['status_order'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                        </select>
                        <button type="submit" name="update_status">Simpan</button>
                    </form>
                </td>
            </tr>
            <?php 
                endwhile; 
            else:
            ?>
            <tr>
                <td colspan="6" style="text-align: center;">Belum ada data pesanan.</td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>