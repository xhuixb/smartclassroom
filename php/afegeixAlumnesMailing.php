<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió
//establim la connexió
session_start();

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$nivell = $_POST['nivell'];
$grup = $_POST['grup'];

if ($nivell == '' && $grup == '') {
    $whereAlumnes = "";
} elseif ($nivell != '' && $grup == '') {
    $whereAlumnes = " and ga12_codi_nivell=" . $nivell;
} elseif ($nivell == '' && $grup != '') {
    $whereAlumnes = " and ga12_codi_grup=" . $grup;
} else {

    $whereAlumnes = " and ga12_codi_nivell=" . $nivell . " and " . " ga12_codi_grup=" . $grup;
}

$query = "select ga12_id_alumne as codialumne,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup, ga11_mail1 as mail1,ga11_mail2 as mail2"
        . " from ga11_alumnes,ga12_alumnes_curs,ga06_nivell,ga07_grup"
        . " where ga12_codi_curs=".$_SESSION['curs_actual']." and ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup" . $whereAlumnes
        . " order by alumne";



//executem la query
$result = $conn->query($query);

if (!$result)
    die($conn->error);


//construim capçalera de la taula
echo '<br>';
echo '<table id="taulaAlumnesMailing" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-3">Alumne</th>';
echo '<th class="col-sm-1">Nivell</th>';
echo '<th class="col-sm-1">Grup</th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {
    $cont = 0;

    echo '<tbody id="costaulaAlumnesMailing">';
    while ($row = $result->fetch_assoc()) {
        //mirem si tenen algun correu electrònic
        if($row['mail1']=='' && $row['mail2']==''){
            $color='danger';
            
        }else{
            $color='success';
        }
        
        
        echo '<tr>';
        echo '<td class="col-sm-3 '.$color.'" data-codi-alumne="'.$row['codialumne'].'">' . $row['alumne'] . '</td>';
        echo '<td class="col-sm-1 '.$color.'">' . $row['nivell'] . '</td>';
        echo '<td class="col-sm-1 '.$color.'">' . $row['grup'] . '</td>';

        echo '</tr>';
    }
    echo '</tbody>';
   
}

 echo '</table>';
 
 $result->close();
 
 $conn->close();