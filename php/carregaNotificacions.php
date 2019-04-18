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
$query = "select concat(ga04_nom,' ',ga04_cognom1,' ',ga04_cognom2) as profe,month(ga04_data_naixement) as mes,day(ga04_data_naixement) as dia from ga04_professors,ga17_professors_curs"
        . " where ga17_codi_curs=" . $_SESSION['curs_actual'] . " and ga04_suspes='0' and ( (month(ga04_data_naixement)=month(now()) and day(ga04_data_naixement)=day(now())) or (month(ga04_data_naixement)=month(now()- INTERVAL 1 DAY) and day(ga04_data_naixement)=day(now()- INTERVAL 1 DAY)) or (month(ga04_data_naixement)=month(now()+ INTERVAL 1 DAY) and day(ga04_data_naixement)=day(now()+ INTERVAL 1 DAY))  ) and ga04_codi_prof=ga17_codi_professor";

//echo $query;

$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    // desen els perfils del profe en un array
    echo '<h3 style="text-decoration: underline;font-weight: bold;">Recordatori d&#39;aniversaris: </u></h3>';

    while ($row = $result->fetch_assoc()) {
        if ($row['mes'] == date("m") && $row['dia'] == date("d")) {
            echo "<h4>Avui és l'aniversari del nostre company/a</h4>";
            echo '<h4><strong>' . $row['profe'] . '</strong></h4>';
            echo '<h3><strong>Per molts anys!!</strong></h3>';
            echo '<hr>';
        } else if ($row['mes'] == date("m", time() - 60 * 60 * 24) && $row['dia'] == date("d", time() - 60 * 60 * 24)) {
            echo "<h4>Ahir va ser l'aniversari del nostre company/a</h4>";
            echo '<h4><strong>' . $row['profe'] . '</strong></h4>';
            echo '<h3><strong>Per molts anys!!</strong></h3>';
            echo '<hr>';
        }else{
            echo "<h4>Demà serà l'aniversari del nostre company/a</h4>";
            echo '<h4><strong>' . $row['profe'] . '</strong></h4>';
            echo '<h3><strong>Per molts anys!!</strong></h3>';
            echo '<hr>';
        }
    }
}

//dates clau


$query = "select ga41_descripcio as descripcio,ga41_data_clau as dataclau from ga41_dates_clau where ga41_curs=" . $_SESSION['curs_actual'] . " and ga41_data_inici_publi<=date(now()) and ga41_data_fi_publi>=date(now()) order by dataclau";


//echo $query;

$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    // desen els perfils del profe en un array

    while ($row = $result->fetch_assoc()) {
        if ($row['dataclau'] == '') {
            $dataClau = "indefinida";
        } else {
            $dataClau = substr($row['dataclau'], 8) . '/' . substr($row['dataclau'], 5, 2) . '/' . substr($row['dataclau'], 0, 4);
        }
        echo '<h3 style="text-decoration: underline;">Data clau: ' . $dataClau . '</h3>';
        echo $row['descripcio'];
        echo '<hr>';
    }
}



$conn->close();
