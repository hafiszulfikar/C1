<?php
// app/controllers/process_batal_mendesak.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservasi_id = $_POST['reservasi_id'];
    $alasan = trim($_POST['alasan']);

    if (empty($alasan)) {
        die("Alasan pembatalan wajib diisi.");
    }

    try {
        $stmt = $pdo->prepare("UPDATE reservations SET status_reservasi = 'dibatalkan', alasan_pembatalan = :alasan WHERE id = :id");
        $stmt->execute([
            ':alasan' => $alasan,
            ':id' => $reservasi_id
        ]);

        header("Location: ../../views/officer/dashboard.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Metode tidak diizinkan.";
}
?>