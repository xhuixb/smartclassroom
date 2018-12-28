<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

sleep(2);

session_start();
//$_SESSION['curs_actual'] = 3;
//$_SESSION['prof_actual'] = 0;

$alumneFalta = $_POST['alumneFalta'];
$profFalta = $_POST['profFalta'];
$nivellFalta = $_POST['nivellFalta'];
$grupFalta = $_POST['grupFalta'];
$checksEstats = $_POST['checksEstats'];
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



if ($profFalta != "") {
    $whereProf = " and ga31_codi_professor=" . $profFalta;
} else {
    $whereProf = "";
}

if ($nivellFalta != "") {
    $whereNivell = " and ga12_codi_nivell=" . $nivellFalta;
} else {
    $whereNivell = "";
}

if ($grupFalta != "") {
    $whereGrup = " and ga12_codi_grup=" . $grupFalta;
} else {
    $whereGrup = "";
}


//comprovem si cal un filtre d'estat
$filtreEstat = false;
for ($i = 0; $i < count($checksEstats); $i++) {
    if ($checksEstats[$i] != 0) {
        $filtreEstat = true;
        $ultim = $i;
    }
}
$filtreEstatText = "";
//construim el filtre d'estat si cal
if ($filtreEstat == true) {
    //sí que en cal
    $filtreEstatText = " and ga31_estat in(";
    for ($i = 0; $i < count($checksEstats); $i++) {
        if ($checksEstats[$i] != 0) {
            $filtreEstatText = $filtreEstatText . $checksEstats[$i];
            if ($i != $ultim) {
                $filtreEstatText = $filtreEstatText . ",";
            }
        }
    }
    $filtreEstatText = $filtreEstatText . ") ";
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

/* $query = "select concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,"
  . "concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as professor,ga15_dia as dia ,ga15_hora_inici as hora,ga15_alumne as codialumne,ga15_codi_professor as codiprof,ga22_nom_falta as tipusfalta,ga15_motiu as motiu,ga15_estat as estat,"
  . " (select count(*) from ga15_cont_presencia where ga15_alumne=codialumne and ga15_tipus_falta<>'') as totalfaltes,"
  . "(select count(*) from ga15_cont_presencia where ga15_alumne=codialumne and ga15_tipus_falta<>'' and (ga15_estat=1 or ga15_estat=2)) as faltesvives"
  . " from ga15_cont_presencia,ga06_nivell,ga07_grup, ga11_alumnes,ga04_professors ,ga12_alumnes_curs,ga22_tipus_falta"
  . " where ga15_tipus_falta is not null and ga15_codi_curs=" . $_SESSION['curs_actual'] . " and concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) like '%" . $alumneFalta . "%'"
  . " and concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) like '%" . $profFalta . "%'".$whereDataInicial.$whereDataFinal
  . " and ga06_descripcio_nivell like '%" . $nivellFalta . "%' and ga07_descripcio_grup like '%" . $grupFalta . "%'" . $filtreEstatText
  . " and ga15_alumne=ga12_id_alumne AND ga12_id_alumne=ga11_id_alumne and ga15_codi_professor=ga04_codi_prof and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and ga15_tipus_falta=ga22_codi_falta "
  . $sqlFaltes. " order by ga15_dia desc,ga15_hora_inici asc,alumne"; */

$query = "select ga31_id as codifalta,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,"
        . "concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as professor,ga31_dia as dia ,ga31_hora_inici as hora,ga31_alumne as codialumne,ga31_codi_professor as codiprof,ga22_nom_falta as tipusfalta,ga31_motiu as motiu,ga31_estat as estat,"
        . " (select count(*) from ga31_faltes_ordre where ga31_alumne=codialumne and ga31_tipus_falta<>'' and ga31_codi_curs=".$_SESSION['curs_actual'].") as totalfaltes,"
        . "(select count(*) from ga31_faltes_ordre where ga31_alumne=codialumne and ga31_tipus_falta<>'' and (ga31_estat=1 or ga31_estat=2) and ga31_codi_curs=".$_SESSION['curs_actual'].") as faltesvives"
        . " from ga31_faltes_ordre,ga06_nivell,ga07_grup, ga11_alumnes,ga04_professors ,ga12_alumnes_curs,ga22_tipus_falta"
        . " where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_codi_curs=ga31_codi_curs and concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) like '%" . $alumneFalta . "%'"
        . $whereProf . $whereDataInicial . $whereDataFinal . $whereNivell . $whereGrup . $filtreEstatText
        . " and ga31_alumne=ga12_id_alumne AND ga12_id_alumne=ga11_id_alumne and ga31_codi_professor=ga04_codi_prof and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and ga31_tipus_falta=ga22_codi_falta "
        . $sqlFaltes . " order by ga31_dia desc,ga31_hora_inici asc,alumne";


//executem la query
//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

//construim capçalera de la taula
echo '<table id="taulaFaltes" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th></th>';
echo'<th>Alumne</th>';
echo '<th>Nivell</th>';
echo '<th>Grup</th>';
echo '<th>Professor</th>';
echo '<th>Dia</th>';
echo'<th>Hora</th>';
echo'<th>Tipus</th>';
echo'<th>Estat</th>';
echo '<th>Total</th>';
echo '<th>Vives</th>';
echo'<th>Motiu</th>';
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
        echo '<td><button type="button" class="btn btn-info form-control" data-toggle="modal" data-target="#faltesEstatModalForm" onclick="canviEstatFalta(this);">';
        echo '<span class="glyphicon glyphicon-pencil"></span></button></td>';
        echo '<td data-codi-falta="' . $row['codifalta'] . '" data-codi-al="' . $row['codialumne'] . '">' . $row['alumne'] . '</td>';
        echo '<td>' . $row['nivell'] . '</td>';
        echo '<td>' . $row['grup'] . '</td>';
        echo '<td data-codi-prof="' . $row['codiprof'] . '">' . $row['professor'] . '</td>';
        echo '<td>' . $row['dia'] . '</td>';
        echo '<td>' . $row['hora'] . '</td>';
        echo '<td>' . $row['tipusfalta'] . '</td>';
        echo '<td data-estat="' . $row['estat'] . '">' . $estat . '</td>';
        echo '<td>' . $row['totalfaltes'] . '</td>';
        echo '<td>' . $row['faltesvives'] . '</td>';
        echo '<td><button data-motiu="' . $row['motiu'] . '" type="button" class="btn btn-info form-control" data-toggle="modal" data-target="#faltesMotiuModalForm" onclick="mostraMotiu(this);">';
        echo '<span class="glyphicon glyphicon-eye-open"></span></button></td>';
        echo '</tr>';
    }
}


//tanquem cos i taula
echo '</tbody>';
echo '</table>';
$result->close();
$conn->close();
