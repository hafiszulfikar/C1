<?php
// public/rekap_pdf.php

session_start();
date_default_timezone_set('Asia/Jakarta');

// Hanya admin yang boleh membuka rekap.
if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    http_response_code(403);
    exit('Akses ditolak.');
}

require_once __DIR__ . '/../config/database.php';


// ========================================
// AMBIL DATA REKAP
// ========================================

$sql = "
    SELECT
        f.nama_fasilitas,
        f.lokasi,

        (
            SELECT COUNT(*)
            FROM reservations r
            WHERE r.facility_id = f.id
              AND r.status_reservasi = 'disetujui'
        ) AS total_reservasi,

        (
            SELECT COUNT(*)
            FROM reports rep
            WHERE rep.facility_id = f.id
        ) AS total_laporan

    FROM facilities f
    ORDER BY f.nama_fasilitas ASC
";

$stmt = $pdo->query($sql);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ========================================
// HITUNG TOTAL
// ========================================

$total_fasilitas = count($data);
$total_reservasi = 0;
$total_laporan = 0;

foreach ($data as $row) {
    $total_reservasi += (int) $row['total_reservasi'];
    $total_laporan += (int) $row['total_laporan'];
}


// ========================================
// FUNGSI ESCAPE HTML
// ========================================

function escapePDF($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Rekap Fasilitas | ReservasiKampus</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            background: #f5f8fc;
            color: #172033;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
        }

        .page {
            max-width: 1100px;
            margin: auto;
            padding: 35px;
            background: white;
            border: 1px solid #e3e9f0;
            border-radius: 12px;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin: 0 auto 20px;
            max-width: 1100px;
        }

        .toolbar a,
        .toolbar button {
            display: inline-block;
            padding: 11px 16px;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 12px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .back-button {
            background: white;
            color: #063b70;
            border: 1px solid #dbe4ef !important;
        }

        .print-button {
            background: #063b70;
            color: white;
        }

        .report-header {
            padding-bottom: 22px;
            margin-bottom: 25px;
            border-bottom: 2px solid #063b70;
        }

        .brand {
            color: #063b70;
            font-size: 21px;
            font-weight: bold;
            margin-bottom: 22px;
        }

        .brand span {
            font-weight: normal;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 25px;
            color: #172033;
        }

        .subtitle {
            color: #6b7280;
            font-size: 12px;
            line-height: 1.6;
        }

        .report-date {
            margin-top: 15px;
            color: #475467;
            font-size: 11px;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 28px;
        }

        .summary-card {
            padding: 16px;
            border: 1px solid #e3e9f0;
            border-radius: 10px;
            background: #f9fbfe;
        }

        .summary-label {
            margin-bottom: 8px;
            color: #6b7280;
            font-size: 11px;
        }

        .summary-value {
            color: #063b70;
            font-size: 25px;
            font-weight: bold;
        }

        h2 {
            margin: 0 0 14px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 13px 12px;
            border: 1px solid #dbe4ef;
            text-align: left;
            vertical-align: top;
            line-height: 1.5;
        }

        th {
            background: #063b70;
            color: white;
            font-size: 11px;
        }

        td {
            font-size: 11px;
        }

        tbody tr:nth-child(even) {
            background: #f5f8fc;
        }

        td.number {
            text-align: center;
        }

        .info {
            margin-top: 20px;
            padding: 13px 15px;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            background: #eff6ff;
            color: #344054;
            font-size: 11px;
            line-height: 1.6;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e3e9f0;
            color: #6b7280;
            font-size: 10px;
            text-align: center;
        }

        @media (max-width: 650px) {
            body {
                padding: 12px;
            }

            .page {
                padding: 18px;
            }

            .summary {
                grid-template-columns: 1fr;
            }

            .toolbar {
                flex-wrap: wrap;
            }

            table {
                font-size: 10px;
            }

            th,
            td {
                padding: 8px 6px;
            }
        }

        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        @media print {
            body {
                padding: 0;
                background: white;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .toolbar {
                display: none !important;
            }

            .page {
                max-width: none;
                padding: 0;
                border: none;
                border-radius: 0;
            }

            .summary-card {
                break-inside: avoid;
            }

            table {
                break-inside: auto;
            }

            tr {
                break-inside: avoid;
                break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            .info,
            .footer {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="toolbar">
        <a
            href="../views/admin/rekap.php"
            class="back-button"
        >
            Kembali ke Rekap
        </a>

        <button
            type="button"
            class="print-button"
            onclick="window.print()"
        >
            Cetak / Simpan PDF
        </button>
    </div>


    <main class="page">

        <header class="report-header">

            <div class="brand">
                Reservasi<span>Kampus</span>
            </div>

            <h1>Rekapitulasi Fasilitas Kampus</h1>

            <p class="subtitle">
                Laporan penggunaan fasilitas dan frekuensi
                kerusakan yang tercatat dalam sistem ReservasiKampus.
            </p>

            <div class="report-date">
                Tanggal laporan:
                <?= date('d-m-Y H:i') ?> WIB
            </div>

        </header>


        <section class="summary">

            <div class="summary-card">
                <div class="summary-label">
                    Total Fasilitas
                </div>

                <div class="summary-value">
                    <?= $total_fasilitas ?>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-label">
                    Total Reservasi Disetujui
                </div>

                <div class="summary-value">
                    <?= $total_reservasi ?>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-label">
                    Total Laporan Kerusakan
                </div>

                <div class="summary-value">
                    <?= $total_laporan ?>
                </div>
            </div>

        </section>


        <section>

            <h2>Rekap Okupansi dan Kerusakan</h2>

            <table>

                <thead>
                    <tr>
                        <th>Nama Fasilitas</th>
                        <th>Lokasi</th>
                        <th>Total Reservasi Disetujui</th>
                        <th>Total Laporan Kerusakan</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (count($data) > 0): ?>

                        <?php foreach ($data as $row): ?>

                            <tr>

                                <td>
                                    <?= escapePDF(
                                        $row['nama_fasilitas']
                                    ) ?>
                                </td>

                                <td>
                                    <?= escapePDF(
                                        $row['lokasi']
                                    ) ?>
                                </td>

                                <td class="number">
                                    <?= (int) $row['total_reservasi'] ?>
                                </td>

                                <td class="number">
                                    <?= (int) $row['total_laporan'] ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="4" class="number">
                                Belum ada data fasilitas.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </section>


        <div class="info">
            <strong>Informasi:</strong>
            Total reservasi dihitung berdasarkan reservasi berstatus
            <strong>Disetujui</strong>. Total laporan kerusakan dihitung
            dari seluruh laporan yang tersimpan untuk setiap fasilitas.
        </div>


        <footer class="footer">
            ReservasiKampus &middot;
            Sistem Reservasi Fasilitas Kampus
        </footer>

    </main>

</body>
</html>