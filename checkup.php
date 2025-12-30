<?php
// checkup.php - UI diperbarui agar konsisten dengan obat.php
session_start();
require 'config.php'; // Koneksi database dan PDO

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username'] ?? 'User');
$pesan_status = ''; 

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'added') {
        $pesan_status = '<p class="alert success" style="color: green; font-weight: bold; text-align: center; margin-bottom: 15px;">Jadwal checkup berhasil ditambahkan!</p>';
    } elseif ($_GET['status'] == 'deleted') {
        $pesan_status = '<p class="alert success" style="color: red; font-weight: bold; text-align: center; margin-bottom: 15px;">Jadwal checkup berhasil dihapus!</p>';
    } elseif ($_GET['status'] == 'error') {
        $pesan_status = '<p class="alert error" style="color: red; font-weight: bold; text-align: center; margin-bottom: 15px;">Gagal memproses data. Coba lagi.</p>';
    }
}

$checkup_ditemukan = false;
$jadwal_checkup = [];

try {
    // Query BENAR: Menggunakan waktu_tanggal, nama_dokter, keterangan
    $stmt = $pdo->prepare("SELECT id, waktu_tanggal, nama_dokter, keterangan FROM checkup WHERE user_id = :user_id ORDER BY waktu_tanggal ASC");
    $stmt->execute(['user_id' => $user_id]);
    $jadwal_checkup = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($jadwal_checkup)) {
        $checkup_ditemukan = true;
    }

} catch (PDOException $e) {
    $pesan_status = '<p class="alert error" style="color: red; text-align: center; margin-bottom: 15px;">Error saat mengambil data checkup.</p>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Checkup | HealtyCare</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* CSS untuk membuat Link Detail Bekerja dengan Baik (Wajib disalin di setiap file daftar jadwal) */
        .schedule-entry-wrapper {
            position: relative;
            display: flex; 
            align-items: center;
            background-color: var(--color-white);
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
            padding: 15px;
            transition: box-shadow 0.2s;
        }

        .schedule-entry-wrapper:hover {
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .full-schedule-entry-link {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            flex-grow: 1; 
            cursor: pointer;
        }
        
        .schedule-actions {
            margin-left: auto;
            padding-left: 10px;
        }

        .action-btn {
            z-index: 10;
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
                <a href="aktivitas.php"><i class="fas fa-running"></i> <span>JADWAL AKTIFITAS</span></a>
                <a href="obat.php"><i class="fas fa-pills"></i> <span>JADWAL MINUM OBAT</span></a>
                <a href="checkup.php" class="active"><i class="fas fa-notes-medical"></i> <span>JADWAL CHECKUP</span></a>
            </nav>
        </div>

        <div class="main-content">
            <div class="top-bar">
                <span></span> <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a>
            </div>
            
            <div class="content-area">
                <div class="content-card">
                    <h2 class="welcome-header" style="color: var(--color-checkup);">🩺 Daftar Jadwal Checkup</h2>
                    
                    <?php if (!empty($pesan_status)): ?>
                        <?php echo $pesan_status; ?>
                    <?php endif; ?>
                    
                    <?php if ($checkup_ditemukan): ?>
                        <div class="schedule-list-full">
                            <?php foreach ($jadwal_checkup as $checkup): 
                                $timestamp = strtotime($checkup['waktu_tanggal']);
                                $tanggal_formatted = date('d M Y', $timestamp);
                                $waktu_formatted = date('H:i', $timestamp);
                            ?>
                            
                            <div class="schedule-entry-wrapper checkup-entry">
                                
                                <a href="detail_jadwal.php?tipe=checkup&id=<?php echo $checkup['id']; ?>" class="full-schedule-entry-link">
                                    <div class="full-schedule-entry" style="display: flex; align-items: center; flex-grow: 1;">
                                        
                                        <div class="schedule-icon-area checkup-type"> 
                                            <i class="fas fa-notes-medical"></i>
                                        </div>
                                        
                                        <div class="schedule-details">
                                            <h4 class="schedule-name"><?php echo htmlspecialchars($checkup['nama_dokter']); ?></h4>
                                            <div class="schedule-meta">
                                                <span><i class="fas fa-clock"></i> Pukul <?php echo $waktu_formatted; ?> (<?php echo $tanggal_formatted; ?>)</span>
                                            </div>
                                            <?php if (!empty($checkup['keterangan'])): ?>
                                                <p class="schedule-note-full">
                                                    <strong>Keterangan</strong>: <?php echo htmlspecialchars($checkup['keterangan']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a> <div class="schedule-actions">
                                    
                                    <a href="delete_checkup.php?id=<?php echo $checkup['id']; ?>" class="action-btn delete-btn" title="Hapus" onclick="return confirm('Hapus jadwal ini?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div> <?php endforeach; ?>

                        </div>
                    <?php else: ?>
                        <p class="no-schedule-message">
                            <i class="fas fa-info-circle"></i> Anda belum memiliki jadwal checkup yang terdaftar.
                        </p>
                    <?php endif; ?>

                    <div class="add-button-container">
                        <a href="tambah_checkup.php" class="add-button" style="background-color: var(--color-checkup);" title="Tambah Jadwal Checkup Baru">
                            <i class="fas fa-plus"></i> Tambah Checkup
                        </a>
                    </div>

                </div>
            </div>

            <div class="app-footer">
                <div class="footer-logo">
                    <img src="logoo.png" alt="Logo" class="logo-img-footer">
                    HealtyCare
                </div>
                <div class="social-icons">
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>