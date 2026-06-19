<?php
session_start();
include "koneksi.php";
if (!isset($_SESSION['login'])) { header("location: index.php"); exit; }

if (isset($_POST['simpan'])) {
    $nama_dpo = mysqli_real_escape_string($koneksi, $_POST['nama_dpo']);
    $kasus_kejahatan = mysqli_real_escape_string($koneksi, $_POST['kasus_kejahatan']);
    $pasal = mysqli_real_escape_string($koneksi, $_POST['pasal']);
    $tingkat_bahaya = $_POST['tingkat_bahaya'];
    $tanggal_ditetapkan = $_POST['tanggal_ditetapkan'];
    $user_id = $_SESSION['id_user'];

    // Siapkan folder uploads jika belum ada
    if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }

    // Proses Upload Video (Jika ada)
    $nama_video = null;
    if (!empty($_FILES['file_video']['name'])) {
        $nama_video = time() . '_video_' . $_FILES['file_video']['name'];
        move_uploaded_file($_FILES['file_video']['tmp_name'], 'uploads/' . $nama_video);
    }

    // Proses Upload Audio (Jika ada)
    $nama_audio = null;
    if (!empty($_FILES['file_audio']['name'])) {
        $nama_audio = time() . '_audio_' . $_FILES['file_audio']['name'];
        move_uploaded_file($_FILES['file_audio']['tmp_name'], 'uploads/' . $nama_audio);
    }

    // Insert ke database termasuk video dan audio
    $query_dpo = "INSERT INTO dpo_kasus (nama_dpo, kasus_kejahatan, pasal, tingkat_bahaya, status, tanggal_ditetapkan, user_id, file_video, file_audio) 
                  VALUES ('$nama_dpo', '$kasus_kejahatan', '$pasal', '$tingkat_bahaya', 0, '$tanggal_ditetapkan', '$user_id', " . 
                  ($nama_video ? "'$nama_video'" : "NULL") . ", " . 
                  ($nama_audio ? "'$nama_audio'" : "NULL") . ")";
    
    if (mysqli_query($koneksi, $query_dpo)) {
        $dpo_id = mysqli_insert_id($koneksi);
        
        // Proses Upload Multiple Foto
        $total_files = count($_FILES['foto_bukti']['name']);
        for ($i = 0; $i < $total_files; $i++) {
            $filename = $_FILES['foto_bukti']['name'][$i];
            $tmp_name = $_FILES['foto_bukti']['tmp_name'][$i];
            if($filename != "") {
                $new_filename = time() . '_' . $filename;
                if (move_uploaded_file($tmp_name, 'uploads/' . $new_filename)) {
                    mysqli_query($koneksi, "INSERT INTO bukti_foto (dpo_id, file_foto) VALUES ('$dpo_id', '$new_filename')");
                }
            }
        }
        echo "<script>alert('DPO Berhasil Dirilis!'); window.location='dashboard.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Input DPO</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-5 mb-5"><div class="row justify-content-center"><div class="col-md-8"><div class="card shadow border-0">
    <div class="card-header bg-dark text-white fw-bold">FORM DATA BURONAN BARU</div>
    <div class="card-body p-4">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3"><label class="form-label fw-bold">Nama Buronan</label><input type="text" name="nama_dpo" class="form-control" required></div>
            <div class="mb-3"><label class="form-label fw-bold">Kasus Kejahatan</label><input type="text" name="kasus_kejahatan" class="form-control" required></div>
            <div class="mb-3"><label class="form-label fw-bold">Pasal Pelanggaran</label><input type="text" name="pasal" class="form-control" required></div>
            <div class="row"><div class="col-md-6 mb-3"><label class="form-label fw-bold">Tingkat Bahaya</label><select name="tingkat_bahaya" class="form-select"><option value="1">LOW</option><option value="2">MEDIUM</option><option value="3">HIGH RISK</option></select></div>
            <div class="col-md-6 mb-3"><label class="form-label fw-bold">Tanggal DPO</label><input type="date" name="tanggal_ditetapkan" class="form-control" value="<?= date('Y-m-d'); ?>"></div></div>
            
            <div class="mb-3"><label class="form-label fw-bold text-primary">Upload Foto Bukti/Wajah (Bisa Banyak)</label><input type="file" name="foto_bukti[]" class="form-control" multiple accept="image/*"></div>
            
            <div class="mb-3"><label class="form-label fw-bold text-success">Upload Rekaman Video CCTV (Opsional)</label><input type="file" name="file_video" class="form-control" accept="video/*"></div>
            <div class="mb-4"><label class="form-label fw-bold text-warning">Upload Rekaman Audio Penyadapan (Opsional)</label><input type="file" name="file_audio" class="form-control" accept="audio/*"></div>
            
            <button type="submit" name="simpan" class="btn btn-danger w-100 fw-bold">🚨 RILIS DAN TRACK TARGET</button>
        </form>
    </div>
</div></div></div></div>
</body>
</html>