<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$dia = $_POST['dia'];
$hora = $_POST['hora'];

$directori = "../uploads/programacions/";


//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


//esborrem els possibles adjunts
$query = "select ga47_nom_intern as nomintern from ga47_adjunts_programador"
        . " where ga47_curs=" . $_SESSION['curs_actual'] . " and ga47_professor=" . $_SESSION['prof_actual'] . " and ga47_dia='" . $dia . "' and ga47_hora='" . $hora . "'";


//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    //hi ha adjunts
    while ($row = $result->fetch_assoc()) {
        //esborrem el fitxer associat
        $fitxer = $directori . $row['nomintern'];
        unlink($fitxer);
    }
}

//finalment esborrem els regisgtres de la base de dades

$query = "delete from ga47_adjunts_programador"
        . " where ga47_curs=" . $_SESSION['curs_actual'] . " and ga47_professor=" . $_SESSION['prof_actual'] . " and ga47_dia='" . $dia . "' and ga47_hora='" . $hora . "'";

//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);

//esborrem el missatge previ
$query = "delete from ga44_programacio_sessio where ga44_codi_curs=" . $_SESSION['curs_actual'] . " and ga44_professor=" . $_SESSION['prof_actual']
        . " and ga44_dia='" . $dia . "' and ga44_hora='" . $hora . "'";

//executem la query
$result = $conn->query($query);
if (!$result)
    die($conn->error);

//tanquem la connexió
$conn->close();
