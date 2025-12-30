<?php
session_start();
require 'config.php';

// PROTEKSI: Jika bukan pengawas, tendang ke login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengawas') {
    header('Location: login.php');
    exit;
}

// 1. Ambil ID Pasien dari URL
$id_user = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_pengawas = $_SESSION['user_id']; 

// 2. Ambil data pasien
$stmt_user = $pdo->prepare("SELECT nama, username FROM users WHERE id = ? AND role = 'pengguna'");
$stmt_user->execute([$id_user]);
$user_info = $stmt_user->fetch();

if (!$user_info) {
    echo "<script>alert('Data pasien tidak ditemukan!'); window.location='beranda_pengawas.php';</script>";
    exit;
}

// 3. Logika Kirim Notifikasi
if (isset($_POST['kirim_notif'])) {
    $nama_tugas = $_POST['nama_tugas'];
    $msg = "Peringatan: Jadwal '$nama_tugas' belum diselesaikan!";
    $stmt_notif = $pdo->prepare("INSERT INTO notifikasi (id_user, id_pengawas, pesan, is_read) VALUES (?, ?, ?, 0)");
    $stmt_notif->execute([$id_user, $id_pengawas, $msg]);
    echo "<script>alert('Notifikasi terkirim ke " . htmlspecialchars($user_info['username']) . "');</script>";
}

// 4. Ambil Jadwal Gabungan
$sql = "SELECT 'Aktivitas' as tipe, nama_kegiatan as nama, waktu_tanggal as waktu, keterangan FROM aktivitas WHERE user_id = ?
        UNION
        SELECT 'Obat' as tipe, nama_obat as nama, waktu_minum as waktu, keterangan FROM obat WHERE user_id = ?
        UNION
        SELECT 'Checkup' as tipe, nama_dokter as nama, waktu_tanggal as waktu, keterangan FROM checkup WHERE user_id = ?
        ORDER BY waktu DESC";
$stmt_jadwal = $pdo->prepare($sql);
$stmt_jadwal->execute([$id_user, $id_user, $id_user]);
$semua_jadwal = $stmt_jadwal->fetchAll();

$waktu_sekarang = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recap Jadwal | HealtyCare</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        .schedule-entry-wrapper.past { border-left: 5px solid #e74c3c; }
        .schedule-entry-wrapper.future { border-left: 5px solid #3498db; }
        
        /* Tombol Lonceng Biru */
        .btn-notif-action {
            background: #1e74c5;
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
                <a href="beranda_pengawas.php"><i class="fas fa-home"></i> <span>BERANDA</span></a>
                                <a href="koneksi.php"><i class="fas fa-link"></i> <span>HUBUNGKAN PASIEN</span></a>
            </nav>
        </div>

        <div class="main-content">
            <div class="top-bar">
                <div class="user-welcome">
                    <i class="fas fa-user-shield"></i> Pengawas: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                </div>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a>
            </div>
            
            <div class="content-area">
                <div class="content-card">
                    <div class="recap-header">
                        <h2 class="welcome-header"><i class="fas fa-clipboard-list"></i> Recap Jadwal: <?php echo htmlspecialchars($user_info['nama'] ?? $user_info['username']); ?></h2>
                        <p style="color: #666;">Memantau riwayat aktivitas, obat, dan checkup pasien.</p>
                    </div>

                    <?php if (empty($semua_jadwal)): ?>
                        <div class="no-schedule-message">
                            <i class="fas fa-info-circle"></i> Tidak ada riwayat jadwal untuk pasien ini.
                        </div>
                    <?php else: ?>
                        <div class="schedule-list-full">
                            <?php foreach ($semua_jadwal as $j): 
                                $is_past = ($j['waktu'] < $waktu_sekarang);
                                $status_class = $is_past ? 'past' : 'future';
                                
                                $icon = "fa-calendar-check";
                                if ($j['tipe'] == 'Obat') $icon = "fa-pills";
                                if ($j['tipe'] == 'Checkup') $icon = "fa-notes-medical";
                                if ($j['tipe'] == 'Aktivitas') $icon = "fa-running";
                            ?>
                            
                            <div class="schedule-entry-wrapper <?php echo $status_class; ?>" style="display: flex; justify-content: space-between; align-items: center; padding: 15px; margin-bottom: 10px; background: #fff; border-radius: 8px;">
                                
                                <div style="display: flex; align-items: center;">
                                    <div class="schedule-icon-area" style="background-color: <?php echo $is_past ? '#fdecea' : '#ebf5ff'; ?>; color: <?php echo $is_past ? '#e74c3c' : '#3498db'; ?>; width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="fas <?php echo $icon; ?> fa-lg"></i>
                                    </div>
                                    <div class="schedule-details">
                                        <h4 style="margin: 0; font-size: 16px;">
                                            <?php echo htmlspecialchars($j['nama']); ?> 
                                            <small style="font-size: 11px; background: #eee; padding: 2px 8px; border-radius: 10px; margin-left: 5px; font-weight: normal;"><?php echo $j['tipe']; ?></small>
                                        </h4>
                                        <div style="font-size: 13px; color: #666; margin-top: 5px;">
                                            <span><i class="fas fa-clock"></i> <?php echo date('d M Y, H:i', strtotime($j['waktu'])); ?></span>
                                            <?php if($is_past): ?>
                                                <span style="color: #e74c3c; font-weight: bold; margin-left: 10px;">(Terlewati)</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($j['keterangan'])): ?>
                                            <p style="margin: 5px 0 0 0; font-size: 13px;"><strong>Ket:</strong> <?php echo htmlspecialchars($j['keterangan']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="schedule-actions" style="margin-left: 15px;">
                                    <form method="POST" onsubmit="return confirm('Kirim pengingat ke pasien?');">
                                        <input type="hidden" name="nama_tugas" value="<?php echo htmlspecialchars($j['nama']); ?>">
                                        <button type="submit" name="kirim_notif" class="btn-notif-action">
                                            <i class="fas fa-bell"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div style="margin-top: 30px;">
                        <a href="beranda_pengawas.php" style="text-decoration: none; padding: 10px 20px; background: #666; color: white; border-radius: 5px; font-size: 14px;">
                            <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>