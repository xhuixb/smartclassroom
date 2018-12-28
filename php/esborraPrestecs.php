<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//esborrem els préstecs ja exiatents
require '../classes/Databases.php';
//fem la connexió

session_start();
//$_SESSION['curs_actual'] = 3;
//$_SESSION['prof_actual'] = 0;
//establim la connexió
$prestecs = $_POST['prestecs'];


if ($_SESSION['admin'] == '1') {

    $conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
    if ($conn->connect_error)
        die($conn->connect_error);

//triem el charset de la cerca
    mysqli_set_charset($conn, "utf8");


    $prestecsString = join(',', $prestecs);

    $query = "delete from ga46_prestecs where ga46_codi in (" . $prestecsString . ")";

//executem la consulta
    $result = $conn->query($query);


    if (!$result)
        die($conn->error);


    $conn->close();
    echo '0';
}else {
    echo '1';
}