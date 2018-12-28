<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require('../../fpdf/fpdf.php');
require '../../classes/Databases.php';

//ens conenctem a la base de dades
//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");


$sql="select * from ga04_professor";

//es crea l'object P portrati mm milímetre A4
$pdf = new FPDF('P','mm','A4');
$pdf->SetFont('Arial','',10);

$attr = array('titleFontSize'=>18, 'titleText'=>'First Example Title.');

$pdf->mysql_report($sql, false, $attr );


$pdf->Output();