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
//establim la connexió
$codiEquip = $_POST['codiEquip'];
$codiAlumne = $_POST['codiAlumne'];


$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//comprovem que no s'hagi prestat abans
$query = "select count(*) as conta from ga46_prestecs where ga46_curs=" . $_SESSION['curs_actual'] . " and ga46_equip=" . $codiEquip . " and ga46_data_devolucio is null";

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

$row = $result->fetch_assoc();

if ($row['conta'] == '0') {



    $query = "insert into ga46_prestecs (ga46_curs,ga46_equip,ga46_alumne,ga46_prof_prestec,ga46_data_prestec,ga46_hora_prestec) values"
            . " (" . $_SESSION['curs_actual'] . "," . $codiEquip . "," . $codiAlumne . "," . $_SESSION['prof_actual'] . ",date(now()),substr(time(now()),1,5))";


//echo $query;
//executem la consulta
    $result = $conn->query($query);


    if (!$result)
        die($conn->error);
    
    echo '0';
}else{
    echo '1';
}
$conn->close();
