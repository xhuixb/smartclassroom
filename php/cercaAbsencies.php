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
$nivell = $_POST['nivell'];
$grup = $_POST['grup'];
$checkJusti = $_POST['checkJusti'];
$checkNoJusti = $_POST['checkNoJusti'];
$checkOrderAlumne = $_POST['checkOrderAlumne'];


$whereJustificat;
$orderByJustificat;

if ($checkJusti == "1" && $checkNoJusti == "1") {
    //totes les absències
    $whereJustificat = "";
} else if ($checkJusti == '1' && $checkNoJusti == "0") {
    //només les jusificades
    $whereJustificat = "and ga15_check_justificat='1'";
} else if ($checkJusti == '0' && $checkNoJusti == "1") {
    //només les no justificades
    $whereJustificat = "and ga15_check_justificat='0'";
} else {
    $whereJustificat = "and ga15_check_justificat='0' and ga15_check_justificat='1'";
}

if ($checkOrderAlumne == "1") {
    $orderByJustificat = " order by nomalumne asc,dia desc,hora asc";
} else {
    $orderByJustificat = " order by dia desc,nomalumne asc,hora asc";
}


//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "select ga15_dia as dia,ga15_hora_inici as hora,ga15_alumne as codialumne, ga15_codi_professor as codiprof, concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as nomalumne,ga15_check_justificat as justificat,ga15_motiu_justificat as motiujustifi"
        . " from ga15_cont_presencia,ga12_alumnes_curs,ga11_alumnes"
        . " where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_codi_nivell=" . $nivell . " and ga12_codi_grup=" . $grup . " and ga15_check_absent='1' and"
        . " ga15_codi_curs=ga12_codi_curs and ga15_alumne=ga12_id_alumne and ga12_id_alumne=ga11_id_alumne " . $whereJustificat
        . $orderByJustificat;

$result = $conn->query($query);


if (!$result)
    die($conn->error);


//construim capçalera de la taula
echo '<br>';
echo '<table id="taulaJustifiAbsencies" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th>Dia</th>';
echo '<th>Hora</th>';
echo '<th>Alumne</th>';
echo '<th><center>Justificat<center></th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {
    $cont = 0;

    echo '<tbody id="costaulaJustifiAbsencies">';
    while ($row = $result->fetch_assoc()) {

        if ($row['justificat'] == "1") {
            $estajustificat = "btn-success";
            $textjustificat = "Justificat";
        } else {
            $estajustificat = "btn-danger";
            $textjustificat = "No justificat";
        }
        echo '<tr>';
        echo '<td class="col-sm-1">' . $row['dia'] . '</td>';
        echo '<td class="col-sm-1">' . $row['hora'] . '</td>';
        echo '<td class="col-sm-2" data-codi="' . $row['codialumne'] . '" data-linia="' . $cont . '">' . $row['nomalumne'] . '</td>';
        echo '<td class="col-sm-1" data-codi-justifi="' . $row['justificat'] . '" data-motiu-justifi="' . $row['motiujustifi'] . '" data-codi-prof="' . $row['codiprof'] . '">';
        echo '<button type="button" class="btn form-control ' . $estajustificat . '" data-toggle="modal" data-target="#absenciesModalForm" onclick="carregaJustifiAbs(this)">';
        echo '<span class="glyphicon glyphicon-pencil"></span>' . $textjustificat . '</button>';
        echo '</td>';

        echo '</tr>';
        $cont++;
    }
}
//tanquem cos i taula
echo '</tbody>';
echo '</table>';

$result->close();
$conn->close();
