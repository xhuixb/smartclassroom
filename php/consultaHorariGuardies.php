<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

session_start();

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$horarisArray = [];

$query = "select ga26_codi_professor as codiprof,"
        . "ga26_dia_setmana as dia,ga26_hora_inici as hora,"
        . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=codiprof) as nomprof,"
        . "(select ga36_descripcio from ga36_tipus_guardia where ga36_codi=ga26_tipus_guardia) as tipusguardia"
        . " from ga26_horaris_docents,ga04_professors"
        . " where ga26_codi_professor=ga04_codi_prof and ga04_suspes='0' and ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_es_guardia=1 order by hora,dia,ga26_tipus_guardia";


//executem la consulta
$result = $conn->query($query);



if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        //posem la informació en un array associativa
        $horarisArray[$row['hora'] . '-' . $row['dia'] . '-' . $row['codiprof']] = join('<#>', $row);
    }
}

$result->close();


//creem la taula dels horaris

$query = "select ga10_hora_inici as horainici,ga10_hora_fi as horafi,ga10_es_descans as esdescans,ga10_tipus_horari as tipushorari from ga10_horaris_aula where ga10_codi_curs=".$_SESSION['curs_actual'];


//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    echo '<table id="taulaHorarisGuardia" class="table table-bordered">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="col-sm-1">HORA</th>';
    echo '<th class="col-sm-2"><center>DILLUNS</center></th>';
    echo '<th class="col-sm-2"><center>DIMARTS</center></th>';
    echo '<th class="col-sm-2"><center>DIMECRES</center></th>';
    echo '<th class="col-sm-2"><center>DIJOUS</center></th>';
    echo '<th class="col-sm-2"><center>DIVENDRES</center></th>';
    echo '</tr>';
    echo '</thead>';

    echo '<tbody id="costaulaHorarisGuardia">';
    while ($row = $result->fetch_assoc()) {


        if ($row['esdescans'] == '1') {
            $esdescans = "(ESBARJO)";
            $fileres = 2;
            $colorDescans = "btn-success";
        } else {

            $esdescans = "";
            $fileres = 4;
            $colorDescans = "";
        }

        $textHorari = [];

        for ($j = 1; $j <= 5; $j++) {
            $textHorari[$j] = '';
        }

        $assigTram = [];

        //inicialitzem l'array
        for ($i = 0; $i <= 5; $i++) {
            $assigTram[$i] = '';
        }

        foreach ($horarisArray as $tramHorari) {


            $contingutHorari = explode('<#>', $tramHorari);
            if ($row['horainici'] == $contingutHorari[2]) {


                $textHorari[$contingutHorari[1]] .= $contingutHorari[4].'-'.$contingutHorari[3].'<br>';

                // $assigTram[$contingutHorari[1]] .= $contingutHorari[4] . '-' . $contingutHorari[5] . '-' . $contingutHorari[12] . '<#>';
            }
        }



        echo '<tr>';
        echo '<td class="col-sm-1' . $colorDescans . '" data-horainici="' . $row['horainici'] . '" data-esdescans="' . $row['esdescans'] . '">Inici: ' . $row['horainici'] . '<br>' . 'Fi: ' . $row['horafi'] . '<br>' . $esdescans . '</td>';

        for ($i = 1; $i <= 5; $i++) {

            echo '<td class="col-sm-2">' . $textHorari[$i] . '</td>';
        }

        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
}

$result->close();
$conn->close();

