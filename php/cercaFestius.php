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

$query = "select ga38_festius as diesfestius from ga38_config_curs where ga38_codi_curs=" . $_SESSION['curs_actual'];

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {

    echo '<table id="taulaFestius" class="table table-fixed">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="col-sm-2">Data</th>';
    echo '<th class="col-sm-1"><span class="glyphicon glyphicon-trash"></th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody id="taulaFestiusCos">';
    $row = $result->fetch_assoc();

    $diesFestius = $row['diesfestius'];

    if ($diesFestius != '') {
        //trenquem els dies festius
        $diesFestiusArray = [];

        $diesFestiusArray = explode('<#>', $diesFestius);


        for ($i = 0; $i < count($diesFestiusArray); $i++) {
            echo '<tr>';
            echo '<td class="col-sm-2 datesfestives">' . $diesFestiusArray[$i] . '</td>';
            echo '<td class="col-sm-1"><input type="checkbox"></td>';
            echo '<tr>';
        }
    }
    echo '</tbody>';
    echo '</table>';
}
$result->close();
$conn->close();
