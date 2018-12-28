<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$profe = $_POST['profe'];
$dataSessio = $_POST['dataSessio'];
$hora = $_POST['hora'];
$profSessio = $_POST['profSessio'];
$grup = $_POST['grup'];
$tipusGrup = $_POST['tipusGrup'];
$nivell = $_POST['nivell'];



//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

if ($profSessio != '') {
    //és una sessio
    $query = "select ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga15_check_present as espresent,ga15_check_absent as esabsent,ga15_check_retard as esretard"
            . " from ga11_alumnes,ga06_nivell,ga07_grup,ga12_alumnes_curs,ga15_cont_presencia"
            . " where ga15_codi_curs=".$_SESSION['curs_actual']." and ga15_codi_professor=" . $profSessio . " and ga15_dia='" . $dataSessio . "' and ga15_hora_inici='" . $hora . "'"
            . " and ga15_codi_curs=ga12_codi_curs and ga15_alumne=ga12_id_alumne and ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup order by alumne";

    

    //executem la query
    //executem la consulta
    $result = $conn->query($query);


    if (!$result)
        die($conn->error);

    //construim capçalera de la taula
 
    echo '<table id="taulaDetallSessions" class="table table-fixed">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="col-sm-4">Nivell/grup</th>';
    echo '<th class="col-sm-8">Alumne</th>';

    echo '</tr>';
    echo '</thead>';

    if ($result->num_rows > 0) {
        echo '<tbody id="cosTaulaDetallAbsencies">';
        while ($row = $result->fetch_assoc()) {

            $nivellGrup = $row['nivell'] . '/' . $row['grup'];
            if($row['espresent']=='1'){
                //present
                $colorAssist='btn-success';
            }elseif($row['esabsent']=='1'){
                //absent
                $colorAssist='btn-danger';
            }else{
                //retard
                $colorAssist='btn-warning';
            }
            
            echo '<tr class="'.$colorAssist.'">';
            echo '<td class="col-sm-4">' . $nivellGrup . '</td>';
            echo '<td class="col-sm-8">' . $row['alumne'] . '</td>';
            echo '</tr>';
        }
        echo '</thead>';
        echo '</table>';
    }

    $result->close();
    $conn->close();
} else {

    //es un horari
    if ($tipusGrup == '1') {
        //és un grup personal de professor
        $query = "select ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne"
                . " from ga11_alumnes,ga06_nivell,ga07_grup,ga24_grups_profes_det,ga12_alumnes_curs"
                . " where ga24_codi_grup=" . $grup . " and ga24_codi_alumne=ga12_id_alumne and ga12_id_alumne=ga11_id_alumne and ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup order by alumne";
    } else {
        //es un grup general
        $query = "select ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne"
                . " from ga11_alumnes,ga06_nivell,ga07_grup,ga12_alumnes_curs"
                . " where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_codi_nivell=" . $nivell . " and ga12_codi_grup=" . $grup . " and ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup order by alumne";
    }
   

    //executem la query
    //executem la consulta
    $result = $conn->query($query);


    if (!$result)
        die($conn->error);

    //construim capçalera de la taula
  
    echo '<table id="taulaDetallSessions" class="table table-fixed">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="col-sm-4">Nivell/grup</th>';
    echo '<th class="col-sm-8">Alumne</th>';

    echo '</tr>';
    echo '</thead>';

    if ($result->num_rows > 0) {
        echo '<tbody id="cosTaulaDetallAbsencies">';
        while ($row = $result->fetch_assoc()) {

            $nivellGrup = $row['nivell'] . '/' . $row['grup'];
            echo '<tr>';
            echo '<td class="col-sm-4">' . $nivellGrup . '</td>';
            echo '<td class="col-sm-8">' . $row['alumne'] . '</td>';
            echo '</tr>';
        }
        echo '</thead>';
        echo '</table>';
    }

    $result->close();
    $conn->close();
}


