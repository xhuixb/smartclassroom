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
$grups = $_POST['grups'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");
$totsEsborrats = true;

for ($i = 0; $i < count($grups); $i++) {
//eliminem les dades del detall
    //comprovem si aquest grup té horaris associats
    $queryHoraris = "select count(*) as conta from ga26_horaris_docents where ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_grup=" . $grups[$i] . " and ga26_tipus_grup=1";

    $result = $conn->query($queryHoraris);

    if (!$result)
        die($conn->error);

    $row = $result->fetch_assoc();

    if ($row['conta'] != '0') {
        //té horaris associats no es pot esborrar
        $esEsborrable = false;
        $totsEsborrats = false;
    } else {
        $esEsborrable = true;
    }

    if ($esEsborrable == true) {
        //esborrem
        $query = "delete from ga24_grups_profes_det where ga24_codi_grup=" . $grups[$i];
        $conn->query($query);


        //eliminem les dades de la capçalera 
        $query = "delete from ga23_grups_profes_cap where ga23_codi_grup=" . $grups[$i];
        $conn->query($query);
    }
}

if (count($grups) == 0) {
    //no hi havia cap grup per esborrar
    echo 0;
} elseif ($totsEsborrats == true) {
    //s'han esborrat tots
    echo 1;
} else {
    //alguns no s'han esborrat
    echo 2;
}

$conn->close();
