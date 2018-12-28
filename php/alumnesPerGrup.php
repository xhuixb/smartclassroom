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

$codiGrup = $_POST["codiGrup"];
$tipusGrup = $_POST['tipusGrup'];
$codiNivell = $_POST["codiNivell"];

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

if ($tipusGrup == '0') {
    //busquem alumnes del grup general
    $query = "select concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as nomalumne,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup"
            . " from ga11_alumnes,ga12_alumnes_curs,ga06_nivell,ga07_grup"
            . " where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and ga12_codi_nivell=" . $codiNivell . " and ga12_codi_grup=" . $codiGrup
            . " order by nomalumne";
} else {
    //busquem alumnes del grup de profe
    $query = "select concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as nomalumne,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup"
            . " from ga11_alumnes,ga24_grups_profes_det,ga06_nivell,ga07_grup,ga12_alumnes_curs"
            . " where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga24_codi_alumne=ga12_id_alumne and ga11_id_alumne=ga12_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and ga24_codi_grup=".$codiGrup
            . " order by nomalumne";
}

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);


//construim capçalera de la taula
echo '<table id="taulaAlumnesGrup" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-5">Alumne</th>';
echo '<th class="col-sm-3">Nivell/Grup</th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {


    echo '<tbody id="cosTaulaAlumnesGrup">';
    while ($row = $result->fetch_assoc()) {

        echo '<tr>';
        echo '<td class="col-sm-5">' . $row['nomalumne'] . '</td>';
        echo '<td class="col-sm-3">' . $row['nivell'] . '/' . $row['grup'] . '</td>';
        echo '</tr>';
    }
    //tanquem el cos
    echo '</tbody>';
}

//tanquem la taula

echo '</table>';

$result->close();
$conn->close();
