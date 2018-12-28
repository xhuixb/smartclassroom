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

$datesFestivesString = $_POST['datesFestivesString'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//modifiquem els dies festius
$query = "update ga38_config_curs set ga38_festius='" . $datesFestivesString . "' where ga38_codi_curs=".$_SESSION['curs_actual'];

//executem
$conn->query($query);

//tanquem
$conn->close();
