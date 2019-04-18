<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();
//$_SESSION['curs_actual'] = 3;
//$_SESSION['prof_actual'] = 0;
$textProgramacio = $_POST['textProgramacio'];
$dia = $_POST['dia'];
$hora = $_POST['hora'];

$textProgramacioNoApostrof = str_replace("'", "''", $textProgramacio);

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//mirem si hi havia missatge previ
$query = "select count(*) as conta from ga44_programacio_sessio where ga44_codi_curs=" . $_SESSION['curs_actual'] . " and ga44_professor=" . $_SESSION['prof_actual'] . " and ga44_dia='" . $dia . "' and ga44_hora='" . $hora . "'";

$result = $conn->query($query);

if (!$result)
    die($conn->error);
$row = $result->fetch_assoc();

$conta = $row['conta'];


if ($conta == '0') {
    //creem la nova programacio
    $query = "insert into ga44_programacio_sessio values(" . $_SESSION['curs_actual'] . "," . $_SESSION['prof_actual'] . ",'" . $dia . "','" . $hora . "','" . $textProgramacioNoApostrof . "')";

    //executem la query
    $result = $conn->query($query);
    if (!$result)
        die($conn->error);
} else {
    //modifiquem la nova programació
    $query = "update ga44_programacio_sessio set ga44_text='" . $textProgramacioNoApostrof."'"
            . " where ga44_codi_curs=" . $_SESSION['curs_actual'] . " and ga44_professor=" . $_SESSION['prof_actual'] . " and ga44_dia='" . $dia . "' and ga44_hora='" . $hora . "'";
    echo $query;
    $result = $conn->query($query);
    if (!$result)
        die($conn->error);
}
echo '1';

//tanquem la connexió
$conn->close();
