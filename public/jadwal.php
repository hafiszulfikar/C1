<?php
// public/jadwal.php
require_once __DIR__ . '/../config/database.php';

$id_fasilitas = $_GET['id'] ?? null;
// Gunakan tanggal hari ini sebagai default jika pengguna belum memilih tanggal
$tanggal = $_GET['tanggal'] ?? date('Y-m-d'); 

if (!$id_fasilitas) {
    die("ID Fasilitas tidak ditemukan.");
}

// Mengambil nama fasilitas
$stmt_fac = $pdo->prepare("SELECT nama_fasilitas FROM facilities WHERE id = :id");
$stmt_fac->execute([':id' => $id_fasilitas]);
$fasilitas = $stmt_fac->fetch();

if (!$fasilitas) {
    die("Data fasilitas tidak ditemukan.");
}

// Mengambil data reservasi yang 'disetujui' pada fasilitas dan tanggal yang dipilih
$stmt_res = $pdo->prepare("SELECT start_time, end_time FROM reservations WHERE facility_id = :id AND tanggal = :tanggal AND status_reservasi = 'disetujui'");
$stmt_res->execute([':id' => $id_fasilitas, ':tanggal' => $tanggal]);
$reservasi = $stmt_res->fetchAll();

// Membuat array slot waktu dari 07:00 hingga 20:00 dengan interval 30 menit
$slots = [];
$start_time = strtotime('07:00');
$end_time = strtotime('20:00');

while ($start_time < $end_time) {
    $slot_start = date('H:i', $start_time);
    $start_time = strtotime('+30 minutes', $start_time);
    $slot_end = date('H:i', $start_time);
    
    $slots[] = [
        'start' => $slot_start,
        'end' => $slot_end
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal - <?php echo htmlspecialchars($fasilitas['nama_fasilitas']); ?></title>
</head>
<body>
    <h2>Jadwal Ketersediaan: <?php echo htmlspecialchars($fasilitas['nama_fasilitas']); ?></h2>
    <a href="index.php">Kembali ke Daftar Fasilitas</a>
    <hr>

    <form action="jadwal.php" method="GET">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_fasilitas); ?>">
        <label>Pilih Tanggal:</label>
        <input type="date" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>" required>
        <button type="submit">Cek Jadwal</button>
    </form>
    <br>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Slot Waktu</th>
                <th>Status Ketersediaan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($slots as $slot): ?>
                <?php 
                    $status = 'Tersedia';
                    $warna = 'green';

                    // Cek apakah slot waktu ini beririsan dengan reservasi yang sudah disetujui
                    foreach ($reservasi as $res) {
                        $res_start = strtotime($res['start_time']);
                        $res_end = strtotime($res['end_time']);
                        $current_slot_start = strtotime($slot['start']);
                        $current_slot_end = strtotime($slot['end']);

                        // Jika waktu mulai slot berada di dalam rentang reservasi, berarti tidak tersedia
                        if ($current_slot_start >= $res_start && $current_slot_end <= $res_end) {
                            $status = 'Tidak Tersedia';
                            $warna = 'red';
                            break;
                        }
                    }
                ?>
                <tr>
                    <td><?php echo $slot['start'] . ' - ' . $slot['end']; ?></td>
                    <td style="color: <?php echo $warna; ?>; font-weight: bold;">
                        <?php echo $status; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>