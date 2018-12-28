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

$nivell = $_POST['nivell'];
$nivellText = $_POST['nivellText'];
$grup = $_POST['grup'];
$grupText = $_POST['grupText'];
$alumnes = $_POST['alumnes'];


//carrreguem els alumnes d'aquest grup
$query = "select ga12_id_alumne as codi,concat(ga11_cognom1, ' ' ,ga11_cognom2 , ', ' , ga11_nom) as alumne,ga11_check_comunica as comunica "
        . "from ga12_alumnes_curs ,ga11_alumnes "
        . "where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_codi_nivell=" . $nivell . " and ga12_codi_grup=" . $grup . " and ga12_id_alumne=ga11_id_alumne "
        . "order by alumne";



$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);


//construim capçalera de la taula
//echo '<p>Conjunto de caracteres actual:</p>';

echo '<table id="taulaAssistGrup" class="table">';
echo '<thead>';
echo '<tr>';
echo '<th><button type="button" class="btn btn-warning form-control" onclick="passaAlumnes(this);"><span class="glyphicon glyphicon-arrow-left"></span></button></th>';
echo'<th>Nivell</th>';
echo'<th>Grup</th>';
echo '<th>Alumne</th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {
    echo '<tbody id="cosTaulaAssistGrup">';
    while ($row = $result->fetch_assoc()) {

        //onstruim el cos de la taula
        //comprovem si l'alumne ha està a la taula de l'esquerra
        if (array_search($row['codi'], $alumnes, true) === false) {
            echo '<tr data-comunica="' . $row['comunica'] . '">';
            echo '<td><input type="checkbox" value="" class="checkEsborrar"></td>';
            echo '<td>' . $nivellText . '</td>';
            echo '<td>' . $grupText . '</td>';
            echo '<td id="al' . $row['codi'] . '" data-tipus="" data-estat="" data-motiu="" data-num="" data-textfalta=""><a data-toggle="modal" data-target="#imatgeAlumneModalSessio" onclick="mostraImatgeSessio(this)">' . $row['alumne'] . '</a></td>';
            echo '</tr>';
        }
    }
}

//tanquem cos i taula
echo '</tbody>';
echo '</table>';



$result->close();

$conn->close();

