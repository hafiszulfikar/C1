<?php
// app/controllers/process_status_laporan.php

session_start();


// =============================================
// FUNGSI REDIRECT DENGAN PESAN
// =============================================

function kembaliKeDashboardPetugas($jenis, $pesan)
{
    $_SESSION[$jenis] = $pesan;

    header(
        "Location: ../../views/officer/dashboard.php"
    );

    exit();
}


// =============================================
// 1. CEK ROLE
// =============================================

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'petugas'
) {
    $_SESSION['flash_error'] =
        'Akses ditolak. Silakan login sebagai petugas.';

    header(
        "Location: ../../views/auth/login.php"
    );

    exit();
}


// =============================================
// 2. CEK METHOD
// =============================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    kembaliKeDashboardPetugas(
        'flash_error',
        'Permintaan tidak valid.'
    );
}


// =============================================
// 3. VALIDASI INPUT
// =============================================

$laporan_id = filter_var(
    $_POST['laporan_id'] ?? '',
    FILTER_VALIDATE_INT
);

$status_input = $_POST['status_baru'] ?? '';
$status_baru = is_string($status_input)
    ? $status_input
    : '';

$catatan_input = $_POST['catatan_resolusi'] ?? '';
$catatan_resolusi = is_string($catatan_input)
    ? trim($catatan_input)
    : '';

$tandai_perbaikan =
    isset($_POST['tandai_perbaikan']) &&
    $_POST['tandai_perbaikan'] === 'ya';

// =============================================
// 4. KONEKSI DATABASE
// =============================================

require_once __DIR__ . '/../../config/database.php';


try {

    $pdo->beginTransaction();


    // =========================================
    // 5. AMBIL LAPORAN DARI DATABASE
    // =========================================
    // facility_id diambil dari database,
    // bukan dipercaya dari hidden input form.

    $stmt = $pdo->prepare("
        SELECT
            id,
            facility_id,
            status_laporan
        FROM reports
        WHERE id = :id
        FOR UPDATE
    ");

    $stmt->execute([
        ':id' => $laporan_id
    ]);

    $laporan = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$laporan) {

        $pdo->rollBack();

        kembaliKeDashboardPetugas(
            'flash_error',
            'Laporan kerusakan tidak ditemukan.'
        );
    }


    // Hanya laporan yang masih ditangani
    // yang boleh diperbarui.

    if (
        !in_array(
            $laporan['status_laporan'],
            ['baru', 'diproses'],
            true
        )
    ) {
        $pdo->rollBack();

        kembaliKeDashboardPetugas(
            'flash_error',
            'Laporan ini sudah selesai diproses dan tidak dapat diperbarui lagi.'
        );
    }


    $facility_id = (int) $laporan['facility_id'];


    // =========================================
    // 6. KUNCI DAN PERIKSA FASILITAS
    // =========================================

    $stmt = $pdo->prepare("
        SELECT id, status_fasilitas
        FROM facilities
        WHERE id = :id
        FOR UPDATE
    ");

    $stmt->execute([
        ':id' => $facility_id
    ]);

    $fasilitas = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$fasilitas) {

        $pdo->rollBack();

        kembaliKeDashboardPetugas(
            'flash_error',
            'Fasilitas terkait laporan tidak ditemukan.'
        );
    }


    $status_fasilitas_sekarang =
        $fasilitas['status_fasilitas'];


    // =========================================
    // 7. UPDATE LAPORAN DAN CATATAN
    // =========================================

    $stmt = $pdo->prepare("
        UPDATE reports
        SET
            status_laporan = :status,
            catatan_resolusi = :catatan
        WHERE id = :id
          AND status_laporan IN ('baru', 'diproses')
    ");

    $stmt->execute([
        ':status' => $status_baru,
        ':catatan' => $catatan_resolusi,
        ':id' => $laporan_id
    ]);



    // =========================================
    // 8. TANDAI FASILITAS DALAM PERBAIKAN
    // =========================================

    if (
        $status_baru === 'diproses' &&
        $tandai_perbaikan
    ) {

        // Jangan mengaktifkan ulang fasilitas
        // yang sebelumnya sengaja dinonaktifkan admin.

        if (
            in_array(
                $status_fasilitas_sekarang,
                ['aktif', 'dalam perbaikan'],
                true
            )
        ) {

            $stmt = $pdo->prepare("
                UPDATE facilities
                SET status_fasilitas = 'dalam perbaikan'
                WHERE id = :id
                  AND status_fasilitas IN (
                      'aktif',
                      'dalam perbaikan'
                  )
            ");

            $stmt->execute([
                ':id' => $facility_id
            ]);
        }
    }


    // =========================================
    // 9. SELESAI / DITOLAK
    // PERIKSA LAPORAN LAIN YANG MASIH TERBUKA
    // =========================================

    $fasilitas_diaktifkan = false;
    $laporan_lain_terbuka = false;


    if (
        in_array(
            $status_baru,
            ['selesai', 'ditolak'],
            true
        )
    ) {

        // Cari laporan lain untuk fasilitas yang
        // sama yang statusnya masih baru/diproses.

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM reports
            WHERE facility_id = :facility_id
              AND id <> :laporan_id
              AND status_laporan IN ('baru', 'diproses')
        ");

        $stmt->execute([
            ':facility_id' => $facility_id,
            ':laporan_id' => $laporan_id
        ]);

        $jumlah_laporan_terbuka =
            (int) $stmt->fetchColumn();


        $laporan_lain_terbuka =
            $jumlah_laporan_terbuka > 0;


        // Aktifkan kembali hanya jika:
        // 1. Tidak ada laporan lain yang terbuka.
        // 2. Fasilitas memang berstatus dalam perbaikan.
        //
        // Status nonaktif tidak akan tertimpa.

        if (
            !$laporan_lain_terbuka &&
            $status_fasilitas_sekarang === 'dalam perbaikan'
        ) {

            $stmt = $pdo->prepare("
                UPDATE facilities
                SET status_fasilitas = 'aktif'
                WHERE id = :id
                  AND status_fasilitas = 'dalam perbaikan'
            ");

            $stmt->execute([
                ':id' => $facility_id
            ]);

            $fasilitas_diaktifkan =
                $stmt->rowCount() === 1;
        }
    }


    // =========================================
    // 10. SIMPAN SEMUA PERUBAHAN
    // =========================================

    $pdo->commit();


    // =========================================
    // 11. PESAN HASIL
    // =========================================

    if ($fasilitas_diaktifkan) {

        kembaliKeDashboardPetugas(
            'flash_success',
            'Laporan berhasil diperbarui. Tidak ada laporan lain yang masih ditangani, sehingga fasilitas telah kembali aktif.'
        );
    }


    if (
        $laporan_lain_terbuka &&
        in_array(
            $status_baru,
            ['selesai', 'ditolak'],
            true
        ) &&
        $status_fasilitas_sekarang === 'dalam perbaikan'
    ) {

        kembaliKeDashboardPetugas(
            'flash_success',
            'Laporan berhasil diperbarui. Fasilitas tetap dalam perbaikan karena masih ada laporan lain yang perlu ditangani.'
        );
    }


    if (
        $status_baru === 'diproses' &&
        $tandai_perbaikan &&
        $status_fasilitas_sekarang === 'nonaktif'
    ) {

        kembaliKeDashboardPetugas(
            'flash_success',
            'Laporan berhasil diperbarui. Fasilitas tetap nonaktif sesuai status yang ditetapkan admin.'
        );
    }


    kembaliKeDashboardPetugas(
        'flash_success',
        'Status laporan dan catatan resolusi berhasil diperbarui.'
    );


} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Detail teknis disimpan di log server.
    error_log(
        'process_status_laporan.php: ' .
        $e->getMessage()
    );

    kembaliKeDashboardPetugas(
        'flash_error',
        'Terjadi kesalahan saat memperbarui laporan. Silakan coba lagi.'
    );
}