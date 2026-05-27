<?php
session_start();
include '../config/db.php'; // Pastikan path ke db.php sudah benar

// Jika sudah login, langsung lempar ke index admin
if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    header("location:index.php");
    exit;
}

if (isset($_POST['login'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);

    // Gunakan query yang sesuai dengan tabel database kamu
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$user' AND password='$pass'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['status'] = "login";
        $_SESSION['username'] = $data['username'];
        header("location:index.php");
        exit; // Selalu gunakan exit setelah header location
    } else {
        $error = "Username atau Password Salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - GSE POWER</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); width: 100%; max-width: 320px; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; font-weight: 800; }
        .password-container { position: relative; width: 100%; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 16px; transition: 0.3s; }
        input:focus { border-color: #007bff; outline: none; box-shadow: 0 0 5px rgba(0,123,255,0.2); }
        .toggle-password { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #888; z-index: 10; }
        button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 16px; transition: 0.3s; margin-top: 10px; }
        button:hover { background: #0056b3; transform: translateY(-2px); }
        .error { background: #fee2e2; color: #dc3545; padding: 10px; border-radius: 8px; text-align: center; font-size: 14px; margin-bottom: 15px; border: 1px solid #fecaca; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>GSE POWER</h2>
        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST" autocomplete="off">
            <input type="text" name="username" placeholder="Username" required>
            <div class="password-container">
                <input type="password" name="password" id="passwordField" placeholder="Password" required>
                <i class="fa-solid fa-eye toggle-password" id="eyeIcon"></i>
            </div>
            <button type="submit" name="login">MASUK KE PANEL</button>
        </form>
        <p style="text-align:center; margin-top:20px;">
            <a href="../index.php" style="color:#888; text-decoration:none; font-size:12px;">← Kembali ke Dashboard</a>
        </p>
    </div>

    <script>
        const passwordField = document.getElementById('passwordField');
        const eyeIcon = document.getElementById('eyeIcon');
        eyeIcon.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>