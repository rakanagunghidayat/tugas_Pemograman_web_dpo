<?php
session_start();
include "koneksi.php";

// Jaga halaman dari user ilegal
if (!isset($_SESSION['login'])) { 
    header("location: index.php"); 
    exit; 
}

// Ambil ID data yang mau dihapus dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Ambil foto-foto bukti lama dari database dulu untuk dihapus dari folder laptop
    $query_foto = mysqli_query($koneksi, "SELECT file_foto FROM bukti_foto WHERE dpo_id = '$id'");
    while ($foto = mysqli_fetch_assoc($query_foto)) {
        $path_foto = 'uploads/' . $foto['file_foto'];
        if (file_exists($path_foto)) {
            unlink($path_foto); // Menghapus file foto fisik di folder uploads
        }
    }

    // 2. Ambil data video dan audio lama untuk dihapus dari folder laptop
    $query_dpo = mysqli_query($koneksi, "SELECT file_video, file_audio FROM dpo_kasus WHERE id = '$id'");
    $data_dpo = mysqli_fetch_assoc($query_dpo);
    
    if (!empty($data_dpo['file_video']) && file_exists('uploads/' . $data_dpo['file_video'])) {
        unlink('uploads/' . $data_dpo['file_video']);
    }
    if (!empty($data_dpo['file_audio']) && file_exists('uploads/' . $data_dpo['file_audio'])) {
        unlink('uploads/' . $data_dpo['file_audio']);
    }

    // 3. Hapus data dari database (otomatis menghapus foto di tabel bukti_foto karena cascade)
    $delete = mysqli_query($koneksi, "DELETE FROM dpo_kasus WHERE id = '$id'");

    if ($delete) {
        echo "<script>
                alert('Data DPO Berhasil Dihapus Bersih!');
                window.location='dashboard.php';
              </script>";
    } else {
        echo "Gagal menghapus data: " . mysqli_error($koneksi);
    }
} else {
    header("location: dashboard.php");
}
?>