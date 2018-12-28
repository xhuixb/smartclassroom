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

ini_set('max_execution_time', 30);

$nivell = $_GET["nivell"];
$grup = $_GET['grup'];
$dataInicial = $_GET['dataInicial'];
$dataFinal = $_GET['dataFinal'];
$professor = $_GET['professor'];
$nivellText = $_GET["nivellText"];
$grupText = $_GET['grupText'];
$professorNom = $_GET['professorNom'];
$nomesAbsents = $_GET['nomesAbsents'];
$nomesRetards = $_GET['nomesRetards'];

//decidim si s'ha de filtrar per nivell
if ($nivell != "") {
    $whereNivell = " and ga06_codi_nivell=" . $nivell;
} else {

    $whereNivell = "";
    $nivellText = "Tots";
}

//decidim si s'ha de filtrar per grup

if ($grup != "") {
    $whereGrup = " and ga07_codi_grup=" . $grup;
} else {
    $whereGrup = "";
    $grupText = "Tots";
}

//decidim si s'ha de filtar per data inicial
if ($dataInicial != "") {
    $dataInicialSql = date("Y-m-d", strtotime($dataInicial));
    $whereDataInicial = " and ga15_dia>='" . $dataInicialSql . "'";
} else {
    $whereDataInicial = "";
}

//decidim si s'ha de filtar per data inicial
if ($dataFinal != "") {
    $dataFinalSql = date("Y-m-d", strtotime($dataFinal));
    $whereDataFinal = " and ga15_dia<='" . $dataFinalSql . "'";
} else {
    $whereDataFinal = "";
}

//decidim si s'ha de filtrar per profe

if ($professor != "") {
    $whereProfessor = " and ga15_codi_professor=" . $professor;
} else {
    $whereProfessor = "";
    $professorNom = "Tots";
}



//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);



//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "select ga12_id_alumne as codialumne,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,"
        . "(select count(*) from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=ga12_id_alumne " . $whereDataInicial . $whereDataFinal . $whereProfessor . " group by ga15_alumne) as sessionstotal,"
        . "(select count(*) from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=ga12_id_alumne and ga15_check_absent=1 " . $whereDataInicial . $whereDataFinal . $whereProfessor . " group by ga15_alumne) as abstotal,"
        . "(select count(*) from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=ga12_id_alumne and ga15_check_absent=1 and ga15_check_justificat=1 " . $whereDataInicial . $whereDataFinal . $whereProfessor . " group by ga15_alumne) as abssijusti,"
        . "(select count(*) from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=ga12_id_alumne and ga15_check_absent=1 and ga15_check_justificat=0 " . $whereDataInicial . $whereDataFinal . $whereProfessor . " group by ga15_alumne) as absnojusti,"
        . "(select count(*) from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=ga12_id_alumne and ga15_check_retard=1 " . $whereDataInicial . $whereDataFinal . $whereProfessor . " group by ga15_alumne) as retards,"
        . "(select count(*) from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=ga12_id_alumne and ga15_check_absent=1 and ga15_check_justificat=0 " . $whereDataInicial . $whereDataFinal . $whereProfessor . " group by ga15_alumne)/(select count(*) from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=ga12_id_alumne " . $whereDataInicial . $whereDataFinal . " group by ga15_alumne)*100 as absentisme"
        . " from ga11_alumnes,ga12_alumnes_curs,ga06_nivell,ga07_grup"
        . " where ga12_codi_curs=" . $_SESSION['curs_actual'] . $whereNivell . $whereGrup
        . " and ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup order by alumne";

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

$content = '';

$content .= "<h2>Llistat d'absències i retards</h2>";

//construim capçalera de la taula
$content .= '<table cellspacing="0" cellpadding="1" border="1">';
$content .= '<thead>';
$content .= '<tr style="font-weight:bold">';
$content .= '<th style="width:30mm;">Nivell</th>';
$content .= '<th style="width:30mm;">Grup</th>';
$content .= '<th style="width:70mm;">Professor</th>';
$content .= '<th style="width:50mm;">Data Inicial</th>';
$content .= '<th style="width:50mm;">Data Final</th>';
$content .= '</tr>';
$content .= '</thead>';
$content .= '<tbody>';
$content .= '<tr>';
$content .= '<td style="width:30mm;">' . $nivellText . '</td>';
$content .= '<td style="width:30mm;">' . $grupText . '</td>';
$content .= '<td style="width:70mm;">' . $professorNom . '</td>';
$content .= '<td style="width:50mm;"><center>' . $dataInicial . '</center></td>';
$content .= '<td style="width:50mm;"><center>' . $dataFinal . '</center></td>';
$content .= '</tr>';
//tanquem cos i taula
$content .= '</tbody>';
$content .= '</table>';
$content .= '<p></p>';



//construim capçalera de la taula
$content .= '<table cellspacing="0" cellpadding="1" border="1">';
$content .= '<thead>';
$content .= '<tr style="font-weight:bold">';
$content .= '<th style="width:80mm;">Alumne</th>';
$content .= '<th style="width:25mm;">Nivell</th>';
$content .= '<th style="width:25mm;">Grup</th>';
$content .= '<th style="width:25mm;"><center>Sessions</center></th>';
$content .= '<th style="width:25mm;"><center>Abs Totals</center></th>';
$content .= '<th style="width:25mm;"><center>Abs Justif</center></th>';
$content .= '<th style="width:25mm;"><center>Abs No justif</center></th>';
$content .= '<th style="width:25mm;"><center>Retards</center></th>';
$content .= '<th style="width:25mm;"><center>Absentisme</center></th>';
$content .= '</tr>';
$content .= '</thead>';

if ($result->num_rows > 0) {

    $sessionstotal = 0;
    $abstotal = 0;
    $abssijusti = 0;
    $absnojusti = 0;
    $retards = 0;

    $content .= '<tbody>';
    while ($row = $result->fetch_assoc()) {


        if (($nomesAbsents === '0' && $nomesRetards === '0') ||
                ($nomesAbsents === '1' && (string) $row['abstotal'] !== '') ||
                ($nomesRetards === '1' && (string) $row['retards'] !== '') ||
                ($nomesAbsents === '1' && $nomesRetards === '1' && (string) $row['abstotal'] !== '' && (string) $row['retards'] !== '' )) {


            $sessionstotal += $row['sessionstotal'];
            $abstotal += $row['abstotal'];
            $abssijusti += $row['abssijusti'];
            $absnojusti += $row['absnojusti'];
            $retards += $row['retards'];

            if ($row['sessionstotal'] > 0) {
                if ($row['absnojusti'] != 0) {
                    $absentisme = $row['absnojusti'] / $row['sessionstotal'] * 100;
                } else {
                    $absentisme = 0;
                }
            } else {
                $absentisme = 0;
            }
            $content .= '<tr>';
            $content .= '<td style="width:80mm;">' . $row['alumne'] . '</td>';
            $content .= '<td style="width:25mm;">' . $row['nivell'] . '</td>';
            $content .= '<td style="width:25mm;">' . $row['grup'] . '</td>';
            $content .= '<td style="width:25mm;"><center>' . $row['sessionstotal'] . '</center></td>';
            $content .= '<td style="width:25mm;"><center>' . $row['abstotal'] . '</center></td>';
            $content .= '<td style="width:25mm;"><center>' . $row['abssijusti'] . '</center></td>';
            $content .= '<td style="width:25mm;"><center>' . $row['absnojusti'] . '</center></td>';
            $content .= '<td style="width:25mm;"><center>' . $row['retards'] . '</center></td>';
            $content .= '<td style="width:25mm;"><center>' . number_format($absentisme, 2) . '%' . '</center></td>';

            $content .= '</tr>';
        }
    }
    //posem la filera dels totals
    $content .= '<tr style="font-weight:bold">';
    $content .= '<td style="width:80mm;">TOTALS</td>';
    $content .= '<td style="width:25mm;"></td>';
    $content .= '<td style="width:25mm;"></td>';
    $content .= '<td style="width:25mm;"><center>' . $sessionstotal . '</center></td>';
    $content .= '<td style="width:25mm;"><center>' . $abstotal . '</center></td>';
    $content .= '<td style="width:25mm;"><center>' . $abssijusti . '</center></td>';
    $content .= '<td style="width:25mm;"><center>' . $absnojusti . '</center></td>';
    $content .= '<td style="width:25mm;"><center>' . $retards . '</center></td>';
    $content .= '<td style="width:25mm;"><center>' . number_format($absnojusti / $sessionstotal * 100, 2) . '%' . '</center></td>';
    $content .= '</tr>';
}
//tanquem cos i taula
$content .= '</tbody>';
$content .= '</table>';

$result->close();
$conn->close();

$pdf = new GeneraPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->configIncial("Servei d'informes");

$pdf->writeHTML($content, true, false, false, false);
//$pdf->writeHTML($content, true, 0, true, 0);
$pdf->lastPage();
$pdf->Output('report.pdf', 'I');

