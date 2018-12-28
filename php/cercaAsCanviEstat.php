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
$dia = $_POST['dia'];
$hora = $_POST['hora'];
$resum = $_POST['resum'];

$diaSql = date("Y-m-d", strtotime($dia));

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

if ($resum == 0) {

    $query = "select  ga15_alumne as codialumne,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga15_codi_professor as codiprof,concat(ga04_cognom1,' ',ga04_cognom2,' ,',ga04_nom) as nomprof,"
            . "ga15_check_present as cpresent,ga15_check_absent as cabsent,ga15_check_retard as cretard,ga15_check_comunica as checkcomunica"
            . " from ga15_cont_presencia,ga11_alumnes,ga12_alumnes_curs,ga17_professors_curs,ga04_professors"
            . " where ga12_codi_nivell=" . $nivell . " and ga12_codi_grup=" . $grup . " and ga15_dia='" . $diaSql . "' and ga15_hora_inici='" . $hora . "' and ga15_codi_curs=" . $_SESSION['curs_actual']
            . " and ga12_codi_curs=ga15_codi_curs and ga15_alumne=ga12_id_alumne and ga15_codi_curs=ga17_codi_curs and ga12_id_alumne=ga11_id_alumne and ga15_codi_professor=ga17_codi_professor and ga17_codi_professor=ga04_codi_prof order by alumne";

//executem la consulta
    $result = $conn->query($query);


    if (!$result)
        die($conn->error);


//construim capçalera de la taula
    echo '<br>';
    echo '<table id="taulaAlumnesCanviEstat" class="table table-fixed">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="col-sm-1">Ordre</th>';
    echo '<th class="col-sm-2">Alumne</th>';
    echo '<th class="col-sm-2">Professor</th>';
    echo '<th class="col-sm-1">Pres</th>';
    echo '<th class="col-sm-1">Abs</th>';
    echo '<th class="col-sm-1">Ret</th>';
    echo '<th class="col-sm-1"><span class="glyphicon glyphicon-envelope"></span></th>';
    echo '<th class="col-sm-1"><center>Actualitza</center></th>';
    echo '</tr>';
    echo '</thead>';

    if ($result->num_rows > 0) {
        $cont = 1;
        echo '<tbody id="costaulaAlumnesCanviEstat">';

        while ($row = $result->fetch_assoc()) {

            echo '<tr>';

            echo '<td class="col-sm-1">' . $cont . '</td>';
            echo '<td class="col-sm-2" data-codi-alumne="' . $row['codialumne'] . '">' . $row['alumne'] . '</td>';
            echo '<td class="col-sm-2" data-codi-prof="' . $row['codiprof'] . '">' . $row['nomprof'] . '</td>';


            if ($row['cpresent'] === '1') {
                echo '<td class="col-sm-1"><input type="checkbox" value="" checked class="checkAssistEstat" onchange="comprovaCheckEstat(this)"></td>';
                echo '<td class="col-sm-1"><input type="checkbox" value="" class="checkAssistEstat" onchange="comprovaCheckEstat(this)"></td>';
                echo '<td class="col-sm-1"><input type="checkbox" value="" class="checkAssistEstat" onchange="comprovaCheckEstat(this)"></td>';
            } elseif ($row['cabsent'] === '1') {
                echo '<td class="col-sm-1"><input type="checkbox" value="" class="checkAssistEstat" onchange="comprovaCheckEstat(this)"></td>';
                echo '<td class="col-sm-1"><input type="checkbox" value="" checked class="checkAssistEstat" onchange="comprovaCheckEstat(this)"></td>';
                echo '<td class="col-sm-1"><input type="checkbox" value="" class="checkAssistEstat" onchange="comprovaCheckEstat(this)"></td>';
            } else {
                echo '<td class="col-sm-1"><input type="checkbox" value="" class="checkAssistEstat" onchange="comprovaCheckEstat(this)"></td>';
                echo '<td class="col-sm-1"><input type="checkbox" value="" class="checkAssistEstat" onchange="comprovaCheckEstat(this)"></td>';
                echo '<td class="col-sm-1"><input type="checkbox" value="" checked class="checkAssistEstat" onchange="comprovaCheckEstat(this)"></td>';
            }

            if ($row['checkcomunica'] == '1') {
                echo '<td class="col-sm-1"><input type="checkbox" value="" checked></td>';
            } else {
                echo '<td class="col-sm-1"><input type="checkbox" value=""></td>';
            }

            echo '<td class="col-sm-1">';
            echo '<button type="button" class="btn btn-success form-control" onclick="actualitzaEstat(this)">';
            echo '<span class="glyphicon glyphicon-refresh">Actualitza</span></button>';
            echo '</td>';
            echo '</tr>';

            $cont++;
        }
        //tanquem cos i taula
        echo '</tbody>';
    }
    echo '</table>';
} else {
    //llistat resumit
    $query = "select ga06_descripcio_nivell as nivell,ga12_codi_nivell as codinivell, ga07_descripcio_grup as grup,ga12_codi_grup as codigrup,count(*) as totalalumnes,"
            . "(select count(*) from ga15_cont_presencia,ga12_alumnes_curs where ga12_id_alumne=ga15_alumne and ga12_codi_curs=".$_SESSION['curs_actual']." and ga12_codi_nivell=codinivell and ga12_codi_grup=codigrup and ga15_dia='".$diaSql."' and ga15_hora_inici='".$hora."') as totalassist"
            . " from ga12_alumnes_curs,ga06_nivell,ga07_grup"
            . " where ga06_codi_nivell=ga12_codi_nivell and ga07_codi_grup=ga12_codi_grup and ga12_codi_curs=".$_SESSION['curs_actual']
            . " group by codinivell,codigrup";

    
    //executem la consulta
    $result = $conn->query($query);


    if (!$result)
        die($conn->error);
    //construim capçalera de la taula
    echo '<br>';
    echo '<table id="taulaAlumnesCanviEstat" class="table table-fixed">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="col-sm-1">Nivell</th>';
    echo '<th class="col-sm-1">Grup</th>';
    echo '<th class="col-sm-1"><center>Alumnes Totals</center></th>';
    echo '<th class="col-sm-1"><center>Control Assist</center></th>';
    echo '<th class="col-sm-1"><center>Diferència</center></th>';
    echo '</tr>';
    echo '</thead>';

    if ($result->num_rows > 0) {

        echo '<tbody id="costaulaAlumnesCanviEstat">';
        while ($row = $result->fetch_assoc()) {
            $diferencia=$row['totalalumnes']-$row['totalassist'];
            echo '<tr>';
            echo '<td class="col-sm-1">' . $row['nivell'] . '</td>';
            echo '<td class="col-sm-1">' . $row['grup'] . '</td>';
            echo '<td class="col-sm-1"><center>' . $row['totalalumnes'] . '</center></td>';
            echo '<td class="col-sm-1"><center>' . $row['totalassist'] . '</center></td>';
            echo '<td class="col-sm-1"><center>' . $diferencia . '</center></td>';
            echo '</tr>';
        }
    }
    echo '</tbody>';
    echo '</table>';
}
$result->close();
$conn->close();
