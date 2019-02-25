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


echo '<h2><u>Dades de connexió</u></h2>';
echo '<h4>Host: <strong>' . Databases2::$host . '</strong></h4>';
echo '<h4>Usuari: <strong>' . Databases2::$user . '</strong></h4>';
echo '<h4>Password: <strong>' . Databases2::$password . '</strong></h4>';
echo '<h4>Base de dades: <strong>' . Databases2::$dbase . '</strong></h4>';
echo '<br>';
echo '<h2><u>Taules de la base de dades</u></h2>';


$query = "SELECT table_name as taula FROM information_schema.tables where table_schema='" . Databases2::$dbase . "'";

$result = $conn->query($query);


if (!$result)
    die($conn->error);

//construim capçalera de la taula
echo '<table id="taulaTaulesDataBase" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-3">Taula</th>';
echo '<th class="col-sm-1">Registres</th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {

    //totals
    $totalRegistres = 0;
    $totalTaules = $result->num_rows;

    echo '<tbody id="cosTaulaTaulesDataBase">';
    while ($row = $result->fetch_assoc()) {
        //per a cada taula obtenim el total de registres
        $query1 = "select count(*) as conta from " . $row['taula'];

        $result1 = $conn->query($query1);

        if (!$result1)
            die($conn->error);

        $row1 = $result1->fetch_assoc();
        $registres = $row1['conta'];
        $totalRegistres += $registres;
        $result1->close();


        echo '<tr>';
        echo '<td class="col-sm-3">' . $row['taula'] . '</td>';
        echo '<td class="col-sm-1">' . $registres . '</td>';
        echo '</tr>';
    }
    //posem els totals
    echo '<tr>';
    echo '<th class="col-sm-3">Total('.$totalTaules.')</th>';
    echo '<th class="col-sm-1">'.$totalRegistres.'</th>';
    echo '</tr>';

    echo '</tbody>';
}

echo '</table>';
$result->close();
$conn->close();
