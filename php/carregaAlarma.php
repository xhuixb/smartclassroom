<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

session_start();
//fem la connexió
//establim la connexió

$tipusFalta = $_POST['tipusFalta'];

if($tipusFalta!=''){
    
    $whereTipusFalta=" and ga31_tipus_falta=".$tipusFalta;
}else{
    
    $whereTipusFalta='';
}

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "select concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga22_nom_falta as tipusfalta,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,ga31_alumne as codialumne,count(*) as total,gs22_quantitat_avis as topall"
        . " from ga31_faltes_ordre,ga06_nivell,ga07_grup, ga11_alumnes,ga12_alumnes_curs,ga22_tipus_falta"
        . " where ga31_codi_curs=" . $_SESSION['curs_actual'].$whereTipusFalta." and ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne AND ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and  (ga31_estat=1 or ga31_estat=2) and ga31_tipus_falta=ga22_codi_falta group by ga31_alumne,ga31_tipus_falta order by alumne";

$result = $conn->query($query);


if (!$result)
    die($conn->error);

//construim capçalera de la taula
echo '<br>';
echo '<table id="taulaAlarmaFaltes" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th>Alumne</th>';
echo '<th>Nivell</th>';
echo'<th>Grup</th>';
echo '<th>Tipus falta</th>';
echo '<th>Total</th>';
echo '<th>Llindar</th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {
    echo '<tbody id="cosTaulaAlarmaFaltes">';
    while ($row = $result->fetch_assoc()) {

        if ($row['total'] >= $row['topall']) {

            echo '<tr>';
            echo '<td class="col-sm-2">' . $row['alumne'] . '</td>';
            echo '<td class="col-sm-1">' . $row['nivell'] . '</td>';
            echo '<td class="col-sm-1">' . $row['grup'] . '</td>';
            echo '<td class="col-sm-1">' . $row['tipusfalta'] . '</td>';
            echo '<td class="col-sm-1">' . $row['total'] . '</td>';
            echo '<td class="col-sm-1">' . $row['topall'] . '</td>';
            echo '</tr>';
        }
    }
}
//tanquem cos i taula
echo '</tbody>';
echo '</table>';
$result->close();
$conn->close();
