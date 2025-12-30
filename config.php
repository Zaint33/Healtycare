<?php
// config.php - Konfigurasi Database

// Fungsi Generate Kode (Dibungkus if agar tidak redeclare error)
if (!function_exists('generateUserCode')) {
    function generateUserCode($pdo) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        do {
            $code = 'HC-' . substr(str_shuffle($characters), 0, 6);
            // Cek apakah kode sudah ada di database
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE kode_unik = ?");
            $stmt->execute([$code]);
            $exists = $stmt->fetchColumn();
        } while ($exists > 0);
        return $code;
    }
}

// Set zona waktu PHP
date_default_timezone_set('Asia/Jakarta');

// Detail koneksi database
$host = 'localhost'; 
$db   = 'healthycare_db'; 
$user = 'root'; 
$pass = ''; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // Memastikan MySQL menggunakan zona waktu yang sama dengan PHP (+07:00 adalah WIB)
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+07:00'"
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     error_log("Database Error: " . $e->getMessage()); 
     die("Koneksi database gagal. Silakan cek pengaturan database Anda.");
}
?>