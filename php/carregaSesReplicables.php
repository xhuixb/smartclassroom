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
//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$dia = $_POST['dia'];
$hora = $_POST['hora'];

if ($_POST['profe'] == '') {
    //no és guàrdia
    $profe = $_SESSION['prof_actual'];
} else {
    //és guàrdia
    $profe = $_POST['profe'];
}


//convertim el dia en el num de setmana per anar a buscar l'horari del profe

$date = date_create_from_format('Y-m-d', $dia);
$diaSetmana = $date->format('w');

//anem a buscar la sessió a replicar
$query = "select ga26_nivell as nivell,ga26_grup as grup, ga26_tipus_grup as tipusgrup"
        . " from ga26_horaris_docents"
        . " where ga26_codi_curs=" . $_SESSION['curs_actual']
        . " and ga26_codi_professor=" . $profe
        . " and ga26_dia_setmana=" . $diaSetmana
        . " and ga26_hora_inici='" . $hora . "'";


//es clau primari per tant un sol resultat
$result = $conn->query($query);


if (!$result)
    die($conn->error);
$row = $result->fetch_assoc();

$nivell = $row['nivell'];
$grup = $row['grup'];
$tipusGrup = $row['tipusgrup'];

//anem a buscar les sessions a replicar
if ($tipusGrup == '0') {
    //grup general
    $query = "select ga26_hora_inici as horainici,ga06_descripcio_nivell as nomnivell,ga07_descripcio_grup as nomgrup,ga18_desc_assignatura as nomassig"
            . " from ga26_horaris_docents,ga06_nivell,ga07_grup,ga18_assignatures"
            . " where ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_codi_professor=" . $profe . " and ga26_dia_setmana=" . $diaSetmana . " and ga26_hora_inici<>'" . $hora . "' and ga26_nivell=" . $nivell . " and ga26_tipus_grup=" . $tipusGrup
            . " and ga26_grup=1 and ga26_nivell=ga06_codi_nivell and ga26_grup=ga07_codi_grup and ga26_codi_assignatura=ga18_codi_assignatura order by horainici";
} else {
    //grup personal
    $query = "select ga26_hora_inici as horainici,ga06_descripcio_nivell as nomnivell,ga23_nom_grup as nomgrup,ga18_desc_assignatura as nomassig"
            . " from ga26_horaris_docents,ga06_nivell,ga18_assignatures,ga23_grups_profes_cap"
            . " where ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_codi_professor=" . $profe . " and ga26_dia_setmana=" . $diaSetmana . " and ga26_hora_inici<>'" . $hora . "' and ga26_nivell=" . $nivell . " and ga26_tipus_grup=1"
            . " and ga26_grup=" . $grup . " and ga26_nivell=ga06_codi_nivell and ga26_grup=ga23_codi_grup and ga26_codi_assignatura=ga18_codi_assignatura order by horainici";
}

$result = $conn->query($query);


if (!$result)
    die($conn->error);


//construim capçalera de la taula
echo '<h4>Només es replicaran les dades d\'assistència. La resta de d\'informació (faltes d\'ordre,comentaris,...) no es replicaran </h4>';
echo '<table id="taulaSessionsReplicables" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-1"><span class="glyphicon glyphicon-repeat"></span></th>';
echo '<th class="col-sm-2">Hora</th>';
echo '<th class="col-sm-2">Nivell</th>';
echo '<th class="col-sm-3">Grup</th>';
echo '<th class="col-sm-4">Assignatura</th>';
echo '<th></th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {
    echo '<tbody id="cosTaulaSessionsReplicables" data-dia="' . $dia . '" data-hora="' . $hora . '" data-profe="' . $profe . '">';
    while ($row = $result->fetch_assoc()) {
        //anem a veure si la sessió existeix
        $query1 = "select count(*) as conta from ga28_cont_presencia_cap"
                . " where ga28_codi_curs=" . $_SESSION['curs_actual'] . " and ga28_professor=" . $profe . " and ga28_dia='" . $dia . "' and ga28_hora='" . $row['horainici'] . "'";

        $result1 = $conn->query($query1);
        if (!$result1)
            die($conn->error);

        $row1 = $result1->fetch_assoc();

        if ($row1['conta'] == '0') {
            //no hi ha sessió
            echo '<tr>';
            echo '<td class="col-sm-1"><input type="checkbox" class="sesAReplicar" value=""></td>';
            echo '<td class="col-sm-2">' . $row['horainici'] . '</td>';
            echo '<td class="col-sm-2">' . $row['nomnivell'] . '</td>';
            echo '<td class="col-sm-3">' . $row['nomgrup'] . '</td>';
            echo '<td class="col-sm-4">' . $row['nomassig'] . '</td>';
            echo '</tr>';
        }
    }
}
//tanquem cos i taula
echo '</tbody>';
echo '</table>';

$result->close();
$conn->close();
