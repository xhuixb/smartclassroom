<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$nomsAlumnes = array('Jordi', 'pere', 'pacual', 'angel', 'enric', 'dalmau', 'marina', 'paula', 'oriol', 'nora', 'clara', 'maria', 'anna', 'veronica', 'marta', 'conxita', 'mercè', 'emilia', 'josep', 'joan');
$cognomsAlumnes = array('pujol', 'garcia', 'lopez', 'tarrida', 'roure', 'grebol', 'riu', 'alzina', 'moreno', 'martin', 'ruiz', 'serra', 'ferrer', 'soler', 'roca', 'lozano', 'font', 'rovira', 'carod', 'sanchez');



//obtenim tots els alumnes

$query = "select ga11_id_alumne as codialumne from ga11_alumnes";

//executem la consulta
$result = $conn->query($query);

$codiAlumnes = [];
$cont = 0;

if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $codiAlumnes[$cont] = $row['codialumne'];
        $cont++;
    }
}

$result->close();

//modifiquem els noms i cognoms
for ($i = 0; $i < count($codiAlumnes); $i++) {

    $nomIndex = rand(0, count($nomsAlumnes) - 1);
    $cognom1Index = rand(0, count($cognomsAlumnes) - 1);
    $cognom2Index = rand(0, count($cognomsAlumnes) - 1);

    $query = "update ga11_alumnes set ga11_nom='" . $nomsAlumnes[$nomIndex] . "',ga11_cognom1='" . $cognomsAlumnes[$cognom1Index] . "', ga11_cognom2='" . $cognomsAlumnes[$cognom2Index] . "',"
            . "ga11_mail1='" . $cognomsAlumnes[$cognom1Index] . '.' . $cognomsAlumnes[$cognom2Index] . '@ximplet.cat' . "',ga11_mail2='" . $cognomsAlumnes[$cognom2Index] . '.' . $cognomsAlumnes[$cognom1Index] . '@ximplet.cat' . "' where ga11_id_alumne=" . $codiAlumnes[$i];

    echo 'alumne: ' . $codiAlumnes[$i] . '<br>';


    //executem la instrucció
    $conn->query($query);
}


//fem el mateix amb el professors

$query = "select ga04_codi_prof as codiprof from ga04_professors";

//executem la consulta
$result = $conn->query($query);

$codiProfessors = [];
$cont = 0;


if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $codiProfessors[$cont] = $row['codiprof'];
        $cont++;
    }
}

$result->close();

for ($i = 0; $i < count($codiProfessors); $i++) {

    $nomIndex = rand(0, count($nomsAlumnes) - 1);
    $cognom1Index = rand(0, count($cognomsAlumnes) - 1);
    $cognom2Index = rand(0, count($cognomsAlumnes) - 1);

    $query = "update ga04_professors set ga04_nom='" . $nomsAlumnes[$nomIndex] . "',ga04_cognom1='" . $cognomsAlumnes[$cognom1Index] . "', ga04_cognom2='" . $cognomsAlumnes[$cognom2Index] . "',"
            . "ga04_mail='" . $cognomsAlumnes[$cognom1Index] . '.' . $cognomsAlumnes[$cognom2Index] . '@ximplet.cat' . "',ga04_login='" . $nomsAlumnes[$nomIndex] . '.' . $cognomsAlumnes[$cognom1Index] .
            "',ga04_password='1234' where ga04_codi_prof=" . $codiProfessors[$i];

    echo 'professor: ' . $codiProfessors[$i] . '<br>';


    //executem la instrucció
    $conn->query($query);
}



$conn->close();
