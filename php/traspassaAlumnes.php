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

$nivellDesti = $_POST["nivellDesti"];
$cursDesti = $_POST['cursDesti'];
$alumnesTraspas = json_decode($_POST['alumnesTraspas']);
$esborraExistents=$_POST['esborraExistents'];


//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

if($esborraExistents==='1'){
    //cal esborrar les dades que hi ha del nivell i curs
    $query="delete from ga12_alumnes_curs where ga12_codi_curs=".$cursDesti." and ga12_codi_nivell=".$nivellDesti;
    $conn->query($query);
}



$query = "insert into ga12_alumnes_curs values ";


for ($i = 0; $i < count($alumnesTraspas); $i++) {
    if ($i !== count($alumnesTraspas) - 1) {
        $query.="(".$cursDesti.",".$alumnesTraspas[$i]->alumne.",".$nivellDesti.",".$alumnesTraspas[$i]->grup.",'','','0','0','0'),";
        
    } else {
        $query.="(".$cursDesti.",".$alumnesTraspas[$i]->alumne.",".$nivellDesti.",".$alumnesTraspas[$i]->grup.",'','','0','0','0')";
    }
}

$conn->query($query);

$conn->close();
