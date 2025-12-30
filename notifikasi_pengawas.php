<?php
session_start();
require 'config.php';

// Pastikan hanya pengawas yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengawas') {
    header('Location: login.php');
    exit;
}

$id_pengawas = $_SESSION['user_id'];

// Ambil notifikasi terbaru yang belum diproses untuk pengawas ini
$stmt = $pdo->prepare("SELECT n.*, u.username FROM notifikasi n 
                       JOIN users u ON n.id_user = u.id 
                       WHERE n.id_pengawas = ? AND n.is_read = 0 
                       ORDER BY n.waktu DESC LIMIT 1");
$stmt->execute([$id_pengawas]);
$notif = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Darurat | HealtyCare</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body { 
            background: linear-gradient(180deg, #1e90ff 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Navbar */
        .navbar {
            background: #1e90ff;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 20px; }
        .brand img { width: 40px; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .btn-logout { 
            background: #ff4d4d; 
            color: white; 
            padding: 5px 15px; 
            border-radius: 20px; 
            text-decoration: none; 
            font-weight: bold; 
            font-size: 14px;
            text-transform: uppercase;
        }

        /* Container Utama */
        .main-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .alert-card {
            background: #d1e9ff;
            width: 100%;
            max-width: 600px;
            border-radius: 30px;
            padding: 50px 30px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .alert-card h2 {
            color: #0d47a1;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .alert-card p {
            color: #1e74c5;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .illustration {
            width: 150px;
            margin: 20px auto;
            display: block;
        }

        .btn-konfirmasi {
            background: #0d47a1;
            color: white;
            padding: 12px 60px;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 20px;
            transition: 0.3s;
            text-transform: uppercase;
        }

        .btn-konfirmasi:hover {
            background: #1565c0;
            transform: translateY(-2px);
        }

        .no-notif { color: #666; font-style: italic; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="brand">
            <img src="logoo.png" alt="Logo"> 
            HealtyCare
        </div>
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="main-container">
        <div class="alert-card">
            <h2>KONTAK DARURAT</h2>

            <?php if ($notif): ?>
                <p>HARAP SEGERA SAMPAIKAN PADA<br>
                "<?php echo strtoupper(htmlspecialchars($notif['username'])); ?>"<br>
                UNTUK SELESAIKAN JADWAL</p>

                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User Illustration" class="illustration">

                <form method="POST" action="update_notif.php">
                    <input type="hidden" name="id_notif" value="<?php echo $notif['id_notif']; ?>">
                    <button type="submit" class="btn-konfirmasi">KONFIRMASI</button>
                </form>
            <?php else: ?>
                <p class="no-notif">Tidak ada jadwal darurat saat ini.</p>
                <button onclick="window.location.href='beranda_pengawas.php'" class="btn-konfirmasi">KEMBALI</button>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>