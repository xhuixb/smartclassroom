<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

//fem la connexió
//rebem les dades

session_start();


//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//aniversaris
$query = "select concat(ga04_nom,' ',ga04_cognom1,' ',ga04_cognom2) as profe from ga04_professors,ga17_professors_curs"
        . " where ga17_codi_curs=" . $_SESSION['curs_actual'] . " and ga04_suspes='0' and month(ga04_data_naixement)=month(now()) and day(ga04_data_naixement)=day(now()) and ga04_codi_prof=ga17_codi_professor";

//echo $query;

$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    // desen els perfils del profe en un array
    echo "<h4>Avui és l'aniversari del nostre/a company/a: </h4>";

    while ($row = $result->fetch_assoc()) {
        echo '<h3>' . $row['profe'] . '</h3>';
        echo '<h4><strong>Per molts anys!!</strong></h4>';
        echo '<hr>';
    }
}

//dates clau


$query = "select ga41_descripcio as descripcio,ga41_data_clau as dataclau from ga41_dates_clau where ga41_curs=" . $_SESSION['curs_actual'] . " and ga41_data_inici_publi<=date(now()) and ga41_data_fi_publi>=date(now()) ";


//echo $query;

$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    // desen els perfils del profe en un array
   
    while ($row = $result->fetch_assoc()) {
        if($row['dataclau']==''){
            $dataClau="indefinida";
        }else{
            $dataClau=$row['dataclau'];
        }
        echo '<h3 style="text-decoration: underline;">Data clau: ' . $dataClau . '</h3>';
        echo $row['descripcio'];
        echo '<hr>';
    }
}



$conn->close();
