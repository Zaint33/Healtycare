<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proses_hubungkan'])) {
    $kode_input = strtoupper(trim($_POST['user_code']));
    
    // 1. Cari pasien dengan role 'pengguna' berdasarkan kode
    $stmt = $pdo->prepare("SELECT id, nama FROM users WHERE kode_unik = ? AND role = 'pengguna'");
    $stmt->execute([$kode_input]);
    $target_user = $stmt->fetch();

    if ($target_user) {
        $id_pengawas = $_SESSION['user_id'];
        $id_pasien = $target_user['id'];

        // 2. Cek apakah sudah terhubung
        $stmt_check = $pdo->prepare("SELECT id_koneksi FROM koneksi_pengawas WHERE id_pengawas = ? AND id_user = ?");
        $stmt_check->execute([$id_pengawas, $id_pasien]);
        
        if ($stmt_check->rowCount() == 0) {
            try {
                // 3. Masukkan ke tabel koneksi
                $stmt_connect = $pdo->prepare("INSERT INTO koneksi_pengawas (id_pengawas, id_user, status) VALUES (?, ?, 'connected')");
                $stmt_connect->execute([$id_pengawas, $id_pasien]);
                
                echo "<script>
                    alert('Berhasil terhubung dengan " . addslashes($target_user['nama']) . "!');
                    window.location.href='beranda_pengawas.php';
                </script>";
                exit;
            } catch (PDOException $e) {
                $error_msg = "Kesalahan Database: " . $e->getMessage();
            }
        } else {
            $error_msg = "Pasien ini sudah ada dalam daftar Anda.";
        }
    } else {
        $error_msg = "Kode tidak ditemukan atau pengguna bukan kategori pasien.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Koneksi User | HealtyCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { background: #00aaff; font-family: sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 20px; width: 350px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .icon { font-size: 50px; color: #00aaff; margin-bottom: 15px; }
        input { width: 100%; padding: 12px; margin: 15px 0; border: 1px solid #ddd; border-radius: 8px; text-align: center; font-weight: bold; }
        button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .error-box { background: #fee; color: #c00; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 13px; border: 1px solid #fcc; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon"><i class="fas fa-link"></i></div>
        <h3>Koneksi Dengan User</h3>
        <p style="font-size: 13px; color: #666;">Masukkan kode pantau pasien untuk mulai memantau.</p>

        <?php if ($error_msg): ?>
            <div class="error-box"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="user_code" placeholder="CONTOH: HC-XXXXXX" required>
            <button type="submit" name="proses_hubungkan">Hubungkan Sekarang</button>
        </form>
        <a href="beranda_pengawas.php" style="display:block; margin-top:15px; font-size:12px; color:#999; text-decoration:none;"> Kembali ke Dashboard</a>
    </div>
</body>
</html>