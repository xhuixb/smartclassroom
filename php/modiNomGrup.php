<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$nouNom = str_replace("'", "''",$_POST['nouNom']);
$codiGrup = $_POST['codiGrup'];

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


$query = "update ga23_grups_profes_cap set ga23_nom_grup='" . $nouNom . "' where ga23_codi_grup=" . $codiGrup;


$result = $conn->query($query);


if (!$result)
    die($conn->error);

$conn->close();
