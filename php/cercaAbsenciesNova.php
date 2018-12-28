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
$dataInicial=$_POST['dataInicial'];
$dataFinal=$_POST['dataFinal'];



if ($dataInicial != "") {
    $dataInicialSql = date("Y-m-d", strtotime($dataInicial));
    $whereDataInicial = " and ga15_dia>='" . $dataInicialSql . "'";
} else {
    $whereDataInicial = "";
}

//decidim si s'ha de filtar per data inicial
if ($dataFinal != "") {
     $dataFinalSql = date("Y-m-d", strtotime($dataFinal));
    $whereDataFinal = " and ga15_dia<='" . $dataFinalSql . "'";
} else {
    $whereDataFinal = "";
}


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
    $orderByJustificat = " order by nomalumne asc,codialumne,dia desc,hora asc";
} else {
    $orderByJustificat = " order by dia desc,nomalumne asc,codialumne,hora asc";
}





//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//obtenim el màxim i mínim de les hores
$query = "select max(ga15_hora_inici) as horamax,min(ga15_hora_inici) as horamin from ga15_cont_presencia,ga12_alumnes_curs,ga11_alumnes where ga15_codi_curs=".$_SESSION['curs_actual']." and ga12_codi_nivell=" . $nivell . " and"
        . " ga12_codi_grup=" . $grup . " and ga15_check_absent='1' and ga15_codi_curs=ga12_codi_curs and ga15_alumne=ga12_id_alumne and ga12_id_alumne=ga11_id_alumne " . $whereJustificat.$whereDataInicial.$whereDataFinal;



$result = $conn->query($query);

if (!$result)
    die($conn->error);

$row = $result->fetch_assoc();

$horaMax = $row['horamax'];
$horaMin = $row['horamin'];



//obtenim la resta d'hores

$query = "SELECT ga10_hora_inici as horainici,ga10_hora_fi as horafi from ga10_horaris_aula where ga10_codi_curs=".$_SESSION['curs_actual']." and ga10_es_descans='0' and ga10_hora_inici>='" . $horaMin . "' and ga10_hora_inici<='" . $horaMax . "' order by horainici";

$result = $conn->query($query);
$hores = [];
$horesFi=[];


if (!$result)
    die($conn->error);


if ($result->num_rows > 0) {
    $cont = 0;
    while ($row = $result->fetch_assoc()) {
        $hores[$cont] = $row['horainici'];
        $horesFi[$cont]=$row['horafi'];
        $cont++;
    }
}



//construim capçalera de la taula
echo '<table id="taulaJustifiAbsencies" class="table table-fixed">';
echo '<thead id="taulaJustifiAbsenciesHeader">';
echo '<tr>';
echo '<th class="col-sm-1">Dia<a id="cercaPerData" onclick="cercaAbsenciesNova(this);"><i class="fa fa-fw fa-sort"></i></a></th>';
echo '<th class="col-sm-2">Alumne<a id="cercaPerAlumne" onclick="cercaAbsenciesNova(this);"><i class="fa fa-fw fa-sort"></i></a></th>';

//inserim les hores
for ($i = 0; $i < count($hores); $i++) {
    echo '<th class="col-sm-1"><center>' . $hores[$i] .' - '.$horesFi[$i].'</center></th>';
}

echo '</tr>';
echo '</thead>';


$query = "select ga15_dia as dia,ga15_hora_inici as hora,ga15_alumne as codialumne, ga15_codi_professor as codiprof, concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as nomalumne,ga15_check_justificat as justificat,ga15_motiu_justificat as motiujustifi"
        . " from ga15_cont_presencia,ga12_alumnes_curs,ga11_alumnes"
        . " where ga15_codi_curs=".$_SESSION['curs_actual']." and ga12_codi_nivell=" . $nivell . " and ga12_codi_grup=" . $grup . " and ga15_check_absent='1' and"
        . " ga15_codi_curs=ga12_codi_curs and ga15_alumne=ga12_id_alumne and ga12_id_alumne=ga11_id_alumne " . $whereJustificat.$whereDataInicial.$whereDataFinal
        . $orderByJustificat;



$result = $conn->query($query);

if (!$result)
    die($conn->error);



if ($result->num_rows > 0) {
    $cont = 0;
    $novaHora = '';

    echo '<tbody id="costaulaJustifiAbsencies">';
    while ($row = $result->fetch_assoc()) {

        if ($cont > 0) {
            //mirem si repetim l'hora cosa que vol dir que s'ha passat llista dues vegades
            if ($row['hora'] == $novaHora && $row['dia']==$nouDiaR && $row['codialumne']==$nouAlumR) {
                //llista duplicada              
                $llistaDuplicada = true;
            } else {
                $llistaDuplicada = false;
                $novaHora = $row['hora'];
                $nouDiaR = $row['dia'];
                $nouAlumR = $row['codialumne'];
            }
        } else {

            $llistaDuplicada = false;
            $novaHora = $row['hora'];
            $nouDiaR = $row['dia'];
            $nouAlumR = $row['codialumne'];
        }

        if ($llistaDuplicada == false) {

            if ($cont == 0) {
                $novaLinia = true;
                $col = 0;
                echo '<tr>';
                $nouDia = $row['dia'];
                $nouAlum = $row['codialumne'];
            } else {
               // echo 'npu dia ' . $nouDia . '-row' . $row['dia'] . 'nou alu' . $nouAlum . '-row' . $row['codialumne'] . '<br>';
                if ($nouDia != $row['dia'] || $nouAlum != $row['codialumne']) {
                    $novaLinia = true;
                    $col = 0;
                    //echo $cont . '-';
                    echo '</tr>';
                    echo '<tr>';
                    $nouDia = $row['dia'];
                    $nouAlum = $row['codialumne'];
                } else {
                    $novaLinia = false;
                }
            }



            if ($row['justificat'] == "1") {
                $estajustificat = "btn-success";
                $textjustificat = "Justificat";
            } else {
                $estajustificat = "btn-danger";
                $textjustificat = "No justificat";
            }


            if ($novaLinia == false) {
                //cal veure on posar 
                //busuem l´'index de l'array d'hores
                //no és llista duplicada no es fa res
                $clau = array_search($row['hora'], $hores);

                $dif = $clau - $col;

                //echo 'clau'.$clau.' col'.$col;
                for ($i = 0; $i < $dif; $i++) {
                    //afegim celles buides

                    echo '<td class="col-sm-1"></td>';
                }
                $col += $dif + 1;

                //afegim el button
                echo '<td class="col-sm-1" data-codi-justifi="'.$row['justificat'].'" data-motiu-justifi="'.$row['motiujustifi'].'" data-codi-prof="'.$row['codiprof'].'"><button type="button" class="btn form-control ' . $estajustificat . '" data-toggle="modal" data-target="#absenciesModalForm" onclick="carregaJustifiAbs(this)">';
                echo '<span class="glyphicon glyphicon-pencil"></span>' . $textjustificat . '</button>';
                echo '</td>';
            } else {
                //nova línia s´'ha de posar data i alumne
                echo '<td class="col-sm-1">' . $row['dia'] . '</td>';
                echo '<td class="col-sm-2" data-codi="' . $row['codialumne'] . '">' . $row['nomalumne'] . '</td>';

                $clau = array_search($row['hora'], $hores);

                $dif = $clau - $col;

                //echo 'clau'.$clau.' col'.$col;
                for ($i = 0; $i < $dif; $i++) {
                    //afegim celles buides

                    echo '<td class="col-sm-1"></td>';
                }
                $col += $dif + 1;

                echo '<td class="col-sm-1" data-codi-justifi="'.$row['justificat'].'" data-motiu-justifi="'.$row['motiujustifi'].'" data-codi-prof="'.$row['codiprof'].'"><button type="button" class="btn form-control ' . $estajustificat . '" data-toggle="modal" data-target="#absenciesModalForm" onclick="carregaJustifiAbs(this)">';
                echo '<span class="glyphicon glyphicon-pencil"></span>' . $textjustificat . '</button>';
                echo '</td>';
            }
        }
        $cont++;
    }
    //tanquem la darrera filera
    echo '</tr>';
}
//tanquem cos i taula
echo '</tbody>';
echo '</table>';

$result->close();
$conn->close();
