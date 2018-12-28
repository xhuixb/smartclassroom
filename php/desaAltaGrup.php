<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió
echo 'ximplet';
session_start();
//$_SESSION['curs_actual'] = 3;
//$_SESSION['prof_actual'] = 0;
$nivell=$_POST['nivell'];
$nomGrup=$_POST['nomGrup'];



//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

$query = "insert into ga23_grups_profes_cap (ga23_curs,ga23_codi_professor,ga23_codi_nivell,ga23_nom_grup) "
        . "values (".$_SESSION['curs_actual'].",".$_SESSION['prof_actual'].",".$nivell.",'".$nomGrup."')";


$result = $conn->query($query);

$conn->close();