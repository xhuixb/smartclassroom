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
$query = $_POST['query'];
$caption = $_POST['caption'];


$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//aneme a veure si el profe és administrador
$queryProfe = "select ga17_codi_professor as codi,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio,ga17_es_admin as admin"
        . " from ga04_professors,ga17_professors_curs where ga04_codi_prof=ga17_codi_professor and"
        . " ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga17_codi_professor=".$_SESSION['prof_actual']." order by descripcio";

//executem la consulta

$resultProfe = $conn->query($queryProfe);
$rowProfe = $resultProfe->fetch_assoc();

if ($rowProfe['admin'] == '1') {
    $habilitaProfe = "";
} else {
    $habilitaProfe = "disabled";
}

if (!$resultProfe)
    die($conn->error);




//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

echo '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="butDrop' . $div . '" ' . $habilitaProfe . ' value="'.$rowProfe['codi'].'">' . $rowProfe['descripcio'] .' '. '<span class="caret"></span></button>';
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
