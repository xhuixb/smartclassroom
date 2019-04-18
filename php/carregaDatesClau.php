<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';


//fem la connexió

session_start();

//fem la connexió a la base de dades per totes les consultes que ens caldran
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


$query = "select ga41_id as id,ga41_data_clau as dataclau,ga41_data_inici_publi as datainici,ga41_data_fi_publi as datafi,ga41_descripcio as descripcio from ga41_dates_clau where ga41_curs=" . $_SESSION['curs_actual']." order by dataclau desc";

$result = $conn->query($query);


if (!$result)
    die($conn->error);


//construim capçalera de la taula
echo '<br>';
echo '<table id="taulaDatesClau" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th><span class="glyphicon glyphicon-trash"></span></th>';
echo '<th>Data clau</th>';
echo '<th>Data ini publi</th>';
echo '<th>Data fi publi</th>';
echo '<th>Descripció</th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {

    echo '<tbody id="costaulaJustifiAbsencies">';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        //posem el format pertinent
        if ($row['dataclau'] != '') {
            $dataClau = substr($row['dataclau'], 8) . '/' . substr($row['dataclau'], 5, 2) . '/' . substr($row['dataclau'], 0, 4);
        } else {
            $dataClau = '';
        }
        if ($row['datainici'] != '') {
            $dataIniciPubli = substr($row['datainici'], 8) . '/' . substr($row['datainici'], 5, 2) . '/' . substr($row['datainici'], 0, 4);
        } else {
            $dataIniciPubli = '';
        }
        if ($row['datafi'] != '') {
            $dataFiPubli = substr($row['datafi'], 8) . '/' . substr($row['datafi'], 5, 2) . '/' . substr($row['datafi'], 0, 4);
        } else {
            $dataFiPubli = '';
        }

        echo '<td class="col-sm-1"><input class="checkEsborrat" type="checkbox"></td>';
        echo '<td class="col-sm-1" data-id="' . $row['id'] . '">' . $dataClau . '</td>';
        echo '<td class="col-sm-1">' . $dataIniciPubli . '</td>';
        echo '<td class="col-sm-1">' . $dataFiPubli . '</td>';
        echo '<td class="col-sm-1"><button type="button" class="btn btn-info form-control" data-toggle="modal" data-target="#editaDatesClauModal" onclick="mostraDetallDataClau(this);">'
        . '<span class="glyphicon glyphicon-pencil"></span>'
        . '</button></td>';
        echo '</tr>';
    }
}

$result->close();
$conn->close();

