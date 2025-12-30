<?php
// delete_checkup.php
session_start();
require 'config.php'; 

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$id_checkup = $_GET['id'] ?? null;

if (empty($id_checkup) || !is_numeric($id_checkup)) {
    // Jika ID tidak valid, arahkan kembali
    header('Location: checkup.php?status=invalid_id');
    exit;
}

try {
    // Pastikan user hanya bisa menghapus jadwal miliknya sendiri
    $sql = "DELETE FROM checkup WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id' => $id_checkup,
        'user_id' => $user_id
    ]);

    // Redirect setelah penghapusan berhasil
    header('Location: checkup.php?status=deleted');
    exit;

} catch (PDOException $e) {
    // Log error, dan arahkan kembali dengan status error
    // error_log("Error deleting checkup: " . $e->getMessage()); 
    header('Location: checkup.php?status=error_db');
    exit;
}
?>