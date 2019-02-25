<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';


//fem la connexió

session_start();

if ($_POST['dataClau'] !== '') {
    $dataClau = "'" . $_POST['dataClau'] . "'";
} else {
    $dataClau = " null";
}

if ($_POST['dataInici'] !== '') {
    $dataInici = "'" . $_POST['dataInici'] . "'";
} else {
    $dataInici = " null";
}


if ($_POST['dataFi'] !== '') {
    $dataFi = "'" . $_POST['dataFi'] . "'";
} else {
    $dataFi = " null";
}



$missatge = str_replace("'", "''", $_POST['missatge']);

$id = $_POST['id'];
$mode = $_POST['mode'];

//fem la connexió a la base de dades per totes les consultes que ens caldran
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

if ($mode === '1') {
    //és una modificació
    $query = "update ga41_dates_clau set ga41_data_clau=" . $dataClau . ",ga41_data_inici_publi=" . $dataInici . ",ga41_data_fi_publi=" . $dataFi . ",ga41_descripcio='" . $missatge . "' where ga41_id=" . $id;
} else {
    //és una alta
    $query = "insert into ga41_dates_clau (ga41_curs,ga41_data_clau,ga41_data_inici_publi,ga41_data_fi_publi,ga41_descripcio) values "
            . " (" . $_SESSION['curs_actual'] . "," . $dataClau . "," . $dataInici . "," . $dataFi . ",'" . $missatge . "')";
}

//echo $query;    
$result = $conn->query($query);

if (!$result)
    die($conn->error);



$conn->close();
