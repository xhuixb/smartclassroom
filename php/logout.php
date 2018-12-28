<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//registrem el fi de sessio
require '../classes/Databases.php';

session_start();

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);

if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");


$query = "update ga25_log_usuaris set ga25_fi_sessio=now()";

$conn->query($query);


$_SESSION = Array();


session_destroy();
