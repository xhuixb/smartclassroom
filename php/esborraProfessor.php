<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$codiProf = $_POST['codiProf'];

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


//l'esborrem de la taula 17
$query = "delete from ga17_professors_curs where ga17_codi_curs=" . $_SESSION['curs_actual'] . " and ga17_codi_professor=" . $codiProf;

$result = $conn->query($query);

if ($result === true) {
    //s'ha pogut esborrar
    //Esborrem els registres d'activitat
    $query="delete from ga42_registre_activitat where ga42_professor=". $codiProf;
    $conn->query($query);
    //ara esborrem de la taula 4
    $query = "delete from ga04_professors where ga04_codi_prof=" . $codiProf;
    $conn->query($query);

    echo '0';
} else {
    echo '1';
}

$conn->close();
