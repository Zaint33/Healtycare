<?php
// beranda_pengawas.php
session_start();
require 'config.php';

// Proteksi Halaman
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengawas') {
    header('Location: login.php');
    exit;
}   

$username_display = htmlspecialchars($_SESSION['username'] ?? 'Pengawas');
$user_id = $_SESSION['user_id'];

// Ambil daftar pasien yang terhubung
$stmt = $pdo->prepare("SELECT u.id, u.username, u.email FROM users u 
                       JOIN koneksi_pengawas kp ON u.id = kp.id_user 
                       WHERE kp.id_pengawas = ? AND kp.status = 'connected'");
$stmt->execute([$user_id]);
$daftar_pasien = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Pengawas | HealtyCare</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Tambahan CSS khusus untuk menyesuaikan Grid Pasien agar serasi dengan Dashboard Pengguna */
        .patient-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .card-patient {
            background: #fff;
            border-radius: 15px;
            padding: 30px 20px;
            text-align: center;
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .card-patient:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .patient-avatar-box {
            width: 80px;
            height: 80px;
            background-color: #f0f7ff;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e74c5;
            font-size: 35px;
        }

        .card-patient h3 {
            font-size: 1.1em;
            color: #333;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        .card-patient h3 strong {
            color: #1e74c5;
            display: block;
            margin-top: 5px;
        }

        .btn-confirm {
            background-color: #1e74c5;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }

        .btn-confirm:hover {
            background-color: #165a9b;
        }

        .empty-state {
            text-align: center;
            padding: 50px;
            background: #f9f9f9;
            border-radius: 15px;
            border: 2px dashed #ddd;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <div class="sidebar">
            <div class="header">
                <img src="logoo.png" alt="Logo" class="logo-img-sidebar"> 
                <h2>HealtyCare</h2> 
            </div>
            <nav>
                <a href="beranda_pengawas.php" class="active"><i class="fas fa-home"></i> <span>BERANDA</span></a>
                
                <a href="koneksi.php"><i class="fas fa-link"></i> <span>HUBUNGKAN USER</span></a>
            </nav>
        </div>

        <div class="main-content">
            <div class="top-bar">
                <span>Selamat Datang, <?php echo $username_display; ?> (Pengawas)</span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a>
            </div>
            
            <div class="content-area">
                <div class="content-card">
                    <h2 class="welcome-header">Ringkasan User Anda</h2>
                    <p style="color: #666; margin-bottom: 30px;">Daftar User yang berada di bawah pengawasan Anda.</p>

                    <hr class="divider">

                    <?php if (count($daftar_pasien) > 0): ?>
                        <div class="patient-grid">
                            <?php foreach ($daftar_pasien as $pasien): ?>
                                <div class="card-patient">
                                    <div class="patient-avatar-box">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <h3>
                                        MASUK PENGAWAS<br>
                                        <strong>"<?php echo strtoupper(htmlspecialchars($pasien['username'])); ?>"</strong>
                                    </h3>
                                    <a href="recap_user.php?id=<?php echo $pasien['id']; ?>" class="btn-confirm">
    <i class="fas fa-eye"></i> Recap Jadwal
</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-user-slash" style="font-size: 50px; margin-bottom: 15px; opacity: 0.5;"></i>
                            <h3>Belum Ada Koneksi Pasien</h3>
                            <p>Hubungkan akun Anda dengan pasien untuk mulai memantau jadwal mereka.</p>
                            <br>
                            <a href="koneksi.php" class="btn-confirm">Hubungkan Sekarang</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="app-footer">
                <div class="footer-logo">
                    <img src="logoo.png" alt="Logo" class="logo-img-footer"> HealtyCare
                </div>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>