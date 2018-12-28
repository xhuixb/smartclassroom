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
$codialumne=$_POST['codialumne'];
$codiprof=$_POST['codiprof'];
$dia=$_POST['dia'];
$hora=$_POST['hora'];
$estat=$_POST['estat'];
$codifalta=$_POST['codifalta'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

$query="update ga31_faltes_ordre set ga31_estat=".$estat.
        " where ga31_id=".$codifalta;

//echo $query;

$result = $conn->query($query);


if (!$result)
    die($conn->error);

$conn->close();