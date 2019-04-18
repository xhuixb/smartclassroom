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
//establim la connexió
$profe=$_POST['profe'];

if($profe==='0'){
    $profeCerca=$_SESSION['prof_actual'];
}else{
    $profeCerca=$profe;
}

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


$query = "SELECT ga23_codi_grup as codigrup,ga23_codi_nivell as codinivell,ga06_descripcio_nivell as nivell,ga23_nom_grup as nomgrup "
        . "FROM ga23_grups_profes_cap,ga06_nivell where"
        . " ga23_curs=" . $_SESSION['curs_actual'] . " and ga23_codi_professor=" . $profeCerca . " and ga23_codi_nivell=ga06_codi_nivell";

$result = $conn->query($query);


if (!$result)
    die($conn->error);


//construim capçalera de la taula
echo '<br>';
echo '<table id="taulaMeusGrups" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-2"><span class="glyphicon glyphicon-trash"></span></th>';
echo '<th class="col-sm-2">Codi</th>';
echo '<th class="col-sm-2">Nivell</th>';
echo '<th class="col-sm-4">Nom</th>';
echo '<th class="col-sm-1"><center><span class="glyphicon glyphicon-user"></span></center></th>';
echo '<th class="col-sm-1"><center><span class="glyphicon glyphicon-copy"></span></center></th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {
    echo '<tbody id="cosTaulaMeusGrups">';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td class="col-sm-2"><input type="checkbox" value=""></td>';
        echo '<td class="col-sm-2">' . $row['codigrup'] . '</td>';
        echo '<td class="col-sm-2" data-nivell="' . $row['codinivell'] . '">' . $row['nivell'] . '</td>';
        echo '<td class="col-sm-4"><div class="input-group">';
        echo '<input type="text" class="form-control prova" value="' . $row['nomgrup'] . '" readonly>';
        echo '<a href="#" class="input-group-addon noms-grups" onclick="editaNomGrup(this)" data-tooltip="tooltip" title="Modifica nom grup"><span class="glyphicon glyphicon-pencil"></span></a>';
        echo '</div>';
        echo '</td>';
        echo '<td class="col-sm-1"><button type="button" class="btn form-control" onclick="cercaAlumnesGrupProfe(this);" data-tooltip="tooltip" title="Membres grup personal">';
        echo '<span class="glyphicon glyphicon-user"></span></button></td>';
         echo '<td class="col-sm-1"><button type="button" class="btn form-control" onclick="copiaGrupProfe(this);" data-tooltip="tooltip" title="Duplica grup personal">';
        echo '<span class="glyphicon glyphicon-copy"></span></button></td>';
        echo '</tr>';
        
    }
}
//tanquem cos i taula
echo '</tbody>';
echo '</table>';
$result->close();
$conn->close();
