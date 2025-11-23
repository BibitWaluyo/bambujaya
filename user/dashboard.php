<?php
session_start();
require_once "../config/koneksi.php";

// =============================================================
// CEK LOGIN USER
// =============================================================
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['role'] != 'user') {
    header("location:../login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// =============================================================
// PROSES PESAN PRODUK
// =============================================================
if (isset($_POST['pesan'])) {
    $id_product = $_POST['id_product'];
    $qty = $_POST['qty'];

    $produk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM products WHERE id_product='$id_product'"));
    $subtotal = $produk['harga'] * $qty;
    $total = $subtotal;

    // Simpan order utama
    mysqli_query($koneksi, "INSERT INTO orders (id_user, tanggal_order, total_bayar, status_order) 
                            VALUES ('$id_user', NOW(), '$total', 'pending')");
    $id_order = mysqli_insert_id($koneksi);

    // Simpan detail order
    mysqli_query($koneksi, "INSERT INTO order_details (id_order, id_product, qty, subtotal) 
                            VALUES ('$id_order', '$id_product', '$qty', '$subtotal')");

    // Update stok produk
    $stok_baru = $produk['stok'] - $qty;
    mysqli_query($koneksi, "UPDATE products SET stok='$stok_baru' WHERE id_product='$id_product'");

    echo "<script>alert('Pesanan berhasil dibuat!'); window.location='orders.php';</script>";
    exit;
}

// =============================================================
// AMBIL DATA PRODUK
// =============================================================
$produk = mysqli_query($koneksi, "SELECT * FROM products WHERE stok > 0 ORDER BY id_product DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User - Bambu Jaya</title>
    <style>
        body { font-family: Arial; background: #f4f6f9; margin: 0; }
        .navbar { background: #4CAF50; color: white; padding: 15px 20px; display: flex; justify-content: space-between; }
        .navbar a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        .navbar a:hover { background: rgba(255,255,255,0.3); }
        .container { max-width: 1000px; margin: 30px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .produk { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: #fafafa; }
        .card h3 { margin-top: 0; }
        .harga { color: #4CAF50; font-weight: bold; }
        input { width: 50px; padding: 5px; text-align: center; }
        button { background: #4CAF50; color: white; border: none; padding: 6px 10px; border-radius: 5px; cursor: pointer; }
        button:hover { background: #45a049; }
    </style>
</head>
<body>
    <div class="navbar">
        <div><strong>Bambu Jaya</strong></div>
        <div>
            <a href="orders.php">Pesanan Saya</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Daftar Produk</h2>
        <div class="produk">
            <?php while ($p = mysqli_fetch_assoc($produk)) : ?>
                <div class="card">
                    <h3><?= htmlspecialchars($p['nama_product']); ?></h3>
                    <p><?= substr($p['deskripsi'], 0, 80) . "..."; ?></p>
                    <p class="harga">Rp <?= number_format($p['harga'], 0, ',', '.'); ?></p>
                    <p>Stok: <?= $p['stok']; ?></p>
                    <form method="POST">
                        <input type="hidden" name="id_product" value="<?= $p['id_product']; ?>">
                        <label>Qty:</label>
                        <input type="number" name="qty" min="1" max="<?= $p['stok']; ?>" value="1" required>
                        <br><br>
                        <button type="submit" name="pesan">Pesan Sekarang</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
