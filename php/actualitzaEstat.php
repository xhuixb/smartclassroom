<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió
//rebem les dades

session_start();



$alumne = $_POST['alumne'];
$professor = $_POST['professor'];
$dia = $_POST['dia'];
$hora = $_POST['hora'];
$pos = $_POST['pos'];
$checkComunica=$_POST['checkComunica'];
$diaSql = date("Y-m-d", strtotime($dia));

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

//decidim els valors a actualitzar

if ($pos == 1) {
    $present = 1;
    $absent = 0;
    $retard = 0;
} else if ($pos == 2) {
    $present = 0;
    $absent = 1;
    $retard = 0;
} else {
    $present = 0;
    $absent = 0;
    $retard = 1;
}


//construim la query

$query = "update ga15_cont_presencia set ga15_check_present=" . $present . ", ga15_check_absent=" . $absent . ", ga15_check_retard=" . $retard.",ga15_check_comunica='".$checkComunica."'"
        . " where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and ga15_codi_professor=" . $professor . " and"
        . " ga15_dia='" . $diaSql . "' and ga15_hora_inici='" . $hora . "'";


//executem la query
$conn->query($query);

//tanquem la connexió

$conn->close();
