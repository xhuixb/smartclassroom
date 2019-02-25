<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../classes/Databases.php';


//fem la connexió

session_start();

//fem la connexió a la base de dades per totes les consultes que ens caldran
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


$codisDates=$_POST['codisDates'];


$codisDatesString= join(',', $codisDates);

$query="delete from ga41_dates_clau where ga41_id in (".$codisDatesString.")";

$result=$conn->query($query);



$conn->close();