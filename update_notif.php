<?php
// update_notif.php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_notif'])) {
    $id_notif = $_POST['id_notif'];
    
    // Set is_read menjadi 1 agar tidak muncul di modal lagi
    $stmt = $pdo->prepare("UPDATE notifikasi SET is_read = 1 WHERE id_notif = ?");
    $stmt->execute([$id_notif]);
}

// Kembalikan ke beranda pengawas
header('Location: beranda_pengawas.php');
exit;