<?php
session_start();
require 'config.php';

// 1. Cek jika user sudah login
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'pengawas') {
        header('Location: beranda_pengawas.php');
    } else {
        header('Location: beranda.php');
    }
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        // Ambil user berdasarkan email saja dulu
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // 2. Verifikasi User dan Password
        if ($user) {
            // Jika Anda menggunakan password_hash() saat register, gunakan password_verify
            // Jika saat register Anda simpan password polos (tanpa hash), gunakan: if($password === $user['password'])
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                
                // Set Session
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                // Redirect sesuai role
                if ($user['role'] === 'pengawas') {
                    header('Location: beranda_pengawas.php');
                } else {
                    header('Location: beranda.php');
                }
                exit;
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Email tidak terdaftar!";
        }
    } catch (PDOException $e) {
        $error = "Kesalahan Database: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | HealtyCare</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; min-height: 100vh; background: #f0f2f5; }

        .left-side {
            flex: 1;
            background: #1e74c5;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
        }
        .left-side img { width: 160px; margin-bottom: 20px; }
        
        .right-side {
            flex: 1.2;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .login-box { width: 100%; max-width: 400px; text-align: center; }
        .login-box h2 { font-size: 32px; margin-bottom: 30px; color: #333; }

        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid #f5c6cb;
        }

        .input-group {
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            padding: 5px 15px;
            transition: 0.3s;
        }
        .input-group:focus-within { border-color: #1e74c5; box-shadow: 0 0 5px rgba(30,116,197,0.2); }
        .input-group i { color: #aaa; width: 25px; }
        .input-group input {
            width: 100%;
            border: none;
            padding: 15px 10px;
            outline: none;
            font-size: 15px;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: #1e74c5;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(30,116,197,0.3);
        }
        .btn-login:hover { background: #165a9b; }
        
        .register-link { margin-top: 25px; font-size: 14px; }
        .register-link a { color: #1e74c5; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

    <div class="left-side">
        <img src="logoo.png" alt="Logo">
        <h1>HealtyCare</h1>
    </div>

    <div class="right-side">
        <div class="login-box">
            <h2>LOGIN</h2>

            <?php if ($error): ?>
                <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Masukkan Email" required>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Masukkan Password" required>
                </div>

                <button type="submit" name="login" class="btn-login">Konfirmasi</button>
            </form>

            <p class="register-link">Belum Punya Akun? <a href="register.php">Register</a></p>
        </div>
    </div>

</body>
</html>