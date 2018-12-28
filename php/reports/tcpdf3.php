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

$alumne = $_GET['codiAlumne'];

//$alumne = $_GET['alumne'];
//ens connectem a la base de dades
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//anem a buscarm, en primer lloc, les dades de l'alumne

$query = "select ga11_id_alumne as codialumne,ga11_nom as nomalumne,ga11_cognom1 as cognom1alumne,ga11_cognom2 as cognom2alumne, ga11_mail1 as mail1alumne,ga11_mail2 as mail2alumne,ga11_check_comunica as checkcomunica,"
        . "ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,"
        . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors,ga29_tutors_curs,ga12_alumnes_curs where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_id_alumne=codialumne and ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga29_tutor=ga04_codi_prof) as nomtutor"
        . " from ga11_alumnes,ga06_nivell,ga07_grup,ga12_alumnes_curs"
        . " where ga12_codi_curs=".$_SESSION['curs_actual']." and ga12_id_alumne=" . $alumne . " and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and ga12_id_alumne=ga11_id_alumne";


//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);

//ens posem en les dades dels alumnes
$row = $result->fetch_assoc();

//recuperem les dades de l'alumne
$codiAlumne = $row['codialumne'];
$nomAlumne = $row['nomalumne'];
$cognom1Alumne = $row['cognom1alumne'];
$cognom2Alumne = $row['cognom2alumne'];
$nivell = $row['nivell'];
$grup = $row['grup'];
$mail1Alumne = $row['mail1alumne'];
$mail2Alumne = $row['mail2alumne'];
$checkComunica = $row['checkcomunica'];
$nomTutor = $row['nomtutor'];

if ($checkComunica == '1') {
    $comunicaText = 'SÍ';
} else {
    $comunicaText = 'NO';
}

$content = '';

$content .= '<h1>Fitxa Alumne</h1>';

//dades de l'alumne
$content .= '<table cellspacing="0" cellpadding="1" border="1">';
$content .= '<tbody>';
$content .= '<tr>';
$content .= '<td style="width:80mm;">Nom: ' . $nomAlumne . '</td>';
$content .= '<td style="width:80mm;">Nivell: ' . $nivell . '</td>';
$content .= '</tr>';
$content .= '<tr>';
$content .= '<td style="width:80mm;">Primer Cognom: ' . $cognom1Alumne . '</td>';
$content .= '<td style="width:80mm;">Grup: ' . $grup . '</td>';
$content .= '</tr>';
$content .= '<tr>';
$content .= '<td style="width:80mm;">Segon Cognom: ' . $cognom2Alumne . '</td>';
$content .= '<td style="width:80mm;">Tutor: ' . $nomTutor . '</td>';
$content .= '</tr>';
$content .= '<tr>';
$content .= '<td style="width:80mm;">Mail Contacte: ' . $mail1Alumne . '</td>';
$content .= '<td style="width:80mm;">Comunicacions actives: ' . $comunicaText . '</td>';
$content .= '</tr>';
$content .= '<tr>';
$content .= '<td style="width:80mm;">Mail Contacte: ' . $mail2Alumne . '</td>';
$content .= '<td style="width:80mm;"></td>';
$content .= '</tr>';

//tanquem cos i taula
$content .= '</tbody>';
$content .= '</table>';
$content .= '<p></p>';



$query = "select ga22_nom_falta as tipusfalta, count(*)  as total from ga22_tipus_falta,ga31_faltes_ordre "
        . "where ga31_tipus_falta=ga22_codi_falta and ga31_alumne=" . $alumne . " and ga31_codi_curs=" . $_SESSION['curs_actual'] . " group by ga31_tipus_falta ";


//dades de falte d'ordre resumides
$content .= "<h2>Resum faltes d'ordre</h2>";

//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);


if ($result->num_rows > 0) {
    $content .= '<table cellspacing="0" cellpadding="1" border="1">';
    $content .= '<thead>';
    $content .= '<tr style="font-weight:bold">';
    $content .= '<th style="width:50mm;">Tipus falta</th>';
    $content .= '<th style="width:25mm;">Quantitat</th>';
    $content .= '</tr>';
    $content .= '</thead>';
    $content .= '<tbody>';
    while ($row = $result->fetch_assoc()) {
        $content .= '<tr>';
        $content .= '<td style="width:50mm;">' . $row['tipusfalta'] . '</td>';
        $content .= '<td style="width:25mm;">' . $row['total'] . '</td>';
        $content .= '</tr>';
    }
    $content .= '</tbody>';
    $content .= '</table>';
}
//tanquem cos i taula


$content .= '<p></p>';

//detall de les faltes d'ordre

$query = "select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as professor, ga31_dia as dia,ga31_hora_inici as hora,ga22_nom_falta as tipusfalta,ga31_motiu as motiu"
        . " from ga04_professors,ga31_faltes_ordre,ga22_tipus_falta"
        . " where ga31_codi_professor=ga04_codi_prof and ga31_tipus_falta=ga22_codi_falta and ga31_alumne=" . $alumne . " and"
        . " ga31_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)"
        . " order by dia desc,hora asc";

//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);


if ($result->num_rows > 0) {
    //capçalera de la taula
    //dades de falte d'ordre resumides
    $content .= "<h2>Detall faltes d'ordre</h2>";

    $content .= '<table cellspacing="0" cellpadding="1" border="1">';
    $content .= '<thead>';
    $content .= '<tr style="font-weight:bold">';
    $content .= '<th style="width:50mm;">Professor</th>';
    $content .= '<th style="width:25mm;">Dia</th>';
    $content .= '<th style="width:20mm;">Hora</th>';
    $content .= '<th style="width:25mm;">Tipus falta</th>';
    $content .= '<th style="width:60mm;">Motiu</th>';
    $content .= '</tr>';
    $content .= '</thead>';
    $content .= '<tbody>';


    while ($row = $result->fetch_assoc()) {
        $content .= '<tr>';
        $content .= '<td style="width:50mm;">' . $row['professor'] . '</td>';
        $content .= '<td style="width:25mm;">' . $row['dia'] . '</td>';
        $content .= '<td style="width:20mm;">' . $row['hora'] . '</td>';
        $content .= '<td style="width:25mm;">' . $row['tipusfalta'] . '</td>';
        $content .= '<td style="width:60mm;">' . $row['motiu'] . '</td>';
        $content .= '</tr>';
    }
    $content .= '</tbody>';
    $content .= '</table>';
}

//tanquem cos i taula

$content .= '<p></p>';
//dades de falte d'absències resumides


$query = "select substr(ga15_dia,1,4) as anyabsencia ,substr(ga15_dia,6,2) as mesabsencia,"
        . "(select count(*)  from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and ga15_check_absent='1' and substr(ga15_dia,6,2)=mesabsencia and substr(ga15_dia,1,4)=anyabsencia group by substr(ga15_dia,6,2),substr(ga15_dia,1,4) order by substr(ga15_dia,6,2) desc,substr(ga15_dia,1,4) desc) as totalabsencia,"
        . "(select count(*)  from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and ga15_check_absent='1' and ga15_check_justificat='1' and substr(ga15_dia,6,2)=mesabsencia and substr(ga15_dia,1,4)=anyabsencia group by substr(ga15_dia,6,2),substr(ga15_dia,1,4) order by substr(ga15_dia,6,2) desc,substr(ga15_dia,1,4) desc) as justificades,"
        . "(select count(*)  from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and ga15_check_absent='1' and ga15_check_justificat='0' and substr(ga15_dia,6,2)=mesabsencia and substr(ga15_dia,1,4)=anyabsencia group by substr(ga15_dia,6,2),substr(ga15_dia,1,4) order by substr(ga15_dia,6,2) desc,substr(ga15_dia,1,4) desc) as nojustificades,"
        . "(select count(*)  from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and ga15_check_retard='1' and substr(ga15_dia,6,2)=mesabsencia and substr(ga15_dia,1,4)=anyabsencia group by substr(ga15_dia,6,2),substr(ga15_dia,1,4)) as retards"
        . " from ga15_cont_presencia as prova where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and (ga15_check_absent='1' or ga15_check_retard='1') group by substr(ga15_dia,1,4),substr(ga15_dia,6,2) order by anyabsencia desc,mesabsencia desc;";


//dades de falte d'ordre resumides
$content .= "<h2>Absències i retards</h2>";


//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);


if ($result->num_rows > 0) {

    $content .= '<table cellspacing="0" cellpadding="1" border="1">';
    $content .= '<thead>';
    $content .= '<tr style="font-weight:bold">';
    $content .= '<th style="width:30mm;">Any</th>';
    $content .= '<th style="width:30mm;">Mes</th>';
    $content .= '<th style="width:30mm;">Total abs.</th>';
    $content .= '<th style="width:30mm;">Justificades</th>';
    $content .= '<th style="width:30mm;">Sense justifi</th>';
    $content .= '<th style="width:30mm;">Retards</th>';
    $content .= '</tr>';
    $content .= '</thead>';


    $content .= '<tbody>';
    while ($row = $result->fetch_assoc()) {
        switch ($row['mesabsencia']) {
            case '01':
                $mesText = 'Gener';
                break;
            case '02':
                $mesText = 'Febrer';
                break;
            case '03':
                $mesText = 'Març';
                break;
            case '04':
                $mesText = 'Abril';
                break;
            case '05':
                $mesText = 'Maig';
                break;
            case '06':
                $mesText = 'Juny';
                break;
            case '07':
                $mesText = 'Juliol';
                break;
            case '08':
                $mesText = 'Agost';
                break;
            case '09':
                $mesText = 'Setembre';
                break;
            case '10':
                $mesText = 'Octubre';
                break;
            case '11':
                $mesText = 'Novembre';
                break;
            case '12':
                $mesText = 'Desembre';
                break;
        }

        //si és null posem un 0
        if ($row['totalabsencia'] == '') {
            $totalAbsencia = 0;
        } else {
            $totalAbsencia = $row['totalabsencia'];
        }

        //si és null posem un 0
        if ($row['justificades'] == '') {
            $justificades = 0;
        } else {
            $justificades = $row['justificades'];
        }

        //si és null posem un 0
        if ($row['nojustificades'] == '') {
            $nojustificades = 0;
        } else {
            $nojustificades = $row['nojustificades'];
        }

        //si és null posem un 0
        if ($row['retards'] == '') {
            $retards = 0;
        } else {
            $retards = $row['retards'];
        }

        $content .= '<tr>';
        $content .= '<td style="width:30mm;">' . $row['anyabsencia'] . '</td>';
        $content .= '<td style="width:30mm;">' . $mesText . '</td>';
        $content .= '<td style="width:30mm;">' . $totalAbsencia . '</td>';
        $content .= '<td style="width:30mm;">' . $justificades . '</td>';
        $content .= '<td style="width:30mm;">' . $nojustificades . '</td>';
        $content .= '<td style="width:30mm;">' . $retards . '</td>';
        $content .= '</tr>';
    }
    $content .= '</tbody>';
    $content .= '</table>';
}

//tanquem cos i taula


$content .= '<p></p>';

$result->close();
$conn->close();

$pdf = new GeneraPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->configIncial("Servei d'informes");

$pdf->writeHTML($content, true, false, false, false);
//$pdf->writeHTML($content, true, 0, true, 0);
$pdf->lastPage();
$pdf->Output('report.pdf', 'I');
