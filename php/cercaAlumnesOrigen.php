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

$nivellOrigen = $_POST["nivellOrigen"];
$cursOrigen = $_POST['cursOrigen'];
$nivellDesti = $_POST["nivellDesti"];
$cursDesti = $_POST['cursDesti'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


//anem a construir la capçalera
$query = "select ga07_descripcio_grup as descrgrup,ga07_codi_grup as codigrup from ga07_grup,ga35_curs_nivell_grup"
        . " where ga35_codi_curs=" . $cursDesti . " and ga35_nivell=" . $nivellDesti . " and ga35_grup=ga07_codi_grup order by codigrup";


//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

echo '<table id="taulaAlumnesOrigen" class="table table-fixed">';
if ($result->num_rows > 0) {
//construim capçalera de la taula

    echo '<thead id="taulaCapAlumnesOrigen">';
    echo '<tr>';
    echo '<th class="col-sm-2">Alumne</th>';
    echo '<th class="col-sm-1">Grup Origen</th>';
    echo '<th class="col-sm-1">No Trasp</th>';
    $numGrups = 0;
    while ($row = $result->fetch_assoc()) {
        echo '<th class="col-sm-1" data-codi-grup="' . $row['codigrup'] . '">' . $row['descrgrup'] . '</th>';
        $numGrups++;
    }
    echo '</tr>';
    echo '</thead>';
}




//ara anem a buscar els alumnes origen
$query = "select ga12_id_alumne as codialumne,(select concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) from ga11_alumnes where ga11_id_alumne=codialumne) as nomalumne,ga07_descripcio_grup as descgruporigen,"
        . "(select ga12_codi_grup from ga12_alumnes_curs where ga12_codi_curs=" . $cursDesti . " and ga12_id_alumne=codialumne) as grupnou,"
        . "(select ga12_codi_nivell from ga12_alumnes_curs where ga12_codi_curs=" . $cursDesti . " and ga12_id_alumne=codialumne) as nivellnou"
        . " from ga12_alumnes_curs,ga11_alumnes,ga07_grup"
        . " where ga12_codi_curs=" . $cursOrigen . " and ga12_codi_nivell=" . $nivellOrigen . " and ga12_id_alumne=ga11_id_alumne and ga12_codi_grup=ga07_codi_grup order by descgruporigen,nomalumne";



//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    $filera = 0;
    echo '<tbody id="taulaBodyAlumnesOrigen">';
    while ($row = $result->fetch_assoc()) {

        //mirem si l'alumne ja s'havia traspassat
        if ((string)$row['nivellnou'] === '') {
            //encara no s'ha traspassat
            echo '<tr>';
            echo '<td class="col-sm-2" data-alumne="' . $row['codialumne'] . '">' . $row['nomalumne'] . '</td>';
            echo '<td class="col-sm-1">' . $row['descgruporigen'] . '</td>';
            echo '<td class="col-sm-1"><input type="radio" name="fil' . $filera . '" checked onchange="comptaAlumnes();"></td>';
            //posem els option
            for ($i = 0; $i < $numGrups; $i++) {
                echo '<td class="col-sm-1"><input type="radio" name="fil' . $filera . '" class="radioTraspas" onchange="comptaAlumnes();"></td>';
            }
            $filera++;
            echo '</tr>';
        }
    }
    echo '</tbody>';
}


echo '</table>';

$result->close();
$conn->close();
