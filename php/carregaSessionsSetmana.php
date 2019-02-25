<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

//sleep(1);

session_start();

$dillunsH = $_POST['dilluns'];
$diumenge = $_POST['diumenge'];

$profe = $_POST['profe'];

if ($profe == '') {
    $profe = $_SESSION['prof_actual'];
    $esguardia = false;
} else {
    $esguardia = true;
}


$dillunsX = date_create($dillunsH);
$dimartsH = date_create($dillunsH);
$dimecresH = date_create($dillunsH);
$dijousH = date_create($dillunsH);
$divendresH = date_create($dillunsH);

date_add($dimartsH, date_interval_create_from_date_string("1 days"));
date_add($dimecresH, date_interval_create_from_date_string("2 days"));
date_add($dijousH, date_interval_create_from_date_string("3 days"));
date_add($divendresH, date_interval_create_from_date_string("4 days"));
//date_format($dimarts,"Y-m-d");

$avuiMarca = [];
$diaSessio = [];
$diaSessio[1] = $dillunsH;
$diaSessio[2] = date_format($dimartsH, 'Y-m-d');
$diaSessio[3] = date_format($dimecresH, 'Y-m-d');
$diaSessio[4] = date_format($dijousH, 'Y-m-d');
$diaSessio[5] = date_format($divendresH, 'Y-m-d');

//marquem el dia d'avui
if (date("Y-m-d") == $dillunsH) {
    $avuiMarca[0] = " btn-success";
} else {
    $avuiMarca[0] = "";
}

if (date("Y-m-d") == date_format($dimartsH, "Y-m-d")) {
    $avuiMarca[1] = " btn-success";
} else {
    $avuiMarca[1] = "";
}

if (date("Y-m-d") == date_format($dimecresH, "Y-m-d")) {
    $avuiMarca[2] = " btn-success";
} else {
    $avuiMarca[2] = "";
}

if (date("Y-m-d") == date_format($dijousH, "Y-m-d")) {
    $avuiMarca[3] = " btn-success";
} else {
    $avuiMarca[3] = "";
}

if (date("Y-m-d") == date_format($divendresH, "Y-m-d")) {
    $avuiMarca[4] = " btn-success";
} else {
    $avuiMarca[4] = "";
}


$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//anem a buscar l'inici i la fi del curs
$query = "select ga38_data_inici as datainici,ga38_data_fi as datafi,datediff(ga38_data_fi,ga38_data_inici)-format(datediff(ga38_data_fi,ga38_data_inici)/7,0)*2 as dies,"
        . "datediff(date(now()),ga38_data_inici) as diesDesdeInici,datediff(date(now()),ga38_data_fi) as diesDesdeFi,datediff(ga38_data_fi,ga38_data_inici) as diesTotals from ga38_config_curs where ga38_codi_curs=" . $_SESSION['curs_actual'];

$result = $conn->query($query);

if (!$result)
    die($conn->error);

$row = $result->fetch_assoc();



$dataIniciCurs = date_create_from_format('Y-m-d', $row['datainici']);
$dataIniciBucle = date_create_from_format('Y-m-d', $row['datainici']);
$dataFiCurs = date_create_from_format('Y-m-d', $row['datafi']);
$dies = $row['dies'];
$diffDesdeIniciNum = $row['diesDesdeInici'];
$diffDesdeFiNum = $row['diesDesdeFi'];
$diesTotals = $row['diesTotals'];

$result->close();


//anem a buscar els horaris del professor

$query1 = "select ga26_dia_setmana as dia,ga26_hora_inici as hora,"
        . "(select ga06_descripcio_nivell from ga06_nivell where ga06_codi_nivell=ga26_nivell) as nivell,"
        . "(select ga07_descripcio_grup from ga07_grup where ga07_codi_grup=ga26_grup) as grupgeneral,"
        . "(select ga23_nom_grup from ga23_grups_profes_cap where ga23_codi_grup=ga26_grup) grupprofe,"
        . "ga26_tipus_grup as tipusgrup,"
        . "ga26_is_lectiva as islectiva,"
        . "(select ga27_descripcio from ga27_tipus_carrec where ga27_codi=ga26_tipus_carrec) as tipuscarrec,"
        . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=ga26_codi_assignatura) as assignatura,"
        . "(select ga01_descripcio_aula from ga01_aula where ga01_codi_aula=ga26_codi_aula) as aula,"
        . "ga26_es_guardia as esguardia,ga26_es_carrec as escarrec,ga26_grup as grup,"
        . "(select ga36_descripcio from ga36_tipus_guardia where ga36_codi=ga26_tipus_guardia) as tipusguardia"
        . " from ga26_horaris_docents"
        . " where ga26_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga26_codi_professor=" . $profe . " order by hora,dia";


//and ga26_is_lectiva=1
//('".$dilluns."','".$dimarts."','".$dimecres."','".$dijous."','".$divendres."')
//executem la consulta
$result1 = $conn->query($query1);

$horarisArray = [];

if (!$result1)
    die($conn->error);

if ($result1->num_rows > 0) {
    while ($row1 = $result1->fetch_assoc()) {

//posem la informació en un array associativa
        $horarisArray[$row1['hora'] . '-' . $row1['dia']] = join('<#>', $row1);
    }
}


//obtenim els festius
$queryFestiu = "select ga38_festius as festius from ga38_config_curs where ga38_codi_curs=" . $_SESSION['curs_actual'];


//executem la consulta
$resultF = $conn->query($queryFestiu);

if (!$resultF)
    die($conn->error);

if ($resultF->num_rows > 0) {
    $rowF = $resultF->fetch_assoc();
    $diesFestius = $rowF['festius'];
} else {
    $diesFestius = '';
}


$resultF->close();


$totalFestius = count(explode('-', $diesFestius));

$totalFestiusArray = explode('<#>', $diesFestius);

$totalDies = $dies - $totalFestius;


if ($diffDesdeFiNum < 0) {

    //encara no ha acabat el curs

    $diesDesdeInici = 0;
    $diesTotalsReals = 0;

//comprovem el primer dia
    if (date_format($dataIniciBucle, 'w') != 0 && date_format($dataIniciBucle, 'w') != 6) {
        //anem a veure si és un festiu
        if (array_search(date_format($dataIniciBucle, 'Y-m-d'), $totalFestiusArray) === false) {
            //busquem la data entre els festius i no la trobem
            $diesDesdeInici++;
        }
    }


    for ($i = 0; $i < $diesTotals; $i++) {

        date_add($dataIniciBucle, date_interval_create_from_date_string("1 days"));

        if (date_format($dataIniciBucle, 'w') != 0 && date_format($dataIniciBucle, 'w') != 6 && $i < ($diffDesdeIniciNum - 1)) {
            //anem a veure si és un festiu
            if (array_search(date_format($dataIniciBucle, 'Y-m-d'), $totalFestiusArray) === false) {
                //busquem la data entre els festius i no la trobem
                $diesDesdeInici++;
                $diesTotalsReals++;
            }
        } else if (date_format($dataIniciBucle, 'w') != 0 && date_format($dataIniciBucle, 'w') != 6) {
            //anem a veure si és un festiu
            if (array_search(date_format($dataIniciBucle, 'Y-m-d'), $totalFestiusArray) === false) {
                //busquem la data entre els festius i no la trobem
                $diesTotalsReals++;
            }
        }
    }


    $cursFet = ($diesDesdeInici / ($diesTotalsReals + 1)) * 100;
} else {
    //ja ha acabat el curs
    $cursFet = 100;
}
//creem la progress bar
//echo '<div>';
//echo '<h6><strong>Evolució del curs</strong></h6>';
//echo '</div>';

echo '<div class = "progress" style="margin-top: 15px;margin-bottom: 5px">';
echo '<div class = "progress-bar progress-bar-success progress-bar-striped" role = "progressbar" aria-valuenow = "' . $cursFet . '" aria-valuemin = "0" aria-valuemax = "100" style = "width:' . $cursFet . '%">' . number_format($cursFet, 1) . '% fet</div>';
echo '<div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" style="width:' . (100 - $cursFet) . '%">' . number_format(100 - $cursFet, 1) . '% pendent</div>';
echo '</div>';

//anema a construir les dates clau

$query = "select ga41_data_clau as data,ga41_descripcio as descripcio from ga41_dates_clau where ga41_curs=(select ga03_codi_curs from ga03_curs where ga03_actual='1')";

//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);

calculaDiesLectius($dataIniciCurs, date_create(date('Y-m-d')));

function calculaDiesLectius($dataInici, $dataFi) {


    $diff1 = (int) date_diff($dataInici, $dataFi)->format("%R%a");
}

/* echo '<div class="progress-meter">';



  echo '<div class="meter meter-left" style="width: 25%;"><span class="meter-text"><a>sa</a></span></div>';
  echo '<div class="meter meter-left" style="width: 25%;"><span class="meter-text">Sorta</span></div>';
  echo '<div class="meter meter-right" style="width: 20%;"><span class="meter-text">MASTER</span></div>';
  echo '<div class="meter meter-right" style="width: 30%;"><span class="meter-text">WOW</span></div>';
  echo '</div>'; */
//creem la taula dels horaris
echo '<br>';
$query = "select ga10_hora_inici as horainici,ga10_hora_fi as horafi,ga10_es_descans as esdescans,ga10_tipus_horari as tipushorari from ga10_horaris_aula where ga10_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)";



//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    echo '<table id="taulaHorarisProfessor" class="table table-bordered">';
    echo '<thead id="captaulaHorarisProfessor">';
    echo '<tr>';
    echo '<th class="col-sm-1">HORA</th>';
    echo '<th class="col-sm-2' . $avuiMarca[0] . '"><center>' . $dillunsH . '</center></th>';
    echo '<th class="col-sm-2' . $avuiMarca[1] . '"><center>' . date_format($dimartsH, "Y-m-d") . '</center></th>';
    echo '<th class="col-sm-2' . $avuiMarca[2] . '"><center>' . date_format($dimecresH, "Y-m-d") . '</center></th>';
    echo '<th class="col-sm-2' . $avuiMarca[3] . '"><center>' . date_format($dijousH, "Y-m-d") . '</center></th>';
    echo '<th class="col-sm-2' . $avuiMarca[4] . '"><center>' . date_format($divendresH, "Y-m-d") . '</center></th>';
    echo '</tr>';
    echo '</thead>';

    $diaDate = [];


    $diaDate[1] = $dillunsX;
    $diaDate[2] = $dimartsH;
    $diaDate[3] = $dimecresH;
    $diaDate[4] = $dijousH;
    $diaDate[5] = $divendresH;

    echo '<tbody id="costaulaHorarisProfessor">';
    $cont = 0;
    while ($row = $result->fetch_assoc()) {

        if ($row['esdescans'] == '1') {
            $esdescans = "(ESBARJO)";
            $fileres = 2;
            $colorDescans = "btn-success";
        } else {

            $esdescans = "";
            $fileres = 4;
            $colorDescans = "";
        }

        $button1 = [];
        $button2 = [];
        $textHorari = [];
        $estilHorari = [];

        //inicialitzem els arrays
        for ($i = 1; $i <= 5; $i++) {
            $button1[$i] = '';
            $button2[$i] = '';
            $textHorari[$i] = '';
            $estilHorari[$i] = '';
        }



        for ($i = 1; $i <= 5; $i++) {

            $diff1 = (int) date_diff($diaDate[$i], $dataIniciCurs)->format("%R%a");
            $diff2 = (int) date_diff($diaDate[$i], $dataFiCurs)->format("%R%a");

            if (array_key_exists($row['horainici'] . '-' . $i, $horarisArray)) {
                $contingutHorari = [];
                $contingutHorari = explode('<#>', $horarisArray[$row['horainici'] . '-' . $i]);


                if ($contingutHorari[6] == '1') {

                    //anem a veure si tenia programacions
                    $query3 = "select ga44_text from ga44_programacio_sessio where ga44_codi_curs=" . $_SESSION['curs_actual'] . " and ga44_professor=" . $profe
                            . " and ga44_dia='" . $diaSessio[$i] . "' and ga44_hora='" . $contingutHorari[1] . "'";


                    //executem la consulta
                    $result3 = $conn->query($query3);


                    if (!$result3)
                        die($conn->error);

                    if ($result3->num_rows > 0) {
                        //hi ha programació
                        $bola = '<span class="glyphicon glyphicon-edit"></span>';
                        $hiHaProgra = 'data-programacio="1"';
                    } else {
                        $bola = '';
                        $hiHaProgra = 'data-programacio="0"';
                    }

                    $result3->close();

                    $estilHorari[$i] = "1";

                    $textHorari[$i] .= '<strong>HORA LECTIVA </strong><a data-toggle="modal" data-target="#programadorSessions" onclick="editaProgramacio(this)" ' . $hiHaProgra . '><span class="glyphicon glyphicon-calendar"></span></a>' . $bola;
                    $textHorari[$i] .= "<br>";
                    $textHorari[$i] .= "Nivell: ";
                    $textHorari[$i] .= $contingutHorari[2];
                    $textHorari[$i] .= "<br>";
                    $textHorari[$i] .= "Grup: ";
                    if ($contingutHorari[5] == '0') {
                        $textHorari[$i] .= $contingutHorari[3];
                    } else {
                        $textHorari[$i] .= $contingutHorari[4];
                    }
                    $textHorari[$i] .= "<br>";
                    $textHorari[$i] .= "Assignatura: ";
                    $textHorari[$i] .= $contingutHorari[8];
                    $textHorari[$i] .= "<br>";
                    $textHorari[$i] .= "Aula: ";

                    if ($contingutHorari[9] != '') {
                        $textHorari[$i] .= $contingutHorari[9];
                    }

                    //echo $i.' ';
                    //echo $diff1.' ';
                    //echo $diff2.'<#>';
                    if ($diff1 > 0 || $diff2 < 0) {

                        $foraCalendari = true;
                    } else {
                        $foraCalendari = false;
                    }

                    //$foraCalendari = false;
                    //mirem si es un festiu
                    $festiu = false;

                    if (strpos($diesFestius, $diaSessio[$i]) === false) {
                        //no és un festiu
                        $festiu = false;
                    } else {
                        //síi és festiu
                        $festiu = true;
                    }

                    //anem a buscar la sessio
                    $query2 = "select ga28_professor,ga28_dia,ga28_hora,ga28_grup as grup,ga28_prof_substituit as profsubs,ga28_estat as estat from ga28_cont_presencia_cap where ga28_codi_curs=" . $_SESSION['curs_actual']
                            . " and ga28_professor=" . $profe . " and ga28_dia='" . $diaSessio[$i] . "' and ga28_hora='" . $contingutHorari[1] . "'";

//executem la consulta
                    $result2 = $conn->query($query2);


                    if (!$result2)
                        die($conn->error);

                    if ($result2->num_rows > 0) {

                        //hi ha sessió  
//anem a veure si es pot gestionar la sessio
//si hi ha grup vol dir que es pot gestionar en cas contrari no
                        $row2 = $result2->fetch_assoc();

                        if ($row2['estat'] == '0') {
                            $button2[$i] = '<div class="col-sm-6">'
                                    . '<label data-toggle="tooltip" title="Sessió passada" class="form-control btn-success">Passada</label>'
                                    . '</div>';
                        } else {
                            $button2[$i] = '<div class="col-sm-6">'
                                    . '<label data-toggle="tooltip" title="Sessió provisional" class="form-control btn-warning">Provi</label>'
                                    . '</div>';
                        }
                        if ($row2['grup'] == '') {
//no hi ha grup per tant no es pot gestionar
//no es pot gestionar
                            $gestio = false;
                        } else {
//si que hi ha grup es pot gestionar si és una guàrdia pròpia o si no és guàrdia
                            if ($esguardia == true && $row2['profsubs'] == $_SESSION['prof_actual']) {
                                $gestio = true;
                                
                            }elseif($esguardia == true && $row2['estat'] == '1') {
                                $gestio = true;
                            } 
                            elseif ($esguardia == false) {
                                $gestio = true;
                            } else {
                                $gestio = false;
                            }
                        }

                        if ($gestio == true) {
                            $button1[$i] = '<div class="col-sm-6"><button type="button" data-toggle="tooltip" title="Ves a la sessió" class="btn btn-info form-control" id="goToSessio" data-toggle="modal" onclick="obreSessio(this);">'
                                    . '<span class="glyphicon glyphicon-pencil"></span>Sessió'
                                    . '</button></div>';
                        } else {
                            $button1[$i] = '<div class="col-sm-6"><button type="button" class="btn btn-info form-control" id="goToSessio" data-toggle="modal" onclick="obreSessio(this);" disabled>'
                                    . '<span class="glyphicon glyphicon-pencil"></span>Sessió'
                                    . '</button></div>';
                        }
                    } else {
                        if ($festiu === true || $foraCalendari == true) {
//si és feetiu o està fora de calendari no es podrà gestionar la sessió
                            if ($festiu === true) {
                                $button2[$i] = '<div class="col-sm-6">'
                                        . '<label class="form-control btn-success">Festiu</label>'
                                        . '</div>';
                                $button1[$i] = '<div class="col-sm-6"><button type="button" class="btn btn-info form-control" id="goToSessio" data-toggle="modal" onclick="obreSessio(this);" disabled>'
                                        . '<span class="glyphicon glyphicon-pencil"></span>Sessió'
                                        . '</button></div>';
                            } else {
                                $button2[$i] = '<div class="col-sm-6">'
                                        . '<label class="form-control btn-success">No lectiu</label>'
                                        . '</div>';
                                $button1[$i] = '<div class="col-sm-6"><button type="button" class="btn btn-info form-control" id="goToSessio" data-toggle="modal" onclick="obreSessio(this);" disabled>'
                                        . '<span class="glyphicon glyphicon-pencil"></span>Sessió'
                                        . '</button></div>';
                            }
                        } else {

//si l'horari té grup el podrem gestiona en cas contrari no

                            if ($contingutHorari[12] != '') {
                                $button1[$i] = '<div class="col-sm-6"><button type="button" data-toggle="tooltip" title="Ves a la sessió" class="btn btn-info form-control" id="goToSessio" data-toggle="modal" onclick="obreSessio(this);">'
                                        . '<span class="glyphicon glyphicon-pencil"></span>Sessió'
                                        . '</button></div>';
                                $button2[$i] = '<div class="col-sm-6">'
                                        . '<label data-toggle="tooltip" title="Sessió pendent" class="form-control btn-danger" >Pendent</label>'
                                        . '</div>';
                            } else {
                                $button1[$i] = '<div class="col-sm-6"><button type="button" class="btn btn-info form-control" id="goToSessio" data-toggle="modal" onclick="obreSessio(this);" disabled>'
                                        . '<span class="glyphicon glyphicon-pencil"></span>Sessió'
                                        . '</button></div>';
                                $button2[$i] = '<div class="col-sm-6">'
                                        . '<label class="form-control btn-success">Exempt</label>'
                                        . '</div>';
                            }
                        }
                    }



                    $result2->close();
                } elseif ($contingutHorari[11] == '1') {
//és carrec
                    $estilHorari[$i] = "2";
                    $textHorari[$i] .= "<strong>REUNIÓ/CÀRREC</strong>";
                    $textHorari[$i] .= "<br>";
                    $textHorari[$i] .= $contingutHorari[7];
                } else {
                    $estilHorari[$i] = "3";
                    $textHorari[$i] .= "<strong>GUÀRDIA</strong>";
                    $textHorari[$i] .= "<br>";
                    $textHorari[$i] .= $contingutHorari[13];
                }
            }
        }


        echo '<tr>';
        echo '<td class="col-sm-1 ' . $colorDescans . '" data-horainici="' . $row['horainici'] . '" data-esdescans="' . $row['esdescans'] . '">Inici: ' . $row['horainici'] . '<br>' . 'Fi: ' . $row['horafi'] . '<br>' . $esdescans . '</td>';

        for ($i = 1; $i <= 5; $i++) {
            echo '<td class="col-sm-2 cellahorari" data-estil="' . $estilHorari[$i] . '" id="ta-' . $cont . '-' . $i . '">' . $textHorari[$i] . '<br>' . $button1[$i] . $button2[$i] . '</td>';
        }

//echo '<td class="col-sm-2 cellahorari" data-estil="' . $divendresEstil . '" id="ta-' . $cont . '-1">' . $divendres . '<br>' . $button1Dv . $button2Dv . '</td>';
//echo '<td class="col-sm-2 cellahorari" data-estil="' . $divendresEstil . '"><textarea rows="' . $fileres . '" class="form-control" id="ta-' . $cont . '-5" readonly>' . $divendres . '</textarea>' . $button1Dv . $button2Dv . '</td>';
        echo '</tr>';
        $cont++;
    }


    echo '</tbody>';
    echo '</table>';
}

$result->close();


$result1->close();
$conn->close();

