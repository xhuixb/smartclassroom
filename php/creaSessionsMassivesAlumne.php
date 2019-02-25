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

$dataSessio = $_POST['dataSessio'];
$nivell = $_POST['nivell'];
$grup = $_POST['grup'];
$alumne = $_POST['alumne'];
$comentari = str_replace("'", "''", $_POST['comentari']);
$presencia = $_POST['presencia'];
//ordinal de les dates que estic tractant
$i = $_POST['i'];

//posem la data en el format universal
$dataSessio = substr($dataSessio, 6) . '-' . substr($dataSessio, 3, 2) . '-' . substr($dataSessio, 0, 2);
//els codis de presencia
if ($presencia == '1') {
    $checkPresent = '1';
    $checkAbsent = '0';
    $checkRetard = '0';
} elseif ($presencia == '2') {
    $checkPresent = '0';
    $checkAbsent = '1';
    $checkRetard = '0';
} else {
    $checkPresent = '0';
    $checkAbsent = '0';
    $checkRetard = '1';
}


//vaig a buscar les sessions que he de tractar
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//sessions dels grups 
$query = "select ga26_codi_professor as codiprof,"
        . "ga26_dia_setmana as dia,ga26_hora_inici as hora,ga26_nivell as codinivell,ga26_codi_assignatura as codiassig,ga26_grup as codigrup,ga26_codi_aula as codiaula,"
        . "ga26_tipus_grup as tipusgrup,"
        . "(select ga28_professor from ga28_cont_presencia_cap where ga28_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga28_professor=codiprof and ga28_dia='" . $dataSessio . "' and ga28_hora=hora) as proftit"
        . " from ga26_horaris_docents,ga23_grups_profes_cap,ga24_grups_profes_det"
        . " where ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_tipus_grup=1 and ga26_grup=ga23_codi_grup and ga26_grup=ga24_codi_grup and ga24_codi_alumne=" . $alumne . " and ga26_dia_setmana=DAYOFWEEK('" . $dataSessio . "')-1 and ga26_is_lectiva=1 "
        . " group by dia,hora,codiprof order by hora,codiassig";



//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        //posem la informació en un array associativa
        $horarisArray[$row['hora'] . '-' . $row['codiprof']] = join('<#>', $row);
    }
}

$result->close();


//ara anem a buscar els horaris dels grups generals

$query = "select ga26_codi_professor as codiprof,"
        . "ga26_dia_setmana as dia,ga26_hora_inici as hora,ga26_nivell as codinivell,ga26_codi_assignatura as codiassig,ga26_grup as codigrup,ga26_codi_aula as codiaula,"
        . "ga26_tipus_grup as tipusgrup,"
        . "(select ga28_professor from ga28_cont_presencia_cap where ga28_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga28_professor=codiprof and ga28_dia='" . $dataSessio . "' and ga28_hora=hora) as proftit"
        . " from ga26_horaris_docents,ga12_alumnes_curs"
        . " where ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_tipus_grup=0 and ga26_codi_curs=ga12_codi_curs and ga26_grup=ga12_codi_grup and ga26_nivell=ga12_codi_nivell and ga12_id_alumne=" . $alumne . " and ga26_dia_setmana=DAYOFWEEK('" . $dataSessio . "')-1 and ga26_is_lectiva=1 "
        . " group by dia,hora,codiprof order by hora,codiassig";


$result = $conn->query($query);

if (!$result)
    die($conn->error);


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        //posem la informació en un array associativa
        $horarisArray[$row['hora'] . '-' . $row['codiprof']] = join('<#>', $row);
    }
}

$result->close();

if (isset($horarisArray)) {
    ksort($horarisArray);
}


if (isset($horarisArray)) {

    foreach ($horarisArray as $tramHorari) {
        //no existia la sessió
        $contingutHorari = explode('<#>', $tramHorari);

        $actiu = comprovaProfeActiu($dataSessio, $contingutHorari[0], $conn);


        if ($actiu === true) {
            //fem alguna cosa només si el profe estava actiu en aquesta data


            $professor = $contingutHorari[0];
            $hora = $contingutHorari[2];




            if ($contingutHorari[6] != '') {
                $aula = $contingutHorari[6];
            } else {
                $aula = 'null';
            }

            $nivell = $contingutHorari[3];
            $grup = $contingutHorari[5];
            $tipusGrup = $contingutHorari[7];
            $assignatura = $contingutHorari[4];
            if ($contingutHorari[8] == '') {


                //no s'ha passat llista no existeix la sessió
                //no s'ha passat llista per tant s'ha de crear la sessió
                $query28 = "insert into ga28_cont_presencia_cap (ga28_codi_curs,ga28_professor,ga28_dia,ga28_hora,ga28_is_guardia,ga28_prof_substituit,ga28_aula,ga28_nivell,ga28_grup,ga28_tipus_grup,ga28_assignatura,ga28_coment_general,ga28_estat) "
                        . " values (" . $_SESSION['curs_actual'] . "," . $professor . ",'" . $dataSessio . "','" . $hora . "','0',null," . $aula . "," . $nivell . "," . $grup . "," . $tipusGrup . "," . $assignatura . ",null,'1')";


                //executem la query
                $conn->query($query28);


                //ara afegim els alumnes
                //preparem la inserció
                $query15 = "insert into ga15_cont_presencia (ga15_codi_curs,ga15_alumne,ga15_codi_professor,ga15_dia,ga15_hora_inici,ga15_check_present,ga15_check_absent,ga15_check_retard,ga15_check_justificat,ga15_motiu_justificat,ga15_data_hora_darrera_mod,ga15_tipus_falta,ga15_estat,ga15_motiu,ga15_num_falta,ga15_just_tutor,ga15_just_resp,ga15_check_comunica,ga15_comentari) values ";
                //anem a buscar els alumnes de la sessió

                if ($tipusGrup == '0') {
                    //és un grup classe
                    $queryAlumnes = "select ga12_id_alumne as codialumne from ga12_alumnes_curs where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_codi_nivell=" . $nivell . " and ga12_codi_grup=" . $grup;
                } else {
                    //és un grup personal
                    $queryAlumnes = "select ga24_codi_alumne as codialumne from ga24_grups_profes_det where ga24_codi_grup=" . $grup;
                }

                $resultAlumnes = $conn->query($queryAlumnes);

                if (!$resultAlumnes)
                    die($conn->error);

                if ($resultAlumnes->num_rows > 0) {
                    $conta = 0;
                    while ($row1 = $resultAlumnes->fetch_assoc()) {
                        $alumne15 = $row1['codialumne'];

                        if ($alumne15 == $alumne) {
                            //aquest és l'alumne que volem crear assistència massiva

                            $alumneDetall = "(" . $_SESSION['curs_actual'] . "," . $alumne . "," . $professor . ",'" . $dataSessio . "','" . $hora . "','" . $checkPresent . "','" . $checkAbsent . "','" . $checkRetard . "','0','',now(),null,null,null,null,'0','0',"
                                    . "(select ga11_check_comunica from ga11_alumnes where ga11_id_alumne=" . $alumne . "),'" . $comentari . "')";
                        } else {
                            //aquest alumne NO és l'alumne de la selecció he de posar que està present
                            $alumneDetall = "(" . $_SESSION['curs_actual'] . "," . $alumne15 . "," . $professor . ",'" . $dataSessio . "','" . $hora . "','1','0','0','0','',now(),null,null,null,null,'0','0',"
                                    . "(select ga11_check_comunica from ga11_alumnes where ga11_id_alumne=" . $alumne . "),'')";
                        }
                        $query15 .= $alumneDetall;
                        if ($conta != $resultAlumnes->num_rows - 1) {
                            $query15 .= ",";
                        }
                        $conta++;
                    }
                }



                //executem la query
                $conn->query($query15);

                $resultAlumnes->close();
            } else {
                //sí que existeix la sessió
                //ja s'havia passat llsita, només es canvia l'estat (de 0 a 1 (provisional)

                $query28 = "update ga28_cont_presencia_cap set ga28_estat='1' "
                        . "where ga28_codi_curs=" . $_SESSION['curs_actual'] . " and ga28_professor=" . $professor . " and ga28_dia='" . $dataSessio . "' and ga28_hora='" . $hora . "' ";

                //executem la query
                $conn->query($query28);

                //ara afegim l'alumne
                //primerament esborrem les possibles faltes d'ordre comentaris i la pròpia assistència


                $query31 = "delete from ga31_faltes_ordre"
                        . " where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and "
                        . "ga31_codi_professor=" . $professor . " and "
                        . "ga31_alumne=" . $alumne . " and "
                        . "ga31_dia='" . $dataSessio . "' and "
                        . "ga31_hora_inici ='" . $hora . "' and "
                        . "ga31_es_sessio=1";

                //executem la query
                $conn->query($query31);

                $query43 = "delete from ga43_comentaris_sessio"
                        . " where ga43_codi_curs=" . $_SESSION['curs_actual'] . " and "
                        . "ga43_codi_professor=" . $professor . " and "
                        . "ga43_alumne=" . $alumne . " and "
                        . "ga43_dia='" . $dataSessio . "' and "
                        . "ga43_hora_inici='" . $hora . "' and "
                        . "ga43_es_sessio=1";


                //executem la query
                $conn->query($query43);

                $query15 = "delete from ga15_cont_presencia "
                        . "where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and "
                        . "ga15_codi_professor=" . $professor . " and "
                        . "ga15_alumne=" . $alumne . " and "
                        . "ga15_dia='" . $dataSessio . "' and "
                        . "ga15_hora_inici ='" . $hora . "'";

                //executem la query
                $conn->query($query15);


                //afegim l'alumne a la sessió
                //preparem la inserció a la taula 15
                $queryInsert = "insert into ga15_cont_presencia (ga15_codi_curs,ga15_alumne,ga15_codi_professor,ga15_dia,ga15_hora_inici,ga15_check_present,ga15_check_absent,ga15_check_retard,ga15_check_justificat,ga15_motiu_justificat,ga15_data_hora_darrera_mod,ga15_tipus_falta,ga15_estat,ga15_motiu,ga15_num_falta,ga15_just_tutor,ga15_just_resp,ga15_check_comunica,ga15_comentari) values ";

                $alumneDetall = "(" . $_SESSION['curs_actual'] . "," . $alumne . "," . $professor . ",'" . $dataSessio . "','" . $hora . "','" . $checkPresent . "','" . $checkAbsent . "','" . $checkRetard . "','0','',now(),null,null,null,null,'0','0',"
                        . "(select ga11_check_comunica from ga11_alumnes where ga11_id_alumne=" . $alumne . "),'" . $comentari . "')";

                $queryInsert .= $alumneDetall;
                $conn->query($queryInsert);
            }
        }
    }
}

$conn->close();

sleep(1);

echo $i;
