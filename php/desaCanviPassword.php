<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

session_start();

//obtenim el professor de les variables de sessió i la nova contrasentya que ens arriba del client
$professor=$_SESSION['prof_actual'];
$nova=$_POST['nova'];

//fem la connexió a la base de dades per totes les consultes que ens caldran
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

$query="update ga04_professors set ga04_password='".$nova."' where ga04_codi_prof=".$professor;
//executem la query

$conn->query($query);

//tanquem la connexió
$conn->close();
