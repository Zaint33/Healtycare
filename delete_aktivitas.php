<?php
// delete_aktivitas.php
session_start();
require 'config.php'; 

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$id_aktivitas = $_GET['id'] ?? null;

if (empty($id_aktivitas) || !is_numeric($id_aktivitas)) {
    // Jika ID tidak valid, arahkan kembali
    header('Location: aktivitas.php?status=invalid_id');
    exit;
}

try {
    // Pastikan user hanya bisa menghapus jadwal miliknya sendiri
    $sql = "DELETE FROM aktivitas WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id' => $id_aktivitas,
        'user_id' => $user_id
    ]);

    // Redirect setelah penghapusan berhasil
    header('Location: aktivitas.php?status=deleted');
    exit;

} catch (PDOException $e) {
    // Log error, dan arahkan kembali dengan status error
    // error_log("Error deleting aktivitas: " . $e->getMessage()); 
    header('Location: aktivitas.php?status=error_db');
    exit;
}
?>