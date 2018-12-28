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

$codisMailings = $_POST['codisMailings'];

$clausulaWhere = "(";

for ($i = 0; $i < count($codisMailings); $i++) {
    $clausulaWhere .= $codisMailings[$i];
    if ($i != count($codisMailings) - 1)
        $clausulaWhere .= ",";
}

$clausulaWhere .= ')';

$query = "delete from ga30_comunicacions where ga30_id in " . $clausulaWhere;

echo $query;

$conn->query($query);


$conn->close();

//esborrem els fitxers adjunts

for ($i = 0; $i < count($codisMailings); $i++) {
    $files = glob('../pdf/mailings/m'.$codisMailings[$i].'_*.pdf'); // get all file names
    foreach ($files as $file) { // iterate files
        if (is_file($file))
            unlink($file); // delete file
    }
}