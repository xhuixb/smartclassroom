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

$query = "select ga12_cotutor as codi, concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as nom from"
        . " ga04_professors,ga12_alumnes_curs where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_id_alumne=" . $codiAlumne . " and ga12_cotutor=ga04_codi_prof";

$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    //hi ha cotutor
    $row = $result->fetch_assoc();
    echo "1<#>" . $row['codi'] . "<#>" . $row['nom'];
} else {
    //no hi ha cotutor
    echo "0<#><#>";
}

$result->close();
$conn->close();
