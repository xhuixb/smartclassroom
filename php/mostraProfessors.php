<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


$query = "select ga04_codi_prof as codiprof,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as nomcomplet, ga04_cognom1 as cognom1,ga04_cognom2 as cognom2,ga04_nom as nom,ga04_mail as mail,ga04_login as login,ga04_password as password,ga04_suspes as suspes,"
        . "(select ga42_data_inici from ga42_registre_activitat where ga42_professor=codiprof order by ga42_data_inici desc limit 1) as datainici,"
        . "(select ga42_data_fi from ga42_registre_activitat where ga42_professor=codiprof order by ga42_data_inici desc limit 1) as datafi"
        . " from ga04_professors,ga17_professors_curs"
        . " where ga17_codi_curs=" . $_SESSION['curs_actual'] . " and ga17_codi_professor=ga04_codi_prof order by nomcomplet";

//echo $query;
$result = $conn->query($query);


if (!$result)
    die($conn->error);


//construim capçalera de la taula
echo '<br>';
echo '<table id="taulaDocents" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-2">Professor</th>';
echo '<th class="col-sm-2">Mail</th>';
echo '<th class="col-sm-2">Login</th>';
echo '<th class="col-sm-1"><center>Edita</center></th>';
echo '<th class="col-sm-1"><center>Esborra</center></th>';
echo '<th class="col-sm-1"><center>Password</center></th>';
echo '<th class="col-sm-1"><center>Traspassa</center></th>';
echo '<th class="col-sm-1"><center>Estat</center></th>';
echo '<th class="col-sm-1"><center>Data Ini/Fi</center></th>';

echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {
    echo '<tbody id="costaulaDocents">';
    while ($row = $result->fetch_assoc()) {

        if ($row['suspes'] == '0') {
            $situacio = "Actiu";
            $situacioColor = "success";
            $dataRellevant = $row['datainici'];
        } else {
            $situacio = "Suspès";
            $situacioColor = "danger";
            $dataRellevant = $row['datafi'];
        }


        echo '<tr>';
        echo '<td class="col-sm-2" data-nom="' . $row['nom'] . '" data-codiprof="' . $row['codiprof'] . '" data-cognom1="' . $row['cognom1'] . '" data-cognom2="' . $row['cognom2'] . '" data-password="' . $row['password'] . '" data-suspes="' . $row['suspes'] . '">' . $row['nomcomplet'] . '</td>';
        echo '<td class="col-sm-2">' . $row['mail'] . '</td>';
        echo '<td class="col-sm-2">' . $row['login'] . '</td>';
        echo '<td class="col-sm-1"><button type="button" class="btn form-control btn-info" data-toggle="modal" data-target="#editaProfessor" onclick="gestioProfessor(1,this);" data-toggle="tooltip" title="Edita el professor">';
        echo '<span class="glyphicon glyphicon-pencil"></span>Edita</button></td>';
        echo '<td class="col-sm-1"><button type="button" class="btn form-control btn-danger" data-toggle="modal" onclick="esborraProfessor(this);" data-toggle="tooltip" title="Esborra el professor">';
        echo '<span class="glyphicon glyphicon-trash"></span>Esborra</button></td>';
        echo '<td class="col-sm-1"><button type="button" class="btn form-control btn-info" data-toggle="modal" onclick="restauraContrasenya(this);" data-toggle="tooltip" title="Restaura la contrasenya">';
        echo '<span class="glyphicon glyphicon-user"></span></button></td>';
        echo '<td class="col-sm-1"><button type="button" class="btn form-control btn-info" data-toggle="modal" data-target="#traspassaProfessor" onclick="traspassaProfessor(this);" data-toggle="tooltip" title="traspassa les dades a un altre professor">';
        echo '<span class="glyphicon glyphicon-retweet"></span></button></td>';
        echo '<td class="col-sm-1"><button type="button" class="btn form-control btn-' . $situacioColor . '" data-toggle="modal" data-target="#canviaEstatModal" onclick="situacioProfessor(this);" data-toggle="tooltip" title="canvia estat">' . $situacio;
        echo '<td class="col-sm-2">' . substr($dataRellevant, 8) . '/' . substr($dataRellevant, 5, 2) . '/' . substr($dataRellevant, 0, 4) . '</td>';
        echo '</button></td>';
        echo '</tr>';
    }
    echo '</tbody>';
}
echo '</table>';

$result->close();
$conn->close();

