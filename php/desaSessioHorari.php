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

if (isset($_POST['dadesGeneralsSessio'])) {
//recullim totes les variables


    $dia = $_POST['dia'];
    $hora = $_POST['hora'];

    $esguardia = $_POST['esguardia'];
    $nivell = $_POST['nivell'];
    $grup = $_POST['grup'];
    $tipusGrup = $_POST['tipusgrup'];
    $assignatura = $_POST['assignatura'];
    if ($_POST['aula'] != '') {
        $aula = $_POST['aula'];
    } else {
        $aula = 'null';
    }
    $dadesGeneralsSessio = $_POST['dadesGeneralsSessio'];
    $tipusSubs = $_POST['tipusSubs'];

    $comentari = str_replace("'", "''", $_POST['comentari']);

    if ($_POST['codiProf'] == '') {
        $codiprof = $_SESSION['prof_actual'];
        $profSubs = 'null';
    } elseif ($_POST['codiProf'] != '' && $tipusSubs == '1') {
        $codiprof = $_POST['codiProf'];
        $profSubs = $_SESSION['prof_actual'];
    } elseif ($_POST['codiProf'] != '' && $tipusSubs == '2') {
        $codiprof = $_SESSION['prof_actual'];
        $profSubs = $_POST['codiProf'];
    }
}


//fem la connexió a la base de dades per totes les consultes que ens caldran
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");
//esborrem les faltes d'ordre anteriors d'aquesta assistència
$query = "delete from ga31_faltes_ordre"
        . " where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and "
        . "ga31_codi_professor=" . $codiprof . " and "
        . "ga31_dia='" . $dia . "' and "
        . "ga31_hora_inici ='" . $hora . "' and "
        . "ga31_es_sessio=1";

//executem la query
$conn->query($query);

//esborrem els comentaris anteriors
$query = "delete from ga43_comentaris_sessio"
        . " where ga43_codi_curs=" . $_SESSION['curs_actual'] . " and "
        . "ga43_codi_professor=" . $codiprof . " and "
        . "ga43_dia='" . $dia . "' and "
        . "ga43_hora_inici='" . $hora . "' and "
        . "ga43_es_sessio=1";


//executem la query
$conn->query($query);

//esborrem l'assistència anterior detall
$query = "delete from ga15_cont_presencia "
        . "where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and "
        . "ga15_codi_professor=" . $codiprof . " and "
        . "ga15_dia='" . $dia . "' and "
        . "ga15_hora_inici ='" . $hora . "'";

//executem la query
$conn->query($query);


////esborrem l'assistència anterior capçalera

$query = "delete from ga28_cont_presencia_cap "
        . "where ga28_codi_curs=" . $_SESSION['curs_actual'] . " and "
        . "ga28_professor=" . $codiprof . " and "
        . "ga28_dia='" . $dia . "' and "
        . "ga28_hora='" . $hora . "'";

//executem la query
$conn->query($query);


//esborrem els comentaris anteriors
//creem la capçalera de l'assitència nova
if (isset($_POST['dadesGeneralsSessio'])) {


    $query = "INSERT INTO ga28_cont_presencia_cap"
            . "(ga28_codi_curs,"
            . "ga28_professor,"
            . "ga28_dia,"
            . "ga28_hora,"
            . "ga28_is_guardia,"
            . "ga28_prof_substituit,"
            . "ga28_aula,"
            . "ga28_nivell,"
            . "ga28_grup,"
            . "ga28_tipus_grup,"
            . "ga28_assignatura,"
            . "ga28_coment_general)"
            . "VALUES"
            . "(" . $_SESSION['curs_actual'] . "," . $codiprof . ",'" . $dia . "','" . $hora . "','" . $esguardia . "'," . $profSubs . "," . $aula . "," . $nivell . "," . $grup . "," . $tipusGrup . "," . $assignatura . ",'" . $comentari . "')";


    //executem la query
    $conn->query($query);
}

if (isset($_POST['dadesGeneralsSessio'])) {
//creem la nova assistència
    //inicialitzem les comunicacions



    for ($x = 0; $x < count($dadesGeneralsSessio); $x++) {
        //fem un split de cada filera
        $fileraAlumne = [];
        // echo $dadesGeneralsSessio[$x];
        $fileraAlumne = explode('<#>', $dadesGeneralsSessio[$x]);

        if ($fileraAlumne[1] !== "") {
            //l'alumne té faltes d'ordre
            //substituiem els apostrofs
            $fileraAlumne[2] = str_replace("'", "''", $fileraAlumne[2]);
            $query = "insert into ga15_cont_presencia values(" . $_SESSION['curs_actual'] . "," . substr($fileraAlumne[0], 2) . "," . $codiprof . ",'" . $dia . "','" . $hora . "','" . $fileraAlumne[5] . "','" . $fileraAlumne[6] . "','" . $fileraAlumne[7] . "','0','',now(),"
                    . "" . $fileraAlumne[1] . ",1,'" . $fileraAlumne[2] . "',null,'" . $fileraAlumne[4] . "','" . $fileraAlumne[3] . "','" . $fileraAlumne[8] . "')";

            //inserim la falta d'ordre a la taula cooresponent
            $query2 = "INSERT INTO ga31_faltes_ordre (ga31_codi_curs,ga31_alumne,ga31_codi_professor,ga31_dia,ga31_hora_inici,ga31_tipus_falta,ga31_estat,ga31_motiu,ga31_es_sessio,ga31_just_tutor,ga31_just_resp,ga31_assignatura)"
                    . " VALUES (" . $_SESSION['curs_actual'] . "," . substr($fileraAlumne[0], 2) . "," . $codiprof . ",'" . $dia . "','" . $hora . "'," . $fileraAlumne[1] . ",1,'" . $fileraAlumne[2] . "',1,'" . $fileraAlumne[4] . "','" . $fileraAlumne[3] . "'," . $assignatura . ")";
            $conn->query($query);
            $conn->query($query2);
        } else {
            //l'alumne no té faltes d'ordre
            $query = "insert into ga15_cont_presencia values(" . $_SESSION['curs_actual'] . "," . substr($fileraAlumne[0], 2) . "," . $codiprof . ",'" . $dia . "','" . $hora . "','" . $fileraAlumne[5] . "','" . $fileraAlumne[6] . "','" . $fileraAlumne[7] . "','0','',now(),null,null,null,null,'0','0','" . $fileraAlumne[8] . "')";

            $conn->query($query);
        }
        if ($fileraAlumne[9] !== "") {
            //l'alumne té un comentari
            $fileraAlumne[9]=str_replace("'", "''", $fileraAlumne[9]);
            $query="insert into ga43_comentaris_sessio (ga43_codi_curs,ga43_alumne,ga43_codi_professor,ga43_dia,ga43_hora_inici,ga43_text,ga43_es_sessio,ga43_enviat,ga43_switch_enviar,ga43_assignatura) values(". $_SESSION['curs_actual'] . "," . substr($fileraAlumne[0], 2) . "," . $codiprof . ",'" . $dia . "','" . $hora . "','".$fileraAlumne[9]."','1','0','".$fileraAlumne[10]."',".$assignatura.")";
            //echo $query;
            $conn->query($query);
        }
    }
}

$conn->close();

