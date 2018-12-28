<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$nivell = $_POST['nivell'];
$grup = $_POST['grup'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


$query = "select ga29_tutor as codi, concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio"
        . " from ga04_professors,ga29_tutors_curs"
        . " where ga29_tutor=ga04_codi_prof and ga29_curs=" . $_SESSION['curs_actual'] . " and ga29_nivell=" . $nivell . " and ga29_grup=" . $grup;



$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "1<#>" . $row['codi'] . "<#>" . $row['descripcio'];
} else {

    echo "0<#><#>";
}

$result->close();
$conn->close();
