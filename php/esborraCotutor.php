<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$codiAlumne = $_POST['codiAlumne'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "update ga12_alumnes_curs set ga12_cotutor=null where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_id_alumne=" . $codiAlumne;

$conn->query($query);
$conn->close();
