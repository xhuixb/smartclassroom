<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../classes/Databases.php';

$conn = new mysqli(Databases1::$host, Databases1::$user, Databases1::$password, Databases1::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

$query="SELECT bi08_id as codiidioma, bi08_nom as nomidioma FROM bi08_idiomes";

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

echo '<select id="idiomaCombo">';
echo '<option value="0">tots</option>';


if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['codiidioma'] . '">'.$row['nomidioma'].'</option>';
    }
}

echo '</select>';


$result->close();
$conn->close();
