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

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//comprovem que no hagi estat esborrat abans

$query = "select count(*) as conta from ga46_prestecs where ga46_curs=" . $_SESSION['curs_actual'] . " and ga46_equip=" . $codiEquip . " and ga46_data_devolucio is null";

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

$row = $result->fetch_assoc();

if ($row['conta'] == '1') {


    $query = "update ga46_prestecs set ga46_data_devolucio=date(now()),ga46_hora_devolucio=substr(time(now()),1,5),"
            . "ga46_prof_devolucio=" . $_SESSION['prof_actual'] . " where ga46_equip=" . $codiEquip . " and ga46_data_devolucio is null";
    

    $result = $conn->query($query);


    if (!$result)
        die($conn->error);

    echo '0';
}else {
    echo '1';
}
$conn->close();
