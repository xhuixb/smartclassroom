<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();
//$_SESSION['curs_actual'] = 3;
//$_SESSION['prof_actual'] = 0;

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

$query = "select distinct(concat(ga15_dia,' a les ',ga15_hora_inici)) as diahora,"
        . "(select count(*) from ga15_cont_presencia where ga15_codi_professor=".$_SESSION['prof_actual']." and ga15_codi_curs=".$_SESSION['curs_actual']." and ga15_dia=substring(diahora,1,10) and ga15_hora_inici=substring(diahora,18,8) and ga15_check_present='1') as totpresents,"
        . "(select count(*) from ga15_cont_presencia where ga15_codi_professor=".$_SESSION['prof_actual']." and ga15_codi_curs=".$_SESSION['curs_actual']." and ga15_dia=substring(diahora,1,10) and ga15_hora_inici=substring(diahora,18,8) and ga15_check_absent='1') as totabsents,"
        . "(select count(*) from ga15_cont_presencia where ga15_codi_professor=".$_SESSION['prof_actual']." and ga15_codi_curs=".$_SESSION['curs_actual']." and ga15_dia=substring(diahora,1,10) and ga15_hora_inici=substring(diahora,18,8) and ga15_check_retard='1') as totretards"
        . " from ga15_cont_presencia"
        . " where ga15_codi_curs=".$_SESSION['curs_actual']." and ga15_codi_professor=".$_SESSION['prof_actual']
        . " order by ga15_dia desc,ga15_hora_inici asc";

$result = $conn->query($query);


if (!$result)
    die($conn->error);


//construim capçalera de la taula
echo '<br>';
echo '<table id="taulaMevesSessions" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th>Dia/Hora</th>';
echo '<th>Presents</th>';
echo '<th>Absents</th>';
echo '<th>Retards</th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {
    echo '<tbody id="costaulaMevesSessions">';
    while ($row = $result->fetch_assoc()) {

        echo '<tr>';
        echo '<td class="col-sm-1">'.$row['diahora'].'</td>';
        echo '<td class="col-sm-1">' . $row['totpresents'] . '</td>';
        echo '<td class="col-sm-1">' . $row['totabsents'] . '</td>';
        echo '<td class="col-sm-1">' . $row['totretards'] . '</td>';
        echo '<tr>';
        echo '</tr>';
    }
}
//tanquem cos i taula
echo '</tbody>';
echo '</table>';

$result->close();
$conn->close();
