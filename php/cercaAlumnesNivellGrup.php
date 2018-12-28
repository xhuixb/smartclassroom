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
//$_SESSION['prof_actual'] = 0;
$nivell = $_POST['nivell'];
$grup = $_POST['grup'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "select ga12_id_alumne as codialumne, concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as nomcomplet,ga11_mail1 as mail1,ga11_mail2 as mail2,ga12_ao as ao,ga12_aa as aa,ga12_usee as usee,ga11_check_comunica as checkcomunica,"
        . " ga11_nom as nom,ga11_cognom1 as cognom1,ga11_cognom2 as cognom2,ga12_cotutor as cotutor"
        . " from ga11_alumnes,ga12_alumnes_curs"
        . " where ga11_id_alumne=ga12_id_alumne and ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_codi_nivell=" . $nivell . " and ga12_codi_grup=" . $grup
        . " order by nomcomplet";

$result = $conn->query($query);


if (!$result)
    die($conn->error);


//construim capçalera de la taula
echo '<br>';
echo '<table id="taulaGestioAlumnes" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-1"><left><span class="glyphicon glyphicon-trash"></span></left></th>';
echo '<th class="col-sm-2">Nom complet</th>';
echo '<th class="col-sm-2">Mail 1</th>';
echo '<th class="col-sm-2">Mail 2</th>';
echo '<th class="col-sm-1"><center><span class="glyphicon glyphicon-envelope"></span></center></th>';
echo '<th class="col-sm-1"><center>Edita</center></th>';
echo '<th class="col-sm-1">Canvi grup</th>';
echo '<th class="col-sm-1">Cotutor</th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {
    echo '<tbody id="costaulaJustifiAbsencies">';
    while ($row = $result->fetch_assoc()) {


        if ($row['checkcomunica'] == '1') {
            $comunicacheck = "checked";
        } else {
            $comunicacheck = "";
        }
        if($row['cotutor']==''){
            //no hi ha cotutor
            $colorTutor='';
        }else{
            $colorTutor="btn-info";
        }

        echo '<tr>';
        echo '<td class="col-sm-1"><input type="checkbox" value="" class="checkBaixa"></td>';
        echo '<td class="col-sm-2" data-codi-alumne="' . $row['codialumne'] . '" data-ao="' . $row['ao'] . '" data-aa="' . $row['aa'] . '" data-usee="' . $row['usee'] . '" data-nom="' . $row['nom'] . '" data-cognom1="' . $row['cognom1'] . '" data-cognom2="' . $row['cognom2'] . '">'
        . '<a data-toggle="modal" data-target="#imatgeAlumneModal" onclick="mostraImatge(this)"><span class="glyphicon glyphicon-camera"></span></a>'
        . '<a onclick="mostraFitxaAlumne(this)"> ' . $row['nomcomplet'] . '</a></td>';
        // echo '<td class="col-sm-1"><a data-toggle="modal" data-target="#imatgeAlumneModal" onclick="mostraImatge(this)"><span class="glyphicon glyphicon-camera"></span></a><a onclick="mostraFitxaAlumne(this)">'.$row['cognom1'] .'</a></td>';        
        echo '<td class="col-sm-2">' . $row['mail1'] . '</td>';
        echo '<td class="col-sm-2">' . $row['mail2'] . '</td>';
        echo '<td class="col-sm-1"><center><input id="checkComunica" type="checkbox" value="0" onclick="return false;" ' . $comunicacheck . '></center></td>';
        echo '<td class="col-sm-1"><button type="button" class="btn form-control" data-toggle="modal" data-target="#altaAlumnesModal" onclick="editaAlumne(this);">';
        echo '<span class="glyphicon glyphicon-pencil"></span></button></td>';
        echo '<td class="col-sm-1"><button type="button" class="btn form-control" data-toggle="modal" data-target="#canviGrupModalModal" onclick="canviGrupAlumne(this);">';
        echo '<span class="glyphicon glyphicon-retweet"></span></button></td>';
        echo '<td class="col-sm-1"><button type="button" class="btn form-control '.$colorTutor.'" data-toggle="modal" data-target="#cotutorModal" onclick="assignaCotutor(this);">';
        echo '<span class="glyphicon glyphicon-education"></span></button></td>';

        echo '</tr>';
    }
}

echo '</tbody>';
echo '</table>';

$result->close();
$conn->close();
