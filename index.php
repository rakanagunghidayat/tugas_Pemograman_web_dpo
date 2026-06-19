<?php
session_start();
include "koneksi.php";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['login'] = true;
        $_SESSION['id_user'] = $data['id'];
        $_SESSION['nama_petugas'] = $data['nama_petugas'];
        $_SESSION['pangkat'] = $data['pangkat'];
        header("location: dashboard.php");
        exit;
    } else {
        $error = "Username atau Password salah, Komandan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Data DPO Kepolisian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: white; 
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card bg-dark text-white p-4 shadow border-secondary">
                <div class="text-center mb-4">
                    <span>🚨</span>
                    <h3 class="fw-bold mt-2 text-uppercase">DATA DPO KEPOLISIAN</h3>
                </div>
                
                <?php if (isset($error)) : ?>
                    <div class="alert alert-danger p-2 small text-center"><?= $error; ?></div>
                <?php endif; ?>
                
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-warning w-100 fw-bold">MASUK SISTEM</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>