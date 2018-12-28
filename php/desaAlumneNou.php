<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();
//$_SESSION['curs_actual'] = 2;
//$_SESSION['prof_actual'] = 0;
$nivell = $_POST['nivell'];
$grup = $_POST['grup'];
$nom = $_POST['nom'];
$cognom1 = $_POST['cognom1'];
$cognom2 = $_POST['cognom2'];
$mode = $_POST['mode'];
$codiAlumne = $_POST['codiAlumne'];
$checkAoAlta = $_POST['checkAoAlta'];
$checkAaAlta = $_POST['checkAaAlta'];
$checkUseeAlta = $_POST['checkUseeAlta'];
$checkComunica = $_POST['checkComunica'];
$mail1 = $_POST['mail1'];
$mail2 = $_POST['mail2'];
$mail1Vell = $_POST['mail1Vell'];
$mail2Vell = $_POST['mail2Vell'];


//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//dicidim si és una alta o una modificació
if ($mode == "") {

    //si hi ha mails activem l'indicador per enviar el correu d'avís
    if ($mail1 != '') {
        $switchMail1 = 1;
    } else {
        $switchMail1 = 0;
    }

    if ($mail2 != '') {
        $switchMail2 = 1;
    } else {
        $switchMail2 = 0;
    }


//creem l'usuari

    $query = "insert into ga11_alumnes (ga11_nom,ga11_cognom1,ga11_cognom2,ga11_mail1,ga11_mail2,ga11_check_comunica,ga11_switch_mail1,ga11_switch_mail2) values('" . $nom . "','" . $cognom1 . "','" . $cognom2 . "','" . $mail1 . "','" . $mail2 . "','" . $checkComunica . "'," . $switchMail1 . "," . $switchMail2 . ")";
    $conn->query($query);

    echo $query;

//obtenim el nou alumne

    $query = "select max(ga11_id_alumne) as maxim from ga11_alumnes";

    $result = $conn->query($query);

    if (!$result)
        die($conn->error);


    $row = $result->fetch_assoc();

    $codiNou = $row['maxim'];

//afegim l'alumne dins del curs actual

    $query = "insert into ga12_alumnes_curs values (" . $_SESSION['curs_actual'] . "," . $codiNou . "," . $nivell . "," . $grup . ",null,null,'" .
            $checkAoAlta . "','" . $checkAaAlta . "','" . $checkUseeAlta . "',null)";

    echo $query;
    $conn->query($query);
}else {

    //desidim si activem l'indicador per enviar el correu d'avís


    if (($mail1 != '' && $mail1 != $mail1Vell) && ($mail2 != '' && $mail2 != $mail2Vell)) {
        //si no és buit i ha canviat activem els dos indicadors
        $switchMail1 = 1;
        $switchMail2 = 1;
        $query = "update ga11_alumnes set ga11_nom='" . $nom . "',ga11_cognom1='" . $cognom1 . "',ga11_cognom2='" . $cognom2 . "',ga11_mail1='" . $mail1 . "',ga11_mail2='" . $mail2 .
                "',ga11_check_comunica='" . $checkComunica . "',ga11_switch_mail1=" . $switchMail1 . ",ga11_switch_mail2=" . $switchMail2
                . " where ga11_id_alumne=" . $codiAlumne;
    } elseif ($mail1 != '' && $mail1 != $mail1Vell) {
        //si no és buit i ha canviat activem indicador mail1
        $switchMail1 = 1;
        $query = "update ga11_alumnes set ga11_nom='" . $nom . "',ga11_cognom1='" . $cognom1 . "',ga11_cognom2='" . $cognom2 . "',ga11_mail1='" . $mail1 . "',ga11_mail2='" . $mail2 .
                "',ga11_check_comunica='" . $checkComunica . "',ga11_switch_mail1=" . $switchMail1
                . " where ga11_id_alumne=" . $codiAlumne;
    } elseif ($mail2 != '' && $mail2 != $mail2Vell) {
        //si no és buit i ha canviat activem indicador mail2
        $switchMail2 = 1;
        $query = "update ga11_alumnes set ga11_nom='" . $nom . "',ga11_cognom1='" . $cognom1 . "',ga11_cognom2='" . $cognom2 . "',ga11_mail1='" . $mail1 . "',ga11_mail2='" . $mail2 .
                "',ga11_check_comunica='" . $checkComunica . "',ga11_switch_mail2=" . $switchMail2
                . " where ga11_id_alumne=" . $codiAlumne;
    } else {
        //no activem cap indicador
        $query = "update ga11_alumnes set ga11_nom='" . $nom . "',ga11_cognom1='" . $cognom1 . "',ga11_cognom2='" . $cognom2 . "',ga11_mail1='" . $mail1 . "',ga11_mail2='" . $mail2 .
                "',ga11_check_comunica='" . $checkComunica . "'"
                . " where ga11_id_alumne=" . $codiAlumne;
    }



    $conn->query($query);

    //modifiquem les aules singulars
    $query = "update ga12_alumnes_curs set ga12_ao='" . $checkAoAlta . "',ga12_aa='" . $checkAaAlta . "',ga12_usee='" . $checkUseeAlta . "' "
            . "where ga12_id_alumne=" . $codiAlumne . " and ga12_codi_curs=" . $_SESSION['curs_actual'];
    $conn->query($query);
}


$conn->close();
