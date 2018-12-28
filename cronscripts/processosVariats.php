<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
require '../classes/GestioMails.php';

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//primerament enviarem un correu als correus nous que s'han introduït o bé als modificats
//seleccionem els mails amb aquestes condicions
$query = "select ga11_id_alumne,ga11_mail1 as mail1,ga11_mail2 as mail2,ga11_switch_mail1 as switch1,ga11_switch_mail2 as switch2 from ga11_alumnes,ga12_alumnes_curs where ga12_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)"
        . " and ga12_id_alumne=ga11_id_alumne and (ga11_switch_mail1=1 or ga11_switch_mail2=1)";

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);


if ($result->num_rows > 0) {
    //fem la connexió
    //si retorna alguna filera ens connectem per smtp al servidor
    $mail = new GestioMails("no_resposta@iesrocagrossa.cat", "xhb110693", "smtp.gmail.com");
    //construïm el cos del correu que sempre serà el mateix
    $cosHtml = '<img src="cid:logo_roca">'
            . '<br>'
            . '<p>Benvolgut pare/mare tutor/tutora,</p>'
            . '<p>S&#39;ha introduït aquesta adreça de correu electrònic a la base de dades de l&#39;Institut Rocagrossa.</p>'
            . '<p>Seguint les lleis de protecció de dades, serà tractada amb total confidencialitat</p>'
            . '<br>'
            . '<p>Atentament</p>';
    //i l'afegim al correu
    $mail->afegeixCosHtml($cosHtml);

    $cont = 0;
    while ($row = $result->fetch_assoc()) {

        if ($row['switch1'] == 1) {
            //s'ha modificat el mail1
            if ($row['mail1'] != '') {
                //si no està buit el mail1 enviem el correu
                $mail->afegeixAdreca($row['mail1']);
                $resultat = $mail->enviaMail();

                sleep(1);
                //esborrem les adreces
                $mail->netejaAdreces();
                echo '<p>mail enviat: ' . $row['mail1'] . '</p>';
            }
        }

        if ($row['switch2'] == 1) {
            //s'ha modificat el mail2
            if ($row['mail2'] != '') {
                //si no està buit el mail2
                $mail->afegeixAdreca($row['mail2']);
                $resultat = $mail->enviaMail();
                sleep(3);
                //esborrem les adreces
                $mail->netejaAdreces();
                echo '<p>mail enviat: ' . $row['mail2'] . '</p>';
            }
        }
    }


    //tornem els indicadors a la posició habitual
    $query = "update ga11_alumnes set ga11_switch_mail1=0,ga11_switch_mail2=0 where (ga11_switch_mail1=1 or ga11_switch_mail2=1)";
    $conn->query($query);
}

//ara enviem els mailings que hi puguin haver
//busquem les comunicacions que estan en aprovades
$query = "SELECT ga30_id as codi,ga30_codi_prof as codiprof,"
        . "ga30_alumnes as alumnes,ga30_missatge as missatge,ga30_adjunts as adjunts,ga30_data_mailing as datamailing,"
        . "(select ga04_mail from ga04_professors where ga04_codi_prof=ga30_codi_prof) as mailprof from ga30_comunicacions where ga30_estat=1";

//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);


if ($result->num_rows > 0) {

    //si almenys hi ha un mailing per fer ens connectem
    $mail = new GestioMails("no_resposta@iesrocagrossa.cat", "xhb110693", "smtp.gmail.com");

    while ($row = $result->fetch_assoc()) {
        $codiMailing = $row['codi'];
        $codiProf = $row['codiprof'];
        $alumnes = $row['alumnes'];
        $missatge = $row['missatge'];
        $adjunts = $row['adjunts'];
        $dataMailing = $row['datamailing'];
        $mailProf = $row['mailprof'];

        $cosHtml = '<img src="cid:logo_roca">'
                . '<br>' . $missatge;
        //i l'afegim al correu

        echo $cosHtml;
        echo '<br>';

        $mail->afegeixCosHtml($cosHtml);
        //ara anem a buscar els mails dels pares dels alumnes i el nom del professor i el seu mail
        //convertim l'string en array
        $alumnes = "(" . str_replace("-", ",", $alumnes) . ")";

        $query1 = "select ga11_mail1 as mail1,ga11_mail2 as mail2 from ga11_alumnes where ga11_id_alumne in " . $alumnes;
        //preparem el mail per enviar
        //executem la consulta
        $result1 = $conn->query($query1);

        if (!$result1)
            die($conn->error);

        $destinataris = '';
        if ($result1->num_rows > 0) {
            while ($row1 = $result1->fetch_assoc()) {
                //afegim els destinataris en mode bbc
                if ($row1['mail1'] != '') {
                    $mail->afegeixBcc($row1['mail1']);
                    echo $row1['mail1'] . '<br>';
                    $destinataris .= $row1['mail1'] . '<br>';
                }
                if ($row1['mail2'] != '') {
                    $mail->afegeixBcc($row1['mail2']);
                    echo $row1['mail2'] . '<br>';
                    $destinataris .= $row1['mail2'] . '<br>';
                }
            }
        }

        $result1->close();
        //mirem si hi ha fitxers adjunts

        if ($adjunts != '') {
            //hi ha fitxers adjunts cal afegir-los
            $mail->adjuntaFitxer($adjunts);
        }


        //enviem el mail
        $resultat = $mail->enviaMail();
        sleep(3);
        //esborrem les adreces
        $mail->netejaAdreces();
        //canviem l'estat a executat
        $queryExec = "update ga30_comunicacions set ga30_estat=2 where ga30_id=" . $codiMailing;
        $conn->query($queryExec);
        //enviem un informe al remitent
        //enviem un report al professor que ha creat el mailing
        $cosHtml = '<img src="cid:logo_roca">'
                . '<br>'
                . '<p>S&#39;ha enviat un mailing amb el següent cos:</p>'
                . $missatge
                . '<p>Als següents destinataris:</p>'
                . $destinataris;
        $mail->afegeixCosHtml($cosHtml);
        $mail->afegeixAdreca($mailProf);
        $resultat = $mail->enviaMail();
        sleep(3);
        //esborrem les adreces
        $mail->netejaAdreces();
        $mail->eliminaAdjunts();
    }
}

$result->close();
$conn->close();
