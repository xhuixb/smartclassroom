<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require('../../fpdf/fpdf.php');

//es crea l'object P portrati mm milímetre A4
$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,iconv('UTF-8', 'windows-1252', '¡Hola, Mundo!'));
$pdf->Cell(60,10,'Hecho con FPDF.',0,1,'C');
$pdf->Output();
?>