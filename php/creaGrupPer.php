<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

//rebem les dades

session_start();

$grupOrigen = $_POST['grupOrigen'];
$profe = $_POST['profe'];
$nomGrup = str_replace("'", "''", $_POST['nomGrup']);

//vaig a buscar les sessions que he de tractar
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//creo la capçalera
$query = "insert into ga23_grups_profes_cap (ga23_curs,ga23_codi_professor,ga23_codi_nivell,ga23_nom_grup)"
        . " select " . $_SESSION['curs_actual'] . "," . $profe . ",(select ga23_codi_nivell from ga23_grups_profes_cap where ga23_codi_grup=" . $grupOrigen . "),'" . $nomGrup . "'";


//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);


//creem el detall a partir dels alumnes d'un grup classe
$query = "insert into ga24_grups_profes_det (ga24_codi_alumne,ga24_codi_grup) "
        . " select ga24_codi_alumne,(select max(ga23_codi_grup) from ga23_grups_profes_cap where ga23_codi_professor=" . $profe . ")"
        . " from ga24_grups_profes_det where ga24_codi_grup=" . $grupOrigen;


echo $query;
//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);

$conn->close();
