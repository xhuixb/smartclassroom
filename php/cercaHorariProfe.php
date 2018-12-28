<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

session_start();


$profe = $_POST["profe"];

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
        . "(select ga36_descripcio from ga36_tipus_guardia where ga36_codi=ga26_tipus_guardia) as tipusguardia,"
        . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=ga26_codi_assignatura) as assignatura,"
        . "(select ga01_descripcio_aula from ga01_aula where ga01_codi_aula=ga26_codi_aula) as aula,"
        . "ga26_es_guardia as esguardia,ga26_es_carrec as escarrec"
        . " from ga26_horaris_docents"
        . " where ga26_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga26_codi_professor=" . $profe . " order by hora,dia";


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

    $button1 = '<div class="col-sm-6"><button type="button" class="btn btn-success form-control" id="editaHorarisProfeButton" data-toggle="modal" data-target="#edicioHorariModalForm" onclick="editaHorarisProfe(this);">'
            . '<span class="glyphicon glyphicon-pencil"></span>Edita'
            . '</button></div>';
    $button2 = '<div class="col-sm-6"><button type="button" class="btn btn-danger form-control" id="esborraHorarisProfeButton" onclick="esborraHorarisProfe(this);">'
            . '<span class="glyphicon glyphicon-trash"></span>Esborra'
            . '</button></div>';

    echo '<tbody id="costaulaHorarisProfessor">';
    $cont = 0;
    while ($row = $result->fetch_assoc()) {

        if ($row['esdescans'] == '1') {
            $esdescans = "(ESBARJO)";
        } else {

            $esdescans = "";
        }
        if (array_key_exists($row['horainici'] . '-1', $horarisArray)) {
            $contingutHorari = [];
            $contingutHorari = explode('<#>', $horarisArray[$row['horainici'] . '-1']);

            $dilluns = '';

            if ($contingutHorari[6] == '1') {
                $dillunsEstil = "1";
                $dilluns .= "HORA LECTIVA";
                $dilluns .= "\n";
                $dilluns .= "Nivell: ";
                $dilluns .= $contingutHorari[2];
                $dilluns .= "\n";
                $dilluns .= "Grup: ";
                if ($contingutHorari[5] == '0') {
                    $dilluns .= $contingutHorari[3];
                } else {
                    $dilluns .= $contingutHorari[4];
                }
                $dilluns .= "\n";
                $dilluns .= "Assignatura: ";
                $dilluns .= $contingutHorari[9];
                $dilluns .= "\n";
                $dilluns .= "Aula: ";

                if ($contingutHorari[10] != '') {
                    $dilluns .= $contingutHorari[10];
                }
            } else {
                //no lectiva
                if ($contingutHorari[11] == '1') {
                    //guàrdia
                    $dilluns .= "GUÀRDIA";
                    $dilluns .= "\n";
                    $dilluns .= $contingutHorari[8];
                    $dillunsEstil = "2";
                } else {
                    //Càrrec
                    $dilluns .= "REUNIÓ/CÀRREC";
                    $dilluns .= "\n";
                    $dilluns .= $contingutHorari[7];
                    $dillunsEstil = "3";
                }
            }
        } else {
            $dilluns = '';
            $dillunsEstil = "0";
        }


        if (array_key_exists($row['horainici'] . '-2', $horarisArray)) {

            $contingutHorari = [];
            $contingutHorari = explode('<#>', $horarisArray[$row['horainici'] . '-2']);
            $dimarts = '';

            if ($contingutHorari[6] == '1') {
                $dimartsEstil = "1";
                $dimarts .= "HORA LECTIVA";
                $dimarts .= "\n";
                $dimarts .= "Nivell: ";
                $dimarts .= $contingutHorari[2];
                $dimarts .= "\n";
                $dimarts .= "Grup: ";
                if ($contingutHorari[5] == '0') {
                    $dimarts .= $contingutHorari[3];
                } else {
                    $dimarts .= $contingutHorari[4];
                }
                $dimarts .= "\n";
                $dimarts .= "Assignatura: ";
                $dimarts .= $contingutHorari[9];
                $dimarts .= "\n";
                $dimarts .= "Aula: ";

                if ($contingutHorari[10] != '') {
                    $dimarts .= $contingutHorari[10];
                }
            } else {
                //no lectiva
                if ($contingutHorari[11] == '1') {
                    //guàrdia
                    $dimarts .= "GUÀRDIA";
                    $dimarts .= "\n";
                    $dimartsEstil = "2";
                    $dimarts .= $contingutHorari[8];
                    $dimartsEstil = "2";
                } else {
                    //Càrrec
                    $dimarts .= "REUNIÓ/CÀRREC";
                    $dimarts .= "\n";
                    $dimarts .= $contingutHorari[7];
                    $dimartsEstil = "3";
                }
            }
        } else {
            $dimarts = '';
            $dimartsEstil = "0";
        }


        if (array_key_exists($row['horainici'] . '-3', $horarisArray)) {
            $contingutHorari = [];
            $contingutHorari = explode('<#>', $horarisArray[$row['horainici'] . '-3']);
            $dimecres = '';

            if ($contingutHorari[6] == '1') {
                $dimecresEstil = "1";
                $dimecres .= "HORA LECTIVA";
                $dimecres .= "\n";
                $dimecres .= "Nivell: ";
                $dimecres .= $contingutHorari[2];
                $dimecres .= "\n";
                $dimecres .= "Grup: ";
                if ($contingutHorari[5] == '0') {
                    $dimecres .= $contingutHorari[3];
                } else {
                    $dimecres .= $contingutHorari[4];
                }
                $dimecres .= "\n";
                $dimecres .= "Assignatura: ";
                $dimecres .= $contingutHorari[9];
                $dimecres .= "\n";
                $dimecres .= "Aula: ";

                if ($contingutHorari[10] != '') {
                    $dimecres .= $contingutHorari[10];
                }
            } else {
                //no lectiva
                if ($contingutHorari[11] == '1') {
                    //guàrdia
                    $dimecres .= "GUÀRDIA";
                    $dimecres .= "\n";
                    $dimecres .= $contingutHorari[8];
                    $dimecresEstil = "2";
                } else {
                    //Càrrec
                    $dimecres .= "REUNIÓ/CÀRREC";
                    $dimecres .= "\n";
                    $dimecres .= $contingutHorari[7];
                    $dimecresEstil = "3";
                }
            }
        } else {
            $dimecres = '';
            $dimecresEstil = "0";
        }

        if (array_key_exists($row['horainici'] . '-4', $horarisArray)) {
            $contingutHorari = [];
            $contingutHorari = explode('<#>', $horarisArray[$row['horainici'] . '-4']);
            $dijous = '';

            if ($contingutHorari[6] == '1') {
                $dijousEstil = "1";
                $dijous .= "HORA LECTIVA";
                $dijous .= "\n";
                $dijous .= "Nivell: ";
                $dijous .= $contingutHorari[2];
                $dijous .= "\n";
                $dijous .= "Grup: ";
                if ($contingutHorari[5] == '0') {
                    $dijous .= $contingutHorari[3];
                } else {
                    $dijous .= $contingutHorari[4];
                }
                $dijous .= "\n";
                $dijous .= "Assignatura: ";
                $dijous .= $contingutHorari[9];
                $dijous .= "\n";
                $dijous .= "Aula: ";

                if ($contingutHorari[10] != '') {
                    $dijous .= $contingutHorari[10];
                }
            } else {
                //no lectiva
                if ($contingutHorari[11] == '1') {
                    //guàrdia
                    $dijous .= "GUÀRDIA";
                    $dijous .= "\n";
                    $dijous .= $contingutHorari[8];
                    $dijousEstil = "2";
                } else {
                    //Càrrec
                    $dijous .= "REUNIÓ/CÀRREC";
                    $dijous .= "\n";
                    $dijous .= $contingutHorari[7];
                    $dijousEstil = "3";
                }
            }
        } else {

            $dijous = '';
            $dijousEstil = "0";
        }

        if (array_key_exists($row['horainici'] . '-5', $horarisArray)) {

            $contingutHorari = [];
            $contingutHorari = explode('<#>', $horarisArray[$row['horainici'] . '-5']);
            $divendres = '';

            if ($contingutHorari[6] == '1') {
                $divendresEstil = "1";
                $divendres .= "HORA LECTIVA";
                $divendres .= "\n";
                $divendres .= "Nivell: ";
                $divendres .= $contingutHorari[2];
                $divendres .= "\n";
                $divendres .= "Grup: ";
                if ($contingutHorari[5] == '0') {
                    $divendres .= $contingutHorari[3];
                } else {
                    $divendres .= $contingutHorari[4];
                }
                $divendres .= "\n";
                $divendres .= "Assignatura: ";
                $divendres .= $contingutHorari[9];
                $divendres .= "\n";
                $divendres .= "Aula: ";

                if ($contingutHorari[10] != '') {
                    $divendres .= $contingutHorari[10];
                }
            } else {
                //no lectiva
                if ($contingutHorari[11] == '1') {
                    //guàrdia
                    $divendres .= "GUÀRDIA";
                    $divendres .= "\n";
                    $divendres .= $contingutHorari[8];
                    $divendresEstil = "2";
                } else {
                    //Càrrec
                    $divendres .= "REUNIÓ/CÀRREC";
                    $divendres .= "\n";
                    $divendres .= $contingutHorari[7];
                    $divendresEstil = "3";
                }
            }
        } else {
            $divendres = '';
            $divendresEstil = "0";
        }



        echo '<tr>';
        echo '<td class="col-sm-1" class="form-control" data-horainici="' . $row['horainici'] . '" data-esdescans="' . $row['esdescans'] . '">Inici: ' . $row['horainici'] . '<br>' . 'Fi: ' . $row['horafi'] . '<br>' . $esdescans . '</td>';
        echo '<td class="col-sm-2" data-estil="' . $dillunsEstil . '"><textarea rows="4" class="form-control" id="ta-' . $cont . '-1" readonly>' . $dilluns . '</textarea>' . $button1 . $button2 . '</td>';
        echo '<td class="col-sm-2" data-estil="' . $dimartsEstil . '"><textarea rows="4" class="form-control" id="ta-' . $cont . '-2" readonly>' . $dimarts . '</textarea>' . $button1 . $button2 . '</td>';
        echo '<td class="col-sm-2" data-estil="' . $dimecresEstil . '"><textarea rows="4" class="form-control" id="ta-' . $cont . '-3" readonly>' . $dimecres . '</textarea>' . $button1 . $button2 . '</td>';
        echo '<td class="col-sm-2" data-estil="' . $dijousEstil . '"><textarea rows="4" class="form-control" id="ta-' . $cont . '-4" readonly>' . $dijous . '</textarea>' . $button1 . $button2 . '</td>';
        echo '<td class="col-sm-2" data-estil="' . $divendresEstil . '"><textarea rows="4" class="form-control" id="ta-' . $cont . '-5" readonly>' . $divendres . '</textarea>' . $button1 . $button2 . '</td>';
        echo '</tr>';
        $cont++;
    }



    echo '</tbody>';
    echo '</table>';
}



$result1->close();
$result->close();
$conn->close();

