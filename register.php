<?php
session_start(); 
require 'config.php'; 

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $username = trim($_POST['username']);
    $role = $_POST['role']; // Menangkap role dari hidden input

    if (empty($email) || empty($password) || empty($username) || empty($role)) {
        $error = "Semua field harus diisi.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetchColumn() > 0) {
                $error = "Email sudah terdaftar.";
            } else {
                // Simpan ke database sesuai role yang dipilih
                $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$username, $email, $hashed_password, $role]);
                
                $success = "Registrasi berhasil sebagai " . ucfirst($role) . "! Silakan <a href='login.php'>Login</a>.";
                // Reset form
                $email = $username = '';
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan database: " . $e->getMessage(); 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register HealtyCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { display: flex; height: 100vh; margin: 0; font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .auth-container { display: flex; width: 100%; }
        .auth-sidebar { flex: 1; background-color: #1e74c5; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; }
        .auth-form-area { flex: 1.5; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; }
        .registration-form { width: 350px; text-align: center; }
        .input-group { position: relative; margin-bottom: 15px; }
        .input-group input { width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; background: #e8f0fe; }
        .input-group .icon { position: absolute; left: 15px; top: 15px; color: #1e74c5; }
        .btn-role { flex: 1; padding: 12px; border-radius: 8px; border: 2px solid #1e74c5; cursor: pointer; background: white; color: #1e74c5; font-weight: bold; transition: 0.3s; }
        .btn-primary { width: 100%; padding: 12px; background-color: #1e74c5; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 16px; }
        .active-role { background-color: #1e74c5 !important; color: white !important; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-sidebar">
            <img src="logoo.png" alt="Logo" width="120"> 
            <h2>HealtyCare</h2>
        </div>
        
        <div class="auth-form-area">
            <h1>REGISTER</h1>
            
            <?php if ($error): ?> <p style="color:red;"><?php echo $error; ?></p> <?php endif; ?>
            <?php if ($success): ?> <p style="color:green;"><?php echo $success; ?></p> <?php endif; ?>

            <form method="POST" class="registration-form">
                <div class="input-group">
                    <i class="fas fa-envelope icon"></i>
                    <input type="email" name="email" placeholder="Email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock icon"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-user icon"></i>
                    <input type="text" name="username" placeholder="Username" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                </div>

                <p style="text-align: left; font-size: 14px; color: #666; margin-bottom: 10px;">Daftar sebagai:</p>
                <div class="btn-role-group" style="display: flex; gap: 10px; margin-bottom: 20px;">
                    <button type="button" id="btn-pengawas" class="btn-role" onclick="setRole('pengawas', this)">Pengawas</button>
                    <button type="button" id="btn-pengguna" class="btn-role" onclick="setRole('pengguna', this)">Pengguna</button>
                    <input type="hidden" name="role" id="role_input" value="pengguna">
                </div>

                <button type="submit" class="btn-primary">Konfirmasi</button>
            </form>
            <p>Sudah Mempunyai Akun? <a href="login.php" style="color: #1e74c5; text-decoration: none;">Login</a></p>
        </div>
    </div>

    <script>
        function setRole(roleValue, clickedButton) {
            document.getElementById('role_input').value = roleValue;
            document.querySelectorAll('.btn-role').forEach(btn => btn.classList.remove('active-role'));
            clickedButton.classList.add('active-role');
        }
        // Set default role visual
        window.onload = function() {
            setRole('pengguna', document.getElementById('btn-pengguna'));
        };
    </script>
</body>
</html>