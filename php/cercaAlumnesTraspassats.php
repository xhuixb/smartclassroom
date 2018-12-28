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

$nivellDesti = $_POST["nivellDesti"];
$cursDesti = $_POST['cursDesti'];
$grupDesti = $_POST['grupDesti'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "select ga12_id_alumne as codialumne,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as nomalumne from ga12_alumnes_curs,ga11_alumnes"
        . " where ga12_id_alumne=ga11_id_alumne and ga12_codi_curs=" . $cursDesti . " and ga12_codi_nivell=" . $nivellDesti . " and ga12_codi_grup=" . $grupDesti
        ." order by nomalumne";

//executem la consulta
$result = $conn->query($query);



if (!$result)
    die($conn->error);

echo '<table id="taulaAlumnesTraspassats" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-2"><span class="glyphicon glyphicon-trash"></span></th>';
echo '<th class="col-sm-8">Alumne</th>';
echo '</tr>';
echo '</thead>';



if ($result->num_rows > 0) {

    echo '<tbody id="costaulaAlumnesTraspassats">';
    while ($row = $result->fetch_assoc()) {


        echo '<tr>';
        echo '<td class="col-sm-2"><input class="checkEsborrar" type="checkbox"/></td>';
        echo '<td class="col-sm-8" data-codi="'.$row['codialumne'].'">' . $row['nomalumne'] . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
}


echo '</table>';

$result->close();
$conn->close();
