<?php
// detail_jadwal.php (Terhubung Database)
session_start();

// Ganti path ini jika lokasi config.php Anda berbeda
require 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username'] ?? 'User');

// --- PENGAMBILAN PARAMETER DARI URL ---
$tipe = $_GET['tipe'] ?? ''; // 'aktivitas', 'obat', atau 'checkup'
$id = $_GET['id'] ?? 0;
$judul_halaman = 'Detail Jadwal';
$data_detail = null;
$error_message = '';
$table_name = '';
$column_name = '';

// --- LOGIKA PENENTUAN DATA & QUERY DATABASE ---
switch ($tipe) {
    case 'aktivitas':
        $table_name = 'aktivitas';
        $column_name = 'nama_kegiatan';
        $judul_halaman = 'Detail Aktivitas';
        $icon_class = 'fas fa-running';
        $label_utama = 'Nama Kegiatan';
        $color_var = "--color-activity";
        break;
    
    case 'obat':
        $table_name = 'obat';
        $column_name = 'nama_obat';
        $judul_halaman = 'Detail Minum Obat';
        $icon_class = 'fas fa-pills';
        $label_utama = 'Nama Obat';
        $color_var = "--color-medicine";
        break;

    case 'checkup':
        $table_name = 'checkup';
        // Asumsi kolom di tabel checkup adalah nama_dokter (seperti pada simulasi)
        $column_name = 'nama_dokter'; 
        $judul_halaman = 'Detail Checkup';
        $icon_class = 'fas fa-stethoscope';
        $label_utama = 'Nama Dokter';
        $color_var = "--color-checkup";
        break;

    default:
        // Tipe tidak valid, arahkan kembali ke beranda
        header('Location: beranda.php');
        exit;
}

// --- FUNGSI AMBIL DATA DARI DATABASE (PDO) ---
try {
    if ($id > 0) {
        // Query untuk mengambil data detail berdasarkan ID dan User ID
        $sql = "SELECT * FROM {$table_name} WHERE id = :id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id, 'user_id' => $user_id]);
        $data_detail = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data_detail) {
            $error_message = 'Data jadwal tidak ditemukan atau Anda tidak memiliki akses.';
        }
    } else {
        $error_message = 'ID jadwal tidak valid.';
    }

} catch (PDOException $e) {
    $error_message = 'Terjadi kesalahan database: ' . $e->getMessage();
}

// Fungsi untuk memformat tanggal (agar terlihat cantik)
function format_tanggal_indonesia($datetime_string) {
    if (empty($datetime_string) || $datetime_string === '0000-00-00 00:00:00') return '-';
    // Hanya ambil bagian tanggal jika kolom adalah datetime-local (YYYY-MM-DD HH:MM)
    $timestamp = strtotime(explode(' ', $datetime_string)[0]);
    if (!$timestamp) return '-';
    setlocale(LC_TIME, 'id_ID.UTF-8');
    return strftime('%d %B %Y', $timestamp);
}

// Fungsi untuk memformat waktu
function format_waktu($datetime_string) {
    if (empty($datetime_string)) return '-';
    // Jika kolom adalah datetime-local, ambil bagian waktu
    $parts = explode(' ', $datetime_string);
    if (count($parts) > 1) {
        return date('H:i', strtotime($parts[1]));
    }
    // Jika kolom hanya menyimpan waktu (HH:MM)
    return date('H:i', strtotime($datetime_string)); 
}

// Tentukan kolom tanggal dan waktu berdasarkan tipe jadwal
$kolom_tanggal = ($tipe === 'obat') ? null : 'waktu_tanggal';
$kolom_waktu = ($tipe === 'obat') ? 'waktu' : 'waktu_tanggal'; // Di tabel aktivitas/checkup, waktu diambil dari kolom waktu_tanggal
$kolom_nama = ($tipe === 'checkup') ? 'nama_dokter' : $column_name;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo $judul_halaman; ?> | HealtyCare</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Gaya spesifik untuk kotak detail (Diperbarui) */
        .detail-card {
            background-color: var(--color-white);
            padding: 45px;
            border-radius: 20px; /* Lebih besar */
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1); /* Bayangan lebih halus */
            max-width: 750px;
            margin: 30px auto;
            border-left: 8px solid var(<?php echo $color_var; ?>); /* Garis samping lebih tebal */
            position: relative;
        }

        .detail-card-icon {
            position: absolute;
            top: -20px;
            right: 40px;
            font-size: 45px;
            color: var(<?php echo $color_var; ?>);
            background-color: var(--color-light);
            padding: 10px;
            border-radius: 50%;
        }

        .detail-header {
            font-size: 32px;
            font-weight: 700;
            color: var(--color-dark);
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ddd; /* Garis putus-putus */
        }
        .detail-item {
            margin-bottom: 20px;
            font-size: 17px;
            color: var(--color-text);
            display: flex; /* Menggunakan flexbox untuk alignment */
            align-items: center;
        }
        .detail-label {
            display: inline-block;
            width: 160px; /* Lebar tetap untuk label */
            font-weight: 600;
            color: var(--color-dark);
        }
        .detail-label i {
            margin-right: 10px;
            color: var(--color-primary);
        }
        .detail-value {
            font-weight: 500;
            flex-grow: 1;
        }
        .detail-keterangan {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa; /* Latar belakang keterangan lebih terang */
            border-radius: 10px;
            border-left: 5px solid var(<?php echo $color_var; ?>);
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.05);
        }
        .detail-keterangan strong {
            display: block;
            margin-bottom: 8px;
            font-size: 1em;
            color: var(--color-dark);
        }
        .action-buttons {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            justify-content: flex-end; /* Rata kanan */
        }
        .edit-btn, .delete-btn, .back-button {
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .edit-btn {
            background-color: var(--color-primary);
            color: var(--color-white);
        }
        .edit-btn:hover {
            background-color: #145a96;
        }
        .delete-btn {
            background-color: var(--color-red);
            color: var(--color-white);
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        .back-button {
            background-color: #95a5a6;
            color: var(--color-white);
            margin-right: auto; /* Agar mepet kiri */
        }
        .back-button:hover {
            background-color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="header">
                 <img src="logoo.png" alt="Logo" class="logo-img-sidebar"> 
            </div>
            <nav>
                <a href="beranda.php"><i class="fas fa-home"></i> <span>BERANDA</span></a>
                <a href="profile.php"><i class="fas fa-user"></i> <span>PROFILE</span></a> 
                <a href="aktivitas.php" class="<?php echo ($tipe == 'aktivitas' ? 'active' : ''); ?>"><i class="fas fa-running"></i> <span>JADWAL AKTIFITAS</span></a>
                <a href="obat.php" class="<?php echo ($tipe == 'obat' ? 'active' : ''); ?>"><i class="fas fa-pills"></i> <span>JADWAL MINUM OBAT</span></a>
                <a href="checkup.php" class="<?php echo ($tipe == 'checkup' ? 'active' : ''); ?>"><i class="fas fa-notes-medical"></i> <span>JADWAL CHECKUP</span></a>
            </nav>
        </div>

        <div class="main-content">
            <div class="top-bar">
                <span></span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a>
            </div>
            
            <div class="content-area">
                <h1 class="welcome-header" style="text-align: center;"><i class="<?php echo $icon_class; ?>"></i> <?php echo $judul_halaman; ?></h1>

                <?php if ($error_message): ?>
                    <div class="detail-card">
                        <div class="alert error" style="margin: 0;"><?php echo $error_message; ?></div>
                        <div class="action-buttons" style="justify-content: flex-start;">
                             <a href="<?php echo $tipe; ?>.php" class="back-button">
                                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Jadwal
                            </a>
                        </div>
                    </div>
                <?php elseif ($data_detail): ?>

                    <div class="detail-card">
                        <i class="<?php echo $icon_class; ?> detail-card-icon"></i>
                        <div class="detail-header"><?php echo htmlspecialchars($data_detail[$kolom_nama] ?? 'Nama Jadwal'); ?></div>

                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-bookmark"></i> <?php echo $label_utama; ?>:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($data_detail[$kolom_nama] ?? '-'); ?></span>
                        </div>

                        <?php if ($kolom_tanggal && isset($data_detail[$kolom_tanggal])): ?>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-calendar-alt"></i> Tanggal:</span>
                            <span class="detail-value"><?php echo format_tanggal_indonesia($data_detail[$kolom_tanggal]); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($data_detail[$kolom_waktu])): ?>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-clock"></i> Waktu:</span>
                            <span class="detail-value"><?php echo format_waktu($data_detail[$kolom_waktu]); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="detail-keterangan">
                            <strong><i class="fas fa-align-left"></i> Keterangan Tambahan:</strong>
                            <p><?php echo nl2br(htmlspecialchars($data_detail['keterangan'] ?? 'Tidak ada keterangan.')); ?></p>
                        </div>

                        <div class="action-buttons">
                            <a href="<?php echo $tipe; ?>.php" class="back-button">
                                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                            </a>
                            <a href="edit_<?php echo $tipe; ?>.php?id=<?php echo $id; ?>" class="edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="proses_hapus.php?tipe=<?php echo $tipe; ?>&id=<?php echo $id; ?>" class="delete-btn" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </a>
                        </div>
                    </div>

                <?php endif; ?>
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