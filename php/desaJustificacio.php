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
$codiJustifi = $_POST['codiJustifi'];
$motiuJustifi = $_POST['motiuJustifi'];
$alumne = $_POST['alumne'];
$dia = $_POST['dia'];
$hora = $_POST['hora'];
$prof=$_POST['prof'];
$totDia=$_POST['totDia'];

//treguem els apostrof
$motiuJustifi=str_replace("'","''",$motiuJustifi);

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

//decidim si el codi està bè

if ($motiuJustifi == "") {
    $codiJustifi = "0";
} else {
    $codiJustifi = "1";
}


//decidim si justifiquem tot el dia
if($totDia==0){
    $totDiaSql=" and ga15_hora_inici='".$hora."'";
   
}else{
    $totDiaSql="";
   
}


$query = "update ga15_cont_presencia set ga15_check_justificat='" . $codiJustifi . "',ga15_motiu_justificat='" . $motiuJustifi . "'"
        . " where ga15_codi_curs=".$_SESSION['curs_actual']." and ga15_alumne=".$alumne." and ga15_dia='".$dia."'".$totDiaSql;
//modifiquem la justificació
//(select ga03_codi_curs from ga03_curs where ga03_actual='1')


echo $query;

$conn->query($query);


$conn->close();

