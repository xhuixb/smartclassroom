
<script>
    jQuery.noConflict();
    $(document).ready(function () {
        $('[data-toggle="popover"]').popover({html: true});
    });
</script>
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$codiEquipament = $_POST['codiEquipament'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "select ga46_codi as codiprestec, concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as professor,ga46_data_prestec as dataprestec,ga46_hora_prestec as horaprestec,ga46_data_devolucio as datadevolucio,ga46_hora_devolucio as horadevolucio,"
        . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=ga46_prof_devolucio) as profdev,"
        . "(select ga06_descripcio_nivell from ga06_nivell,ga12_alumnes_curs where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_id_alumne=ga46_alumne and ga12_codi_nivell=ga06_codi_nivell) as nivell,"
        . "(select ga07_descripcio_grup from ga07_grup,ga12_alumnes_curs where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_id_alumne=ga46_alumne and ga12_codi_grup=ga07_codi_grup) as grup"
        . " from ga46_prestecs,ga11_alumnes,ga04_professors"
        . " where ga46_alumne=ga11_id_alumne and ga46_prof_prestec=ga04_codi_prof"
        . " and ga46_equip=" . $codiEquipament . " and ga46_curs=" . $_SESSION['curs_actual'] . " order by ga46_data_prestec desc,ga46_hora_prestec desc";


$result = $conn->query($query);

if (!$result)
    die($conn->error);

echo '<table id="taulaDetallPrestecs" class="table">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-1"><span class="glyphicon glyphicon-trash"></span></th>';
echo '<th class="col-sm-3">Alumne</th>';
echo '<th class="col-sm-1">Prof.</th>';
echo '<th class="col-sm-2">Préstec</th>';
echo '<th class="col-sm-1"></th>';
echo '<th class="col-sm-2">Devolució</th>';
echo '<th class="col-sm-1"></th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {
    echo '<tbody id="cosTaulaDetallPrestecs">';
    while ($row = $result->fetch_assoc()) {

        $dataPrestec = substr($row['dataprestec'], 8) . "/" . substr($row['dataprestec'], 5, 2) . "/" . substr($row['dataprestec'], 0, 4);
        if ($row['datadevolucio'] != '') {
            $dataDevolucio = substr($row['datadevolucio'], 8) . "/" . substr($row['datadevolucio'], 5, 2) . "/" . substr($row['datadevolucio'], 0, 4);
        } else {
            $dataDevolucio = '';
        }

        $profDetall = "<ul>";
        $profDetall .= "<li>Préstec: <strong>" . $row['professor'] . '</strong></li>';
        $profDetall .= "<li>Devolució: <strong>" . $row['profdev'] . '</strong></li>';
        $profDetall .= "</ul>";

        $alumneDetall = "<ul>";
        $alumneDetall .= "<li>Nivell: <strong>" . $row['nivell'] . "</strong></li>";
        $alumneDetall .= "<li>Grup: <strong>" . $row['grup'] . "</strong></li>";
        $alumneDetall .= "</ul>";


        echo '<tr>';
        echo '<td class="col-sm-1"><input type="checkbox" value="" class="checkEsborrar" onclick="comprovaEsborra();" data-codi-prestec="' . $row['codiprestec'] . '"/></td>';
        echo '<td class="col-sm-3"><a href="#" data-toggle="popover" data-trigger="focus" title="<strong>' . $row['alumne'] . '</strong>" data-content="' . $alumneDetall . '">' . $row['alumne'] . '</a></td>';
        echo '<td class="col-sm-1" data-prof="' . $row['professor'] . '" data-prof-dev="' . $row['profdev'] . '"><button type="button" class="btn btn-info" data-toggle="popover" data-trigger="focus" title="<strong>Professors</strong>" data-content="' . $profDetall . '"><span class="glyphicon glyphicon-education"></span></button></td>';
        echo '<td class="col-sm-2">' . $dataPrestec . '</td>';
        echo '<td class="col-sm-1">' . $row['horaprestec'] . '</td>';
        echo '<td class="col-sm-2">' . $dataDevolucio . '</td>';
        echo '<td class="col-sm-1">' . $row['horadevolucio'] . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
}

//tanquem cos i taula

echo '</table>';


$result->close();

$conn->close();



