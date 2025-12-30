<?php
// obat.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config.php'; 

$username = htmlspecialchars($_SESSION['username'] ?? 'User');
$user_id = $_SESSION['user_id']; 
$pesan_status = ''; 

// Menampilkan pesan status jika ada
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'added') {
        $pesan_status = '<p class="alert success" style="color: green; font-weight: bold; text-align: center; margin-bottom: 15px;">Jadwal obat berhasil ditambahkan!</p>';
    } elseif ($_GET['status'] == 'deleted') {
        $pesan_status = '<p class="alert success" style="color: red; font-weight: bold; text-align: center; margin-bottom: 15px;">Jadwal obat berhasil dihapus!</p>';
    }
}

// LOGIKA AMBIL DATA DARI DATABASE: Menggunakan kolom 'waktu_minum'
try {
    $stmt = $pdo->prepare("SELECT id, nama_obat, waktu_minum, keterangan FROM obat WHERE user_id = :user_id ORDER BY waktu_minum ASC");
    $stmt->execute(['user_id' => $user_id]);
    $jadwal_obat = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $jadwal_obat = [];
    $pesan_status = '<p class="alert error" style="color: red; text-align: center; margin-bottom: 15px;">Error saat mengambil data obat.</p>';
}
// -------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Minum Obat | HealtyCare</title>
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
            <div class="header"><img src="logoo.png" alt="Logo" class="logo-img-sidebar">
        <h2>HealtyCare</h2> </div>
            <nav>
                <a href="beranda.php"><i class="fas fa-home"></i> <span>BERANDA</span></a>
                <a href="profile.php"><i class="fas fa-user"></i> <span>PROFILE</span></a>
                <a href="aktivitas.php"><i class="fas fa-running"></i> <span>JADWAL AKTIFITAS</span></a>
                <a href="obat.php" class="active"><i class="fas fa-pills"></i> <span>JADWAL MINUM OBAT</span></a>
                <a href="checkup.php"><i class="fas fa-notes-medical"></i> <span>JADWAL CHECKUP</span></a>
            </nav>
        </div>

        <div class="main-content">
            <div class="top-bar">
                <span></span> <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a>
            </div>
            
            <div class="content-area">
                <div class="content-card">
                    <h2 class="welcome-header" style="color: var(--color-medicine);">💊 Daftar Jadwal Minum Obat</h2>
                    
                    <?php if (!empty($pesan_status)): ?>
                        <?php echo $pesan_status; ?>
                    <?php endif; ?>

                    <?php if (!empty($jadwal_obat)): ?>
                        <div class="schedule-list-full">
                            
                            <?php foreach ($jadwal_obat as $obat): ?>
                            
                            <div class="schedule-entry-wrapper medicine-entry">
                                
                                <a href="detail_jadwal.php?tipe=obat&id=<?php echo $obat['id']; ?>" class="full-schedule-entry-link">

                                    <div class="full-schedule-entry" style="display: flex; align-items: center; flex-grow: 1;">
                                        <div class="schedule-icon-area obat-type">
                                            <i class="fas fa-pills"></i>
                                        </div>
                                        <div class="schedule-details">
                                            <h4 class="schedule-name"><?php echo htmlspecialchars($obat['nama_obat']); ?></h4>
                                            <div class="schedule-meta">
                                                <span><i class="fas fa-clock"></i> Pukul <?php echo date('H:i', strtotime($obat['waktu_minum'])); ?></span>
                                            </div>
                                            <?php if (!empty($obat['keterangan'])): ?>
                                                <p class="schedule-note-full">
                                                    <strong>Keterangan</strong>: <?php echo htmlspecialchars($obat['keterangan']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a> <div class="schedule-actions">
                                    
                                    <a href="delete_obat.php?id=<?php echo $obat['id']; ?>" class="action-btn delete-btn" title="Hapus" onclick="return confirm('Hapus jadwal ini?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div> <?php endforeach; ?>

                        </div>
                    <?php else: ?>
                        <p class="no-schedule-message">
                            <i class="fas fa-info-circle"></i> Anda belum memiliki jadwal minum obat yang terdaftar.
                        </p>
                    <?php endif; ?>

                    <div class="add-button-container">
                        <a href="tambah_obat.php" class="add-button" style="background-color: var(--color-medicine);" title="Tambah Jadwal Obat Baru">
                            <i class="fas fa-plus"></i> Tambah Obat
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