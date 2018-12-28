<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../classes/Databases.php';
//fem la connexió

session_start();
//$_SESSION['curs_actual'] = 2;
//$_SESSION['prof_actual'] = 1;

$nivell = $_POST['nivell'];
$grup = $_POST['grup'];
$tipusgrup = $_POST['tipusgrup'];
$hora = $_POST['hora'];
$dia = $_POST['dia'];

$diaFormSql = date("Y-m-d", strtotime($dia));

//comprovem si s'havia passat llista
$query1 = "select count(*) as comptador from ga15_cont_presencia"
        . " where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_codi_professor=" . $_SESSION['prof_actual'] . " and ga15_dia='" . $diaFormSql . "' and ga15_hora_inici='" . $hora . "'";


$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$result1 = $conn->query($query1);

$row = $result1->fetch_assoc();

if ($row['comptador'] == 0) {
//no s'ha passat mai llista
    //no és una aula singular
    if ($tipusgrup == "0") {
        //és un grup general
        $query = "select ga12_id_alumne as codi, ga07_descripcio_grup as descrgrup,ga06_descripcio_nivell as descrnivell,concat(ga11_cognom1, ' ' ,ga11_cognom2 , ', ' , ga11_nom) as alumne,ga11_check_comunica as checkcomunica "
                . "from ga12_alumnes_curs ,ga11_alumnes,ga07_grup,ga06_nivell "
                . "where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_codi_nivell=" . $nivell . " and ga12_codi_grup=" . $grup . " and ga12_id_alumne=ga11_id_alumne and ga12_codi_grup=ga07_codi_grup and ga06_codi_nivell=ga12_codi_nivell "
                . "order by alumne";
    } else {
        //és un grup personal d'un profe
        $query = "select ga24_codi_alumne as codi,ga07_descripcio_grup as descrgrup,ga06_descripcio_nivell as descrnivell,concat(ga11_cognom1, ' ' ,ga11_cognom2 , ', ' , ga11_nom) as alumne, ga11_check_comunica as checkcomunica "
                . " from ga24_grups_profes_det,ga23_grups_profes_cap,ga11_alumnes,ga07_grup,ga12_alumnes_curs,ga06_nivell"
                . " where ga24_codi_grup=" . $grup . " and ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga24_codi_grup=ga23_codi_grup and ga24_codi_alumne=ga11_id_alumne and ga24_codi_alumne=ga12_id_alumne and ga12_codi_grup=ga07_codi_grup and ga06_codi_nivell=ga12_codi_nivell order by alumne";
    }

//executem la consulta
    $result = $conn->query($query);


    if (!$result)
        die($conn->error);

//construim capçalera de la taula
    echo '<br>';
    echo '<table id="taulaAssist" class="table" data-nou="1" data-prof-subs="" data-nom-prof-subs="">';
    echo '<thead>';
    echo '<tr>';
    echo '<th><form class="form-inline"><input type="checkbox" value="" id="checkMarcaDesmarca" onclick="seleccionaTot();"><button type="button" class="btn btn-warning form-control" onclick="esborraAlumnes()"><span class="glyphicon glyphicon-trash"></span></button></form></th>';
    echo '<th>Nivell</th>';
    echo '<th>Grup</th>';
    echo '<th>Alumne</th>';
    echo '<th>Pres</th>';
    echo '<th>Abs</th>';
    echo '<th>Ret</th>';
    echo'<th><center>CCC</center></th>';
    echo '<th><span class="glyphicon glyphicon-envelope"></span></th>';
    echo '</tr>';
    echo '</thead>';




    if ($result->num_rows > 0) {
        echo '<tbody id="cosTaulaAssist" data-modifi="">';
        while ($row = $result->fetch_assoc()) {
            //onstruim el cos de la taula


            echo '<tr>';
            echo '<td><input type="checkbox" value="" class="checkEsborrar"></td>';
            echo '<td>' . $row['descrnivell'] . '</td>';
            echo '<td>' . $row['descrgrup'] . '</td>';
            echo '<td id="al' . $row['codi'] . '" data-tipus="" data-estat="" data-motiu="" data-num="" data-textfalta="" data-avisresponsables="0" data-avistutor="0"><a data-toggle="modal" data-target="#imatgeAlumneModalSessio" onclick="mostraImatgeSessio(this)">' . $row['alumne'] . '</a></td>';
            echo '<td><input type="checkbox" value="" checked class="checkAssist" onchange="comprovaCheck(this)"></td>';
            echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
            echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
            echo '<td><button type="button" class="btn form-control" data-toggle="modal" data-target="#faltesModalForm" onclick="carregaFaltes(this)">';
            echo '<span class="glyphicon glyphicon-pencil"></span>CCC</button></td>';
            if ($row['checkcomunica'] == '1') {
                echo '<td><input type="checkbox" value="" checked disabled></td>';
            } else {
                echo '<td><input type="checkbox" value="" disabled></td>';
            }
            echo '</tr>';
        }
    }


//tanquem cos i taula
    echo '</tbody>';
    echo '</table>';
    $result->close();
} else {

    //ja s'havia passat llista
    //construim capçalera de la taula
    //abans que res mirem si és una guàrdia
    $query3 = "select ga28_is_guardia as isguardia,ga28_prof_substituit as codi,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as nom"
            . " from ga04_professors,ga28_cont_presencia_cap,ga17_professors_curs"
            . " where ga28_codi_curs=ga17_codi_curs and ga28_prof_substituit=ga17_codi_professor and ga17_codi_professor=ga04_codi_prof and"
            . " ga28_codi_curs=" . $_SESSION['curs_actual'] . " and ga28_professor=" . $_SESSION['prof_actual'] . " and ga28_dia='" . $diaFormSql . "' and ga28_hora='" . $hora . "'";

    //executem la consulta
    $result3 = $conn->query($query3);

    if (!$result3)
        die($conn->error);

    $row = $result3->fetch_assoc();

    //obtenim el registre de la capçalera

    if ($row['isguardia'] == "1") {
        //si és una guàrdia
        $codi_prof_subs = $row['codi'];
        $nom_prof_subs = $row['nom'];
    } else {
        $codi_prof_subs = "";
        $nom_prof_subs = "";
    }
    $result3->close();


    echo '<br>';
    echo '<table id="taulaAssist" class="table" data-nou="0" data-codi-prof-subs="' . $codi_prof_subs . '" data-nom-prof-subs="' . $nom_prof_subs . '">';
    echo '<thead>';
    echo '<tr>';
    echo '<th><form class="form-inline"><input type="checkbox" value="" id="checkMarcaDesmarca" onclick="seleccionaTot();
    "><button type="button" class="btn btn-warning form-control" onclick="esborraAlumnes()"><span class="glyphicon glyphicon-trash"></span></button></form></th>';
    echo '<th>Nivell</th>';
    echo'<th>Grup</th>';
    echo '<th>Alumne</th>';
    echo '<th>Pres</th>';
    echo '<th>Abs</th>';
    echo '<th>Ret</th>';
    echo'<th><center>CCC</center></th>';
    echo '<th><span class="glyphicon glyphicon-envelope"></span></th>';
    echo '</tr>';
    echo '</thead>';
    //ja s'havia passat llista

    /* $query2 = "select ga15_alumne as codi, ga07_descripcio_grup as descrgrup, ga06_descripcio_nivell as descrnivell, concat(ga11_cognom1, ' ', ga11_cognom2, ', ', ga11_nom) as alumne,ga15_check_comunica as checkcomunica,"
      . "ga15_check_present, ga15_check_absent, ga15_check_retard, ga15_data_hora_darrera_mod, "
      . "ga15_tipus_falta as tipusfalta, ga15_estat as estatfalta, ga15_motiu as motiufalta, ga15_num_falta as numfalta, (select ga22_nom_falta from ga22_tipus_falta where ga22_codi_falta = ga15_tipus_falta) as textfalta,"
      . "ga15_just_tutor as justtutor,ga15_just_resp as justresp "
      . "from ga15_cont_presencia, ga11_alumnes, ga07_grup, ga12_alumnes_curs, ga06_nivell"
      . " where ga15_codi_curs = " . $_SESSION['curs_actual'] . " and ga15_codi_professor = " . $_SESSION['prof_actual'] . " and ga15_dia = '" . $diaFormSql . "' and ga15_hora_inici = '" . $hora . "' and "
      . "ga15_alumne = ga11_id_alumne and ga15_alumne = ga12_id_alumne and ga12_codi_grup = ga07_codi_grup and ga06_codi_nivell = ga12_codi_nivell"
      . " order by alumne"; */

    $query2 = "select ga15_alumne as codi, ga07_descripcio_grup as descrgrup, ga06_descripcio_nivell as descrnivell, concat(ga11_cognom1, ' ', ga11_cognom2, ', ', ga11_nom) as alumne,"
            . "ga15_check_comunica as checkcomunica,ga15_check_present, ga15_check_absent, ga15_check_retard, ga15_data_hora_darrera_mod,"
            . "(select ga31_tipus_falta from ga31_faltes_ordre where ga15_codi_curs = " . $_SESSION['curs_actual'] . " and ga31_alumne=codi and ga31_codi_professor=" . $_SESSION['prof_actual'] . " and ga31_dia='" . $diaFormSql . "' and ga31_hora_inici='" . $hora . "' and ga31_es_sessio=1) as tipusfalta,"
            . "(select ga31_estat from ga31_faltes_ordre where ga15_codi_curs = " . $_SESSION['curs_actual'] . " and ga31_alumne=codi and ga31_codi_professor=" . $_SESSION['prof_actual'] . " and ga31_dia='" . $diaFormSql . "' and ga31_hora_inici='" . $hora . "' and ga31_es_sessio=1) as estatfalta,"
            . "(select ga31_motiu from ga31_faltes_ordre where ga15_codi_curs = " . $_SESSION['curs_actual'] . " and ga31_alumne=codi and ga31_codi_professor=" . $_SESSION['prof_actual'] . " and ga31_dia='" . $diaFormSql . "' and ga31_hora_inici='" . $hora . "' and ga31_es_sessio=1) as motiufalta,"
            . "(select ga31_id from ga31_faltes_ordre where ga15_codi_curs = " . $_SESSION['curs_actual'] . " and ga31_alumne=codi and ga31_codi_professor=" . $_SESSION['prof_actual'] . " and ga31_dia='" . $diaFormSql . "' and ga31_hora_inici='" . $hora . "' and ga31_es_sessio=1) as numfalta,"
            . "(select ga22_nom_falta from ga22_tipus_falta where ga22_codi_falta = tipusfalta) as textfalta,"
            . "(select ga31_just_tutor from ga31_faltes_ordre where ga15_codi_curs = " . $_SESSION['curs_actual'] . " and ga31_alumne=codi and ga31_codi_professor=" . $_SESSION['prof_actual'] . " and ga31_dia='" . $diaFormSql . "' and ga31_hora_inici='" . $hora . "' and ga31_es_sessio=1) as justtutor,"
            . "(select ga31_just_resp from ga31_faltes_ordre where ga15_codi_curs = " . $_SESSION['curs_actual'] . " and ga31_alumne=codi and ga31_codi_professor=" . $_SESSION['prof_actual'] . " and ga31_dia='" . $diaFormSql . "' and ga31_hora_inici='" . $hora . "' and ga31_es_sessio=1) as justresp"
            . " from ga15_cont_presencia, ga11_alumnes, ga07_grup, ga12_alumnes_curs, ga06_nivell"
            . " where ga15_codi_curs = " . $_SESSION['curs_actual'] . " and ga15_codi_professor = " . $_SESSION['prof_actual'] . " and ga15_dia = '" . $diaFormSql . "' and ga15_hora_inici = '" . $hora . " ' and ga15_codi_curs=ga12_codi_curs and ga15_alumne = ga11_id_alumne and"
            . " ga15_alumne = ga12_id_alumne and ga12_codi_grup = ga07_codi_grup and ga06_codi_nivell = ga12_codi_nivell order by alumne";

    //echo $query2;
    //executem la consulta
    $result2 = $conn->query($query2);


    if (!$result2)
        die($conn->error);

    $cont = 0;
    if ($result2->num_rows > 0) {

        while ($row = $result2->fetch_assoc()) {
            if ($cont === 0) {

                echo '<tbody id="cosTaulaAssist" data-modifi="' . $row['ga15_data_hora_darrera_mod'] . '">';
            }
            $colorbuto = "";
            if ($row['tipusfalta'] != "") {
                //'alumne té falta d'ordre
                $colorbuto = "btn-danger";
            } else {
                $colorbuto = "";
            }

            $cont++;
            //construim el cos de la taula
            echo '<tr>';
            echo '<td><input type="checkbox" value="" class="checkEsborrar"></td>';
            echo '<td>' . $row['descrnivell'] . '</td>';
            echo '<td>' . $row['descrgrup'] . '</td>';

            //en aquesta cella posarem el codi de l'alumne i les dades de la possible falta d'ordre
            echo '<td id="al' . $row['codi'] . '" data-tipus="' . $row['tipusfalta'] . '" data-estat="' . $row['estatfalta'] . '" data-motiu="' . $row['motiufalta'] . '" data-num="' . $row['numfalta'] . '" data-textfalta="' . $row['textfalta'] . '" data-avisresponsables="' . $row['justresp'] . '" data-avistutor="' . $row['justtutor'] . '"><a data-toggle="modal" data-target="#imatgeAlumneModalSessio" onclick="mostraImatgeSessio(this)">' . $row['alumne'] . '</a></td>';



            if ($row['ga15_check_present'] === '1') {
                echo '<td><input type="checkbox" value="" checked class="checkAssist" onchange="comprovaCheck(this)"></td>';
                echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
                echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
            } elseif ($row['ga15_check_absent'] === '1') {
                echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
                echo '<td><input type="checkbox" value="" checked class="checkAssist" onchange="comprovaCheck(this)"></td>';
                echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
            } else {
                echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
                echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
                echo '<td><input type="checkbox" value="" checked class="checkAssist" onchange="comprovaCheck(this)"></td>';
            }

            echo '<td><button type="button" class="btn form-control ' . $colorbuto . '" data-toggle="modal" data-target="#faltesModalForm" onclick="carregaFaltes(this)">';
            echo '<span class="glyphicon glyphicon-pencil"></span>CCC</button></td>';

            if ($row['checkcomunica'] == '1') {
                echo '<td><input type="checkbox" value="" checked disabled></td>';
            } else {
                echo '<td><input type="checkbox" value="" disabled></td>';
            }
            echo '</tr>';
        }
    }

//tanquem cos i taula
    echo '</tbody>';
    echo '</table>';
    $result2->close();
}

$result1->close();
$conn->close();
