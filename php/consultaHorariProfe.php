<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

session_start();


$codiProf = $_POST["codiProf"];
//$codiProf = 68;
//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//anem a buscar els horaris del professor

$query1 = "select ga26_dia_setmana as dia,ga26_hora_inici as hora,"
        . "(select ga06_descripcio_nivell from ga06_nivell where ga06_codi_nivell=ga26_nivell) as nivell,"
        . "(select ga07_descripcio_grup from ga07_grup where ga07_codi_grup=ga26_grup) as grupgeneral,"
        . "(select ga23_nom_grup from ga23_grups_profes_cap where ga23_codi_grup=ga26_grup) grupprofe,"
        . "ga26_tipus_grup as tipusgrup,"
        . "ga26_is_lectiva as islectiva,"
        . "(select ga27_descripcio from ga27_tipus_carrec where ga27_codi=ga26_tipus_carrec) as tipuscarrec,"
        . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=ga26_codi_assignatura) as assignatura,"
        . "(select ga01_descripcio_aula from ga01_aula where ga01_codi_aula=ga26_codi_aula) as aula,"
        . "ga26_es_guardia as esguardia,ga26_es_carrec as escarrec,ga26_grup as grup,"
        . "(select ga36_descripcio from ga36_tipus_guardia where ga36_codi=ga26_tipus_guardia) as tipusguardia"
        . " from ga26_horaris_docents"
        . " where ga26_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga26_codi_professor=" . $codiProf . " order by hora,dia";


//executem la consulta
$result1 = $conn->query($query1);

$horarisArray = [];

if (!$result1)
    die($conn->error);

if ($result1->num_rows > 0) {
    while ($row1 = $result1->fetch_assoc()) {

        //posem la informació en un array associativa
        $horarisArray[$row1['hora'] . '-' . $row1['dia']] = join('<#>', $row1);
    }
}

$result1->close();

//creem la taula dels horaris

$query = "select ga10_hora_inici as horainici,ga10_hora_fi as horafi,ga10_es_descans as esdescans,ga10_tipus_horari as tipushorari from ga10_horaris_aula where ga10_codi_curs=".$_SESSION['curs_actual'];


//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    echo '<table id="taulaHorarisProfessor" class="table table-bordered">';
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

    echo '<tbody id="costaulaHorarisProfessor">';

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
        $estilHorari = [];

        //inicialitzem els arrays
        for ($i = 1; $i <= 5; $i++) {

            $textHorari[$i] = '';
            $estilHorari[$i] = '';
        }
        for ($i = 1; $i <= 5; $i++) {
            if (array_key_exists($row['horainici'] . '-' . $i, $horarisArray)) {
                $contingutHorari = [];
                $contingutHorari = explode('<#>', $horarisArray[$row['horainici'] . '-' . $i]);


                if ($contingutHorari[6] == '1') {

                    $estilHorari[$i] = "1";
                    $textHorari[$i] .= "<strong>HORA LECTIVA</strong>";
                    $textHorari[$i] .= "<br>";
                    $textHorari[$i] .= "Nivell: ";
                    $textHorari[$i] .= $contingutHorari[2];
                    $textHorari[$i] .= "<br>";
                    $textHorari[$i] .= "Grup: ";
                    if ($contingutHorari[5] == '0') {
                        $textHorari[$i] .= $contingutHorari[3];
                    } else {
                        $textHorari[$i] .= $contingutHorari[4];
                    }
                    $textHorari[$i] .= "<br>";
                    $textHorari[$i] .= "Assignatura: ";
                    $textHorari[$i] .= $contingutHorari[8];
                    $textHorari[$i] .= "<br>";
                    $textHorari[$i] .= "Aula: ";

                    if ($contingutHorari[9] != '') {
                        $textHorari[$i] .= $contingutHorari[9];
                    }
                } elseif ($contingutHorari[11] == '1') {
                    //és carrec
                    $estilHorari[$i] = "2";
                    $textHorari[$i] .= "<strong>REUNIÓ/CÀRREC</strong>";
                    $textHorari[$i] .= "<br>";
                    $textHorari[$i] .= $contingutHorari[7];
                } else {
                    //es guàrdia
                    $estilHorari[$i] = "3";
                    $textHorari[$i] .= "<strong>GUÀRDIA</strong>";
                    $textHorari[$i] .= "<br>";
                    $textHorari[$i] .= $contingutHorari[13];
                }
            }
        }

        echo '<tr>';
        echo '<td class="col-sm-1' . $colorDescans . '" data-horainici="' . $row['horainici'] . '" data-esdescans="' . $row['esdescans'] . '">Inici: ' . $row['horainici'] . '<br>' . 'Fi: ' . $row['horafi'] . '<br>' . $esdescans . '</td>';

        for ($i = 1; $i <= 5; $i++) {
            echo '<td class="col-sm-2" data-estil="' . $estilHorari[$i] . '">' . $textHorari[$i] . '</td>';
        }

        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
}

$result->close();

$conn->close();

