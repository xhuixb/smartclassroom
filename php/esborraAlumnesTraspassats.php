<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió
//rebem les dades

session_start();

$curs = $_POST['curs'];
$alumnes = $_POST['alumnes'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query="delete from ga12_alumnes_curs where ga12_codi_curs=".$curs." and ga12_id_alumne in (".join(',', $alumnes).")";

$resposta = $conn->query($query);

if($resposta===true){
    echo '0';
}else{
    echo '1';
}

