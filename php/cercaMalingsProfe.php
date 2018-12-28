<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió
//establim la connexió
session_start();

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);


//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "SELECT ga30_id as codimailing, ga30_curs as codicurs,ga03_descripcio as nomcurs,ga30_alumnes as alumnes,ga30_missatge as missatge,ga30_estat as estat,ga30_data_mailing as datamailing,ga30_adjunts as adjunts"
        . " from ga30_comunicacions,ga03_curs"
        . " where ga30_codi_prof=".$_SESSION['prof_actual']." and ga30_curs=ga03_codi_curs";

//executem la query
$result = $conn->query($query);

if (!$result)
    die($conn->error);


//construim capçalera de la taula


if ($result->num_rows > 0) {
    $cont = 0;

    echo '<br>';
    echo '<table id="taulaProfesMailing" class="table table-fixed">';
    echo '<thead>';
    echo '<tr>';
    echo '<td class="col-sm-1"><button id="esborraMailingsProfe" type="button" class="btn btn-danger" onclick="esborraMailingsProfe();"><span class="glyphicon glyphicon-trash"></span></button></td>';
    echo '<th class="col-sm-1">Curs</th>';
    echo '<th class="col-sm-1">Data</th>';
    echo '<th class="col-sm-1">Estat</th>';
    echo '<th class="col-sm-1"><center>Detalls</center></th>';
    echo '</tr>';
    echo '</thead>';

    echo '<tbody id="cosTaulaProfesMailing">';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';

        if ($row['estat'] == 0) {
            $estat = 'Pendent';
            $color = 'danger';
        } elseif ($row['estat'] == 1) {
            $estat = 'Aprovat';
            $color = 'info';
        } else {

            $estat = 'Executat';
            $color = 'success';
        }
        
        //substituim les cometes dobles
        $missatgeSenseCometes=str_replace('"','&quot;',$row['missatge']);

        echo '<td><input type="checkbox" value="" class="checkEsborraMailing" data-codi-mailing="'.$row['codimailing'].'"></td>';
        echo '<td class="col-sm-1">' . $row['nomcurs'] . '</td>';
        echo '<td class="col-sm-1">' . $row['datamailing'] . '</td>';
        echo '<td class="col-sm-1 ' . $color . '">' . $estat . '</td>';
        echo '<td class="col-sm-1"><center><button type="button" class="btn btn-info"  data-toggle="modal" data-target="#detallMailingModal" onclick="mostraDadesMailing(this)" data-cos-mailing="'.$missatgeSenseCometes.'" data-alumnes="'.$row['alumnes'].'" data-adjunts="'.$row['adjunts'].'" >';
        echo '<span class="glyphicon glyphicon-option-vertical"></span></button></center></td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}

$result->close();
$conn->close();


//esborrem els fitxers antics
$files = glob('../pdf/provisional/p'.$_SESSION['prof_actual'].'_*.pdf'); // get all file names
foreach ($files as $file) { // iterate files
    if (is_file($file))
        unlink($file); // delete file
}

