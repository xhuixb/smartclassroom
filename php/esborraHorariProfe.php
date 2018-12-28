<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';


session_start();

//recollim les dades
$profe = $_POST['profe'];
$dia = $_POST['dia'];
$hora = $_POST['hora'];

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//esborrem l'horari anterior

$query="delete from ga26_horaris_docents where ga26_codi_curs=".$_SESSION['curs_actual']." and ga26_codi_professor=".$profe." and ga26_dia_setmana=".$dia." and ga26_hora_inici='".$hora."'";

//executem la sentència sql
$conn->query($query);

$conn->close();