<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$codiProfDesti = $_POST['codiProfDesti'];


//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "select count(*) as conta from ga26_horaris_docents where ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_codi_professor=" . $codiProfDesti;

$result = $conn->query($query);


if (!$result)
    die($conn->error);

$row = $result->fetch_assoc();
if ($row['conta'] != '0') {
    echo '0';
} else {
    echo '1';
}

$result->close();
$conn->close();
