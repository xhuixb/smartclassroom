<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

//recollim les dades del client
$dia = $_POST['dia'];
$hora = $_POST['hora'];

if ($_POST['profe'] == '') {
    //profe titular
    $profe = $_SESSION['prof_actual'];
   
} else {
    //guàrdia
    $profe = $_POST['profe'];
   
}



//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


$query = "select ga47_id as id,ga47_nom_intern as nomintern,ga47_nom_extern as nomextern from ga47_adjunts_programador "
        . "where ga47_curs=" . $_SESSION['curs_actual'] . " and ga47_professor=" . $profe . " and ga47_dia='" . $dia . "' and ga47_hora='" . $hora . "'";

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    //hi ha adjunts
    while ($row = $result->fetch_assoc()) {
        if ($_POST['profe'] == '') {
            //profe titular
            $potEsborrar = '</span></a><a href="#" onclick="esborraAdjunt(this);" data-codi="' . $row['id'] . '"><span class="glyphicon glyphicon-remove"></span></a> ';
        } else {
            //guàrdia
            $potEsborrar = "";
        }

        echo '<a href="uploads/programacions/' . $row['nomintern'] . '" target="_blank" data-codi="' . $row['id'] . '"><span data-nomintern="' . $row['nomintern'] . '">';
        echo $row['nomextern'];
        echo $potEsborrar;
    }
}

$result->close();

$conn->close();
