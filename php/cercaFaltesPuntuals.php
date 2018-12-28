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


$totesFaltes = $_POST['totesFaltes'];
$checksTipus = $_POST['checksTipus'];
$dataInicial = $_POST['dataInicial'];
$dataFinal = $_POST['dataFinal'];


//decidim si s'ha de filtar per data inicial
if ($dataInicial != "") {
    $dataInicialSql = date("Y-m-d", strtotime($dataInicial));
    $whereDataInicial = " and ga31_dia>='" . $dataInicialSql . "'";
} else {
    $whereDataInicial = "";
}

//decidim si s'ha de filtar per data inicial
if ($dataFinal != "") {
    $dataFinalSql = date("Y-m-d", strtotime($dataFinal));
    $whereDataFinal = " and ga31_dia<='" . $dataFinalSql . "'";
} else {
    $whereDataFinal = "";
}

//construim el filtre de tipus

$sqlFaltes = "";
if ($totesFaltes == 'false') {
    $sqlFaltes = " and (";

    for ($i = 0; $i < count($checksTipus); $i++) {
        if ($i < count($checksTipus) - 1) {
            $sqlFaltes .= "ga31_tipus_falta=" . $checksTipus[$i] . " or ";
        } else {
            //la darrera
            $sqlFaltes .= "ga31_tipus_falta=" . $checksTipus[$i] . ") ";
        }
    }
}

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "select ga31_id as codifalta,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,"
        . "concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as professor,ga31_dia as dia ,ga31_hora_inici as hora,ga31_alumne as codialumne,ga31_codi_professor as codiprof,ga22_nom_falta as tipusfalta,ga31_motiu as motiu,ga31_estat as estat,"
        . "ga31_just_tutor as enviatutor,ga31_just_resp as enviapares from ga31_faltes_ordre,ga06_nivell,ga07_grup, ga11_alumnes,ga04_professors ,ga12_alumnes_curs,ga22_tipus_falta"
        . " where ga31_codi_curs=" . $_SESSION['curs_actual'] . $whereDataInicial . $whereDataFinal
        . " and ga31_alumne=ga12_id_alumne AND ga31_codi_curs=ga12_codi_curs and ga12_id_alumne=ga11_id_alumne and ga31_codi_professor=ga04_codi_prof and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and ga31_tipus_falta=ga22_codi_falta "
        . $sqlFaltes . " and ga31_es_sessio=0 order by ga31_dia desc,ga31_hora_inici asc,alumne";

$result = $conn->query($query);

if (!$result)
    die($conn->error);

//construim capçalera de la taula
echo '<table id="taulaFaltes" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo'<th class="col-sm-2">Alumne</th>';
echo '<th class="col-sm-1">Nivell</th>';
echo '<th class="col-sm-1">Grup</th>';
echo '<th class="col-sm-2">Professor</th>';
echo '<th class="col-sm-1">Dia</th>';
echo'<th class="col-sm-1">Hora</th>';
echo'<th class="col-sm-1">Tipus</th>';
echo'<th class="col-sm-1">Estat</th>';
echo'<th class="col-sm-1"><center>Edita</center></th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {
    echo '<tbody id="cosTaulaFaltes">';
    while ($row = $result->fetch_assoc()) {
        //onstruim el cos de la taula
        //decidim el literal de l'estat de falta
        $estat;
        if ($row['estat'] == "1") {
            $estat = "Imposada";
        } elseif ($row['estat'] == "2") {
            $estat = "Revisada";
        } elseif ($row['estat'] == "3") {
            $estat = "Expedientada";
        } elseif ($row['estat'] == "4") {
            $estat = "Amnistiada";
        }


        echo '<tr>';
        echo '<td class="col-sm-2" data-codi-falta="' . $row['codifalta'] . '" data-codi-al="' . $row['codialumne'] . '">' . $row['alumne'] . '</td>';
        echo '<td class="col-sm-1">' . $row['nivell'] . '</td>';
        echo '<td class="col-sm-1">' . $row['grup'] . '</td>';
        echo '<td class="col-sm-2" data-codi-prof="' . $row['codiprof'] . '">' . $row['professor'] . '</td>';
        echo '<td class="col-sm-1">' . $row['dia'] . '</td>';
        echo '<td class="col-sm-1">' . $row['hora'] . '</td>';
        echo '<td class="col-sm-1">' . $row['tipusfalta'] . '</td>';
        echo '<td class="col-sm-1" data-estat="' . $row['estat'] . '">' . $estat . '</td>';
        echo '<td class="col-sm-1"><button data-tutor="' . $row['enviatutor'] . '" data-pares="' . $row['enviapares'] . '" data-motiu="' . $row['motiu'] . '" type="button" class="btn btn-info form-control" data-toggle="modal" data-target="#faltesMotiuModalForm" onclick="mostraMotiu(this);">';
        echo '<span class="glyphicon glyphicon-pencil"></span></button></td>';
        echo '</tr>';
    }
}


//tanquem cos i taula
echo '</tbody>';
echo '</table>';
$result->close();
$conn->close();

