<?php
// app/controllers/process_batal_reservasi.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengguna') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservasi_id = $_POST['reservasi_id'];
    $user_id = $_SESSION['user_id']; // Keamanan ekstra: pastikan ini milik user yang sedang login

    try {
        // Update status menjadi dibatalkan HANYA jika status saat ini masih 'menunggu'
        $stmt = $pdo->prepare("UPDATE reservations SET status_reservasi = 'dibatalkan' WHERE id = :id AND user_id = :user_id AND status_reservasi = 'menunggu'");
        $stmt->execute([
            ':id' => $reservasi_id,
            ':user_id' => $user_id
        ]);

        header("Location: ../../views/user/dashboard.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Metode tidak diizinkan.";
}
?>