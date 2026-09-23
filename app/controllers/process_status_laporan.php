<?php
// app/controllers/process_status_laporan.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $laporan_id = $_POST['laporan_id'];
    $facility_id = $_POST['facility_id'];
    $status_baru = $_POST['status_baru'];
    $catatan_resolusi = trim($_POST['catatan_resolusi']);
    $tandai_perbaikan = isset($_POST['tandai_perbaikan']) ? true : false;

    if (empty($catatan_resolusi)) {
        die("Catatan resolusi wajib diisi.");
    }

    try {
        // Mulai transaksi database agar kedua tabel ter-update secara bersamaan (konsisten)
        $pdo->beginTransaction();

        // 1. Update status laporan dan catatan
        $stmt_rep = $pdo->prepare("UPDATE reports SET status_laporan = :status, catatan_resolusi = :catatan WHERE id = :id");
        $stmt_rep->execute([
            ':status' => $status_baru,
            ':catatan' => $catatan_resolusi,
            ':id' => $laporan_id
        ]);

        // 2. Logika perubahan status fasilitas
        if ($status_baru === 'diproses' && $tandai_perbaikan) {
            // Ubah fasilitas jadi dalam perbaikan
            $stmt_fac = $pdo->prepare("UPDATE facilities SET status_fasilitas = 'dalam perbaikan' WHERE id = :id");
            $stmt_fac->execute([':id' => $facility_id]);
            
        } elseif ($status_baru === 'selesai' || $status_baru === 'ditolak') {
            // Kembalikan fasilitas ke status aktif setelah laporan selesai/ditolak
            $stmt_fac = $pdo->prepare("UPDATE facilities SET status_fasilitas = 'aktif' WHERE id = :id");
            $stmt_fac->execute([':id' => $facility_id]);
        }

        // Commit transaksi
        $pdo->commit();

        header("Location: ../../views/officer/dashboard.php");
        exit();
        
    } catch (PDOException $e) {
        $pdo->rollBack(); // Batalkan semua perubahan jika ada error
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Metode tidak diizinkan.";
}
?>