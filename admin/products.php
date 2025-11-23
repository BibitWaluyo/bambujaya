<?php
session_start();
require_once "../config/koneksi.php";

// =============================================================
// 1. CEK SESSION ADMIN
// =============================================================
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login' || $_SESSION['role'] != 'admin') {
    header("location:../login.php");
    exit();
}

// =============================================================
// 2. TAMBAH PRODUK
// =============================================================
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);

    mysqli_query($koneksi, "INSERT INTO products (nama_product, deskripsi, harga, stok) 
                            VALUES ('$nama', '$deskripsi', '$harga', '$stok')");
    header("Location: products.php");
    exit;
}

// =============================================================
// 3. HAPUS PRODUK
// =============================================================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM products WHERE id_product='$id'");
    header("Location: products.php");
    exit;
}

// =============================================================
// 4. EDIT PRODUK
// =============================================================
if (isset($_POST['edit'])) {
    $id = $_POST['id_product'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);

    mysqli_query($koneksi, "UPDATE products 
                            SET nama_product='$nama', deskripsi='$deskripsi', harga='$harga', stok='$stok' 
                            WHERE id_product='$id'");
    header("Location: products.php");
    exit;
}

// =============================================================
// 5. AMBIL DATA PRODUK
// =============================================================
$produk = mysqli_query($koneksi, "SELECT * FROM products ORDER BY id_product DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk - Bambu Jaya</title>
    <style>
        body { font-family: Arial; background-color: #f4f6f9; margin: 0; }
        .navbar { background-color: #4CAF50; color: white; padding: 15px 20px; display: flex; justify-content: space-between; }
        .navbar a { color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 6px; }
        .navbar a:hover { background: rgba(255,255,255,0.3); }
        .container { max-width: 900px; margin: 30px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; }
        th { background: #4CAF50; color: white; }
        .form-popup { background: #f9fff9; padding: 15px; border-radius: 8px; margin-top: 25px; }
        input, textarea { width: 100%; padding: 8px; margin: 6px 0; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #4CAF50; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; }
        .delete { background-color: red; }
    </style>
</head>
<body>
    <div class="navbar">
        <div><strong>Bambu Jaya Admin</strong></div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="orders.php">Pesanan</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Daftar Produk</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
            <?php while ($p = mysqli_fetch_assoc($produk)) : ?>
            <tr>
                <td><?= $p['id_product']; ?></td>
                <td><?= htmlspecialchars($p['nama_product']); ?></td>
                <td>Rp <?= number_format($p['harga'], 0, ',', '.'); ?></td>
                <td><?= $p['stok']; ?></td>
                <td><?= substr($p['deskripsi'], 0, 50) . "..."; ?></td>
                <td>
                    <form style="display:inline;" method="POST">
                        <input type="hidden" name="id_product" value="<?= $p['id_product']; ?>">
                        <button type="button" onclick="editProduk(<?= htmlspecialchars(json_encode($p)); ?>)">Edit</button>
                    </form>
                    <a href="?hapus=<?= $p['id_product']; ?>" onclick="return confirm('Yakin hapus produk ini?')">
                        <button class="delete">Hapus</button>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <div class="form-popup">
            <h3>Tambah Produk Baru</h3>
            <form method="POST">
                <input type="text" name="nama" placeholder="Nama Produk" required>
                <textarea name="deskripsi" placeholder="Deskripsi" required></textarea>
                <input type="number" name="harga" placeholder="Harga" required>
                <input type="number" name="stok" placeholder="Stok" required>
                <button type="submit" name="tambah">Tambah</button>
            </form>
        </div>

        <div id="editForm" class="form-popup" style="display:none;">
            <h3>Edit Produk</h3>
            <form method="POST">
                <input type="hidden" id="edit_id" name="id_product">
                <input type="text" id="edit_nama" name="nama" required>
                <textarea id="edit_deskripsi" name="deskripsi" required></textarea>
                <input type="number" id="edit_harga" name="harga" required>
                <input type="number" id="edit_stok" name="stok" required>
                <button type="submit" name="edit">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <script>
        function editProduk(data) {
            document.getElementById('editForm').style.display = 'block';
            document.getElementById('edit_id').value = data.id_product;
            document.getElementById('edit_nama').value = data.nama_product;
            document.getElementById('edit_deskripsi').value = data.deskripsi;
            document.getElementById('edit_harga').value = data.harga;
            document.getElementById('edit_stok').value = data.stok;
            window.scrollTo(0, document.body.scrollHeight);
        }
    </script>
</body>
</html>
