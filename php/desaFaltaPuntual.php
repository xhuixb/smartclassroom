<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$alumne = $_POST['alumne'];
$dia = $_POST['dia'];
$hora = $_POST['hora'];
$tipus = $_POST['tipus'];
$enviaPares = $_POST['enviaPares'];
$enviaTutor = $_POST['enviaTutor'];
$motiu=$_POST['motiu'];

$diaFormSql = date("Y-m-d", strtotime($dia));
$hora = $hora;

$motiu = str_replace("'", "''", $motiu);


//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


$query = "insert into ga31_faltes_ordre (ga31_codi_curs,ga31_alumne,ga31_codi_professor,ga31_dia,ga31_hora_inici,ga31_tipus_falta,ga31_estat,ga31_motiu,ga31_es_sessio,ga31_just_tutor,ga31_just_resp,ga31_assignatura) values "
        . "(".$_SESSION['curs_actual'].",".$alumne.",".$_SESSION['prof_actual'].",'" . $diaFormSql . "','" . $hora . "'," . $tipus . ",1,'" . $motiu . "',0,'" . $enviaTutor . "','" . $enviaPares . "',null)";
//executem la query
$conn->query($query);


$conn->close();
