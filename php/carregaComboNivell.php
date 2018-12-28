<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

$query="SELECT ga06_codi_nivell as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell";

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

echo '<select id="nivellCombo">';


if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['codi'] . '">'.$row['descripcio'].'</option>';
    }
}

echo '</select>';


$result->close();
$conn->close();
