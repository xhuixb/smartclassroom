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
$alumnes = $_POST['alumnes'];
$grupprofe = $_POST['grupprofe'];

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

//eliminem les dades del grup 
$query = "delete from ga24_grups_profes_det where ga24_codi_grup=" . $grupprofe;
$result = $conn->query($query);

if (!$result)
    die($conn->error);

//creem els nous alumnes
for ($i = 0; $i < count($alumnes); $i++) {

    $query = "insert into ga24_grups_profes_det values(" . $grupprofe . "," . $alumnes[$i] . ")";
    $result = $conn->query($query);

    if (!$result)
        die($conn->error);
}

$conn->close();
