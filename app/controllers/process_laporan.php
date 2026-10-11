
<?php
// app/controllers/process_laporan.php

session_start();

// ========================================
// CEK LOGIN DAN ROLE
// ========================================
if (!isset($_SESSION['role'])) {
    header("Location: ../../views/auth/login.php");
    exit();
}

if ($_SESSION['role'] !== 'pengguna') {
    $_SESSION['flash_error'] =
        "Anda tidak memiliki izin untuk mengirim laporan.";

    header("Location: ../../public/index.php");
    exit();
}

require_once __DIR__ . '/../../config/database.php';

// ========================================
// FUNGSI REDIRECT ERROR
// ========================================
function gagalLaporan($pesan)
{
    $_SESSION['flash_error'] = $pesan;

    header("Location: ../../views/user/buat_laporan.php");
    exit();
}

// ========================================
// CEK METODE REQUEST
// ========================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    gagalLaporan("Permintaan tidak valid.");
}

// ========================================
// AMBIL DAN VALIDASI INPUT
// ========================================
$user_id = $_SESSION['user_id'] ?? null;

$facility_id = filter_var(
    $_POST['facility_id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

$kategori = is_string($_POST['kategori'] ?? null)
    ? trim($_POST['kategori'])
    : '';

$deskripsi = is_string($_POST['deskripsi'] ?? null)
    ? trim($_POST['deskripsi'])
    : '';

if (!$user_id) {
    gagalLaporan("Sesi pengguna tidak valid. Silakan login kembali.");
}

if (!$facility_id) {
    gagalLaporan("Silakan pilih fasilitas yang akan dilaporkan.");
}

if ($kategori === '' || $deskripsi === '') {
    gagalLaporan("Kategori dan deskripsi laporan wajib diisi.");
}

if (trim($deskripsi) === '') {
    gagalLaporan("Deskripsi laporan tidak boleh kosong.");
}

// ========================================
// PROSES FOTO DAN SIMPAN LAPORAN
// ========================================
$nama_file_foto = null;
$path_foto_tersimpan = null;

try {
    // Pastikan fasilitas tersedia dan aktif
    $stmt_fasilitas = $pdo->prepare("
        SELECT id, status_fasilitas
        FROM facilities
        WHERE id = :id
    ");

    $stmt_fasilitas->execute([
        ':id' => $facility_id
    ]);

    $fasilitas = $stmt_fasilitas->fetch(PDO::FETCH_ASSOC);

    if (!$fasilitas) {
        gagalLaporan("Fasilitas yang dipilih tidak ditemukan.");
    }

    if ($fasilitas['status_fasilitas'] !== 'aktif') {
        gagalLaporan(
            "Fasilitas tidak tersedia untuk dilaporkan melalui form ini."
        );
    }

    // ------------------------------------
    // FOTO OPSIONAL
    // ------------------------------------
    if (isset($_FILES['foto'])) {
        $foto = $_FILES['foto'];

        if ($foto['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($foto['error'] !== UPLOAD_ERR_OK) {
                gagalLaporan(
                    "Foto gagal diunggah. Silakan pilih ulang foto."
                );
            }

            // Maksimal 5 MB
            if ($foto['size'] > 5 * 1024 * 1024) {
                gagalLaporan(
                    "Ukuran foto maksimal 5 MB."
                );
            }

            if (!is_uploaded_file($foto['tmp_name'])) {
                gagalLaporan("Berkas foto tidak valid.");
            }

            // Verifikasi isi file, bukan hanya ekstensi namanya
            $info_gambar = getimagesize($foto['tmp_name']);

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = $finfo
                ? finfo_file($finfo, $foto['tmp_name'])
                : false;

            if ($finfo) {
                finfo_close($finfo);
            }

            if (
                $info_gambar === false ||
                !in_array(
                    $mime,
                    ['image/jpeg', 'image/png'],
                    true
                )
            ) {
                gagalLaporan(
                    "Format foto tidak didukung. Gunakan JPG, JPEG, atau PNG."
                );
            }

            // Nama file acak untuk menghindari bentrok nama
            $ekstensi = ($mime === 'image/png') ? 'png' : 'jpg';

            $nama_file_foto =
                bin2hex(random_bytes(16)) . '.' . $ekstensi;

            $folder_upload =
                __DIR__ . '/../../public/uploads/';

            if (
                !is_dir($folder_upload) &&
                !mkdir($folder_upload, 0755, true) &&
                !is_dir($folder_upload)
            ) {
                gagalLaporan(
                    "Folder penyimpanan foto tidak dapat disiapkan."
                );
            }

            $path_foto_tersimpan =
                $folder_upload . $nama_file_foto;

            if (
                !move_uploaded_file(
                    $foto['tmp_name'],
                    $path_foto_tersimpan
                )
            ) {
                $path_foto_tersimpan = null;
                $nama_file_foto = null;

                gagalLaporan(
                    "Foto gagal disimpan. Silakan coba kembali."
                );
            }
        }
    }

    // ------------------------------------
    // SIMPAN LAPORAN KE DATABASE
    // ------------------------------------
    $sql = "
        INSERT INTO reports (
            user_id,
            facility_id,
            kategori_laporan,
            deskripsi,
            foto,
            status_laporan
        )
        VALUES (
            :user_id,
            :facility_id,
            :kategori,
            :deskripsi,
            :foto,
            'baru'
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':user_id' => $user_id,
        ':facility_id' => $facility_id,
        ':kategori' => $kategori,
        ':deskripsi' => $deskripsi,
        ':foto' => $nama_file_foto
    ]);

    $_SESSION['flash_success'] =
        "Laporan kerusakan berhasil dikirim.";

    header("Location: ../../views/user/dashboard.php");
    exit();

} catch (Throwable $e) {
    // Hapus foto jika sudah dipindahkan tetapi laporan gagal disimpan
    if (
        $path_foto_tersimpan !== null &&
        is_file($path_foto_tersimpan)
    ) {
        unlink($path_foto_tersimpan);
    }

    error_log(
        'process_laporan.php: ' . $e->getMessage()
    );

    gagalLaporan(
        "Laporan gagal dikirim karena terjadi kesalahan. Silakan coba kembali."
    );
}
