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
$dataIniciCerca = $_POST['dataIniciCerca'];
$dataFiCerca = $_POST['dataFiCerca'];

if ($dataIniciCerca != '') {

    $whereIniciCerca = " and ga31_dia>='" . $dataIniciCerca . "'";
} else {
    $whereIniciCerca = "";
}

if ($dataFiCerca != '') {

    $whereFiCerca = " and ga31_dia<='" . $dataFiCerca . "'";
} else {

    $whereFiCerca = "";
}

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//obtenim els tipus de faltes d'ordre

$query = "select distinct(ga31_tipus_falta) as tipusfaltacodi,ga22_nom_falta as tipusfaltanom from ga31_faltes_ordre,ga22_tipus_falta"
        . " where ga31_tipus_falta=ga22_codi_falta and ga31_codi_curs=" . $_SESSION['curs_actual'] . $whereIniciCerca . $whereFiCerca . " order by ga31_tipus_falta";



$result = $conn->query($query);


if (!$result)
    die($conn->error);


//construim capçalera de la taula
echo '<br>';
echo '<table id="taulaFaltesOrdre" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-1">Nivell</th>';
echo '<th class="col-sm-1">Grup</th>';
//acabem de construir la capçalera amb els tipus de falta
//posicio de cada tipus
$posTipus = [];
$cont = 2;

$totalGeneral = [];
$totalEstat3 = [];
$totalEstat4 = [];

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {
        echo '<th class="col-sm-1" data-codi-falta="' . $row['tipusfaltacodi'] . '">' . $row['tipusfaltanom'] . '</th>';
        $posTipus[$row['tipusfaltacodi']] = $cont;

        $totalGeneral[$cont] = 0;
        $totalEstat3[$cont] = 0;
        $totalEstat4[$cont] = 0;
        $cont++;
    }
}

echo '</tr>';
echo '</thead>';

$result->close();

//construim el cos del report
$query = "select  ga12_codi_nivell as nivell,ga12_codi_grup as grup,ga06_descripcio_nivell as nivellnom,ga07_descripcio_grup as grupnom,ga31_codi_curs as curs,ga31_tipus_falta as tipusfalta,count(*) as total,"
        . "(select count(*) from ga31_faltes_ordre,ga12_alumnes_curs where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne and ga12_codi_nivell=nivell and ga12_codi_grup=grup and ga31_tipus_falta=tipusfalta and ga31_estat='1'" . $whereIniciCerca . $whereFiCerca . " group by curs,nivell,grup,tipusfalta) as estat1,"
        . "(select count(*) from ga31_faltes_ordre,ga12_alumnes_curs where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne and ga12_codi_nivell=nivell and ga12_codi_grup=grup and ga31_tipus_falta=tipusfalta and ga31_estat='2'" . $whereIniciCerca . $whereFiCerca . " group by curs,nivell,grup,tipusfalta) as estat2,"
        . "(select count(*) from ga31_faltes_ordre,ga12_alumnes_curs where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne and ga12_codi_nivell=nivell and ga12_codi_grup=grup and ga31_tipus_falta=tipusfalta and ga31_estat='3'" . $whereIniciCerca . $whereFiCerca . " group by curs,nivell,grup,tipusfalta) as estat3,"
        . "(select count(*) from ga31_faltes_ordre,ga12_alumnes_curs where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne and ga12_codi_nivell=nivell and ga12_codi_grup=grup and ga31_tipus_falta=tipusfalta and ga31_estat='4'" . $whereIniciCerca . $whereFiCerca . " group by curs,nivell,grup,tipusfalta) as estat4"
        . " from ga31_faltes_ordre,ga12_alumnes_curs,ga06_nivell,ga07_grup"
        . " where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup" . $whereIniciCerca . $whereFiCerca
        . " group by ga12_codi_nivell,ga12_codi_grup,ga31_tipus_falta";



$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {

    echo '<tbody id="costaulaFaltesOrdre">';
    $nivellVell = '';
    $grupVell = '';
    $cont = 0;



    while ($row = $result->fetch_assoc()) {
        if ($row['nivell'] != $nivellVell || $row['grup'] != $grupVell) {

            //inicialitzem la columna

            $col = 2;
            //canvi de línia
            if ($cont > 0) {
                //tanquem la línia anterior
                echo '</tr>';
                //obrim la següent
                echo '<tr>';
                //si és nivell nou, abans de continuar posem els totals

                if ($row['nivell'] != $nivellVell) {
                    $query1 = "select  ga12_codi_nivell as nivell,ga12_codi_grup as grup,ga06_descripcio_nivell as nivellnom,ga07_descripcio_grup as grupnom,ga31_codi_curs as curs,ga31_tipus_falta as tipusfalta,count(*) as total,"
                            . "(select count(*) from ga31_faltes_ordre,ga12_alumnes_curs where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne and ga12_codi_nivell=nivell and ga31_tipus_falta=tipusfalta and ga31_estat='3'" . $whereIniciCerca . $whereFiCerca . " group by curs,nivell,tipusfalta) as estat3,"
                            . "(select count(*) from ga31_faltes_ordre,ga12_alumnes_curs where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne and ga12_codi_nivell=nivell and ga31_tipus_falta=tipusfalta and ga31_estat='4'" . $whereIniciCerca . $whereFiCerca . " group by curs,nivell,tipusfalta) as estat4"
                            . " from ga31_faltes_ordre,ga12_alumnes_curs,ga06_nivell,ga07_grup"
                            . " where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and ga12_codi_nivell=" . $nivellVell . $whereIniciCerca . $whereFiCerca
                            . " group by ga12_codi_nivell,ga31_tipus_falta";


                    $result1 = $conn->query($query1);

                    //posem el total
                    echo '<td class="col-sm-1"><strong>TOTAL</strong></td>';
                    echo '<td class="col-sm-1"></td>';

                    $colTotals = 2;

                    if (!$result1)
                        die($conn->error);

                    if ($result1->num_rows > 0) {

                        while ($row1 = $result1->fetch_assoc()) {
                            //posem les celles buides que calguin
                            while ($posTipus[$row1['tipusfalta']] != $colTotals) {
                                echo '<td class="col-sm-1"></td>';
                                $colTotals++;
                            }



                            if ($row1['total'] == '') {
                                $row1['total'] = 0;
                            }
                            if ($row1['estat3'] == '') {
                                $row1['estat3'] = 0;
                            }
                            if ($row1['estat4'] == '') {
                                $row1['estat4'] = 0;
                            }

                            //posem la informació
                            echo '<td class="col-sm-1"><strong>T(' . $row1['total'] . ')-E(' . $row1['estat3'] . ')-A(' . $row1['estat4'] . ')</strong></td>';

                            $colTotals++;
                        }
                    }

                    $result1->close();
                    //tanquem la filera de totals
                    echo '</tr>';
                    //obrim la següent filera
                    echo '<tr>';
                }
            } else {
                //és la primera línia
                echo '<tr>';
            }

            echo '<td class="col-sm-1">' . $row['nivellnom'] . '</td>';
            echo '<td class="col-sm-1">' . $row['grupnom'] . '</td>';
        } else {
            //continuem a la mateixa línia
        }

        //posem les celles buides que calguin
        while ($posTipus[$row['tipusfalta']] != $col) {
            echo '<td class="col-sm-1"></td>';
            $col++;
        }

        if ($row['total'] == '') {
            $row['total'] = 0;
        }
        if ($row['estat3'] == '') {
            $row['estat3'] = 0;
        }
        if ($row['estat4'] == '') {
            $row['estat4'] = 0;
        }

        //posem la informació
        echo '<td class="col-sm-1">T(' . $row['total'] . ')-E(' . $row['estat3'] . ')-A(' . $row['estat4'] . ')</td>';

        //totalitzem
        $totalGeneral[$col] += $row['total'];
        $totalEstat3[$col] += $row['estat3'];
        $totalEstat4[$col] += $row['estat4'];

        $col++;

        $cont++;
        $nivellVell = $row['nivell'];
        $grupVell = $row['grup'];
    }

    //posem el darrer total

    echo '<tr>';
    $query1 = "select  ga12_codi_nivell as nivell,ga12_codi_grup as grup,ga06_descripcio_nivell as nivellnom,ga07_descripcio_grup as grupnom,ga31_codi_curs as curs,ga31_tipus_falta as tipusfalta,count(*) as total,"
            . "(select count(*) from ga31_faltes_ordre,ga12_alumnes_curs where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne and ga12_codi_nivell=nivell and ga31_tipus_falta=tipusfalta and ga31_estat='3'" . $whereIniciCerca . $whereFiCerca . " group by curs,nivell,tipusfalta) as estat3,"
            . "(select count(*) from ga31_faltes_ordre,ga12_alumnes_curs where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne and ga12_codi_nivell=nivell and ga31_tipus_falta=tipusfalta and ga31_estat='4'" . $whereIniciCerca . $whereFiCerca . " group by curs,nivell,tipusfalta) as estat4"
            . " from ga31_faltes_ordre,ga12_alumnes_curs,ga06_nivell,ga07_grup"
            . " where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and ga12_codi_nivell=" . $nivellVell . $whereIniciCerca . $whereFiCerca
            . " group by ga12_codi_nivell,ga31_tipus_falta";


    $result1 = $conn->query($query1);

    //posem el total
    echo '<td class="col-sm-1"><strong>TOTAL</strong></td>';
    echo '<td class="col-sm-1"></td>';

    $colTotals = 2;

    if (!$result1)
        die($conn->error);

    if ($result1->num_rows > 0) {

        while ($row1 = $result1->fetch_assoc()) {
            //posem les celles buides que calguin
            while ($posTipus[$row1['tipusfalta']] != $colTotals) {
                echo '<td class="col-sm-1"></td>';
                $colTotals++;
            }



            if ($row1['total'] == '') {
                $row1['total'] = 0;
            }
            if ($row1['estat3'] == '') {
                $row1['estat3'] = 0;
            }
            if ($row1['estat4'] == '') {
                $row1['estat4'] = 0;
            }

            //posem la informació
            echo '<td class="col-sm-1"><strong>T(' . $row1['total'] . ')-E(' . $row1['estat3'] . ')-A(' . $row1['estat4'] . ')</strong></td>';

            $colTotals++;
        }
    }

    $result1->close();
    //tanquem la filera de totals
    echo '</tr>';

    //posem les filera dels totals generals

    echo '<tr>';
    //posem el total
    echo '<td class="col-sm-1"><strong>GENERAL</strong></td>';
    echo '<td class="col-sm-1"></td>';

    for ($i = 0; $i < count($totalGeneral); $i++) {
        echo '<td class="col-sm-1"><strong>T(' . $totalGeneral[$i + 2] . ')-E(' . $totalEstat3[$i + 2] . ')-A(' . $totalEstat4[$i + 2] . ')</strong></td>';
    }


    echo '</tr>';

    echo '</tbody>';
}


$result->close();
$conn->close();

echo '</table>';




