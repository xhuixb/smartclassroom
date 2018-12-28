<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió
session_start();

$div = $_POST['div'];
$nivell = $_POST['nivell'];


$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "select ga14_codi_assignatura as codi,ga18_desc_assignatura as descripcio from ga18_assignatures,ga14_assignatura_nivell where ga18_codi_assignatura=ga14_codi_assignatura and ga14_curs=" . $_SESSION['curs_actual']
        . " and ga14_nivell=" . $nivell;

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

echo '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="butDrop' . $div . '">Tria Assignatura<span class="caret"></span></button>';
echo '<ul class="dropdown-menu" id="drop' . $div . '" style="height: 250px; overflow: auto">';

if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {

        echo '<li><a data-val="' . $row['codi'] . '" onclick="mostra' . $div . '(this);">' . $row['descripcio'] . '</a></li>';
    }
}

echo '</ul>';


$result->close();
$conn->close();
