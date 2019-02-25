



<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$filtre = $_POST['filtre'];


//$_SESSION['curs_actual'] = 3;
//$_SESSION['prof_actual'] = 0;
//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "select ga45_codi as codiequipament,ga45_descripcio as descequipament,"
        . "(select ga46_alumne as codialumne from ga46_prestecs where ga46_equip=codiequipament and ga46_data_devolucio is null) as codialumne,"
        . "(select concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) from ga11_alumnes where ga11_id_alumne=codialumne) as nomalumne,"
        . "(select ga46_prof_prestec as codialumne from ga46_prestecs where ga46_equip=codiequipament and ga46_data_devolucio is null) as codiprof,"
        . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ' ,ga04_nom) from ga04_professors where ga04_codi_prof=codiprof) as nomprofessor,"
        . "(select count(*) from ga46_prestecs where ga46_equip=codiequipament and ga46_curs=" . $_SESSION['curs_actual'] . ") as totalprestecs"
        . " from ga45_inventari where ga45_es_actiu='1' order by totalprestecs asc,ga45_codi asc";

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

echo '<table id="taulaEquipament" class="table">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-2">Equipament</th>';
echo '<th class="col-sm-2">Alumne</th>';
echo '<th class="col-sm-2">Professor</th>';
echo '<th class="col-sm-1"><center>Estat</center></th>';
echo '<th class="col-sm-1"><center><span class="glyphicon glyphicon-option-vertical"></span></center></th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {
    echo '<tbody id="cosTaulaEquipament">';
    while ($row = $result->fetch_assoc()) {

        if ($row['codialumne'] == '') {
            $colorEstat = "btn-success";
            $textEstat = "Disponible";
            $estat = '0';
            $toolTip = "Prem per a prestar";
        } else {
            $colorEstat = "btn-danger";
            $textEstat = "Prestat";
            $estat = '1';
            $toolTip = "Prem per a retornar";
        }

        if ($row['totalprestecs'] == 0) {
            //encara no hi ha prestecs
            $habilitaDetall = "disabled";
        } else {
            //sí que hi ha prestecs
            $habilitaDetall = '';
        }

        if ($filtre == "1" || (($filtre == "2") && ($row['codialumne'] == '')) || (($filtre == "3") && ($row['codialumne'] != ''))) {

            echo '<tr>';
            echo '<td class="col-sm-2" data-codi="' . $row['codiequipament'] . '">' . $row['descequipament'] . '</td>';
            echo '<td class="col-sm-2">' . $row['nomalumne'] . '</td>';
            echo '<td class="col-sm-2">' . $row['nomprofessor'] . '</td>';
            echo '<td class="col-sm-1"><button class="btn ' . $colorEstat . ' form-control" data-toggle="tooltip" title="' . $toolTip . '" type="button" onclick="canviaEstatPrestec(this)" data-estat="' . $estat . '">' . $textEstat . '</button></td>';
            echo '<td class="col-sm-1"><center><button type="button" class="btn btn-info form-control" ' . $habilitaDetall . ' data-toggle="modal" data-target="#detallPrestecs" onclick="mostraDetallPrestecs(this)"><span class="glyphicon glyphicon-option-vertical">(' . $row['totalprestecs'] . ')</span></button></center></td>';
            echo '</tr>';
        }
    }
    echo '</tbody>';
}

//tanquem cos i taula

echo '</table>';


$result->close();

$conn->close();

