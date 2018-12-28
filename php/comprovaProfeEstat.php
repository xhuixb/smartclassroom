<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

session_start();


$codiProf = $_POST["codiProf"];
//$codiProf = 68;
//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "select ga04_suspes as suspes from ga04_professors where ga04_codi_prof=" . $codiProf;

$result = $conn->query($query);

if (!$result)
    die($conn->error);


$row = $result->fetch_assoc();

echo $row['suspes'];


$result->close();
$conn->close();
