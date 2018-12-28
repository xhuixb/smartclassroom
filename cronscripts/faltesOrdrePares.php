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

//constuïm la query

$query = "select ga31_alumne as codialumne, concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,ga11_mail1 as mail1,ga11_mail2 as mail2, concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as professor,ga04_mail as mailprof,ga31_dia as dia,ga31_hora_inici as hora,ga22_nom_falta as tipusfalta,ga31_motiu as motiu,ga18_desc_assignatura as assignatura,"
        . "(select ga29_tutor from ga12_alumnes_curs,ga29_tutors_curs where ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga12_id_alumne=ga31_alumne and ga29_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)) as coditutor,"
        . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors,ga12_alumnes_curs,ga29_tutors_curs where ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga12_id_alumne=ga31_alumne and ga29_tutor=ga04_codi_prof and ga29_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)) as nomtutor,"
        . "(select ga04_mail from ga04_professors,ga12_alumnes_curs,ga29_tutors_curs where ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga12_id_alumne=ga31_alumne and ga29_tutor=ga04_codi_prof and ga29_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)) as mailtutor"
        . " from ga11_alumnes,ga04_professors,ga31_faltes_ordre,ga22_tipus_falta,ga07_grup,ga06_nivell,ga12_alumnes_curs,ga18_assignatures"
        . " where ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne and ga12_id_alumne=ga11_id_alumne and ga31_codi_professor=ga04_codi_prof and ga31_tipus_falta=ga22_codi_falta and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and"
        . " ga31_assignatura=ga18_codi_assignatura and ga31_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)"
        . " and ga31_enviada=0 and ga31_just_resp='1' order by alumne,codialumne";

//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);

//deixem les faltes com enviades

$query="update ga31_faltes_ordre set ga31_enviada=1 where ga31_enviada=0 and ga31_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)";
$conn->query($query);



//enviem els correus electrònics
if ($result->num_rows > 0) {
    //fem la connexió
    $mail = new GestioMails("no_resposta@iesrocagrossa.cat", "xhb110693", "smtp.gmail.com");
    $cont = 0;
    while ($row = $result->fetch_assoc()) {

        echo 'alumne:' . $row['alumne'] . ' mail-1:' . $row['mail1'] . ' mail-2:' . $row['mail2'] . '<br>';

        if ($row['mail1'] == "" && $row['mail2'] == "") {
            //com que no hi ha correu, n'enviem un per avisar del fet

            $cosHtml = '<img src="cid:logo_roca">'
                    . '<p> No s&#39;ha pogut enviar la falta d&#39;ordre als responsables de l&#39;alumne: ' . $row['alumne'] . ' de ' . $row['nivell'] . '-' . $row['grup'] . ' perquè, a la base de dades, no hi consten adreces de correu</p>';

            //  $mail = new GestioMails("no_resposta@iesrocagrossa.cat", "xhb110693", "smtp.gmail.com");
            //$mail->afegeixAdreca("no_resposta@iesrocagrossa.cat");
            $mail->afegeixAdreca($row['mailprof']);
            $mail->afegeixCosHtml($cosHtml);
            $resultat = $mail->enviaMail();
            sleep(1);
            //esborrem les adreces
            $mail->netejaAdreces();
        } else {

            if((string)$row['assignatura']===''){
                $textAssignatura="fora de sessions";
            }else{
                $textAssignatura=$row['assignatura'];
            }
            
            $cosHtml = '<img src="cid:logo_roca">'
                    . '<h1 style="text-decoration: underline;">Notificació</h1>'
                    . '<p style="font-size: 20px;">L&#39;alumne/a: <strong>' . $row['alumne'] . '</strong> de <strong>' . $row['nivell'] . '</strong> grup <strong>' . $row['grup'] . '</strong> té una notificació amb les següents característiques:</p>'
                    . '<ul>'
                    . '<li style="font-size: 18px;"><strong>Data: </strong>' . $row['dia'] . '</li>'
                    . '<li style="font-size: 18px;"><strong>Hora: </strong>' . $row['hora'] . '</li>'
                    . '<li style="font-size: 18px;"><strong>Assignatura: </strong>' . $textAssignatura . '</li>'
                    . '<li style="font-size: 18px;"><strong>Professor/a: </strong>' . $row['professor'] . '</li>'
                    . '<li style="font-size: 18px;"><strong>Tipus: </strong>' . $row['tipusfalta'] . '</li>'
                    . '<li style="font-size: 18px;"><strong>Motiu: </strong>' . $row['motiu'] . '</li>'
                    . '</ul>'
                    . '<br>'
                    . '<p style="font-size: 18px;">Atentament</p> ';




            if ($row['mail1'] != "") {
                $mail->afegeixAdreca($row['mail1']);
            }

            if ($row['mail2'] != "") {
                $mail->afegeixAdreca($row['mail2']);
            }
            $mail->afegeixCosHtml($cosHtml);
            $resultat = $mail->enviaMail();
            sleep(1);
        }

        //esborrem les adreces
        $mail->netejaAdreces();

        $cont++;
    }
}

$result->close();
$conn->close();


echo 'fi de la tramesa de mails als pares';
