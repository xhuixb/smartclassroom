<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$nom = $_POST['nom'];
$cognom1 = $_POST['cognom1'];
$cognom2 = $_POST['cognom2'];
$mail = $_POST['mail'];
$login = $_POST['login'];
$password = $_POST['password'];
$codiProf = $_POST['codiProf'];
$mode = $_POST['mode'];
//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

if ($mode == '0') {

    //mirem que el login no existeixi
    $query = "select count(*) as conta from ga04_professors where ga04_login='" . $login . "'";

    $result = $conn->query($query);


    if (!$result)
        die($conn->error);

    $row = $result->fetch_assoc();

    if ((int) $row['conta'] === 0) {
        //no n'hi ha cap, es pot fer la inserció
        //és una alta
        //obtenim el nou codi:
        $query = "select max(ga04_codi_prof)+1 as noucodi from ga04_professors";

        $result = $conn->query($query);


        if (!$result)
            die($conn->error);

        $row = $result->fetch_assoc();

        $nouCodiProf = $row['noucodi'];
        $result->close();
        //creem el professor a la taula 4

        $query = "insert into ga04_professors (ga04_codi_prof,ga04_nom,ga04_cognom1,ga04_cognom2,ga04_codi_especialitat,ga04_codi_dedicacio,ga04_mail,ga04_login,ga04_password,ga04_suspes)" .
                " values(" . $nouCodiProf . ",'" . $nom . "','" . $cognom1 . "','" . $cognom2 . "',0,0,'" . $mail . "','" . $login . "','" . $password . "','0')";
        //executem la sentència

        $conn->query($query);

        //posem el noou docenc dis del curs actual
        $query = "insert into ga17_professors_curs values (" . $_SESSION['curs_actual'] . "," . $nouCodiProf . ",0)";
        //executem la sentència

        $conn->query($query);

        echo '0';
    }else {
        echo '1';
    }
} else {
    //mirem que no existeixi el login que no sigui del propi profe
    $query = "select count(*) as conta from ga04_professors where ga04_login='" . $login . "' and ga04_codi_prof<>" . $codiProf;

    $result = $conn->query($query);


    if (!$result)
        die($conn->error);

    $row = $result->fetch_assoc();

    if ((int) $row['conta'] === 0) {
        //és una modificació
        $query = "update ga04_professors set ga04_nom='" . $nom . "',ga04_cognom1='" . $cognom1 . "',ga04_cognom2='" . $cognom2 . "',ga04_mail='" . $mail . "',ga04_login='" . $login . "' where ga04_codi_prof=" . $codiProf;
        //executem la sentència

        $conn->query($query);
        echo '0';
    }else{
        echo '1';
    }
}

$conn->close();
