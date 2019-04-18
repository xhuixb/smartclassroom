<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

if ($_POST['dataIniciResum'] != '') {
    $dataIniciResum = substr($_POST['dataIniciResum'], 6) . '-' . substr($_POST['dataIniciResum'], 3, 2) . '-' . substr($_POST['dataIniciResum'], 0, 2);
    $whereDataIniciResum = " and ga46_data_prestec>='" . $dataIniciResum . "' ";
} else {
    $dataIniciResum = '';
    $whereDataIniciResum = "";
}
if ($_POST['dataFiResum'] != '') {
    $dataFiResum = substr($_POST['dataFiResum'], 6) . '-' . substr($_POST['dataFiResum'], 3, 2) . '-' . substr($_POST['dataFiResum'], 0, 2);
    $whereDataFiResum = " and ga46_data_prestec<='" . $dataFiResum . "' ";
} else {
    $dataFiResum = '';
    $whereDataFiResum = "";
}


$codiAgrupacio = $_POST['codiAgrupacio'];


//echo $dataIniciResum . '-' . $dataFiResum . '-' . $codiAgrupacio;
//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

if ($codiAgrupacio == '1') {
    $query = "select count(*) as conta,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne from ga11_alumnes,ga46_prestecs"
            . " where ga46_curs=" . $_SESSION['curs_actual'] . " and ga46_alumne=ga11_id_alumne " . $whereDataIniciResum . $whereDataFiResum . " group by ga46_alumne order by conta desc,alumne";
} elseif ($codiAgrupacio == '0') {
    $query = "select count(*) as conta,ga45_descripcio as equip from ga46_prestecs,ga45_inventari"
            . " where ga46_curs=" . $_SESSION['curs_actual'] . " and ga46_equip=ga45_codi " . $whereDataIniciResum . $whereDataFiResum . " group by ga46_equip order by conta desc";
} else {
    //codi agrupació =2
    $query = "select count(*) as conta,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as professor from ga04_professors,ga46_prestecs"
            . " where ga46_curs=" . $_SESSION['curs_actual'] . " and ga46_prof_prestec=ga04_codi_prof " . $whereDataIniciResum . $whereDataFiResum . " group by ga46_prof_prestec order by conta desc,professor";
}

$result = $conn->query($query);

if (!$result)
    die($conn->error);



if ($codiAgrupacio == '1') {
    echo '<table id="taulaResumPrestecs" class="table">';

    echo '<thead>';
    echo '<tr>';
    echo '<th class="col-sm-1">Alumne</th>';
    echo '<th class="col-sm-3" style="text-align: right;">Quantitat</th>';
    echo '</tr>';
    echo '</thead>';

    if ($result->num_rows > 0) {
        $contador = 0;
        echo '<tbody id="cosTaulaDetallPrestecs">';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td class="col-sm-4">' . $row['alumne'] . '</td>';
            echo '<td class="col-sm-2" style="text-align: right;">' . $row['conta'] . '</td>';
            echo '</tr>';
            $contador += $row['conta'];
        }
        echo '<tr>';
        echo '<th class="col-sm-1">Total</th>';
        echo '<th class="col-sm-3" style="text-align: right;">' . $contador . '</th>';
        echo '</tr>';
        echo '</tbody>';
    }

//tanquem cos i taula

    echo '</table>';
} elseif ($codiAgrupacio == '0') {
    echo '<table id="taulaResumPrestecs" class="table">';

    echo '<thead>';
    echo '<tr>';
    echo '<th class="col-sm-1">Equip</th>';
    echo '<th class="col-sm-3" style="text-align: right;">Quantitat</th>';
    echo '</tr>';
    echo '</thead>';

    if ($result->num_rows > 0) {
        $contador = 0;
        echo '<tbody id="cosTaulaDetallPrestecs">';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td class="col-sm-4">' . $row['equip'] . '</td>';
            echo '<td class="col-sm-2" style="text-align: right;">' . $row['conta'] . '</td>';
            echo '</tr>';
            $contador += $row['conta'];
        }
        echo '<tr>';
        echo '<th class="col-sm-1">Total</th>';
        echo '<th class="col-sm-3" style="text-align: right;">' . $contador . '</th>';
        echo '</tr>';
        echo '</tbody>';

        echo '</tbody>';
    }

//tanquem cos i taula

    echo '</table>';
} else {

    echo '<table id="taulaResumPrestecs" class="table">';

    echo '<thead>';
    echo '<tr>';
    echo '<th class="col-sm-1">Prestador</th>';
    echo '<th class="col-sm-3" style="text-align: right;">Quantitat</th>';
    echo '</tr>';
    echo '</thead>';

    if ($result->num_rows > 0) {
        $contador = 0;
        echo '<tbody id="cosTaulaDetallPrestecs">';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td class="col-sm-4">' . $row['professor'] . '</td>';
            echo '<td class="col-sm-2" style="text-align: right;">' . $row['conta'] . '</td>';
            echo '</tr>';
            $contador += $row['conta'];
        }
        echo '<tr>';
        echo '<th class="col-sm-1">Total</th>';
        echo '<th class="col-sm-3" style="text-align: right;">' . $contador . '</th>';
        echo '</tr>';
        echo '</tbody>';

        echo '</tbody>';
    }

//tanquem cos i taula

    echo '</table>';
}
$result->close();

$conn->close();

