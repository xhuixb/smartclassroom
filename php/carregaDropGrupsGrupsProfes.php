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

$div = $_POST['div'];
$caption = $_POST['caption'];

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

$query = "SELECT ga07_codi_grup as codi, ga07_descripcio_grup as descripcio FROM ga07_grup";


//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

echo '<button data-tipusgrup="" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="butDrop' . $div . '">' . $caption . '<span class="caret"></span></button>';
echo '<ul class="dropdown-menu" id="drop' . $div . '" style="height: 250px; overflow: auto">';

if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {

        echo '<li><a data-tipusgrup="0" data-val="' . $row['codi'] . '" onclick="mostra' . $div . '(this);">' . $row['descripcio'] . '</a></li>';
    }
}

$query1 = "select ga23_codi_grup as codi,ga23_nom_grup as descripcio,ga23_codi_nivell as nivell from ga23_grups_profes_cap where ga23_curs=" . $_SESSION['curs_actual'] .
        " and ga23_codi_professor=" . $_SESSION['prof_actual'];


//executem la consulta
$result1 = $conn->query($query1);


if (!$result1)
    die($conn->error);

if ($result1->num_rows > 0) {
    // output data of each row
    while ($row1 = $result1->fetch_assoc()) {
        
        echo '<li hidden><a data-tipusgrup="1" data-nivell="'.$row1['nivell'].'" data-val="' . $row1['codi'] . '" onclick="mostra' . $div . '(this);">' . $row1['descripcio'] . '</a></li>';
    }
}


echo '</ul>';

$result->close();
$result1->close();
$conn->close();
