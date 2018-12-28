<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "select ga03_codi_curs as codicurs,ga03_descripcio as descrcurs from ga03_curs where ga03_actual='1'";

//executem la consulta
$result = $conn->query($query);

$cont = 0;
if (!$result)
    die($conn->error);

$row = $result->fetch_assoc();

echo '<input id="cursActual" type="text" class="form-control" value="'.$row['descrcurs'].'" data-codi-cus="'.$row['codicurs'].'" readonly>';



$result->close();

$conn->close();
