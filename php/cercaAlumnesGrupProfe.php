<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();
//$_SESSION['curs_actual'] = 3;
//$_SESSION['prof_actual'] = 0;
$codinivell = $_POST['codinivell'];
$grup = $_POST['grup'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


//mirem si és un grup general
if ($codinivell == '0') {
    $whereNivell = '';
} else {
    $whereNivell = " and ga12_codi_nivell=" . $codinivell;
}


$query = "select ga12_id_alumne as codialumne,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as nomalumne,ga06_descripcio_nivell as descnivell,ga07_descripcio_grup as descgrup,"
        . "(select ga24_codi_alumne from ga24_grups_profes_det where ga24_codi_grup=" . $grup . " and ga24_codi_alumne=ga12_id_alumne) as alumnegrup"
        . " from ga11_alumnes,ga12_alumnes_curs,ga07_grup,ga06_nivell"
        . " where ga12_codi_curs=" . $_SESSION['curs_actual'] . $whereNivell . " and ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup"
        . " order by nomalumne";

$result = $conn->query($query);


if (!$result)
    die($conn->error);


//construim capçalera de la taula

echo '<table id="taulaAlumnesMeuGrup" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-2">Membre</th>';
echo '<th class="col-sm-2">Nivell/grup</th>';
echo '<th class="col-sm-8">Alumne</th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {
    echo '<tbody id="costaulaAlumnesMeuGrup" data-codigrup="' . $grup . '">';
    while ($row = $result->fetch_assoc()) {

        $marcat;
        if ($row['alumnegrup'] != "") {
            $marcat = "checked";
        } else {
            $marcat = "";
        }
        echo '<tr>';
        echo '<td class="col-sm-2"><input type="checkbox" value="" ' . $marcat . '></td>';
        echo '<td class="col-sm-2">' . $row['descnivell'] . '-' . $row['descgrup'] . '</td>';
        echo '<td class="col-sm-8" data-codialumne="' . $row['codialumne'] . '">' . $row['nomalumne'] . '</td>';
        echo '<tr>';
        echo '</tr>';
    }
}
//tanquem cos i taula
echo '</tbody>';
echo '</table>';

$result->close();
$conn->close();
