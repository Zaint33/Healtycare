<?php
// profile.php - Halaman Profil Pengguna
session_start();
require 'config.php'; // Fungsi generateUserCode() sudah dipanggil dari sini

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username_session = htmlspecialchars($_SESSION['username'] ?? 'User');
$pesan_status = '';

// --- 1. AMBIL DATA PROFILE SAAT INI ---
try {
    $stmt = $pdo->prepare("SELECT username, nama, gender, birthday, email, phone, ket, kode_unik FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_data) {
        header('Location: logout.php');
        exit;
    }

    // LOGIKA GENERATE KODE KHUSUS JIKA BELUM ADA
    if (empty($user_data['kode_unik'])) {
        $new_code = generateUserCode($pdo);
        $update_stmt = $pdo->prepare("UPDATE users SET kode_unik = ? WHERE id = ?");
        $update_stmt->execute([$new_code, $user_id]);
        $user_data['kode_unik'] = $new_code; // Update variabel lokal agar langsung tampil
    }

} catch (PDOException $e) {
    $pesan_status = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
    $user_data = [];
}

// --- 2. LOGIKA UPDATE DATA PROFILE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $birthday = trim($_POST['birthday'] ?? null); 
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $ket = trim($_POST['ket'] ?? '');

    if (empty($nama) || empty($email)) {
        $pesan_status = '<div class="alert error">Nama dan Email wajib diisi!</div>';
    } else {
        try {
            $sql = "UPDATE users SET nama = :nama, gender = :gender, birthday = :birthday, 
                    email = :email, phone = :phone, ket = :ket WHERE id = :user_id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nama' => $nama, 'gender' => $gender, 'birthday' => $birthday, 
                'email' => $email, 'phone' => $phone, 'ket' => $ket, 'user_id' => $user_id
            ]);

            $_SESSION['username'] = $nama; 
            $pesan_status = '<div class="alert success">Profil berhasil diperbarui!</div>';
            
            // Refresh data lokal agar input form langsung terisi data baru
            $user_data['nama'] = $nama;
            $user_data['gender'] = $gender;
            $user_data['birthday'] = $birthday;
            $user_data['email'] = $email;
            $user_data['phone'] = $phone;
            $user_data['ket'] = $ket;

        } catch (PDOException $e) {
            $pesan_status = '<div class="alert error">Gagal memperbarui profil: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna | HealtyCare</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        .unique-code-container {
            background: #f0f7ff;
            border: 2px dashed #007bff;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        .code-display { font-size: 24px; font-weight: 700; color: #007bff; letter-spacing: 2px; }
        .alert.success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .alert.error { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
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
                <a href="profile.php" class="active"><i class="fas fa-user"></i> <span>PROFILE</span></a> 
                <a href="aktivitas.php"><i class="fas fa-running"></i> <span>JADWAL AKTIFITAS</span></a>
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
                <h2 class="welcome-header">Edit Profil Pengguna</h2>

                <div class="profile-container">
                    <div class="profile-avatar-card">
                        <div class="profile-avatar">
                            <i class="fas fa-user-circle"></i> 
                        </div>
                        <p class="profile-welcome">SELAMAT DATANG</p>
                        <h3 class="profile-name-display"><?php echo htmlspecialchars($user_data['nama'] ?: $user_data['username']); ?></h3>
                        
                        <div class="unique-code-container">
                            <p style="margin:0; font-size: 12px; color: #666;">KODE PANTAU ANDA</p>
                            <div class="code-display"><?php echo htmlspecialchars($user_data['kode_unik']); ?></div>
                            <small>Berikan kode ini ke Pengawas Anda</small>
                        </div>
                    </div>

                    <div class="content-card profile-info-form">
                        <?php echo $pesan_status; ?>
                        <form action="profile.php" method="POST">
                            <div class="form-group">
                                <label for="nama">NAMA</label>
                                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user_data['nama']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>GENDER</label>
                                <div class="gender-options">
                                    <label>
                                        <input type="radio" name="gender" value="Laki Laki" 
                                            <?php echo ($user_data['gender'] == 'Laki Laki' ? 'checked' : ''); ?>>
                                        Laki Laki
                                    </label>
                                    <label>
                                        <input type="radio" name="gender" value="Perempuan" 
                                            <?php echo ($user_data['gender'] == 'Perempuan' ? 'checked' : ''); ?>>
                                        Perempuan
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="birthday">TANGGAL LAHIR</label>
                                <input type="date" id="birthday" name="birthday" value="<?php echo $user_data['birthday']; ?>">
                            </div>

                            <div class="form-group">
                                <label for="email">EMAIL</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="phone">PHONE</label>
                                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="ket">KETERANGAN</label>
                                <textarea id="ket" name="ket"><?php echo htmlspecialchars($user_data['ket'] ?? ''); ?></textarea>
                            </div>

                            <button type="submit" class="form-submit-button">
                                <i class="fas fa-check-circle"></i> SIMPAN PERUBAHAN
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>