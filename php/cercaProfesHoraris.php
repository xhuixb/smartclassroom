<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió
session_start();
$admin = $_SESSION['admin'];
$profe = $_SESSION['prof_actual'];


$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

if ($admin == '1') {

    $query = "select ga17_codi_professor as codi,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio from ga04_professors,ga17_professors_curs where ga04_codi_prof=ga17_codi_professor and ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) order by descripcio";
} else {

    $query = "select ga17_codi_professor as codi,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio from ga04_professors,ga17_professors_curs where ga04_codi_prof=ga17_codi_professor and ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga17_codi_professor=" . $profe . " order by descripcio";
}

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

echo '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="butDropprofeCreacioHorari">Tria Professor <span class="caret"></span></button>';
echo '<ul class="dropdown-menu" id="dropprofeCreacioHorari" style="height: 250px; overflow: auto">';

if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {

        echo '<li><a data-val="' . $row['codi'] . '" onclick="mostraprofeCreacioHorari(this);">' . $row['descripcio'] . '</a></li>';
    }
}

echo '</ul>';


$result->close();
$conn->close();


