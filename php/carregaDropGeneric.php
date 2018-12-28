<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió


$div = $_POST['div'];
$query = $_POST['query'];
$caption=$_POST['caption'];


$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

    echo '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="butDrop'.$div.'">'.$caption.'<span class="caret"></span></button>';
    echo '<ul class="dropdown-menu" id="drop'.$div.'" style="height: 250px; overflow: auto">';

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
       
        echo '<li><a data-val="'.$row['codi'].'" onclick="mostra'.$div.'(this);">'.$row['descripcio'].'</a></li>';
    }
}

echo '</ul>';


$result->close();
$conn->close();
