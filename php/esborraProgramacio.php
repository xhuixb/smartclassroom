<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$dia = $_POST['dia'];
$hora = $_POST['hora'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


//esborrem el missatge previ
$query = "delete from ga44_programacio_sessio where ga44_codi_curs=" . $_SESSION['curs_actual'] . " and ga44_professor=" . $_SESSION['prof_actual']
        . " and ga44_dia='" . $dia . "' and ga44_hora='" . $hora . "'";

//executem la query
$conn->query($query);

//tanquem la connexió
$conn->close();
