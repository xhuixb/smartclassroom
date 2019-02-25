<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
require '../classes/utilitatsProfe.php';
//fem la connexió
//rebem les dades

session_start();

//$_SESSION['curs_actual'] = 2;

$nivell = $_POST["nivell"];
$grup = $_POST['grup'];
$dataInicial = $_POST['dataInicial'];
$dataFinal = $_POST['dataFinal'];
$professor = $_POST['professor'];
$assignatura = $_POST['assignatura'];
$nomesAbsents = $_POST['nomesAbsents'];
$nomesRetards = $_POST['nomesRetards'];


//decidim si s'ha de filtrar per nivell
if ($nivell != "") {
    $whereNivell = " and ga06_codi_nivell=" . $nivell;
} else {

    $whereNivell = "";
}

//decidim si s'ha de filtrar per grup

if ($grup != "") {
    $whereGrup = " and ga07_codi_grup=" . $grup;
} else {
    $whereGrup = "";
}

//decidim si s'ha de filtar per data inicial
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

//decidim si s'ha de filtrar per profe

if ($professor != "") {
    $whereProfessor = " and ga15_codi_professor=" . $professor;
    $whereProfessorHorari = " and ga26_codi_professor=" . $professor;
} else {
    $whereProfessor = "";
    $whereProfessorHorari = "";
}

//decidim si s'ha de filtrar per assignatura

if ($assignatura != "") {
    $whereAssignatura = " and ga28_assignatura=" . $assignatura;
    $whereAssignaturaHorari = " and ga26_codi_assignatura=" . $assignatura;
} else {
    $whereAssignatura = "";
    $whereAssignaturaHorari = "";
}

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//anem a buscar els dies festius
$query = "select ga38_festius as diesfestius from ga38_config_curs where ga38_codi_curs=" . $_SESSION['curs_actual'];

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $diesFestius = $row['diesfestius'];
}


$result->close();

//desem els festius en un array
$diesFestiusArray = [];

$diesFestiusArray = explode('<#>', $diesFestius);


//fem la ¨query
$query = "select ga12_id_alumne as codialumne,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,"
        . "ga35_inici_curs as inicicurs,ga35_fi_curs as ficurs,"
        . "(select count(*) from ga15_cont_presencia,ga28_cont_presencia_cap where ga28_codi_curs=ga15_codi_curs and ga28_professor=ga15_codi_professor and ga28_dia=ga15_dia and ga28_hora=ga15_hora_inici and ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=ga12_id_alumne " . $whereDataInicial . $whereDataFinal . $whereProfessor . $whereAssignatura . " group by ga15_alumne) as sessionstotal,"
        . "(select count(*) from ga15_cont_presencia,ga28_cont_presencia_cap where ga28_codi_curs=ga15_codi_curs and ga28_professor=ga15_codi_professor and ga28_dia=ga15_dia and ga28_hora=ga15_hora_inici and ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=ga12_id_alumne and ga15_check_absent=1 " . $whereDataInicial . $whereDataFinal . $whereProfessor . $whereAssignatura . " group by ga15_alumne) as abstotal,"
        . "(select count(*) from ga15_cont_presencia,ga28_cont_presencia_cap where ga28_codi_curs=ga15_codi_curs and ga28_professor=ga15_codi_professor and ga28_dia=ga15_dia and ga28_hora=ga15_hora_inici and ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=ga12_id_alumne and ga15_check_absent=1 and ga15_check_justificat=1 " . $whereDataInicial . $whereDataFinal . $whereProfessor . $whereAssignatura . " group by ga15_alumne) as abssijusti,"
        . "(select count(*) from ga15_cont_presencia,ga28_cont_presencia_cap where ga28_codi_curs=ga15_codi_curs and ga28_professor=ga15_codi_professor and ga28_dia=ga15_dia and ga28_hora=ga15_hora_inici and ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=ga12_id_alumne and ga15_check_absent=1 and ga15_check_justificat=0 " . $whereDataInicial . $whereDataFinal . $whereProfessor . $whereAssignatura . " group by ga15_alumne) as absnojusti,"
        . "(select count(*) from ga15_cont_presencia,ga28_cont_presencia_cap where ga28_codi_curs=ga15_codi_curs and ga28_professor=ga15_codi_professor and ga28_dia=ga15_dia and ga28_hora=ga15_hora_inici and ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=ga12_id_alumne and ga15_check_retard=1 " . $whereDataInicial . $whereDataFinal . $whereProfessor . $whereAssignatura . " group by ga15_alumne) as retards,"
        . "(select count(*) from ga15_cont_presencia,ga28_cont_presencia_cap where ga28_codi_curs=ga15_codi_curs and ga28_professor=ga15_codi_professor and ga28_dia=ga15_dia and ga28_hora=ga15_hora_inici and ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=ga12_id_alumne and ga15_check_absent=1 and ga15_check_justificat=0 " . $whereDataInicial . $whereDataFinal . $whereProfessor . $whereAssignatura . " group by ga15_alumne)/(select count(*) from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=ga12_id_alumne " . $whereDataInicial . $whereDataFinal . " group by ga15_alumne)*100 as absentisme"
        . " from ga11_alumnes,ga12_alumnes_curs,ga06_nivell,ga07_grup,ga35_curs_nivell_grup"
        . " where ga12_codi_curs=" . $_SESSION['curs_actual'] . $whereNivell . $whereGrup
        . " and ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and ga35_codi_curs=ga12_codi_curs and ga35_nivell=ga12_codi_nivell and ga35_grup=ga12_codi_grup order by alumne";

//echo $query;
//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);


//construim capçalera de la taula
echo '<table id="taulaAbsenciesRetards" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-2">Alumne</th>';
echo '<th class="col-sm-1">Nivell</th>';
echo '<th class="col-sm-1">Grup</th>';
echo '<th class="col-sm-1"><center>Ses Teòr</center></th>';
echo '<th class="col-sm-1"><center>Ses Reals</center></th>';
echo '<th class="col-sm-1"><center>Abs Totals</center></th>';
echo '<th class="col-sm-1"><center>Abs Justif</center></th>';
echo '<th class="col-sm-1"><center>Abs No justif</center></th>';
echo '<th class="col-sm-1"><center>Retards</center></th>';
echo '<th class="col-sm-1"><center>Abs Teòric</center></th>';
echo '<th class="col-sm-1"><center>Abs Real</center></th>';
echo '</tr>';
echo '</thead>';


if ($result->num_rows > 0) {

    $sessionstotal = 0;
    $abstotal = 0;
    $abssijusti = 0;
    $absnojusti = 0;
    $retards = 0;
    $sessionsTeoriquesTotals = 0;
    $contaAlumnes=0;

    echo '<tbody id="costaulaAbsenciesRetards">';
    while ($row = $result->fetch_assoc()) {



        if (($nomesAbsents === '0' && $nomesRetards === '0') ||
                ($nomesAbsents === '1' && (string) $row['abstotal'] !== '') ||
                ($nomesRetards === '1' && (string) $row['retards'] !== '') ||
                ($nomesAbsents === '1' && $nomesRetards === '1' && (string) $row['abstotal'] !== '' && (string) $row['retards'] !== '' )) {
            $sessionstotal += $row['sessionstotal'];
            $abstotal += $row['abstotal'];
            $abssijusti += $row['abssijusti'];
            $absnojusti += $row['absnojusti'];
            $retards += $row['retards'];
            $contaAlumnes++;
            
            if ((int) $row['sessionstotal'] > 0) {
                if ((int) $row['absnojusti'] > 0) {
                    $absentisme = ($row['absnojusti'] / $row['sessionstotal']) * 100;
                } else {
                    $absentisme = 0;
                }
                if ((int) $row['abstotal'] > 0) {
                    $absentismeTassa = ($row['abstotal'] / $row['sessionstotal']) * 100;
                } else {
                    $absentismeTassa = 0;
                }
            } else {
                $absentisme = 0;
                $absentismeTassa = 0;
            }

            //vaig a calcular les sessions teòriques
            //mirem si hi ha dates, en cas contrari posem les de la configuració
            if ($dataInicial != "") {
                $dataIniHoresTeor = date_create_from_format("m/d/Y", $dataInicial);
            } else {
                $dataIniHoresTeor = date_create_from_format("Y-m-d", $row['inicicurs']);
            }



            if ($dataFinal != "") {
                $dataFiHoresTeor = date_create_from_format("m/d/Y", $dataFinal);
            } else {
                $dataFiHoresTeor = date_create_from_format("Y-m-d", $row['ficurs']);
            }

            $diff = date_diff($dataIniHoresTeor, $dataFiHoresTeor);
            $diff = intval($diff->format("%R%a")) + 1;
            $dataInicialBucle = $dataIniHoresTeor;
            $dataInicialBucle1 = $dataIniHoresTeor;
            $sessionsTeoriques = 0;
            $i = 0;
            $diesLectius = 0;

            do {
                //mirem si és dissabte o diumenge
                $diaSetmana = $dataInicialBucle->format("w");
                if ($diaSetmana != '0' && $diaSetmana != '6') {
                    //no és dissabte ni diumenge
                    //anem a veure si és un dia festiu
                    if (array_search($dataInicialBucle->format('Y-m-d'), $diesFestiusArray) === false) {
                        //no és un festiu
                        $diesLectius++;
                        //anem a buscar les sessions d'aquest alumne per aquesta data (sessions dels grups personals)
                        //només ens cal saber, de moment, el dia de la setmana
                        $query1 = "select ga26_codi_professor as codiprof,ga26_hora_inici"
                                . " from ga26_horaris_docents,ga23_grups_profes_cap,ga24_grups_profes_det"
                                . " where ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_tipus_grup=1 and ga26_grup=ga23_codi_grup and ga26_grup=ga24_codi_grup and ga24_codi_alumne=" . $row['codialumne'] . $whereProfessorHorari . $whereAssignaturaHorari . " and ga26_is_lectiva=1 and ga26_dia_setmana=" . $diaSetmana . " order by ga26_hora_inici";

                        //executem la consulta
                        $result1 = $conn->query($query1);


                        if (!$result1)
                            die($conn->error);
                        if ($result1->num_rows > 0) {

                            while ($row1 = $result1->fetch_assoc()) {
                                $esActiu = comprovaProfeActiu($dataInicialBucle->format('Y-m-d'), $row1['codiprof'], $conn);
                                if ($esActiu === TRUE) {
                                    //el profe és actiu, per tant li podem comptar la sessió
                                    $sessionsTeoriques++;
                                }
                            }
                        }
                        $result1->close();

                        //ara anem a buscar les sessions dels grups generals
                        $query1 = "select ga26_codi_professor as codiprof,ga26_hora_inici"
                                . " from ga26_horaris_docents,ga12_alumnes_curs"
                                . " where ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_codi_curs=ga12_codi_curs and ga26_tipus_grup=0 and ga26_grup=ga12_codi_grup and ga26_nivell=ga12_codi_nivell and ga12_id_alumne=" . $row['codialumne'] . $whereProfessorHorari . $whereAssignaturaHorari . " and ga26_is_lectiva=1 and ga26_dia_setmana=" . $diaSetmana . " order by ga26_hora_inici";

                        //executem la consulta
                        $result1 = $conn->query($query1);


                        if (!$result1)
                            die($conn->error);
                        if ($result1->num_rows > 0) {

                            while ($row1 = $result1->fetch_assoc()) {
                                $esActiu = comprovaProfeActiu($dataInicialBucle->format('Y-m-d'), $row1['codiprof'], $conn);
                                if ($esActiu === TRUE) {
                                    //el profe és actiu, per tant li podem comptar la sessió
                                    $sessionsTeoriques++;
                                }
                            }
                        }
                        $result1->close();
                    }
                }


                //incrementem la data
                date_add($dataInicialBucle, date_interval_create_from_date_string("1 days"));
                $i++;
            } while ($i < $diff);
            $sessionsTeoriquesTotals += $sessionsTeoriques;

            if ($sessionsTeoriques > 0) {
                if ((int) $row['absnojusti'] > 0) {
                    $absTeoric = $row['absnojusti'] / $sessionsTeoriques * 100;
                } else {
                    $absTeoric = 0;
                }
                if ((int) $row['abstotal'] > 0) {
                    $absTeoricTassa = $row['abstotal'] / $sessionsTeoriques * 100;
                } else {
                    $absTeoricTassa = 0;
                }
            } else {
                $absTeoric = 0;
                $absTeoricTassa = 0;
            }


            echo '<tr>';
            echo '<td class="col-sm-2" data-codi-alumne="' . $row['codialumne'] . '">' . $row['alumne'] . ' <a href="#" data-toggle="modal" data-target="#detallAbsencies" onclick="mostraDetall(this)"><span class="glyphicon glyphicon-search"></span></a></td>';
            echo '<td class="col-sm-1">' . $row['nivell'] . '</td>';
            echo '<td class="col-sm-1">' . $row['grup'] . '</td>';
            echo '<td class="col-sm-1"><center>' . $sessionsTeoriques . '</center></td>';
            echo '<td class="col-sm-1"><center>' . $row['sessionstotal'] . '</center></td>';
            echo '<td class="col-sm-1"><center>' . $row['abstotal'] . '</center></td>';
            echo '<td class="col-sm-1"><center>' . $row['abssijusti'] . '</center></td>';
            echo '<td class="col-sm-1"><center>' . $row['absnojusti'] . '</center></td>';
            echo '<td class="col-sm-1"><center>' . $row['retards'] . '</center></td>';
            echo '<td class="col-sm-1"><center>' . number_format($absTeoricTassa, 1) . '%-' . number_format($absTeoric, 1) . '%NJ' . '</center></td>';
            echo '<td class="col-sm-1"><center>' . number_format($absentismeTassa, 1) . '%-' . number_format($absentisme, 1) . '%NJ' . '</center></td>';

            echo '</tr>';
        }
    }
    //posem la filera dels totals
    echo '<tr style="font-weight:bold">';
    echo '<td class="col-sm-2">TOTALS('.$contaAlumnes.')</td>';
    echo '<td class="col-sm-1"></td>';
    echo '<td class="col-sm-1"></td>';
    echo '<td class="col-sm-1"><center>' . $sessionsTeoriquesTotals . '</center></td>';
    echo '<td class="col-sm-1"><center>' . $sessionstotal . '</center></td>';
    echo '<td class="col-sm-1"><center>' . $abstotal . '</center></td>';
    echo '<td class="col-sm-1"><center>' . $abssijusti . '</center></td>';
    echo '<td class="col-sm-1"><center>' . $absnojusti . '</center></td>';
    echo '<td class="col-sm-1"><center>' . $retards . '</center></td>';


    if ($sessionsTeoriquesTotals > 0) {
        echo '<td class="col-sm-1"><center>' . number_format($abstotal / $sessionsTeoriquesTotals * 100, 1) . '%-' . number_format($absnojusti / $sessionsTeoriquesTotals * 100, 1) . '%NJ' . '</center></td>';
    } else {
        echo '<td class="col-sm-1"><center>0</center></td>';
    }

    if ($sessionstotal > 0) {

        echo '<td class="col-sm-1"><center>' . number_format($abstotal / $sessionstotal * 100, 1) . '%-' . number_format($absnojusti / $sessionstotal * 100, 1) . '%NJ' . '</center></td>';
    } else {
        echo '<td class="col-sm-1"><center>0</center></td>';
    }


    echo '</tr>';
}
//tanquem cos i taula
echo '</tbody>';
echo '</table>';

$result->close();
$conn->close();

