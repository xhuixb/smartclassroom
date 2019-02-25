<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
require '../classes/utilitatsProfe.php';

//fem la connexió

session_start();

$mode = $_POST['mode'];
if ($mode == "1") {
    $dataSessio = $_POST['dataSessio'];
    $horaProfe = $_POST['horaProfe'];
    $item = $_POST['conta'];
    $max = $_POST['max'];
} else {
    $dadesSessio = [];
    $dadesSessio = $_POST['dadesSessio'];
    $item = $_POST['conta'];
    $max = $_POST['max'];
    $alumnesSessioMassiva = [];
    $alumnesSessioMassiva = $_POST['alumnesSessioMassiva'];
    $alumnesSessioIndex = [];

    for ($i = 0; $i < count($alumnesSessioMassiva); $i++) {

        $alumnesEstat = explode('<#>', $alumnesSessioMassiva[$i]);

        $alumnesSessioIndex[$alumnesEstat[0]] = $alumnesEstat[1];
    }
}


//fem la connexió a la base de dades per totes les consultes que ens caldran
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


if ($mode == '1') {

    //esborrem les sessions massivament
    $horesProfeArray = explode('<#>', $horaProfe);

//esborrem les faltes d'ordre anteriors d'aquesta assistència
    $query = "delete from ga31_faltes_ordre"
            . " where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and "
            . "ga31_codi_professor=" . $horesProfeArray[1] . " and "
            . "ga31_dia='" . $dataSessio . "' and "
            . "ga31_hora_inici ='" . $horesProfeArray[0] . "' and "
            . "ga31_es_sessio=1";



//executem la query
    $conn->query($query);


//esborrem els comentaris
    $query = "delete from ga43_comentaris_sessio"
            . " where ga43_codi_curs=" . $_SESSION['curs_actual'] . " and "
            . "ga43_codi_professor=" . $horesProfeArray[1] . " and "
            . "ga43_dia='" . $dataSessio . "' and "
            . "ga43_hora_inici='" . $horesProfeArray[0] . "' and "
            . "ga43_es_sessio=1";


//executem la query
    $conn->query($query);

//esborrem l'assistència anterior detall
    $query = "delete from ga15_cont_presencia "
            . "where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and "
            . "ga15_codi_professor=" . $horesProfeArray[1] . " and "
            . "ga15_dia='" . $dataSessio . "' and "
            . "ga15_hora_inici ='" . $horesProfeArray[0] . "'";

//executem la query
    $conn->query($query);


////esborrem l'assistència anterior capçalera

    $query = "delete from ga28_cont_presencia_cap "
            . "where ga28_codi_curs=" . $_SESSION['curs_actual'] . " and "
            . "ga28_professor=" . $horesProfeArray[1] . " and "
            . "ga28_dia='" . $dataSessio . "' and "
            . "ga28_hora='" . $horesProfeArray[0] . "'";

//executem la query
    $conn->query($query);

    $percent = round((($item + 1) / $max) * 100, 2);

    echo '<div class="progress">';
    echo '<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="' . $percent . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $percent . '%">';
    echo $percent . '% processat';
    echo '</div>';
    echo '</div >';

    sleep(1);
} else {
    //creem les sessions massivament

    $dadesSessioArray = explode('<#>', $dadesSessio);

    $professor = $dadesSessioArray[0];
    $dia = $dadesSessioArray[1];
    //echo $dia.'-'.$professor;
    //comprovem que es tracti de la sessió d'un profe actiu
    $actiu = comprovaProfeActiu($dia, $professor, $conn);

    if ($actiu === true) {


        $hora = $dadesSessioArray[2];


        if ($dadesSessioArray[3] != '') {
            $aula = $dadesSessioArray[3];
        } else {
            $aula = 'null';
        }

        $nivell = $dadesSessioArray[4];
        $grup = $dadesSessioArray[5];
        $tipusGrup = $dadesSessioArray[6];
        $assignatura = $dadesSessioArray[7];
        $comentari = str_replace("'", "''", $dadesSessioArray[8]);
        $estatSessio = $dadesSessioArray[9];

        if ($estatSessio === '0') {

            //no s'ha passat llista per tant s'ha de crear la sessió
            $query = "insert into ga28_cont_presencia_cap (ga28_codi_curs,ga28_professor,ga28_dia,ga28_hora,ga28_is_guardia,ga28_prof_substituit,ga28_aula,ga28_nivell,ga28_grup,ga28_tipus_grup,ga28_assignatura,ga28_coment_general,ga28_estat) "
                    . " values (" . $_SESSION['curs_actual'] . "," . $professor . ",'" . $dia . "','" . $hora . "','0',null," . $aula . "," . $nivell . "," . $grup . "," . $tipusGrup . "," . $assignatura . ",null,'1')";

            //executem la query
            $conn->query($query);


            //ara afegim els alumnes
            //preparem la inserció
            $query = "insert into ga15_cont_presencia (ga15_codi_curs,ga15_alumne,ga15_codi_professor,ga15_dia,ga15_hora_inici,ga15_check_present,ga15_check_absent,ga15_check_retard,ga15_check_justificat,ga15_motiu_justificat,ga15_data_hora_darrera_mod,ga15_tipus_falta,ga15_estat,ga15_motiu,ga15_num_falta,ga15_just_tutor,ga15_just_resp,ga15_check_comunica,ga15_comentari) values ";
            //anem a buscar els alumnes de la sessió

            if ($tipusGrup == '0') {
                //és un grup classe
                $queryAlumnes = "select ga12_id_alumne as codialumne from ga12_alumnes_curs where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_codi_nivell=" . $nivell . " and ga12_codi_grup=" . $grup;
            } else {
                //és un grup personal
                $queryAlumnes = "select ga24_codi_alumne as codialumne from ga24_grups_profes_det where ga24_codi_grup=" . $grup;
            }

            $result = $conn->query($queryAlumnes);

            if (!$result)
                die($conn->error);

            if ($result->num_rows > 0) {
                $conta = 0;
                while ($row = $result->fetch_assoc()) {
                    $alumne = $row['codialumne'];

                    if (array_key_exists($alumne, $alumnesSessioIndex)) {
                        //aquest alumne està en la selecció massiva dels alumnes hem de posar l'assistència indicada
                        $presencia = $alumnesSessioIndex[$alumne];
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
                        $alumneDetall = "(" . $_SESSION['curs_actual'] . "," . $alumne . "," . $professor . ",'" . $dia . "','" . $hora . "','" . $checkPresent . "','" . $checkAbsent . "','" . $checkRetard . "','0','',now(),null,null,null,null,'0','0',"
                                . "(select ga11_check_comunica from ga11_alumnes where ga11_id_alumne=" . $alumne . "),'" . $comentari . "')";
                    } else {
                        //aquest alumne NO està en la selecció massiva dels alumnes he de posar que està present
                        $alumneDetall = "(" . $_SESSION['curs_actual'] . "," . $alumne . "," . $professor . ",'" . $dia . "','" . $hora . "','1','0','0','0','',now(),null,null,null,null,'0','0',"
                                . "(select ga11_check_comunica from ga11_alumnes where ga11_id_alumne=" . $alumne . "),'')";
                    }
                    $query .= $alumneDetall;
                    if ($conta != $result->num_rows - 1) {
                        $query .= ",";
                    }
                    $conta++;
                }
            }



            //executem la query
            $conn->query($query);

            $result->close();
        } else {
            //ja s'havia passat llsita, només es canvia l'estat (de 0 a 1 (provisional)

            $query = "update ga28_cont_presencia_cap set ga28_estat='1' "
                    . "where ga28_codi_curs=" . $_SESSION['curs_actual'] . " and ga28_professor=" . $professor . " and ga28_dia='" . $dia . "' and ga28_hora='" . $hora . "' ";

            //executem la query
            $conn->query($query);

            //ara afegim els alumnes

            if ($tipusGrup == '0') {
                //és un grup classe
                $queryAlumnes = "select ga12_id_alumne as codialumne from ga12_alumnes_curs where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_codi_nivell=" . $nivell . " and ga12_codi_grup=" . $grup;
            } else {
                //és un grup personal
                $queryAlumnes = "select ga24_codi_alumne as codialumne from ga24_grups_profes_det where ga24_codi_grup=" . $grup;
            }

            $result = $conn->query($queryAlumnes);

            if (!$result)
                die($conn->error);

            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {
                    $alumne = $row['codialumne'];

                    if (array_key_exists($alumne, $alumnesSessioIndex)) {
                        //aquest alumne està en la selecció massiva dels alumnes hem de posar l'assistència indicada
                        //preparem la inserció
                        //esborrem l'assistència anterior i la resta de dades per si de cas
                        $query = "delete from ga31_faltes_ordre"
                                . " where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and "
                                . "ga31_codi_professor=" . $professor . " and "
                                . "ga31_alumne=" . $alumne . " and "
                                . "ga31_dia='" . $dia . "' and "
                                . "ga31_hora_inici ='" . $hora . "' and "
                                . "ga31_es_sessio=1";

                        //executem la query
                        $conn->query($query);

                        $query = "delete from ga43_comentaris_sessio"
                                . " where ga43_codi_curs=" . $_SESSION['curs_actual'] . " and "
                                . "ga43_codi_professor=" . $professor . " and "
                                . "ga43_alumne=" . $alumne . " and "
                                . "ga43_dia='" . $dia . "' and "
                                . "ga43_hora_inici='" . $hora . "' and "
                                . "ga43_es_sessio=1";


                        //executem la query
                        $conn->query($query);

                        $query = "delete from ga15_cont_presencia "
                                . "where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and "
                                . "ga15_codi_professor=" . $professor . " and "
                                . "ga15_alumne=" . $alumne . " and "
                                . "ga15_dia='" . $dia . "' and "
                                . "ga15_hora_inici ='" . $hora . "'";

                        //executem la query
                        $conn->query($query);

                        //preparem la inserció a la taula 15
                        $query = "insert into ga15_cont_presencia (ga15_codi_curs,ga15_alumne,ga15_codi_professor,ga15_dia,ga15_hora_inici,ga15_check_present,ga15_check_absent,ga15_check_retard,ga15_check_justificat,ga15_motiu_justificat,ga15_data_hora_darrera_mod,ga15_tipus_falta,ga15_estat,ga15_motiu,ga15_num_falta,ga15_just_tutor,ga15_just_resp,ga15_check_comunica,ga15_comentari) values ";

                        $presencia = $alumnesSessioIndex[$alumne];
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
                        $alumneDetall = "(" . $_SESSION['curs_actual'] . "," . $alumne . "," . $professor . ",'" . $dia . "','" . $hora . "','" . $checkPresent . "','" . $checkAbsent . "','" . $checkRetard . "','0','',now(),null,null,null,null,'0','0',"
                                . "(select ga11_check_comunica from ga11_alumnes where ga11_id_alumne=" . $alumne . "),'" . $comentari . "')";

                        $query .= $alumneDetall;
                        //executem la query
                        $conn->query($query);
                    }
                }
            }



            $result->close();
        }



        $percent = round((($item + 1) / $max) * 100, 2);

        echo '<div class="progress">';
        echo '<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="' . $percent . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $percent . '%">';
        echo $percent . '% processat';
        echo '</div>';
        echo '</div >';

        sleep(1);
    }
}
$conn->close();
