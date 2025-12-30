<?php
// tambah_obat.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config.php'; 

$username = htmlspecialchars($_SESSION['username'] ?? 'User');
$user_id = $_SESSION['user_id'];
$pesan_status = ''; 
$nama_obat = '';
$waktu_minum = '';
$keterangan = '';

// LOGIKA PEMROSESAN FORMULIR (INSERT DATA)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_obat = trim($_POST['nama_obat']);
    $waktu_minum = trim($_POST['waktu_minum']);
    $keterangan = trim($_POST['keterangan']);

    if (empty($nama_obat) || empty($waktu_minum)) {
        $pesan_status = '<div class="alert error" style="color: red; font-weight: bold;">Nama Obat dan Waktu Minum wajib diisi!</div>';
    } else {
        try {
            // SQL menggunakan nama kolom yang benar: waktu_minum
            $sql = "INSERT INTO obat (user_id, nama_obat, waktu_minum, keterangan) 
                    VALUES (:user_id, :nama_obat, :waktu_minum, :keterangan)";
            
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                'user_id' => $user_id,
                'nama_obat' => $nama_obat,
                'waktu_minum' => $waktu_minum, 
                'keterangan' => $keterangan
            ]);

            header('Location: obat.php?status=added');
            exit;

        } catch (PDOException $e) {
            $pesan_status = '<div class="alert error" style="color: red; font-weight: bold;">Terjadi kesalahan database saat menyimpan: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Jadwal Minum Obat | HealtyCare</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
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
                <a href="aktivitas.php"><i class="fas fa-running"></i> <span>JADWAL AKTIFITAS</span></a>
                <a href="obat.php" class="active"><i class="fas fa-pills"></i> <span>JADWAL MINUM OBAT</span></a>
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
                    <h2 class="welcome-header" style="text-align: center;">Tambah Jadwal Minum Obat</h2>

                    <?php if (!empty($pesan_status)): ?>
                        <div style="text-align: center; margin-bottom: 20px;">
                            <?php echo $pesan_status; ?>
                        </div>
                    <?php endif; ?>

                    <form action="tambah_obat.php" method="POST">
                        
                        <div class="form-group">
                            <label for="nama_obat">NAMA OBAT</label>
                            <input type="text" id="nama_obat" name="nama_obat" value="<?php echo htmlspecialchars($nama_obat); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="waktu_minum">WAKTU MINUM OBAT (HH:MM)</label>
                            <input type="time" id="waktu_minum" name="waktu_minum" value="<?php echo htmlspecialchars($waktu_minum); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="keterangan">KETERANGAN (Opsional)</label>
                            <textarea id="keterangan" name="keterangan"><?php echo htmlspecialchars($keterangan); ?></textarea>
                        </div>

                        <button type="submit" class="form-submit-button" style="background-color: var(--color-medicine);">
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