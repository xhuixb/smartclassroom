<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

//fem la connexió

session_start();

$dataSessio = $_POST['dataSessio'];
$horaProfe = $_POST['horaProfe'];
$conta = $_POST['conta'];
$max=$_POST['max'];

//fem la connexió a la base de dades per totes les consultes que ens caldran
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


$horesProfeArray = explode('<#>', $horaProfe);

//esborrem les faltes d'ordre anteriors d'aquesta assistència
$query = "delete from ga31_faltes_ordre"
        . " where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and "
        . "ga31_codi_professor=" . $horesProfeArray[1] . " and "
        . "ga31_dia='" . $dataSessio . "' and "
        . "ga31_hora_inici ='" . $horesProfeArray[0] . "' and "
        . "ga31_es_sessio=1";


//executem la query
$conn->query($query);


//esborrem els comentaris
$query = "delete from ga43_comentaris_sessio"
        . " where ga43_codi_curs=" . $_SESSION['curs_actual'] . " and "
        . "ga43_codi_professor=" . $horesProfeArray[1] . " and "
        . "ga43_dia='" . $dataSessio . "' and "
        . "ga43_hora_inici='" . $horesProfeArray[0] . "' and "
        . "ga43_es_sessio=1";


//executem la query
$conn->query($query);

//esborrem l'assistència anterior detall
$query = "delete from ga15_cont_presencia "
        . "where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and "
        . "ga15_codi_professor=" . $horesProfeArray[1] . " and "
        . "ga15_dia='" . $dataSessio . "' and "
        . "ga15_hora_inici ='" . $horesProfeArray[0] . "'";

//executem la query
$conn->query($query);


////esborrem l'assistència anterior capçalera

$query = "delete from ga28_cont_presencia_cap "
        . "where ga28_codi_curs=" . $_SESSION['curs_actual'] . " and "
        . "ga28_professor=" . $horesProfeArray[1] . " and "
        . "ga28_dia='" . $dataSessio . "' and "
        . "ga28_hora='" . $horesProfeArray[0] . "'";

//executem la query
$conn->query($query);

$percent= round((($conta+1)/$max)*100,2);

echo '<div class="progress">';
echo '<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100" style="width:'.$percent.'%">';
echo $percent.'% Completat';
echo '</div>';
echo '</div >';

sleep(1);
$conn->close();
