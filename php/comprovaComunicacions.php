<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query="select count(*) as conta from ga30_comunicacions where ga30_estat='0'";

//executem la sentència sql

$result = $conn->query($query);


if (!$result)
    die($conn->error);

$row=$result->fetch_assoc();

if($_SESSION['admin']==='1' && $row['conta']!=='0'){
    echo '1';
}else{
    echo '0';
    
}

$result->close();
$conn->close();
