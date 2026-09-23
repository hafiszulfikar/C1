<?php
// app/controllers/process_reservasi.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengguna') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $facility_id = $_POST['facility_id'];
    $tanggal = $_POST['tanggal'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $tujuan = trim($_POST['tujuan_penggunaan']);

    if (empty($facility_id) || empty($tanggal) || empty($start_time) || empty($end_time) || empty($tujuan)) {
        die("Semua field wajib diisi.");
    }

    // --- VALIDASI SERVER-SIDE: Jam operasional & Kelipatan 30 menit ---
    $start_ts = strtotime($start_time);
    $end_ts = strtotime($end_time);
    $jam_buka = strtotime('07:00');
    $jam_tutup = strtotime('20:00');

    if ($start_ts >= $end_ts) {
        die("Waktu mulai harus lebih awal dari waktu selesai.");
    }
    
    // Cek jam operasional 07:00 - 20:00
    if ($start_ts < $jam_buka || $end_ts > $jam_tutup) {
        die("Reservasi hanya diperbolehkan pada jam operasional (07:00 - 20:00).");
    }

    // Cek apakah menit merupakan kelipatan 30 (00 atau 30)
    $start_min = (int)date('i', $start_ts);
    $end_min = (int)date('i', $end_ts);
    
    if (($start_min % 30 !== 0) || ($end_min % 30 !== 0)) {
        die("Waktu reservasi harus dalam kelipatan 30 menit (contoh: 07:00, 07:30).");
    }
    // ------------------------------------------------------------------

    try {
        $sql = "INSERT INTO reservations (user_id, facility_id, tujuan_penggunaan, tanggal, start_time, end_time, status_reservasi) 
                VALUES (:user_id, :facility_id, :tujuan, :tanggal, :start_time, :end_time, 'menunggu')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':facility_id' => $facility_id,
            ':tujuan' => $tujuan,
            ':tanggal' => $tanggal,
            ':start_time' => $start_time,
            ':end_time' => $end_time
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