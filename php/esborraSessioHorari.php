<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

//fem la connexió

session_start();


if ($_POST['codiProf'] == '') {
    $codiprof = $_SESSION['prof_actual'];
} elseif ($_POST['codiProf'] != '' && $_POST['tipusSubs'] == '1') {
    $codiprof = $_POST['codiProf'];
} elseif ($_POST['codiProf'] != '' && $_POST['tipusSubs'] == '2') {
    $codiprof = $_SESSION['prof_actual'];
}



$dia = $_POST['dia'];
$hora = $_POST['hora'];

$esguardia = $_POST['esguardia'];

//fem la connexió a la base de dades per totes les consultes que ens caldran
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");
//esborrem les faltes d'ordre anteriors d'aquesta assistència
$query = "delete from ga31_faltes_ordre"
        . " where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and "
        . "ga31_codi_professor=" . $codiprof . " and "
        . "ga31_dia='" . $dia . "' and "
        . "ga31_hora_inici ='" . $hora . "' and "
        . "ga31_es_sessio=1";


//executem la query
$conn->query($query);


//esborrem els comentaris
$query = "delete from ga43_comentaris_sessio"
        . " where ga43_codi_curs=" . $_SESSION['curs_actual'] . " and "
        . "ga43_codi_professor=" . $codiprof . " and "
        . "ga43_dia='" . $dia . "' and "
        . "ga43_hora_inici='" . $hora . "' and "
        . "ga43_es_sessio=1";


//executem la query
$conn->query($query);

//esborrem l'assistència anterior detall
$query = "delete from ga15_cont_presencia "
        . "where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and "
        . "ga15_codi_professor=" . $codiprof . " and "
        . "ga15_dia='" . $dia . "' and "
        . "ga15_hora_inici ='" . $hora . "'";

//executem la query
$conn->query($query);


////esborrem l'assistència anterior capçalera

$query = "delete from ga28_cont_presencia_cap "
        . "where ga28_codi_curs=" . $_SESSION['curs_actual'] . " and "
        . "ga28_professor=" . $codiprof . " and "
        . "ga28_dia='" . $dia . "' and "
        . "ga28_hora='" . $hora . "'";

//executem la query
$conn->query($query);

$conn->close();
