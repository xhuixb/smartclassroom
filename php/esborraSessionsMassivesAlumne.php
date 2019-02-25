<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió
//rebem les dades

session_start();

$dataSessio = $_POST['dataSessio'];
$nivell = $_POST['nivell'];
$grup = $_POST['grup'];
$alumne = $_POST['alumne'];
$i = $_POST['i'];

//posem la data en el format universal
$dataSessio = substr($dataSessio, 6) . '-' . substr($dataSessio, 3, 2) . '-' . substr($dataSessio, 0, 2);

//vaig a buscar les sessions que he de tractar
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


//anem a buscar les sessions dels grups personals
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
        //esborrarem les dades de les sessions que existeixin
        if ($row['proftit'] != '') {
            //vol dir que hi ha sessió
            $professor = $row['codiprof'];
            $hora = $row['hora'];

            //esborrem les faltes d'ordre
            $query = "delete from ga31_faltes_ordre"
                    . " where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and "
                    . "ga31_codi_professor=" . $professor . " and "
                    . "ga31_dia='" . $dataSessio . "' and "
                    . "ga31_hora_inici ='" . $hora . "' and "
                    . "ga31_es_sessio=1";


            //executem la query
            $conn->query($query);

            //esborrem l'assistència anterior detall
            $query = "delete from ga15_cont_presencia "
                    . "where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and "
                    . "ga15_codi_professor=" . $professor . " and "
                    . "ga15_dia='" . $dataSessio . "' and "
                    . "ga15_hora_inici ='" . $hora . "'";

            //executem la query
            $conn->query($query);


            //esborrem l'assistència anterior capçalera

            $query = "delete from ga28_cont_presencia_cap "
                    . "where ga28_codi_curs=" . $_SESSION['curs_actual'] . " and "
                    . "ga28_professor=" . $professor . " and "
                    . "ga28_dia='" . $dataSessio . "' and "
                    . "ga28_hora='" . $hora . "'";

            //executem la query
            $conn->query($query);
        }
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



//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);


if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {
        //esborrarem les dades de les sessions que existeixin
        if ($row['proftit'] != '') {
            //vol dir que hi ha sessió
            $professor = $row['codiprof'];
            $hora = $row['hora'];

            //esborrem les faltes d'ordre
            $query = "delete from ga31_faltes_ordre"
                    . " where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and "
                    . "ga31_codi_professor=" . $professor . " and "
                    . "ga31_dia='" . $dataSessio . "' and "
                    . "ga31_hora_inici ='" . $hora . "' and "
                    . "ga31_es_sessio=1";


            //executem la query
            $conn->query($query);

            //esborrem l'assistència anterior detall
            $query = "delete from ga15_cont_presencia "
                    . "where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and "
                    . "ga15_codi_professor=" . $professor . " and "
                    . "ga15_dia='" . $dataSessio . "' and "
                    . "ga15_hora_inici ='" . $hora . "'";

            //executem la query
            $conn->query($query);


            //esborrem l'assistència anterior capçalera

            $query = "delete from ga28_cont_presencia_cap "
                    . "where ga28_codi_curs=" . $_SESSION['curs_actual'] . " and "
                    . "ga28_professor=" . $professor . " and "
                    . "ga28_dia='" . $dataSessio . "' and "
                    . "ga28_hora='" . $hora . "'";

            //executem la query
            $conn->query($query);
        }
    }
}


$result->close();
$conn->close();

sleep(1);

echo $i;
