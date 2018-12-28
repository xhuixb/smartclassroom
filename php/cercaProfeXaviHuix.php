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

$query = "select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as profe from ga04_professors order by profe";

$result = $conn->query($query);


$resposta = array();

if (!$result)
    die($conn->error);

while ($row = $result->fetch_assoc()) {

    $resposta[] = $row;
}
header('Content-Type: application/json');

echo json_encode(array("profes" => $resposta), JSON_UNESCAPED_UNICODE);


$result->close();
$conn->close();
