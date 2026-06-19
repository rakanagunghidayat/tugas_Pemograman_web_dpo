<?php
require('fpdf/fpdf.php');
include('koneksi.php');

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Header PDF
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'LAPORAN DAFTAR BURONAN AKTIF (DPO)', 0, 1, 'C');
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 8, 'Sistem Pemantauan dan Pelacakan Data Terintegrasi', 0, 1, 'C');
$pdf->Line(10, 28, 200, 28);
$pdf->Ln(10);

// Header Tabel PDF
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(10, 10, 'NO', 1, 0, 'C');
$pdf->Cell(45, 10, 'NAMA BURONAN', 1, 0, 'C');
$pdf->Cell(55, 10, 'KASUS KEJAHATAN', 1, 0, 'C');
$pdf->Cell(50, 10, 'PASAL', 1, 0, 'C');
$pdf->Cell(30, 10, 'STATUS', 1, 1, 'C');

// Isi Tabel Data dari Database
$pdf->SetFont('Arial', '', 10);
$query = mysqli_query($koneksi, "SELECT * FROM dpo_kasus ORDER BY id DESC");
$no = 1;

while ($data = mysqli_fetch_assoc($query)) {
    $statusText = ($data['status'] == 1) ? 'TERTANGKAP' : 'BURON';
    
    $pdf->Cell(10, 10, $no++, 1, 0, 'C');
    $pdf->Cell(45, 10, $data['nama_dpo'], 1, 0, 'L');
    $pdf->Cell(55, 10, $data['kasus_kejahatan'], 1, 0, 'L');
    $pdf->Cell(50, 10, $data['pasal'], 1, 0, 'L');
    $pdf->Cell(30, 10, $statusText, 1, 1, 'C');
}

$pdf->Output('I', 'Laporan_Seluruh_Data_DPO.pdf');
?>