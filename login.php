<?php
session_start();
require_once "config/koneksi.php";

$error = "";

if (isset($_POST['login_btn'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = md5($_POST['password']); 
    
    $is_logged_in = false;
    $data = null;
    $role = null;

    // 1. COBA LOGIN SEBAGAI ADMIN (Tabel 'admin')
    $query_admin = "SELECT * FROM admin WHERE email_admin='$email' AND password_admin='$password'";
    $result_admin = mysqli_query($koneksi, $query_admin);

    if (!$result_admin) {
        // Jika query error, tampilkan pesan error database
        die("Query error (Tabel Admin): " . mysqli_error($koneksi)); 
    }

    if (mysqli_num_rows($result_admin) == 1) {
        $data = mysqli_fetch_assoc($result_admin);
        $role = 'admin';
        $is_logged_in = true;
    }

    // 2. COBA LOGIN SEBAGAI USER (Tabel 'users')
    if (!$is_logged_in) {
        $query_user = "SELECT * FROM users WHERE email_user='$email' AND password_user='$password'";
        $result_user = mysqli_query($koneksi, $query_user);
        
        if (!$result_user) {
            // Jika query error, tampilkan pesan error database
            die("Query error (Tabel User): " . mysqli_error($koneksi)); 
        }

        if (mysqli_num_rows($result_user) == 1) {
            $data = mysqli_fetch_assoc($result_user);
            $role = 'user';
            $is_logged_in = true;
        }
    }

    // 3. PROSES SESSION DAN REDIRECT
    if ($is_logged_in) {
        if ($role == 'admin') {
            // Data untuk Admin
            $_SESSION['id_user'] = $data['id_admin']; 
            $_SESSION['nama'] = $data['nama_admin'];
            $_SESSION['role'] = $role;
            $_SESSION['status'] = "login";
            header("Location: admin/dashboard.php");
        } else { // Role User
            // Data untuk User
            $_SESSION['id_user'] = $data['id_user'];
            $_SESSION['nama'] = $data['nama_user'];
            $_SESSION['role'] = $role;
            $_SESSION['status'] = "login";
            header("Location: user/dashboard.php");
        }
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login - Bambu Jaya</title>
<style>
body { font-family: Arial; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
.container { background: white; padding: 20px; border-radius: 10px; width: 300px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
h2 { text-align: center; color: #4CAF50; }
input, button { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; }
button { background: #4CAF50; color: white; border: none; cursor: pointer; border-radius: 5px; }
button:hover { background: #45a049; }
.error { color: red; text-align: center; }
.link-register { text-align: center; margin-top: 10px; font-size: 0.9em; }
</style>
</head>
<body>
<div class="container">
<h2>Login Bambu Jaya</h2>
<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login_btn">Login</button>
</form>
<?php if($error) echo "<p class='error'>$error</p>"; ?>
<div class="link-register">Belum punya akun? <a href="register.php">Daftar di sini</a></div>
</div>
</body>
</html>