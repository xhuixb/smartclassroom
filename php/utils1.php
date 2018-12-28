<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// put your code here
//adjuntem les dades de la connexió
require '../classes/Databases.php';
//fem la connexió


$titol = $_POST["titol"];
$autor = $_POST['autor'];
$editorial = $_POST['editorial'];
$idioma = $_POST['idioma'];
$disponibilitat = $_POST['disponibilitat'];
$ordenacio = $_POST['ordenacio'];

$conn = new mysqli(Databases1::$host, Databases1::$user, Databases1::$password, Databases1::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

$criteriIdioma = '';

//idioma = 0 vol dir que no es filtra per idioma
if ($idioma != '0') {
    //seleccionem per idioma
    $criteriIdioma = "and bi01_idioma=" . $idioma;
}
//filtrem per disponibilitat
//0 tots 1 només disponibles 2 reservats

$filtreDisponibilitat = '';

switch ($disponibilitat) {
    case '1':
        $filtreDisponibilitat = " and bi01_estat='0' ";
        break;
    case '2':
        $filtreDisponibilitat = " and bi01_estat='1' ";
        break;
}

$criteriOrdenacio = "";
//criteri d'ordenacio
switch ($ordenacio) {
    case '0':
        //per títol
        $criteriOrdenacio = " order by bi01_titol";
        break;
    case '1':
        //per autor
        $criteriOrdenacio = " order by bi04_nom_complet";
        break;
    case '2':
        //per editorial
        $criteriOrdenacio = " order by bi05_nom";
        break;
}




//creem la consulta
$query = "select bi01_id as codi, "
        . "bi01_titol as titol, "
        . "(select bi04_nom_complet from bi04_autors where bi04_id=bi01_autor) as autor, "
        . "(select bi05_nom from bi05_editorials where bi05_id=bi01_editorial) as editorial, "
        . "(select  bi02_data_prestec from bi02_prestecs where bi02_codi_llibre=bi01_id and bi02_data_lliurament is null) as dataprestec, "
        . "(select  bi02_data_venciment from bi02_prestecs where bi02_codi_llibre=bi01_id and bi02_data_lliurament is null) as datavenciment, "
        . "(select bi08_nom from bi08_idiomes where bi08_id=bi01_idioma) as idioma, "
        . "bi01_estat as estat from bi01_llibres,bi04_autors,bi05_editorials where bi01_titol like '%" . $titol . "%'"
        . " and bi04_nom_complet like '%" . $autor . "%' "
        . "and bi05_nom like '%" . $editorial . "%' "
        . $criteriIdioma . $filtreDisponibilitat . " and bi04_id=bi01_autor and bi05_id=bi01_editorial" . $criteriOrdenacio;

//echo $query;
//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);


//obtenim les dades
$numFileres = $result->num_rows;
echo '<h2>Els llibres de color vermell estan prestats</h2>';
echo '<table id="taula1" class="table">';
echo '<tr>';
echo '<th>codi</th>';
echo '<th>titol</th>';
echo '<th>autor</th>';
echo '<th>editorial</th>';
echo '<th>Idioma</th>';
echo '<th>Data préstec</th>';
echo '<th>Data venciment</th>';
echo '</tr>';

/* for ($i = 0; $i < $numFileres; $i++) {
  $result->data_seek($i);
  if ($result->fetch_assoc()['estat'] == "1") {
  echo '<tr class="danger">';
  } else {
  echo '<tr>';
  }


  $result->data_seek($i);
  echo '<td>' . $result->fetch_assoc()['codi'] . '</td>';
  $result->data_seek($i);
  echo '<td>' . $result->fetch_assoc()['titol'] . '</td>';
  $result->data_seek($i);
  echo '<td>' . $result->fetch_assoc()['autor'] . '</td>';
  $result->data_seek($i);
  echo '<td>' . $result->fetch_assoc()['editorial'] . '</td>';
  $result->data_seek($i);
  echo '<td>' . $result->fetch_assoc()['idioma'] . '</td>';
  $result->data_seek($i);
  echo '<td>' . $result->fetch_assoc()['dataprestec'] . '</td>';
  $result->data_seek($i);
  echo '<td>' . $result->fetch_assoc()['datavenciment'] . '</td>';
  echo '</tr>';
  } */



if ($result->num_rows > 0) {


    while ($row = $result->fetch_assoc()) {
        if ($row['estat'] == "1") {
            echo '<tr class="danger">';
        } else {
            echo '<tr>';
        }

        
        echo '<td>' . $row['codi'] . '</td>';
       
        echo '<td>' . $row['titol'] . '</td>';
        
        echo '<td>' . $row['autor'] . '</td>';
        
        echo '<td>' . $row['editorial'] . '</td>';
        
        echo '<td>' . $row['idioma'] . '</td>';
        
        echo '<td>' . $row['dataprestec'] . '</td>';
        
        echo '<td>' . $row['datavenciment'] . '</td>';
        echo '</tr>';
    }
}
echo '</table>';

$result->close();
$conn->close();
