<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';


session_start();

//recollim les dades
$codiProf = $_POST['codiProf'];
$codiDia = $_POST['codiDia'];
$horaInici = $_POST['horaInici'];
$nivell = $_POST['nivell'];
$grup = $_POST['grup'];
$tipusGrup = $_POST['tipusGrup'];
$assignatura = $_POST['assignatura'];
$aula = $_POST['aula'];
$horaLectiva = $_POST['horaLectiva'];
$tipusHora = $_POST['tipusHora'];
$tipusGuardia = $_POST['tipusGuardia'];


$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//esborrem l'horari anterior

$query="delete from ga26_horaris_docents where ga26_codi_curs=".$_SESSION['curs_actual']." and ga26_codi_professor=".$codiProf." and ga26_dia_setmana=".$codiDia." and ga26_hora_inici='".$horaInici."'";

//executem la sentència sql
$conn->query($query);

//echo $query;

if ($horaLectiva == '1') {
    //hora lectiva
    //comprovem grup i aula
    if ($grup == '') {
        $grup = 'null';
        $tipusGrup = 0;
    }

    if ($aula == '') {
        $aula = 'null';
    }

    //construim la query
    $query = "INSERT INTO ga26_horaris_docents (ga26_codi_curs,ga26_codi_professor,ga26_dia_setmana,ga26_hora_inici,ga26_nivell,ga26_grup,ga26_tipus_grup,ga26_is_lectiva,ga26_tipus_carrec,ga26_tipus_guardia,ga26_codi_assignatura,ga26_codi_aula,ga26_es_guardia,ga26_es_carrec)"
            . " VALUES (" . $_SESSION['curs_actual'] . "," . $codiProf . "," . $codiDia . ",'" . $horaInici . "'," . $nivell . "," . $grup . "," . $tipusGrup . ",1,null,null," . $assignatura . "," . $aula . ",0,0)";
} else {
    //no és una hora lectiva
    if ($tipusHora == '') {
        //és una guàrdia
        $query = "INSERT INTO ga26_horaris_docents (ga26_codi_curs,ga26_codi_professor,ga26_dia_setmana,ga26_hora_inici,ga26_nivell,ga26_grup,ga26_tipus_grup,ga26_is_lectiva,ga26_tipus_carrec,ga26_tipus_guardia,ga26_codi_assignatura,ga26_codi_aula,ga26_es_guardia,ga26_es_carrec)"
                . " VALUES (" . $_SESSION['curs_actual'] . "," . $codiProf . "," . $codiDia . ",'" . $horaInici . "',null,null,0,0,null,".$tipusGuardia.",null,null,1,0)";
    } else {
        //és un càrrec o reunió
        $query = "INSERT INTO ga26_horaris_docents (ga26_codi_curs,ga26_codi_professor,ga26_dia_setmana,ga26_hora_inici,ga26_nivell,ga26_grup,ga26_tipus_grup,ga26_is_lectiva,ga26_tipus_carrec,ga26_tipus_guardia,ga26_codi_assignatura,ga26_codi_aula,ga26_es_guardia,ga26_es_carrec)"
                . " VALUES (" . $_SESSION['curs_actual'] . "," . $codiProf . "," . $codiDia . ",'" . $horaInici . "',null,null,0,0,".$tipusHora.",null,null,null,0,1)";
    }
}


//executem la sentència sql
$conn->query($query);

$conn->close();

