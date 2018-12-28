<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió
//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

$query = "select ga22_codi_falta as codi,ga22_nom_falta as nom from ga22_tipus_falta";

//executem la query
//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

//construim capçalera de la taula
echo '<table id="taulaTipusFalta" class="table borderless">';

if ($result->num_rows > 0) {
    echo '<tbody id="cosTaulaTipusFalta">';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td class="col-sm-1">' . $row['nom'] . '</td>';
        echo '</tr>';
    }
}

//tanquem cos i taula
echo '</tbody>';
echo '</table>';
$result->close();
$conn->close();