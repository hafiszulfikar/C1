<?php
// app/controllers/process_laporan.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengguna') {
    die("Akses ditolak.");
}

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $facility_id = $_POST['facility_id'];
    $kategori = $_POST['kategori'];
    $deskripsi = trim($_POST['deskripsi']);
    $nama_file_foto = null;

    if (empty($facility_id) || empty($kategori) || empty($deskripsi)) {
        die("Data fasilitas, kategori, dan deskripsi wajib diisi.");
    }

    // Proses unggah foto jika ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_ext = ['jpg', 'jpeg', 'png'];
        if (in_array($file_ext, $allowed_ext)) {
            // Beri nama unik agar tidak tertimpa
            $nama_file_foto = uniqid() . '.' . $file_ext;
            $tujuan_upload = __DIR__ . '/../../public/uploads/' . $nama_file_foto;
            
            // Pindahkan file ke folder uploads
            move_uploaded_file($file_tmp, $tujuan_upload);
        } else {
            die("Format foto tidak didukung. Gunakan JPG atau PNG.");
        }
    }

    try {
        $sql = "INSERT INTO reports (user_id, facility_id, kategori_laporan, deskripsi, foto, status_laporan) 
                VALUES (:user_id, :facility_id, :kategori, :deskripsi, :foto, 'baru')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':facility_id' => $facility_id,
            ':kategori' => $kategori,
            ':deskripsi' => $deskripsi,
            ':foto' => $nama_file_foto
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