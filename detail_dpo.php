<?php
session_start();
include "koneksi.php";
if (!isset($_SESSION['login'])) { header("location: index.php"); exit; }
$id = $_GET['id'];

if (isset($_POST['tanda_tangan_bap'])) {
    $img = $_POST['tanda_tangan_bap'];
    mysqli_query($koneksi, "UPDATE dpo_kasus SET status = 1, ttd_bap = '$img' WHERE id = '$id'");
    echo "<script>alert('Target Tertangkap!'); window.location='detail_dpo.php?id=$id';</script>";
}

// Selesai diperbaiki: Tutup kurung sudah lengkap di ujung baris ini
$dpo = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM dpo_kasus WHERE id = '$id'"));
$query_foto = mysqli_query($koneksi, "SELECT * FROM bukti_foto WHERE dpo_id = '$id'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail DPO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .canvas-container { border: 2px dashed #ccc; background: #fff; cursor: crosshair; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">🚨 DAFTAR DASHBOARD BURONAN</a>
        <a href="dashboard.php" class="btn btn-sm btn-outline-light">Kembali</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow p-4 h-100">
                <h3 class="fw-bold text-danger text-uppercase mb-3"><?= $dpo['nama_dpo']; ?></h3>
                <p><strong>Kasus:</strong> <?= $dpo['kasus_kejahatan']; ?></p>
                <p><strong>Pasal:</strong> <?= $dpo['pasal']; ?></p>
                <p><strong>Status:</strong> <?= $dpo['status'] == 0 ? '<span class="badge bg-danger">🔴 BURON</span>' : '<span class="badge bg-success">🟢 TERTANGKAP</span>'; ?></p>
                
                <?php if (!empty($dpo['file_audio'])) : ?>
                    <div class="mt-3 p-3 bg-light rounded border border-warning">
                        <label class="fw-bold d-block mb-2">🔊 Rekaman Audio Bukti Penyadapan:</label>
                        <audio controls class="w-100"><source src="uploads/<?= $dpo['file_audio']; ?>" type="audio/mpeg"></audio>
                    </div>
                <?php endif; ?>
                
                <?php if ($dpo['status'] == 0) : ?>
                    <button class="btn btn-success fw-bold w-100 mt-4" data-bs-toggle="modal" data-bs-target="#modalTangkap">🔒 AMANKAN BURONAN</button>
                <?php else : ?>
                    <div class="mt-4 text-center">
                        <h5 class="fw-bold text-success">TANDA TANGAN VALIDASI BAP</h5>
                        <img src="<?= $dpo['ttd_bap']; ?>" class="bg-white border rounded p-2 img-fluid">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow p-4 h-100">
                <?php if (mysqli_num_rows($query_foto) > 0) : ?>
                    <h5 class="fw-bold mb-3">📸 FOTO TARGET:</h5>
                    <div class="row g-2 mb-4">
                        <?php while($foto = mysqli_fetch_assoc($query_foto)) : ?>
                            <div class="col-4"><img src="uploads/<?= $foto['file_foto']; ?>" class="img-fluid img-thumbnail"></div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($dpo['file_video'])) : ?>
                    <h5 class="fw-bold mb-3">🎥 VIDEO REKAMAN CCTV:</h5>
                    <div class="ratio ratio-16x9">
                        <video controls class="rounded shadow-sm"><source src="uploads/<?= $dpo['file_video']; ?>" type="video/mp4"></video>
                    </div>
                <?php endif; ?>
                
                <?php if (mysqli_num_rows($query_foto) == 0 && empty($dpo['file_video'])) : ?>
                    <div class="text-center text-muted my-5"><p>Tidak ada berkas dokumentasi foto/video untuk kasus ini.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTangkap" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">🚨 TANDA TANGAN BAP</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="formBap">
                <div class="modal-body">
                    <label class="form-label fw-bold">Goreskan TTD Penyidik di atas Canvas:</label>
                    <div class="canvas-container rounded mb-2">
                        <canvas id="canvasTtd" width="460" height="200"></canvas>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="clearCanvas()">Hapus</button>
                    <input type="hidden" name="tanda_tangan_bap" id="inputTtd">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold" onclick="playSuccessSound()">💾 SIMPAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const canvas = document.getElementById('canvasTtd'); const ctx = canvas.getContext('2d'); let isDrawing = false; let lastX = 0; let lastY = 0;
    ctx.lineWidth = 3; ctx.lineCap = 'round'; ctx.strokeStyle = '#000';
    canvas.addEventListener('mousedown', (e) => { isDrawing = true; [lastX, lastY] = [e.offsetX, e.offsetY]; });
    canvas.addEventListener('mousemove', (e) => { if (!isDrawing) return; ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(e.offsetX, e.offsetY); ctx.stroke(); [lastX, lastY] = [e.offsetX, e.offsetY]; });
    canvas.addEventListener('mouseup', () => isDrawing = false); canvas.addEventListener('mouseout', () => isDrawing = false);
    function clearCanvas() { ctx.clearRect(0, 0, canvas.width, canvas.height); }
    document.getElementById('formBap').addEventListener('submit', function() { document.getElementById('inputTtd').value = canvas.toDataURL(); });
    function playSuccessSound() { new Audio('https://assets.mixkit.co/active_storage/sfx/2568/2568-84.wav').play(); }
</script>
</body>
</html>