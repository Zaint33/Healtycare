<?php
// aktivitas.php
session_start();
require 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username'] ?? 'User');
$pesan_status = ''; 

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'added') {
        $pesan_status = '<p class="alert success" style="color: #28a745; background: #e8f5e9; padding: 10px; border-radius: 5px; font-weight: bold; text-align: center; margin-bottom: 15px;">Jadwal aktivitas berhasil ditambahkan!</p>';
    } elseif ($_GET['status'] == 'deleted') {
        $pesan_status = '<p class="alert success" style="color: #dc3545; background: #ffebee; padding: 10px; border-radius: 5px; font-weight: bold; text-align: center; margin-bottom: 15px;">Jadwal aktivitas berhasil dihapus!</p>';
    }
}

$jadwal_aktivitas = [];

try {
    $stmt = $pdo->prepare("SELECT id, nama_kegiatan, waktu_tanggal, keterangan FROM aktivitas WHERE user_id = :user_id ORDER BY waktu_tanggal ASC");
    $stmt->execute(['user_id' => $user_id]);
    $jadwal_aktivitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pesan_status = '<p class="alert error" style="color: red; text-align: center; margin-bottom: 15px;">Error: ' . $e->getMessage() . '</p>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Aktivitas | HealtyCare</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-activity: #28a745;
            --color-white: #ffffff;
        }
        
        .schedule-entry-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background-color: var(--color-white);
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
            padding: 15px;
            transition: 0.3s;
            border: 1px solid #eee;
        }

        .schedule-entry-wrapper:hover {
            transform: scale(1.01);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .full-schedule-entry-link {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            flex-grow: 1;
        }

        .activity-type {
            background-color: var(--color-activity);
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-right: 15px;
        }

        .schedule-details h4 { margin: 0; color: #333; }
        .schedule-meta { font-size: 0.85rem; color: #666; margin-top: 5px; }
        .schedule-meta i { margin-right: 5px; color: #999; }

        .schedule-actions { margin-left: auto; }
        .delete-btn { color: #dc3545; font-size: 1.2rem; transition: 0.2s; }
        .delete-btn:hover { color: #a71d2a; }

        .add-button-container { margin-top: 25px; text-align: center; }
        .add-button {
            display: inline-block;
            padding: 12px 25px;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
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
                <a href="beranda.php"><i class="fas fa-home"></i> <span>BERANDA</span></a>
                <a href="profile.php"><i class="fas fa-user"></i> <span>PROFILE</span></a>
                <a href="aktivitas.php" class="active"><i class="fas fa-running"></i> <span>JADWAL AKTIFITAS</span></a>
                <a href="obat.php"><i class="fas fa-pills"></i> <span>JADWAL MINUM OBAT</span></a>
                <a href="checkup.php"><i class="fas fa-notes-medical"></i> <span>JADWAL CHECKUP</span></a>
            </nav>
        </div>

        <div class="main-content">
            <div class="top-bar">
                <span></span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a>
            </div>
            
            <div class="content-area">
                <div class="content-card">
                    <h2 class="welcome-header" style="color: var(--color-activity);">🏃 Daftar Jadwal Aktivitas</h2>
                    
                    <?php echo $pesan_status; ?>

                    <?php if (!empty($jadwal_aktivitas)): ?>
                        <div class="schedule-list-full">
                            <?php foreach ($jadwal_aktivitas as $aktivitas): 
                                $timestamp = strtotime($aktivitas['waktu_tanggal']);
                                $tanggal_formatted = date('d M Y', $timestamp);
                                $waktu_formatted = date('H:i', $timestamp);
                            ?>
                                <div class="schedule-entry-wrapper">
                                    <a href="detail_jadwal.php?tipe=aktivitas&id=<?php echo $aktivitas['id']; ?>" class="full-schedule-entry-link">
                                        <div class="activity-type">
                                            <i class="fas fa-running"></i>
                                        </div>
                                        <div class="schedule-details">
                                            <h4><?php echo htmlspecialchars($aktivitas['nama_kegiatan']); ?></h4>
                                            <div class="schedule-meta">
                                                <span><i class="fas fa-calendar-alt"></i> <?php echo $tanggal_formatted; ?></span>
                                                <span style="margin-left: 15px;"><i class="fas fa-clock"></i> Pukul <?php echo $waktu_formatted; ?></span>
                                            </div>
                                            <?php if (!empty($aktivitas['keterangan'])): ?>
                                                <p style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                                    <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($aktivitas['keterangan']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                    <div class="schedule-actions">
                                        <a href="delete_aktivitas.php?id=<?php echo $aktivitas['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Hapus jadwal ini?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px; color: #999;">
                            <i class="fas fa-running" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                            <p>Belum ada jadwal aktivitas. Mulai hidup sehat hari ini!</p>
                        </div>
                    <?php endif; ?>

                    <div class="add-button-container">
                        <a href="tambah_aktivitas.php" class="add-button" style="background-color: var(--color-activity);">
                            <i class="fas fa-plus"></i> Tambah Aktivitas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>