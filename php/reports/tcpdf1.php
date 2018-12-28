<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../../classes/GeneraPDF.php';
require '../../classes/Databases.php';
//fem la connexió

session_start();

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);



//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as docent, ga04_mail as mail,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup"
        . " from ga04_professors,ga29_tutors_curs,ga06_nivell,ga07_grup where  ga29_tutor=ga04_codi_prof and ga29_nivell=ga06_codi_nivell and ga29_grup=ga07_codi_grup and ga29_curs=".$_SESSION['curs_actual']
        . " order by ga29_nivell,ga29_grup";



$result = $conn->query($query);


if (!$result)
    die($conn->error);

$content = '';
$content.='<h1>Relació de tutors</h1>';


//construim capçalera de la taula
$content .= '<br>';
$content .= '<table cellspacing="0" cellpadding="1" border="1">';
$content .= '<thead>';
$content .= '<tr>';
$content .= '<th style="width:25mm;"><strong>Nivell</strong></th>';
$content .= '<th style="width:25mm;"><strong>Grup</strong></th>';
$content .= '<th style="width:70mm;"><strong>Docent</strong></th>';
$content .= '<th style="width:70mm;"><strong>Mail</strong></th>';
$content .= '</tr>';
$content .= '</thead>';

if ($result->num_rows > 0) {
    $content .= '<tbody>';
    while ($row = $result->fetch_assoc()) {
        $content .= '<tr>';
        $content .= '<td style="width:25mm;">' . $row['nivell'] . '</td>';
        $content .= '<td style="width:25mm;">' . $row['grup'] . '</td>';
        $content .= '<td style="width:70mm;">' . $row['docent'] . '</td>';
        $content .= '<td style="width:70mm;">' . $row['mail'] . '</td>';
        $content .= '</tr>';
    }
}

//tanquem cos i taula
$content .= '</tbody>';
$content .= '</table>';


$pdf = new GeneraPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->configIncial("Servei d'informes");

$pdf->writeHTML($content, true, false, false, false);
//$pdf->writeHTML($content, true, 0, true, 0);
$pdf->lastPage();
$pdf->Output('report.pdf', 'I');

?>

<!--<!doctype html>
<html>
    <head>
        <title>Gestió d'alumnes</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="../../jquery/gestioAlumnesjquery.js"></script>
        <script src="../../jquery/sessionsjquery.js"></script>
        <script src="../../jquery/funcionsjquery.js"></script>
        <link rel="stylesheet" href="../../css/gestioAlumnes.css">
    </head>
</html>-->
