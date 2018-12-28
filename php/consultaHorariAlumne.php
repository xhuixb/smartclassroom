<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

session_start();


$codiAlumne = $_POST["codiAlumne"];
$nivell = $_POST["nivell"];
$grup = $_POST["grup"];
$aula = $_POST["aula"];


//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$horarisArray = [];

if ($aula == '') {
    if ($codiAlumne != '') {
        //anem a buscar els horaris de l'alumne

        $query = "select ga26_codi_professor as codiprof,"
                . "ga26_dia_setmana as dia,ga26_hora_inici as hora,ga26_nivell as codinivell,ga26_codi_assignatura as codiassig,ga26_grup as codigrup,ga26_codi_aula as codiaula,"
                . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=codiprof) as nomprof,"
                . "(select ga06_descripcio_nivell from ga06_nivell where ga06_codi_nivell=codinivell) as nomnivell,"
                . "(select ga23_nom_grup from ga23_grups_profes_cap where ga23_codi_grup=codigrup) as nomgrup,"
                . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=codiassig) as nomassig,"
                . "(select ga01_descripcio_aula from ga01_aula where ga01_codi_aula=codiaula) as nomaula,"
                . "ga26_tipus_grup"
                . " from ga26_horaris_docents,ga23_grups_profes_cap,ga24_grups_profes_det,ga04_professors"
                . " where ga26_codi_professor=ga04_codi_prof and ga04_suspes='0' and ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_tipus_grup=1 and ga26_grup=ga23_codi_grup and ga26_grup=ga24_codi_grup and ga24_codi_alumne=" . $codiAlumne . " and ga26_is_lectiva=1 order by hora,dia";

       
        

        //executem la consulta
        $result = $conn->query($query);



        if (!$result)
            die($conn->error);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                //posem la informació en un array associativa
                $horarisArray[$row['hora'] . '-' . $row['dia'] . '-' . $row['codiprof']] = join('<#>', $row);
            }
        }

        $result->close();

        //ara anem a buscar els horaris dels grups generals

        $query = "select ga26_codi_professor as codiprof,"
                . "ga26_dia_setmana as dia,ga26_hora_inici as hora,ga26_nivell as codinivell,ga26_codi_assignatura as codiassig,ga26_grup as codigrup,ga26_codi_aula as codiaula,"
                . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=codiprof) as nomprof,"
                . "(select ga06_descripcio_nivell from ga06_nivell where ga06_codi_nivell=codinivell) as nomnivell,"
                . "(select ga07_descripcio_grup from ga07_grup where ga07_codi_grup=codigrup) as nomgrup,"
                . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=codiassig) as nomassig,"
                . "(select ga01_descripcio_aula from ga01_aula where ga01_codi_aula=codiaula) as nomaula,"
                . "ga26_tipus_grup"
                . " from ga26_horaris_docents,ga12_alumnes_curs,ga04_professors"
                . " where ga26_codi_professor=ga04_codi_prof and ga04_suspes='0' and ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_codi_curs=ga12_codi_curs and ga26_tipus_grup=0 and ga26_grup=ga12_codi_grup and ga26_nivell=ga12_codi_nivell and ga12_id_alumne=" . $codiAlumne . " and ga26_is_lectiva=1 order by hora,dia";

    
        
        //executem la consulta
        $result = $conn->query($query);



        if (!$result)
            die($conn->error);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                //posem la informació en un array associativa
                $horarisArray[$row['hora'] . '-' . $row['dia'] . '-' . $row['codiprof']] = join('<#>', $row);
            }
        }

        $result->close();
    } else {

        //anem a buscar els hoaris del nivell grup
        //anem a buscar els horaris de l'alumne

        $query = "select ga26_codi_professor as codiprof,"
                . "ga26_dia_setmana as dia,ga26_hora_inici as hora,ga26_nivell as codinivell,ga26_codi_assignatura as codiassig,ga26_grup as codigrup,ga26_codi_aula as codiaula,"
                . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=codiprof) as nomprof,"
                . "(select ga06_descripcio_nivell from ga06_nivell where ga06_codi_nivell=codinivell) as nomnivell,"
                . "(select ga23_nom_grup from ga23_grups_profes_cap where ga23_codi_grup=codigrup) as nomgrup,"
                . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=codiassig) as nomassig,"
                . "(select ga01_descripcio_aula from ga01_aula where ga01_codi_aula=codiaula) as nomaula,"
                . "ga26_tipus_grup"
                . " from ga26_horaris_docents,ga23_grups_profes_cap,ga24_grups_profes_det,ga04_professors"
                . " where ga26_codi_professor=ga04_codi_prof and ga04_suspes='0' and ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_tipus_grup=1 and ga26_grup=ga23_codi_grup and ga26_grup=ga24_codi_grup and ga24_codi_alumne in (select ga12_id_alumne from ga12_alumnes_curs where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_codi_nivell=" . $nivell . " and ga12_codi_grup=" . $grup . ") and ga26_is_lectiva=1 order by hora,dia";



        //executem la consulta
        $result = $conn->query($query);



        if (!$result)
            die($conn->error);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                //posem la informació en un array associativa
                $horarisArray[$row['hora'] . '-' . $row['dia'] . '-' . $row['codiprof']] = join('<#>', $row);
            }
        }

        $result->close();

        //ara anem a buscar els horaris dels grups generals

        $query = "select ga26_codi_professor as codiprof,"
                . "ga26_dia_setmana as dia,ga26_hora_inici as hora,ga26_nivell as codinivell,ga26_codi_assignatura as codiassig,ga26_grup as codigrup,ga26_codi_aula as codiaula,"
                . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=codiprof) as nomprof,"
                . "(select ga06_descripcio_nivell from ga06_nivell where ga06_codi_nivell=codinivell) as nomnivell,"
                . "(select ga07_descripcio_grup from ga07_grup where ga07_codi_grup=codigrup) as nomgrup,"
                . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=codiassig) as nomassig,"
                . "(select ga01_descripcio_aula from ga01_aula where ga01_codi_aula=codiaula) as nomaula,"
                . "ga26_tipus_grup"
                . " from ga26_horaris_docents,ga04_professors"
                . " where ga26_codi_professor=ga04_codi_prof and ga04_suspes='0' and ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_tipus_grup=0 and ga26_grup=" . $grup . " and ga26_nivell=" . $nivell . " and ga26_is_lectiva=1 order by ga26_dia_setmana,ga26_hora_inici";


        //executem la consulta
        $result = $conn->query($query);



        if (!$result)
            die($conn->error);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                //posem la informació en un array associativa
                $horarisArray[$row['hora'] . '-' . $row['dia'] . '-' . $row['codiprof']] = join('<#>', $row);
            }
        }

        $result->close();
    }
    //ordenem l'array per la clau
    ksort($horarisArray);
} else {
    //cerquem els horaris per aula
    //cerquem els horaris per aula dels grups particulars
    $query = "select ga26_codi_professor as codiprof,ga26_dia_setmana as dia,ga26_hora_inici as hora,ga26_nivell as codinivell,ga26_codi_assignatura as codiassig,ga26_grup as codigrup,ga26_codi_aula as codiaula,"
            . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=codiprof) as nomprof,"
            . "(select ga06_descripcio_nivell from ga06_nivell where ga06_codi_nivell=codinivell) as nomnivell,"
            . "(select ga23_nom_grup from ga23_grups_profes_cap where ga23_codi_grup=codigrup) as nomgrup,"
            . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=codiassig) as nomassig,"
            . "(select ga01_descripcio_aula from ga01_aula where ga01_codi_aula=codiaula) as nomaula,"
            . "ga26_tipus_grup"
            . " from ga26_horaris_docents,ga04_professors where ga26_codi_professor=ga04_codi_prof and ga04_suspes='0' and ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_tipus_grup=1  and ga26_codi_aula=" . $aula . " and ga26_is_lectiva=1"
            . " order by hora,dia";

    //executem la consulta
    $result = $conn->query($query);



    if (!$result)
        die($conn->error);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            //posem la informació en un array associativa
            $horarisArray[$row['hora'] . '-' . $row['dia'] . '-' . $row['codiprof']] = join('<#>', $row);
        }
    }

    $result->close();

    //anem a buscar les aules del grups generals
    $query = "select ga26_codi_professor as codiprof,"
            . "ga26_dia_setmana as dia,ga26_hora_inici as hora,ga26_nivell as codinivell,ga26_codi_assignatura as codiassig,ga26_grup as codigrup,ga26_codi_aula as codiaula,"
            . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=codiprof) as nomprof,"
            . "(select ga06_descripcio_nivell from ga06_nivell where ga06_codi_nivell=codinivell) as nomnivell,"
            . "(select ga07_descripcio_grup from ga07_grup where ga07_codi_grup=codigrup) as nomgrup,"
            . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=codiassig) as nomassig,"
            . "(select ga01_descripcio_aula from ga01_aula where ga01_codi_aula=codiaula) as nomaula,"
            . "ga26_tipus_grup"
            . " from ga26_horaris_docents,ga04_professors"
            . " where ga26_codi_professor=ga04_codi_prof and ga04_suspes='0' and ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_tipus_grup=0 and ga26_grup<>'' and ga26_codi_aula=" . $aula . " and ga26_is_lectiva=1 order by ga26_dia_setmana,ga26_hora_inici";

    //executem la consulta
    $result = $conn->query($query);



    if (!$result)
        die($conn->error);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            //posem la informació en un array associativa
            $horarisArray[$row['hora'] . '-' . $row['dia'] . '-' . $row['codiprof']] = join('<#>', $row);
        }
    }

    $result->close();



    krsort($horarisArray);
}



//creem la taula dels horaris

$query = "select ga10_hora_inici as horainici,ga10_hora_fi as horafi,ga10_es_descans as esdescans,ga10_tipus_horari as tipushorari from ga10_horaris_aula where ga10_codi_curs=".$_SESSION['curs_actual'];


//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    echo '<table id="taulaHorarisAlumne" class="table table-bordered">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="col-sm-1">HORA</th>';
    echo '<th class="col-sm-2"><center>DILLUNS</center></th>';
    echo '<th class="col-sm-2"><center>DIMARTS</center></th>';
    echo '<th class="col-sm-2"><center>DIMECRES</center></th>';
    echo '<th class="col-sm-2"><center>DIJOUS</center></th>';
    echo '<th class="col-sm-2"><center>DIVENDRES</center></th>';
    echo '</tr>';
    echo '</thead>';

    echo '<tbody id="costaulaHorarisAlumne">';
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

        $textHorari = [];

        for ($j = 1; $j <= 5; $j++) {
            $textHorari[$j] = '';
        }

        $assigTram = [];

        //inicialitzem l'array
        for ($i = 0; $i <= 5; $i++) {
            $assigTram[$i] = '';
        }

        foreach ($horarisArray as $tramHorari) {


            $contingutHorari = explode('<#>', $tramHorari);
            if ($row['horainici'] == $contingutHorari[2]) {

                if (strpos($assigTram[$contingutHorari[1]], $contingutHorari[4] . '-' . $contingutHorari[5] . '-' . $contingutHorari[12]) === false) {
                    //no es repeteix una assig-grup-tipusgrup
                    //estem en el tram horari
                    $textHorari[$contingutHorari[1]] .= '<a data-toggle="modal" data-target="#detallHorariAlumneModalForm" onclick="mostraDetallHorari(this)" onmouseover="provaTooltip(this);" href="#" data-toggle="tooltip" title="" data-dia="' . $contingutHorari[1] . '" data-horainici="' . $contingutHorari[2] . '" data-horafi="' . $row['horafi'] . '" data-prof="' . $contingutHorari[7] . '" data-assig="' . $contingutHorari[10] . '" data-nivell="' . $contingutHorari[8] . '" data-grup="' . $contingutHorari[9] . '" data-aula="' . $contingutHorari[11] . '" data-codi-grup="' . $contingutHorari[5] . '" data-tipus-grup="' . $contingutHorari[12] . '" data-codi-nivell="' . $contingutHorari[3] . '">' . $contingutHorari[10] . '</a><br>';
                } else {
                    //es repeteix assig-grup-tipusgrup
                    //inserim el professor
                    $posicioInser = strpos($textHorari[$contingutHorari[1]], 'data-prof=') + 11;

                    $textHorari[$contingutHorari[1]] = substr_replace($textHorari[$contingutHorari[1]], $contingutHorari[7] . ';', $posicioInser, 0);
                }
                $assigTram[$contingutHorari[1]] .= $contingutHorari[4] . '-' . $contingutHorari[5] . '-' . $contingutHorari[12] . '<#>';
            }
        }



        echo '<tr>';
        echo '<td class="col-sm-1' . $colorDescans . '" data-horainici="' . $row['horainici'] . '" data-esdescans="' . $row['esdescans'] . '">Inici: ' . $row['horainici'] . '<br>' . 'Fi: ' . $row['horafi'] . '<br>' . $esdescans . '</td>';

        for ($i = 1; $i <= 5; $i++) {

            echo '<td class="col-sm-2">' . $textHorari[$i] . '</td>';
        }

        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
}

$result->close();

$conn->close();
