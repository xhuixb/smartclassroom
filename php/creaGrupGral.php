<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

//rebem les dades

session_start();

$nivell = $_POST['nivell'];
$grup = $_POST['grup'];
$profe = $_POST['profe'];
$nomGrup = str_replace("'","''",$_POST['nomGrup']);

//vaig a buscar les sessions que he de tractar
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//creo la capçalera
$query = "insert into ga23_grups_profes_cap (ga23_curs,ga23_codi_professor,ga23_codi_nivell,ga23_nom_grup)"
        . " values (" . $_SESSION['curs_actual'] . "," . $profe . "," . $nivell . ",'" . $nomGrup . "')";



//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);

//creem el detall a partir dels alumnes d'un grup classe
$query = "insert into ga24_grups_profes_det (ga24_codi_alumne,ga24_codi_grup)"
        . "select ga12_id_alumne,(select max(ga23_codi_grup) from ga23_grups_profes_cap where ga23_codi_professor=".$profe.") from ga12_alumnes_curs where ga12_codi_curs=".$_SESSION['curs_actual']." and ga12_codi_nivell=".$nivell." and ga12_codi_grup=".$grup;


//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);

$conn->close();
