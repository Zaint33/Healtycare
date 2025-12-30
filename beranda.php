<?php
// beranda.php - Dihubungkan ke Database (aktivitas, obat, checkup)
session_start();
require 'config.php'; // Pastikan koneksi database melalui config.php

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username'] ?? 'User'); 

$jadwal_ditemukan = false;
$daftar_jadwal = [];
$jumlah_aktivitas = 0;
$jumlah_obat = 0;
$jumlah_checkup = 0;

// LOGIKA NOTIFIKASI DARI PENGAWAS (DITAMBAHKAN)
$notif_pesan = null;
try {
    // Cek notifikasi terbaru yang belum dibaca
    $stmt_check_notif = $pdo->prepare("SELECT id_notif, pesan FROM notifikasi WHERE id_user = ? AND is_read = 0 ORDER BY id_notif DESC LIMIT 1");
    $stmt_check_notif->execute([$user_id]);
    $notif_data = $stmt_check_notif->fetch();

    if ($notif_data) {
        $notif_pesan = $notif_data['pesan'];
        // Update status is_read agar tidak muncul terus menerus setelah refresh
        $stmt_update_notif = $pdo->prepare("UPDATE notifikasi SET is_read = 1 WHERE id_notif = ?");
        $stmt_update_notif->execute([$notif_data['id_notif']]);
    }
} catch (PDOException $e) {
    // Diamkan jika tabel notifikasi belum ada
}

try {
    // 1. MENGAMBIL JUMLAH DATA UNTUK SUMMARY CARDS
    $stmt_count_aktivitas = $pdo->prepare("SELECT COUNT(*) FROM aktivitas WHERE user_id = ?");
    $stmt_count_aktivitas->execute([$user_id]);
    $jumlah_aktivitas = $stmt_count_aktivitas->fetchColumn();

    $stmt_count_obat = $pdo->prepare("SELECT COUNT(*) FROM obat WHERE user_id = ?");
    $stmt_count_obat->execute([$user_id]);
    $jumlah_obat = $stmt_count_obat->fetchColumn();

    $stmt_count_checkup = $pdo->prepare("SELECT COUNT(*) FROM checkup WHERE user_id = ?");
    $stmt_count_checkup->execute([$user_id]);
    $jumlah_checkup = $stmt_count_checkup->fetchColumn();

    // 2. MENGAMBIL DAFTAR JADWAL UNTUK TAMPILAN
    $stmt_aktivitas = $pdo->prepare("SELECT 'Aktivitas' AS tipe, nama_kegiatan AS nama, waktu_tanggal AS tanggal_waktu, keterangan FROM aktivitas WHERE user_id = :user_id");
    $stmt_aktivitas->execute(['user_id' => $user_id]);
    $activities = $stmt_aktivitas->fetchAll();
    
    $stmt_obat = $pdo->prepare("SELECT 'Obat' AS tipe, nama_obat AS nama, waktu_minum AS tanggal_waktu, keterangan FROM obat WHERE user_id = :user_id");
    $stmt_obat->execute(['user_id' => $user_id]);
    $medicines = $stmt_obat->fetchAll();
    
    $stmt_checkup = $pdo->prepare("SELECT 'Checkup' AS tipe, nama_dokter AS nama, waktu_tanggal AS tanggal_waktu, keterangan FROM checkup WHERE user_id = :user_id");
    $stmt_checkup->execute(['user_id' => $user_id]);
    $checkups = $stmt_checkup->fetchAll();

    $semua_jadwal = array_merge($activities, $medicines, $checkups);
    
    if (!empty($semua_jadwal)) {
        $sekarang = time();
        $semua_jadwal_mendatang = array_filter($semua_jadwal, function($jadwal) use ($sekarang) {
            $waktu_jadwal = strtotime($jadwal['tanggal_waktu']);
            if ($jadwal['tipe'] == 'Obat') {
                return date('Y-m-d', $waktu_jadwal) == date('Y-m-d') || $waktu_jadwal >= $sekarang;
            } else {
                return $waktu_jadwal >= $sekarang;
            }
        });
        
        if (!empty($semua_jadwal_mendatang)) {
            $jadwal_ditemukan = true;
            usort($semua_jadwal_mendatang, function($a, $b) {
                return strtotime($a['tanggal_waktu']) - strtotime($b['tanggal_waktu']);
            });
            $daftar_jadwal = array_slice($semua_jadwal_mendatang, 0, 5);
        }
    }
} catch (PDOException $e) {
    $jadwal_ditemukan = false;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beranda HealtyCare</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* CSS Modal Notifikasi */
        .notif-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.7); display: flex; align-items: center;
            justify-content: center; z-index: 10000;
        }
        .notif-box {
            background: white; padding: 30px; border-radius: 15px;
            text-align: center; max-width: 400px; width: 90%;
            border-top: 10px solid #1e74c5; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .notif-box i { font-size: 50px; color: #1e74c5; margin-bottom: 15px; }
        .btn-close-notif {
            background: #1e74c5; color: white; border: none;
            padding: 10px 25px; border-radius: 5px; cursor: pointer;
            margin-top: 20px; font-weight: bold;
        }

        /* CSS Summary Cards */
        .summary-cards { display: flex; gap: 20px; justify-content: space-between; margin-bottom: 30px; }
        .summary-item { flex: 1; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); transition: transform 0.2s; }
        .summary-item:hover { transform: translateY(-5px); }
        .summary-item h3 { margin: 10px 0 5px 0; font-weight: 600; color: #444; font-size: 1em; }
        .count-number { font-size: 2.5em; font-weight: 700; margin-top: 0; }
        .activity-card { border-top: 5px solid #28a745; }
        .activity-card .count-number { color: #28a745; }
        .medicine-card { border-top: 5px solid #17a2b8; }
        .medicine-card .count-number { color: #17a2b8; }
        .checkup-card { border-top: 5px solid #ffc107; }
        .checkup-card .count-number { color: #ffc107; }
        .icon-wrapper { font-size: 2em; margin-bottom: 10px; }
        .activity-card .icon-wrapper i { color: #28a745; }
        .medicine-card .icon-wrapper i { color: #17a2b8; }
        .checkup-card .icon-wrapper i { color: #ffc107; }

        /* Daftar Jadwal */
        .divider { border: 0; height: 1px; background-color: #eee; margin: 20px 0; }
        .upcoming-header { font-size: 1.5em; color: #333; margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
        .schedule-list { display: flex; flex-direction: column; gap: 15px; }
        .schedule-item-detail { display: flex; align-items: center; padding: 15px; background: #fff; border-radius: 8px; border: 1px solid #e0e0e0; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03); }
        .schedule-icon-area { width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5em; color: #fff; margin-right: 15px; }
        .aktivitas-type { background-color: #28a745; }
        .obat-type { background-color: #17a2b8; }
        .checkup-type { background-color: #ffc107; }
        .schedule-info { flex-grow: 1; }
        .schedule-name { margin: 0; font-weight: 600; color: #333; font-size: 1.1em; }
        .schedule-type-tag { font-size: 0.75em; font-weight: 700; padding: 2px 8px; border-radius: 4px; color: #fff; background-color: #999; display: inline-block; margin-bottom: 5px; }
        .schedule-note { font-size: 0.85em; color: #666; margin: 5px 0 0 0; }
        .schedule-datetime { text-align: right; font-size: 0.9em; color: #555; line-height: 1.4; }
        .schedule-datetime i { margin-right: 5px; color: #aaa; }
        .time-part { font-weight: 600; color: #333; }
        .no-schedule-message { text-align: center; padding: 30px; border: 1px dashed #ccc; border-radius: 8px; color: #777; background-color: #f9f9f9; }
    </style>
</head>
<body>

    <?php if ($notif_pesan): ?>
    <div class="notif-overlay" id="notifOverlay">
        <div class="notif-box">
            <i class="fas fa-bell"></i>
            <h2>Pesan Pengawas</h2>
            <p style="font-style: italic; font-size: 1.1em;">"<?php echo htmlspecialchars($notif_pesan); ?>"</p>
            <button class="btn-close-notif" onclick="document.getElementById('notifOverlay').style.display='none'">SAYA MENGERTI</button>
        </div>
    </div>
    <?php endif; ?>

    <div class="dashboard-container">
        <div class="sidebar">
            <div class="header">
                <img src="logoo.png" alt="Logo" class="logo-img-sidebar"> 
                <h2>HealtyCare</h2> 
            </div>
            <nav>
                <a href="beranda.php" class="active"><i class="fas fa-home"></i> <span>BERANDA</span></a>
                <a href="profile.php"><i class="fas fa-user"></i> <span>PROFILE</span></a>
                <a href="aktivitas.php"><i class="fas fa-running"></i> <span>JADWAL AKTIFITAS</span></a>
                <a href="obat.php"><i class="fas fa-pills"></i> <span>JADWAL MINUM OBAT</span></a>
                <a href="checkup.php"><i class="fas fa-notes-medical"></i> <span>JADWAL CHECKUP</span></a>
            </nav>
        </div>

        <div class="main-content">
            <div class="top-bar">
                <span>Selamat Datang, <?php echo $username; ?></span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a>
            </div>
            
            <div class="content-area">
                <div class="content-card">
                    <h2 class="welcome-header">Ringkasan Jadwal Anda</h2>

                    <div class="summary-cards">
                        <div class="summary-item activity-card">
                            <div class="icon-wrapper"><i class="fas fa-running"></i></div>
                            <h3>Jadwal Aktivitas</h3>
                            <p class="count-number"><?php echo $jumlah_aktivitas; ?></p>
                        </div>
                        <div class="summary-item medicine-card">
                            <div class="icon-wrapper"><i class="fas fa-pills"></i></div>
                            <h3>Jadwal Minum Obat</h3>
                            <p class="count-number"><?php echo $jumlah_obat; ?></p>
                        </div>
                        <div class="summary-item checkup-card">
                            <div class="icon-wrapper"><i class="fas fa-notes-medical"></i></div>
                            <h3>Jadwal Checkup</h3>
                            <p class="count-number"><?php echo $jumlah_checkup; ?></p>
                        </div>
                    </div>

                    <hr class="divider">
                    
                    <?php if ($jadwal_ditemukan): ?>
                        <h3 class="upcoming-header">Jadwal Mendatang Terdekat (Max 5)</h3>
                        <div class="schedule-list">
                            <?php foreach ($daftar_jadwal as $jadwal): ?>
                                <?php 
                                    $icon = ''; $class_tipe = ''; $link = '';
                                    if ($jadwal['tipe'] == 'Aktivitas') {
                                        $icon = 'fas fa-running'; $class_tipe = 'aktivitas-type'; $link = 'aktivitas.php';
                                    } elseif ($jadwal['tipe'] == 'Obat') {
                                        $icon = 'fas fa-pills'; $class_tipe = 'obat-type'; $link = 'obat.php';
                                    } elseif ($jadwal['tipe'] == 'Checkup') {
                                        $icon = 'fas fa-notes-medical'; $class_tipe = 'checkup-type'; $link = 'checkup.php';
                                    }
                                    $waktu_tampil = date('H:i', strtotime($jadwal['tanggal_waktu']));
                                    $tanggal_tampil = date('d M Y', strtotime($jadwal['tanggal_waktu']));
                                ?>
                                <a href="<?php echo $link; ?>" class="schedule-item-detail" style="text-decoration: none; color: inherit;">
                                    <div class="schedule-icon-area <?php echo $class_tipe; ?>">
                                        <i class="<?php echo $icon; ?>"></i>
                                    </div>
                                    <div class="schedule-info">
                                        <span class="schedule-type-tag"><?php echo htmlspecialchars($jadwal['tipe']); ?></span>
                                        <h4 class="schedule-name"><?php echo htmlspecialchars($jadwal['nama']); ?></h4>
                                        <?php if (!empty($jadwal['keterangan'])): ?>
                                            <p class="schedule-note"><i class="fas fa-info-circle"></i> Ket: <?php echo htmlspecialchars($jadwal['keterangan']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="schedule-datetime">
                                        <i class="fas fa-calendar-alt"></i> <?php echo $tanggal_tampil; ?><br>
                                        <span class="time-part">Pukul: <?php echo $waktu_tampil; ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-schedule-message">
                            <i class="fas fa-info-circle"></i> Anda belum memiliki jadwal terdaftar.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="app-footer">
                <div class="footer-logo"><img src="logoo.png" alt="Logo" class="logo-img-footer"> HealtyCare</div>
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