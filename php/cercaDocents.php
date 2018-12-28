<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$nomDocent = $_POST['nomDocent'];
$checkTutors = $_POST['checkTutors'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


if ($checkTutors == 0) {
    //aquesta és la query
    //NO és un llistat només de tutors
    $query = "select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as docent, ga04_mail as mail,ga04_suspes as suspes from ga04_professors,ga17_professors_curs"
            . " where  ga17_codi_professor=ga04_codi_prof and ga17_codi_curs=" . $_SESSION['curs_actual'] . " and concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) like '%" . $nomDocent . "%'"
            . " order by docent";

    $result = $conn->query($query);


    if (!$result)
        die($conn->error);


//construim capçalera de la taula
    echo '<br>';
    echo '<table id="taulaDocents" class="table table-fixed">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="col-sm-4">Docent</th>';
    echo '<th class="col-sm-4">Mail</th>';
    echo '</tr>';
    echo '</thead>';

    if ($result->num_rows > 0) {
        echo '<tbody id="cosTaulaDocents">';
        while ($row = $result->fetch_assoc()) {

            if ($row['suspes'] == '1') {
                $estilFilera = 'class="btn-danger"';
            } else {
                $estilFilera = "";
            }


            echo '<tr ' . $estilFilera . '>';
            echo '<td class="col-sm-4">' . $row['docent'] . '</td>';
            echo '<td class="col-sm-4">' . $row['mail'] . '</td>';

            echo '<tr>';
            echo '</tr>';
        }
    }

//tanquem cos i taula
    echo '</tbody>';
    echo '</table>';
} else {
    //és un llistat només de tutors
    $query = "select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as docent, ga04_mail as mail,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,ga04_suspes as suspes"
            . " from ga04_professors,ga29_tutors_curs,ga06_nivell,ga07_grup where  ga29_tutor=ga04_codi_prof and ga29_nivell=ga06_codi_nivell and ga29_grup=ga07_codi_grup and ga29_curs=" . $_SESSION['curs_actual']
            . " order by ga29_nivell,ga29_grup";

    $result = $conn->query($query);


    if (!$result)
        die($conn->error);


//construim capçalera de la taula
    echo '<br>';
    echo '<table id="taulaDocents" class="table table-fixed">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="col-sm-1">Nivell</th>';
    echo '<th class="col-sm-1">Grup</th>';
    echo '<th class="col-sm-4">Docent</th>';
    echo '<th class="col-sm-4">Mail</th>';
    echo '</tr>';
    echo '</thead>';

    if ($result->num_rows > 0) {
        echo '<tbody id="cosTaulaDocents">';
        while ($row = $result->fetch_assoc()) {
            if ($row['suspes'] == '1') {
                $estilFilera = 'class="btn-danger"';
            } else {
                $estilFilera = "";
            }


            echo '<tr '.$estilFilera.'>';
            echo '<td class="col-sm-1">' . $row['nivell'] . '</td>';
            echo '<td class="col-sm-1">' . $row['grup'] . '</td>';
            echo '<td class="col-sm-4">' . $row['docent'] . '</td>';
            echo '<td class="col-sm-4">' . $row['mail'] . '</td>';
            echo '</tr>';
        }
    }

//tanquem cos i taula
    echo '</tbody>';
    echo '</table>';

    //posem el button per exportar en pdf

    echo '<div class = "col-sm-2" >';
    echo '<br>';
    echo '<button type = "button" class = "btn btn-success form-control" id = "exportaPdfTutors" onclick="exportaPdfTutors()">'
    . '==>PDF'
    . '</button>';

    echo '</div>';
}



$result->close();
$conn->close();
