<?php
// delete_obat.php
session_start();
require 'config.php'; 

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$id_obat = $_GET['id'] ?? null;

if (empty($id_obat) || !is_numeric($id_obat)) {
    // Jika ID tidak valid, arahkan kembali
    header('Location: obat.php?status=invalid_id');
    exit;
}

try {
    // Pastikan user hanya bisa menghapus jadwal miliknya sendiri
    $sql = "DELETE FROM obat WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id' => $id_obat,
        'user_id' => $user_id
    ]);

    // Redirect setelah penghapusan berhasil
    header('Location: obat.php?status=deleted');
    exit;

} catch (PDOException $e) {
    // Log error, dan arahkan kembali dengan status error
    // error_log("Error deleting obat: " . $e->getMessage()); 
    header('Location: obat.php?status=error_db');
    exit;
}
?>