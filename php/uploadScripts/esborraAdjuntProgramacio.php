<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../../classes/Databases.php';

session_start();

$codi = $_POST['codi'];
$directori = "../../uploads/programacions/";


$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//primerament obtenim el com del fitxer a esborrar del servidor
$query = "select ga47_nom_intern as nomintern from ga47_adjunts_programador where ga47_id=" . $codi;

$result = $conn->query($query);


if (!$result)
    die($conn->error);
$row = $result->fetch_assoc();

$nomIntern = $row['nomintern'];
//esborrem el fitxer del servidor
$fitxer = $directori . $nomIntern;
unlink($fitxer);


//esborrem el registre de la base de dades

$query = "delete from ga47_adjunts_programador where ga47_id=" . $codi;
$result = $conn->query($query);

if (!$result)
    die($conn->error);
echo '<p class="btn-success">El fitxer ha estat esborrat amb èxit</p>';

$conn->close();


