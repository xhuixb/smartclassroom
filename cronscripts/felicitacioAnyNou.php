<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
require '../classes/GestioMailsNadal.php';

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//connectaem via smtp

$mail = new GestioMailsNadal("no_resposta@iesrocagrossa.cat", "xhb110693", "smtp.gmail.com");

//creem el cos
$cosHtml = "<h1>La direcció de l'Institut Rocagrossa juntament amb tot el claustre de professors i el personal d'adminstració i serveis us desitgem una molt bona entrada d'any 2018</h1>"
        . '<br>'
        . '<img src="cid:nadal_roca">';
$mail->afegeixCosHtml($cosHtml);

$query = "select distinct(ga11_mail1) as mail1"
        . " from ga11_alumnes,ga12_alumnes_curs where ga11_mail1<>'' and ga12_codi_curs=2 and ga12_id_alumne=ga11_id_alumne;";

//obtenim el mail1
//executem la query
//executem la consulta


$result = $conn->query($query);

if (!$result)
    die($conn->error);


if ($result->num_rows > 0) {
    $cont = 1;
    //$mail->afegeixBcc('xavier.huix@iesrocagrossa.cat');
    while ($row = $result->fetch_assoc()) {
        //afegim les adreces del mail1
        if ($cont > 399 && $cont < 500) {
            $mail->afegeixBcc($row['mail1']);
        }
        echo $cont . '-' . $row['mail1'] . '<br>';
        $cont++;
    }
}


$query = "select distinct(ga11_mail2) as mail2"
        . " from ga11_alumnes,ga12_alumnes_curs where ga11_mail2<>'' and ga12_codi_curs=2 and ga12_id_alumne=ga11_id_alumne;";


$result = $conn->query($query);

if (!$result)
    die($conn->error);


if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {
        //afegim les adreces del mail12

        if ($cont > 399 && $cont < 500) {
            $mail->afegeixBcc($row['mail2']);
        }
        echo $cont . '-' . $row['mail2'] . '<br>';
        $cont++;
    }
}

echo $cont;

//enviem els mails
$resultat = $mail->enviaMail();

$result->close();
$conn->close();
