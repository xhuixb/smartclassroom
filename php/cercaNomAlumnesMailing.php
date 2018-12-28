<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió
//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


$alumnesArray = $_POST['alumnesArray'];

$clausulaWhere = "(";

for ($i = 0; $i < count($alumnesArray); $i++) {
    $clausulaWhere .= $alumnesArray[$i];
    if ($i != count($alumnesArray) - 1)
        $clausulaWhere .= ",";
}

$clausulaWhere .=')';


//composem la query
$query="SELECT concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as nomAlumne FROM ga11_alumnes where ga11_id_alumne in ".$clausulaWhere;

//executem la query

$result = $conn->query($query);


if (!$result)
    die($conn->error);
$valorTextArea="";
if ($result->num_rows > 0) {
    $cont = 0;
     while ($row = $result->fetch_assoc()) {
         $valorTextArea.=$row['nomAlumne'];
         $valorTextArea.="\n";
     }
}
echo '<h4>Alumnes inclosos en el mailing</h4>';
echo '<textarea style="height: 400px;" class="form-control" readonly>';
echo $valorTextArea;
echo '</textarea>';

//tanquem
$result->close();
$conn->close();