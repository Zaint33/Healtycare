<?php
// tambah_aktivitas.php - UI Form Diseragamkan dengan Obat & Query DB Sudah Benar
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username'] ?? 'User');
$error = '';
$nama_kegiatan_val = '';
$waktu_tanggal_val = '';
$keterangan_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kegiatan = trim($_POST['nama_kegiatan'] ?? '');
    $waktu_tanggal = trim($_POST['waktu_tanggal'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    $nama_kegiatan_val = $nama_kegiatan;
    $waktu_tanggal_val = $waktu_tanggal;
    $keterangan_val = $keterangan;

    if (empty($nama_kegiatan) || empty($waktu_tanggal)) {
        $error = 'Nama kegiatan dan waktu wajib diisi.';
    } else {
        try {
            // Query sudah benar: Menggunakan kolom waktu_tanggal
            $sql = "INSERT INTO aktivitas (user_id, nama_kegiatan, waktu_tanggal, keterangan) 
                    VALUES (:user_id, :nama_kegiatan, :waktu_tanggal, :keterangan)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'user_id' => $user_id,
                'nama_kegiatan' => $nama_kegiatan,
                'waktu_tanggal' => $waktu_tanggal, 
                'keterangan' => $keterangan
            ]);

            header('Location: aktivitas.php?status=added');
            exit;

        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan database saat menyimpan data.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Jadwal Aktivitas | HealtyCare</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="header"><img src="logoo.png" alt="Logo" class="logo-img-sidebar">
        <h2>HealtyCare</h2></div>
            <nav>
                <a href="beranda.php"><i class="fas fa-home"></i> <span>BERANDA</span></a>
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
                <div class="content-card form-container">
                    <h2 class="welcome-header" style="text-align: center;">Tambah Jadwal Kegiatan</h2>
                    
                    <?php if (!empty($error)): ?>
                        <div style="text-align: center; margin-bottom: 20px;">
                            <div class="alert error" style="color: red; font-weight: bold;">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="tambah_aktivitas.php" method="POST">
                        
                        <div class="form-group">
                            <label for="nama_kegiatan">NAMA KEGIATAN</label>
                            <input type="text" id="nama_kegiatan" name="nama_kegiatan" value="<?php echo htmlspecialchars($nama_kegiatan_val); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="waktu_tanggal">WAKTU & TANGGAL KEGIATAN</label>
                            <input type="datetime-local" id="waktu_tanggal" name="waktu_tanggal" value="<?php echo htmlspecialchars($waktu_tanggal_val); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="keterangan">KETERANGAN (Opsional)</label>
                            <textarea id="keterangan" name="keterangan"><?php echo htmlspecialchars($keterangan_val); ?></textarea>
                        </div>

                        <button type="submit" class="form-submit-button" style="background-color: var(--color-activity);">
                            KONFIRMASI
                        </button>
                    </form>
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