<?php
// app/controllers/process_status_reservasi.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservasi_id = $_POST['reservasi_id'];
    $action = $_POST['action'];
    $facility_id = $_POST['facility_id'];
    $tanggal = $_POST['tanggal'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    try {
        if ($action === 'reject') {
            // Langsung tolak tanpa cek bentrok
            $stmt = $pdo->prepare("UPDATE reservations SET status_reservasi = 'ditolak' WHERE id = :id");
            $stmt->execute([':id' => $reservasi_id]);
            
        } elseif ($action === 'approve') {
            // Logika pengecekan bentrok jadwal
            // Bentrok terjadi jika: (waktu_mulai_baru < waktu_selesai_lama) DAN (waktu_selesai_baru > waktu_mulai_lama)
            $sql_cek = "SELECT COUNT(*) FROM reservations 
                        WHERE facility_id = :facility_id 
                        AND tanggal = :tanggal 
                        AND status_reservasi = 'disetujui' 
                        AND (start_time < :end_time AND end_time > :start_time)";
                        
            $stmt_cek = $pdo->prepare($sql_cek);
            $stmt_cek->execute([
                ':facility_id' => $facility_id,
                ':tanggal' => $tanggal,
                ':end_time' => $end_time,
                ':start_time' => $start_time
            ]);
            
            $jumlah_bentrok = $stmt_cek->fetchColumn();

            if ($jumlah_bentrok > 0) {
                // Jika bentrok, hentikan proses (bisa dikembangkan menggunakan session flash message untuk UI yang lebih baik)
                die("Gagal menyetujui: Jadwal ini bentrok dengan reservasi lain yang sudah disetujui. <br><a href='../../views/officer/dashboard.php'>Kembali</a>");
            } else {
                // Jika aman, setujui reservasi
                $stmt = $pdo->prepare("UPDATE reservations SET status_reservasi = 'disetujui' WHERE id = :id");
                $stmt->execute([':id' => $reservasi_id]);
            }
        }

        header("Location: ../../views/officer/dashboard.php");
        exit();
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Metode tidak diizinkan.";
}
?>