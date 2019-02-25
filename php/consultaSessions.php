<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();


$dataSessio = $_POST['dataSessio'];

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//anem a buscar els nivells
$query = "select distinct(ga06_codi_nivell) as codinivell,ga06_descripcio_nivell as nomnivell from ga06_nivell,ga35_curs_nivell_grup"
        . " where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual='1') and ga35_nivell=ga06_codi_nivell";

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {

    echo '<table id="taulaSessions" class="table table-bordered">';
    echo '<thead id="captaulaSessions">';
    echo '<tr>';
    echo '<th>HORA</th>';
    $posTipus = [];
    $cont = 1;
    while ($row = $result->fetch_assoc()) {
        echo '<th data-codi-nivell="' . $row['codinivell'] . '">' . $row['nomnivell'] . '</th>';
        $posTipus[$row['codinivell']] = $cont;

        $cont++;
    }

    echo '</tr>';
    echo '</thead>';
}

$result->close();

//anem a buscar les sessions del dia



$query = "select ga26_dia_setmana as dia,ga26_hora_inici as hora,ga26_codi_professor as codiprof,ga26_nivell as nivell,"
        . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=codiprof) as nomprof,"
        . "(select ga06_descripcio_nivell from ga06_nivell where ga06_codi_nivell=ga26_nivell) as nomnivell,"
        . "(select ga07_descripcio_grup from ga07_grup where ga07_codi_grup=ga26_grup) as grupgeneral,"
        . "(select ga23_nom_grup from ga23_grups_profes_cap where ga23_codi_grup=ga26_grup) grupprofe,ga26_tipus_grup as tipusgrup,ga26_is_lectiva as islectiva,"
        . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=ga26_codi_assignatura) as assignatura,"
        . "(select ga01_descripcio_aula from ga01_aula where ga01_codi_aula=ga26_codi_aula) as aula,ga26_grup as grup,"
        . "(select ga28_professor from ga28_cont_presencia_cap where ga28_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga28_professor=codiprof and ga28_dia='" . $dataSessio . "' and ga28_hora=hora) as proftit,"
        . "(select ga28_prof_substituit from ga28_cont_presencia_cap where ga28_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga28_professor=codiprof and ga28_dia='" . $dataSessio . "' and ga28_hora=hora) as profsubs,"
        . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=profsubs) as nomprofsubs"
        . " from ga26_horaris_docents,ga04_professors where ga26_codi_professor=ga04_codi_prof and ga26_codi_curs=(select ga03_codi_curs from ga03_curs"
        . " where ga03_actual=1) and ga26_grup is not null and ga26_is_lectiva=1 and ga26_dia_setmana=DAYOFWEEK('" . $dataSessio . "')-1 order by hora,ga26_nivell,grup";


//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    echo '<tbody id="bodytaulaSessions">';

    $cont = 0;
  

    while ($row = $result->fetch_assoc()) {
        //anem a veure si, en aquesta data, el pofessor estava suspès
        $actiu = false;
        $dataSessioDate = date_create_from_format('Y-m-d', $dataSessio);

        $queryProfe = "select ga42_data_inici as datainici,ga42_data_fi as datafi from ga42_registre_activitat where ga42_professor=" . $row['codiprof'];

        $result1 = $conn->query($queryProfe);

        if (!$result1)
            die($conn->error);

        if ($result1->num_rows > 0) {
            while ($row1 = $result1->fetch_assoc()) {

                $dataIniciDate = date_create_from_format('Y-m-d', $row1['datainici']);
                $diffDataInici = (int) date_diff($dataSessioDate, $dataIniciDate)->format("%R%a");

                if ($diffDataInici <= 0) {
                    //si la diferència és >=0 la sessió és posteriro a la data d'inici
                    //anem a veure la data de fi no fos cas que ens passessim

                    if ($row1['datafi'] == '') {
                        //no hi ha data de fi per tant estem dins del tram horari
                        $actiu = true;
                    } else {
                        $dataFiDate = date_create_from_format('Y-m-d', $row1['datafi']);
                        $diffDataFi = (int) date_diff($dataSessioDate, $dataFiDate)->format("%R%a");
                        if ($diffDataFi >= 0) {
                            //estem dis del tran
                            $actiu = true;
                        }
                    }
                }
            }
        }

        if ($actiu === true) {
            if ($cont == 0) {
                //primer registre extret
                //creem la primera filera
                echo '<tr>';
                //creem la primera hora           
                echo '<td>' . $row['hora'] . '</td>';
                $col = 1;
                //creem tantes celles com calguin fins arribat a la cella actual
                while ($posTipus[$row['nivell']] != $col) {
                    echo '<td></td>';
                    $col++;
                }

                if ($row['proftit'] != '') {
                    //és una sessio
                    $color = 'class="btn-success"';
                    if ($row['profsubs'] != '') {
                        //és una guàrdia
                        $guardia = '(G)';
                    } else {
                        $guardia = '';
                    }
                } else {
                    $guardia = '';
                    $color = 'class="btn-danger"';
                }

                if ($row['tipusgrup'] == '0') {
                    $grupTaula = $row['grupgeneral'];
                } else {
                    $grupTaula = $row['grupprofe'];
                }
                //guardem la primera sessio
                $sessioTotal = '<a ' . $color . ' data-toggle="modal" data-target="#detallSessionsModalForm" data-codiprof="' . $row['codiprof'] . '" data-hora="' . $row['hora'] . '" data-assig-detall="' . $row['assignatura'] . '" data-profe-detall="' . $row['nomprof'] . '" data-nivell-detall="' . $row['nomnivell'] .
                        '" data-grup-detall="' . $grupTaula . '" data-grup="' . $row['grup'] . '" data-tipusgrup="' . $row['tipusgrup'] . '" data-nivell="' . $row['nivell'] . '" data-aula-detall="' . $row['aula'] . '" data-proftit="' . $row['proftit'] . '"  data-profsubs="' . $row['profsubs'] . '"  data-nomprofsubs="' . $row['nomprofsubs'] . '" onclick="mostraSessioDetall(this)">' . $guardia . $row['assignatura'] . '(' . $grupTaula . ')</a><br>';
                //posem els valors actuals
                $nivellVell = $row['nivell'];
                $horaVell = $row['hora'];
            } else {
                if ($row['hora'] != $horaVell) {


                    //canvi d'hora
                    //posem les sesisons
                    //creem tantes celles com calguin fins arribat a la cella actual

                    echo '<td>' . $sessioTotal . '</td>';

                    //inicialitzem la sessio
                    $sessioTotal = '';


                    if ($row['proftit'] != '') {
                        $color = 'class="btn-success"';
                        if ($row['profsubs'] != '') {
                            //és una guàrdia
                            $guardia = '(G)';
                        } else {
                            $guardia = '';
                        }
                    } else {
                        $guardia = '';
                        $color = 'class="btn-danger"';
                    }
                    if ($row['tipusgrup'] == '0') {
                        $grupTaula = $row['grupgeneral'];
                    } else {
                        $grupTaula = $row['grupprofe'];
                    }

                    //comencem la següent sessio
                    $sessioTotal = '<a ' . $color . ' data-toggle="modal" data-target="#detallSessionsModalForm" data-codiprof="' . $row['codiprof'] . '" data-hora="' . $row['hora'] . '" data-assig-detall="' . $row['assignatura'] . '" data-profe-detall="' . $row['nomprof'] . '" data-nivell-detall="' . $row['nomnivell'] .
                            '" data-grup-detall="' . $grupTaula . '" data-grup="' . $row['grup'] . '" data-tipusgrup="' . $row['tipusgrup'] . '" data-nivell="' . $row['nivell'] . '" data-aula-detall="' . $row['aula'] . '" data-proftit="' . $row['proftit'] . '"  data-profsubs="' . $row['profsubs'] . '"  data-nomprofsubs="' . $row['nomprofsubs'] . '" onclick="mostraSessioDetall(this)">' . $guardia . $row['assignatura'] . '(' . $grupTaula . ')</a><br>';
                    //tanquem la filera
                    echo '</tr>';
                    //comencem la següent
                    echo '<tr>';
                    $col = 1;
                    //creem la nova cella d'hora
                    echo '<td>' . $row['hora'] . '</td>';
                    while ($posTipus[$row['nivell']] != $col) {
                        echo '<td></td>';
                        $col++;
                    }
                } elseif ($row['nivell'] != $nivellVell) {
                    //canvi de nivell
                    //posem les sesisons

                    echo '<td>' . $sessioTotal . '</td>';
                    $col++;
                    while ($posTipus[$row['nivell']] != $col) {
                        echo '<td></td>';
                        $col++;
                    }

                    //inicialitzem la sessio
                    $sessioTotal = '';

                    if ($row['proftit'] != '') {
                        $color = 'class="btn-success"';
                        if ($row['profsubs'] != '') {
                            //és una guàrdia
                            $guardia = '(G)';
                        } else {
                            $guardia = '';
                        }
                    } else {
                        $guardia = '';
                        $color = 'class="btn-danger"';
                    }
                    if ($row['tipusgrup'] == '0') {
                        $grupTaula = $row['grupgeneral'];
                    } else {
                        $grupTaula = $row['grupprofe'];
                    }
                    //comencem la següent sessio
                    $sessioTotal = '<a ' . $color . ' data-toggle="modal" data-target="#detallSessionsModalForm" data-codiprof="' . $row['codiprof'] . '" data-hora="' . $row['hora'] . '" data-assig-detall="' . $row['assignatura'] . '" data-profe-detall="' . $row['nomprof'] . '" data-nivell-detall="' . $row['nomnivell'] .
                            '" data-grup-detall="' . $grupTaula . '" data-grup="' . $row['grup'] . '" data-tipusgrup="' . $row['tipusgrup'] . '" data-nivell="' . $row['nivell'] . '" data-aula-detall="' . $row['aula'] . '" data-proftit="' . $row['proftit'] . '"  data-profsubs="' . $row['profsubs'] . '"  data-nomprofsubs="' . $row['nomprofsubs'] . '" onclick="mostraSessioDetall(this)">' . $guardia . $row['assignatura'] . '(' . $grupTaula . ')</a><br>';
                } else {
                    //cotinuem en la hora i nivell
                    //concatenem

                    if ($row['proftit'] != '') {
                        $color = 'class="btn-success"';
                        if ($row['profsubs'] != '') {
                            //és una guàrdia
                            $guardia = '(G)';
                        } else {
                            $guardia = '';
                        }
                    } else {
                        $guardia = '';
                        $color = 'class="btn-danger"';
                    }
                    if ($row['tipusgrup'] == '0') {
                        $grupTaula = $row['grupgeneral'];
                    } else {
                        $grupTaula = $row['grupprofe'];
                    }
                    $sessioTotal .= '<a ' . $color . ' data-toggle="modal" data-target="#detallSessionsModalForm" data-codiprof="' . $row['codiprof'] . '" data-hora="' . $row['hora'] . '" data-assig-detall="' . $row['assignatura'] . '" data-profe-detall="' . $row['nomprof'] . '" data-nivell-detall="' . $row['nomnivell'] .
                            '" data-grup-detall="' . $grupTaula . '" data-grup="' . $row['grup'] . '" data-tipusgrup="' . $row['tipusgrup'] . '" data-nivell="' . $row['nivell'] . '" data-aula-detall="' . $row['aula'] . '" data-proftit="' . $row['proftit'] . '" data-profsubs="' . $row['profsubs'] . '"  data-nomprofsubs="' . $row['nomprofsubs'] . '" onclick="mostraSessioDetall(this)">' . $guardia . $row['assignatura'] . '(' . $grupTaula . ')</a><br>';
                }
                $nivellVell = $row['nivell'];
                $horaVell = $row['hora'];
            }
            $cont++;
        }
    }
    //posem la darrera cella
    echo '<td>' . $sessioTotal . '</td>';
    echo '</tr>';
    echo '</tbody>';
}


echo '</table>';
$result->close();
$conn->close();



