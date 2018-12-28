<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$nouEstat = $_POST['nouEstat'];
$codiProf = $_POST['codiProf'];


$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "update ga04_professors set ga04_suspes='" . $nouEstat . "' where ga04_codi_prof=" . $codiProf;

$conn->query($query);


$conn->close();
