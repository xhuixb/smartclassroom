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

$nivell = $_POST["nivell"];
$grup = $_POST['grup'];
$dataSessio = $_POST["dataSessio"];

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


$query = "select ga12_id_alumne as codialumne, concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as nomcomplet"
        . " from ga11_alumnes,ga12_alumnes_curs"
        . " where ga11_id_alumne=ga12_id_alumne and ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_codi_nivell=" . $nivell . " and ga12_codi_grup=" . $grup
        . " order by nomcomplet";

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);




if ($result->num_rows > 0) {

//construim capçalera de la taula
    echo '<table id="taulaAlumnesGrup" class="table table-fixed">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="col-sm-1"><form class="form-inline"><input type="checkbox" value="" id="checkMarcaDesmarca" onclick="seleccionaTot();"><button type="button" class="btn btn-warning form-control" onclick="esborraAlumnes()"><span class="glyphicon glyphicon-trash"></span></button></form></th>';
    echo '<th class="col-sm-3">Alumne</th>';
    echo '<th class="col-sm-1">Pres</th>';
    echo '<th class="col-sm-1">Abs</th>';
    echo '<th class="col-sm-1">Ret</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody id="cosTaulaAlumnesGrup">';
    while ($row = $result->fetch_assoc()) {

        echo '<tr>';
        echo '<td class="col-sm-1"><input type="checkbox" value="" class="checkEsborrar"/></td>';
        echo '<td class="col-sm-3" data-codi-alumne="'.$row['codialumne'].'">' . $row['nomcomplet'] . '</td>';
        echo '<td class="col-sm-1"><input type="checkbox" value="" checked class="checkAssist" onchange="comprovaCheckMassiu(this)"></td>';
        echo '<td class="col-sm-1"><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheckMassiu(this)"></td>';
        echo '<td class="col-sm-1"><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheckMassiu(this)"></td>';

        echo '</tr>';
    }
    //tanquem el cos
    echo '</tbody>';
}

//tanquem la taula

echo '</table>';

$result->close();
$conn->close();
