<?php
session_start();
include "koneksi.php";
if (!isset($_SESSION['login'])) { header("location: index.php"); exit; }

// FIX SINKRONISASI: Mengambil pangkat dan nama petugas yang asli dari session login index.php
$pangkat = isset($_SESSION['pangkat']) ? $_SESSION['pangkat'] : 'Letnan';
$nama_petugas = isset($_SESSION['nama_petugas']) ? $_SESSION['nama_petugas'] : 'Penyidik';

// Gabungkan pangkat dan nama untuk ditampilkan di navbar
$penyidik = $pangkat . " " . $nama_petugas;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Daftar Buronan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .navbar-custom { background-color: #1e293b; }
        .table-container { background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark navbar-custom shadow mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">🚨 DAFTAR DASHBOARD BURONAN</a>
        <span class="navbar-text text-white me-3">
            Penyidik: <strong><?= $penyidik; ?></strong> | 
            <a href="logout.php" class="btn btn-sm btn-danger ms-2">Keluar</a>
        </span>
    </div>
</nav>

<div class="container mt-4">
    <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Daftar Buronan Aktif</h3>
                <p class="text-muted small mb-0">Sistem Pemantauan dan Pelacakan Data Terintegrasi</p>
            </div>
            <div>
                <a href="cetak_pdf.php" class="btn btn-danger fw-bold me-2" target="_blank">
                    <i class="bi bi-file-earmark-pdf-fill"></i> EKSPOR PDF
                </a>
                <a href="tambah_dpo.php" class="btn btn-primary fw-bold">
                    <i class="bi bi-plus-lg"></i> INPUT DPO BARU
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>NO</th>
                        <th>NAMA BURONAN</th>
                        <th>KASUS</th>
                        <th>PASAL</th>
                        <th>TINGKAT BAHAYA</th>
                        <th>STATUS</th>
                        <th>TANGGAL RILIS</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = mysqli_query($koneksi, "SELECT * FROM dpo_kasus ORDER BY id DESC");
                    while ($row = mysqli_fetch_assoc($query)) {
                        
                        // --- LOGIKA FIX TOTAL TINGKAT BAHAYA ---
                        $nilai_bahaya = 'TIDAK ADA DATA';
                        if (isset($row['tingkat_bahaya'])) {
                            $nilai_bahaya = trim($row['tingkat_bahaya']);
                        } elseif (isset($row['bahaya'])) {
                            $nilai_bahaya = trim($row['bahaya']);
                        }

                        // Mengubah ke huruf besar untuk pengecekan badge warna
                        $cek_bahaya = strtoupper($nilai_bahaya);

                        // Penentuan badge warna yang fleksibel (bisa membaca teks huruf kecil/besar atau angka dari database)
                        if (strpos($cek_bahaya, 'MEDIUM') !== false || $cek_bahaya == '2') {
                            $badgeBahaya = '<span class="badge bg-warning text-dark">🟡 MEDIUM</span>';
                        } elseif (strpos($cek_bahaya, 'HIGH') !== false || strpos($cek_bahaya, 'RISK') !== false || $cek_bahaya == '3') {
                            $badgeBahaya = '<span class="badge bg-danger">🔴 HIGH RISK</span>';
                        } elseif (strpos($cek_bahaya, 'LOW') !== false || $cek_bahaya == '1') {
                            $badgeBahaya = '<span class="badge bg-info">🔵 LOW</span>';
                        } else {
                            // Jika isi di databasemu berbeda teksnya, dia akan langsung menampilkan isi asli database di sini
                            $badgeBahaya = '<span class="badge bg-secondary">⚪ ' . $nilai_bahaya . '</span>';
                        }

                        // Logika status buron/tertangkap
                        $statusBadge = ($row['status'] == 0) ? '<span class="badge bg-danger">🔴 BURON</span>' : '<span class="badge bg-success">🟢 TERTANGKAP</span>';
                        
                        // Logika tanggal rilis jika kosong
                        $tanggal = isset($row['tanggal_rilis']) ? $row['tanggal_rilis'] : '19 Jun 2026';
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td class="fw-bold"><?= $row['nama_dpo']; ?></td>
                        <td><?= $row['kasus_kejahatan']; ?></td>
                        <td><small class="text-muted"><?= $row['pasal']; ?></small></td>
                        <td><?= $badgeBahaya; ?></td>
                        <td><?= $statusBadge; ?></td>
                        <td><?= $tanggal; ?></td>
                        <td class="text-center">
                            <a href="detail_dpo.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-dark">Detail</a>
                            <a href="hapus_dpo.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus data?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>