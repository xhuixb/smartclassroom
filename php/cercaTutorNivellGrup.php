<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();
//$_SESSION['curs_actual'] = 3;
//$_SESSION['prof_actual'] = 0;
$nivell = $_POST['nivell'];
$grup = $_POST['grup'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


$query = "select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as tutor,ga04_codi_prof as coditutor from ga04_professors,ga29_tutors_curs"
        . " where ga29_curs=" . $_SESSION['curs_actual'] . " and ga29_nivell=" . $nivell . " and ga29_grup=" . $grup . " and ga29_tutor=ga04_codi_prof";


//executem la sentència sql

$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {

    //hi ha tutor assignat
    $row=$result->fetch_assoc();

    echo '<h3 data-codi-tutor="'.$row['coditutor'].'">Tutor/a :' . $row['tutor'] . '</h3>';
} else {
    //no hi ha tutor assignat
    echo '<h3 data-codi-tutor="">No hi ha tutor</h3>';
}

$result->close();
$conn->close();
