<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';


//fem la connexió

session_start();

$id=$_POST['id'];

//fem la connexió a la base de dades per totes les consultes que ens caldran
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


$query = "select ga41_descripcio as descripcio from ga41_dates_clau where ga41_id=".$id;

$result = $conn->query($query);


if (!$result)
    die($conn->error);


$row = $result->fetch_assoc();

echo $row['descripcio'];

$result->close();
$conn->close();
