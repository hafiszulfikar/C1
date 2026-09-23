<?php
// app/controllers/process_verify_user.php
session_start();

// Pastikan hanya admin yang bisa melakukan eksekusi skrip ini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once '../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $action = $_POST['action']; // Berisi 'verify' atau 'reject'

    // Tentukan status baru berdasarkan tombol yang ditekan
    $new_status = ($action === 'verify') ? 'verified' : 'rejected';

    try {
        // Update status_akun di database
        $stmt = $pdo->prepare("UPDATE users SET status_akun = :status WHERE id = :id");
        $stmt->execute([
            ':status' => $new_status,
            ':id' => $user_id
        ]);

        // Kembalikan admin ke halaman dashboard setelah proses selesai
        header("Location: ../../views/admin/dashboard.php");
        exit();
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Akses ditolak.";
}
?>