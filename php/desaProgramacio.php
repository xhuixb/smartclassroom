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


//esborrem el missatge previ
$query = "delete from ga44_programacio_sessio where ga44_codi_curs=" . $_SESSION['curs_actual'] . " and ga44_professor=" . $_SESSION['prof_actual']
        . " and ga44_dia='" . $dia . "' and ga44_hora='" . $hora . "'";

echo $query;

//executem la query
$conn->query($query);


if ($textProgramacioNoApostrof !== '') {
//creem la nova programacio si és que n'hi ha
    $query = "insert into ga44_programacio_sessio values(" . $_SESSION['curs_actual'] . "," . $_SESSION['prof_actual'] . ",'" . $dia . "','" . $hora . "','" . $textProgramacioNoApostrof . "')";

//executem la query
    $conn->query($query);
    //hi ha programacio
    echo '1';
}else{
    //no hi ha programacio
    echo '0';
}
//tanquem la connexió
$conn->close();
