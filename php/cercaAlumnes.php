<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

//recuperem les dades del formulari
$nom = $_POST['nom'];
$cognom1 = $_POST['cognom1'];
$cognom2 = $_POST['cognom2'];
$mail = $_POST['mail'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "select ga11_id_alumne as codialumne,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga11_mail1 as mail1,ga11_mail2 as mail2,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,"
        ."(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors,ga29_tutors_curs where ga29_curs=". $_SESSION['curs_actual'] . " and ga29_nivell=ga06_codi_nivell and ga29_grup=ga07_codi_grup and ga29_tutor=ga04_codi_prof) as tutor"
        . " from ga11_alumnes,ga12_alumnes_curs,ga06_nivell,ga07_grup"
        . " where ga11_id_alumne=ga12_id_alumne and ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup"
        . " and ga11_nom like '%" . $nom . "%' and ga11_cognom1  like '%" . $cognom1 . "%' and ga11_cognom2 like '%" . $cognom2 . "%' and (ga11_mail1 like '%" . $mail . "%' or ga11_mail2 like '%" . $mail . "%')"
        . " order by alumne";


$result = $conn->query($query);


if (!$result)
    die($conn->error);


//construim capçalera de la taula
echo '<br>';
echo '<table id="taulaCercaAlumnes" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-3">Alumne</th>';
echo '<th class="col-sm-1">Nivell</th>';
echo '<th class="col-sm-1">Grup</th>';
echo '<th class="col-sm-3">Tutor</th>';
echo '<th class="col-sm-2">Mail1</th>';
echo '<th class="col-sm-2">Mail2</th>';
echo '</tr>';
echo '</thead>';


if ($result->num_rows > 0) {
    $cont = 0;

    echo '<tbody id="cosTaulaCercaAlumnes">';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td class="col-sm-3" data-codi-alumne="'.$row['codialumne'].'"><a onclick="mostraFitxaAlumne(this)">' . $row['alumne'] . '<a></td>';
        echo '<td class="col-sm-1">' . $row['nivell'] . '</td>';
        echo '<td class="col-sm-1">' . $row['grup'] . '</td>';
        echo '<td class="col-sm-3">' . $row['tutor'] . '</td>';
        echo '<td class="col-sm-2">' . $row['mail1'] . '</td>';
        echo '<td class="col-sm-2">' . $row['mail2'] . '</td>';
        echo '</tr>';
    }
}

//tanquem cos i taula
echo '</tbody>';
echo '</table>';

$result->close();
$conn->close();
