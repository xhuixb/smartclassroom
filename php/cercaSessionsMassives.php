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

$nivell = $_POST["nivell"];
$grup = $_POST['grup'];
$dataSessio = $_POST['dataSessio'];
$alumnes = [];
$estats = [];
$alumnesEstat = $_POST['alumnes'];

//aconseguim els estats i ela slumnes que venien junts en l'array alumnesEstats
for ($i = 0; $i < count($alumnesEstat); $i++) {
    $alumnesEstatElement = explode('<#>', $alumnesEstat[$i]);
    $alumnes[$i] = $alumnesEstatElement[0];
    $estats[$i] = $alumnesEstatElement[1];
}



$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");



for ($i = 0; $i < count($alumnes); $i++) {
    // echo $alumnes[$i].'-'.$estat[$alumnes[$i]].'-';
    $whereAlumnes = '(' . join(',', $alumnes) . ')';
    //echo $whereAlumnes;
}


$query = "select ga26_codi_professor as codiprof,"
        . "ga26_dia_setmana as dia,ga26_hora_inici as hora,ga26_nivell as codinivell,ga26_codi_assignatura as codiassig,ga26_grup as codigrup,ga26_codi_aula as codiaula,"
        . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=codiprof) as nomprof,"
        . "(select ga06_descripcio_nivell from ga06_nivell where ga06_codi_nivell=codinivell) as nomnivell,"
        . "(select ga23_nom_grup from ga23_grups_profes_cap where ga23_codi_grup=codigrup) as nomgrup,"
        . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=codiassig) as nomassig,"
        . "(select ga01_descripcio_aula from ga01_aula where ga01_codi_aula=codiaula) as nomaula,"
        . "ga26_tipus_grup,"
        . "(select ga28_professor from ga28_cont_presencia_cap where ga28_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga28_professor=codiprof and ga28_dia='" . $dataSessio . "' and ga28_hora=hora) as proftit"
        . " from ga26_horaris_docents,ga23_grups_profes_cap,ga24_grups_profes_det"
        . " where ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_tipus_grup=1 and ga26_grup=ga23_codi_grup and ga26_grup=ga24_codi_grup and ga24_codi_alumne in " . $whereAlumnes . " and ga26_dia_setmana=DAYOFWEEK('" . $dataSessio . "')-1 and ga26_is_lectiva=1 "
        . " group by dia,hora,codiprof order by hora,codiassig";

//echo $query;
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
        . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=codiprof) as nomprof,"
        . "(select ga06_descripcio_nivell from ga06_nivell where ga06_codi_nivell=codinivell) as nomnivell,"
        . "(select ga07_descripcio_grup from ga07_grup where ga07_codi_grup=codigrup) as nomgrupgral,"
        . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=codiassig) as nomassig,"
        . "(select ga01_descripcio_aula from ga01_aula where ga01_codi_aula=codiaula) as nomaula,"
        . "ga26_tipus_grup,"
        . "(select ga28_professor from ga28_cont_presencia_cap where ga28_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga28_professor=codiprof and ga28_dia='" . $dataSessio . "' and ga28_hora=hora) as proftit"
        . " from ga26_horaris_docents,ga12_alumnes_curs"
        . " where ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_tipus_grup=0 and ga26_grup=ga12_codi_grup and ga26_nivell=ga12_codi_nivell and ga12_id_alumne in " . $whereAlumnes . " and ga26_dia_setmana=DAYOFWEEK('" . $dataSessio . "')-1 and ga26_is_lectiva=1 "
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
if (isset($horarisArray)) {
    ksort($horarisArray);
}


//construim la taula

echo '<table id="taulaSessionsMassives" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-1"><input type="checkbox" value="" id="checkEsborraSessions" onclick="seleccionaSessions();"></th>';
echo '<th class="col-sm-2">Hora</th>';
echo '<th class="col-sm-3">Assignatura</th>';
echo '<th class="col-sm-2">Grup</th>';
echo '<th class="col-sm-3">Professor</th>';
echo '<th class="col-sm-1">Estat</th>';
echo '</tr>';
echo '</thead>';

if (isset($horarisArray)) {

    if (count($horarisArray) > 0) {
        echo '<tbody id="costaulaSessionsMassives">';
    }

    foreach ($horarisArray as $tramHorari) {

        $contingutHorari = explode('<#>', $tramHorari);
        if ($contingutHorari[13] != '') {

            $estatColor = 'class="btn-success"';
            $estatText='Passada';
            $estatTria="";
        } else {

            $estatColor = 'class="btn-danger"';
            $estatText='Pendent';
            $estatTria="disabled";
        }

        if ($contingutHorari[13] == '1') {
            //és un grup personal
            $nomGrupSessio = $contingutHorari[10];
        } else {
            $nomGrupSessio = $contingutHorari[9];
        }

        echo '<tr>';
        echo '<td class="col-sm-1"><input type="checkbox" value="" class="sessionsSeleccionades" '.$estatTria.'></td>';
        echo '<td class="col-sm-2">' . $contingutHorari[2] . '</td>';
        echo '<td class="col-sm-3">' . $contingutHorari[10] . '</td>';

        echo '<td class="col-sm-2">' . $nomGrupSessio . '</td>';
        echo '<td class="col-sm-3" data-codiprof="'.$contingutHorari[0].'">' . $contingutHorari[7] . '</td>';
        echo '<td ' . $estatColor . '>'.$estatText.'</td>';
        echo '</tr>';
    }
    if (count($horarisArray) > 0) {
        echo '</tbody>';
    }
}
echo '</table>';
