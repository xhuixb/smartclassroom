<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
require '../classes/utilitatsProfe.php';

session_start();

//recollim les dades
$nivell = $_POST['nivell'];
$grup = $_POST['grup'];
$alumne = $_POST['alumne'];
$dataInici = $_POST['dataInici'];
$dataFinal = $_POST['dataFinal'];
$comentari = $_POST['comentari'];
$presencia = $_POST['presencia'];


$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


//sessions on hi ha alumne dels grups personals
$query = "select ga26_codi_professor as codiprof,"
        . "ga26_dia_setmana as dia,ga26_hora_inici as hora,ga26_nivell as codinivell,ga26_codi_assignatura as codiassig,ga26_grup as codigrup,ga26_codi_aula as codiaula,"
        . "ga26_tipus_grup as tipusgrup"
        . " from ga26_horaris_docents,ga23_grups_profes_cap,ga24_grups_profes_det"
        . " where ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_tipus_grup=1 and ga26_grup=ga23_codi_grup and ga26_grup=ga24_codi_grup and ga24_codi_alumne=" . $alumne . " and ga26_is_lectiva=1 "
        . " order by codiprof";

//group by dia,hora,codiprof order by hora,codiassig
//echo $query;
//executem la consulta
$result = $conn->query($query);



if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    $conta = 0;
    while ($row = $result->fetch_assoc()) {

        //posem la informació en un array associativa
        $sessionsArrayGrupsPersonals[$conta] = join('<#>', $row);
        $conta++;
    }
}

$result->close();


//ara anem a buscar els horaris dels grups generals

$query = "select ga26_codi_professor as codiprof,"
        . "ga26_dia_setmana as dia,ga26_hora_inici as hora,ga26_nivell as codinivell,ga26_codi_assignatura as codiassig,ga26_grup as codigrup,ga26_codi_aula as codiaula,"
        . "ga26_tipus_grup as tipusgrup"
        . " from ga26_horaris_docents,ga12_alumnes_curs"
        . " where ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_tipus_grup=0 and ga26_codi_curs=ga12_codi_curs and ga26_grup=ga12_codi_grup and ga26_nivell=ga12_codi_nivell and ga12_id_alumne=" . $alumne . " and ga26_is_lectiva=1 "
        . " order by codiprof";

//group by dia,hora,codiprof order by hora,codiassig
//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    $conta = 0;
    while ($row = $result->fetch_assoc()) {

        //posem la informació en un array associativa
        $sessionsArrayGrupsGenerals[$conta] = join('<#>', $row);
        $conta++;
    }
}

$result->close();



//echo $dataInici;
//echo $dataFinal;
$dataI = date_create_from_format('d/m/Y', $dataInici);
$dataF = date_create_from_format('d/m/Y', $dataFinal);

$diff = date_diff($dataI, $dataF);
$difDies = $diff->format("%R%a");


//mirem les sessions per el primer dia que sempre hi serà
$diaSetmanaInici = $dataI->format("w");
$sessionsTractar = [];

//no dissabtes ni diumenges
if ($diaSetmanaInici != '0' && $diaSetmanaInici != '6') {
    $contaSessions = 0;

//pels grups personals
    for ($i = 0; $i < count($sessionsArrayGrupsPersonals); $i++) {

        $sessionsStringGrupsPersonals = explode('<#>', $sessionsArrayGrupsPersonals[$i]);
        if ($diaSetmanaInici === $sessionsStringGrupsPersonals[1]) {
            //anem a comprovar si el profe és actiu en aquesta data
            $actiu = comprovaProfeActiu($dataI->format('Y-m-d'), $sessionsStringGrupsPersonals[0], $conn);

            if ($actiu === true) {
                $contaSessions++;
            
            }
        }
    }

//pels grups generals
    for ($i = 0; $i < count($sessionsArrayGrupsGenerals); $i++) {

        $sessionsStringGrupsGenerals = explode('<#>', $sessionsArrayGrupsGenerals[$i]);
        if ($diaSetmanaInici === $sessionsStringGrupsGenerals[1]) {

            $actiu = comprovaProfeActiu($dataI->format('Y-m-d'), $sessionsStringGrupsGenerals[0], $conn);

            if ($actiu === true) {
                $contaSessions++;
               
            }
        }
    }


    $sessionsTractar[$dataInici] = $contaSessions;
}



for ($i = 0; $i < $difDies; $i++) {
    //afegim un dia
    date_add($dataI, date_interval_create_from_date_string("1 days"));
    //mirem el dia de la setmana que és
    $diaSetmana = $dataI->format("w");
    //fem el mateix que a la data inicial
    if ($diaSetmana != '0' && $diaSetmana != '6') {
        $contaSessions = 0;
        for ($j = 0; $j < count($sessionsArrayGrupsPersonals); $j++) {

            $sessionsStringGrupsPersonals = explode('<#>', $sessionsArrayGrupsPersonals[$j]);
            if ($diaSetmana === $sessionsStringGrupsPersonals[1]) {
                //anem a comprovar si el profe és actiu en aquesta data
                $actiu = comprovaProfeActiu($dataI->format('Y-m-d'), $sessionsStringGrupsPersonals[0], $conn);

                if ($actiu === true) {
                    $contaSessions++;
                
                }
            }
        }
        for ($j = 0; $j < count($sessionsArrayGrupsGenerals); $j++) {

            $sessionsStringGrupsGenerals = explode('<#>', $sessionsArrayGrupsGenerals[$j]);
            if ($diaSetmana === $sessionsStringGrupsGenerals[1]) {
                //anem a comprovar si el profe és actiu en aquesta data
                $actiu = comprovaProfeActiu($dataI->format('Y-m-d'), $sessionsStringGrupsGenerals[0], $conn);

                if ($actiu === true) {
                    $contaSessions++;
                }
            }
        }

        $sessionsTractar[date_format($dataI, "d/m/Y")] = $contaSessions;
    }
}

$progressBar = '';

$progressBar .= '<div class="progress">';
$progressBar .= '<div class="progress progress-bar progress-bar-info" role="progressbar" aria-valuenow="50"';
$progressBar .= 'aria-valuemin="0" aria-valuemax="100" style="width:100%">';
$progressBar .= '</div>';
$progressBar .= '</div>';




//construim capçalera de la taula
echo '<br>';
echo '<table id="taulaSessionsAlumne" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-1">Data</th>';
echo '<th class="col-sm-1">Sessions</th>';
echo '<th class="col-sm-2">Estat</th>';
echo '</tr>';
echo '</thead>';

echo '<tbody id="cosTaulaSessionsAlumne">';
$conta = 0;
foreach ($sessionsTractar as $x => $x_value) {
    if ($x_value != 0) {
        echo '<tr>';
        echo '<td class="col-sm-1">' . $x . '</td>';
        echo '<td class="col-sm-1">' . $x_value . '</td>';
        echo '<td class="col-sm-2"><div id="ses' . $conta . '" class="sessionsAlumne">' . $progressBar . '</div></td>';
        echo '</tr>';
        $conta++;
    }
}

//tanquem cos i taula
echo '</tbody>';
echo '</table>';


$conn->close();

