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


//abans que res netegem els comentaris sense text

$query="delete from ga43_comentaris_sessio where ga43_switch_enviar=''";

$conn->query($query);


//els comentaris que s'han d'enviar

$query="select ga43_alumne as codialumne, concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,ga11_mail1 as mail1,ga11_mail2 as mail2, concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as professor,ga04_mail as mailprof,ga43_dia as dia,ga43_hora_inici as hora,ga43_text as textcoment,ga18_desc_assignatura as assignatura"
." from ga11_alumnes,ga04_professors,ga43_comentaris_sessio,ga07_grup,ga06_nivell,ga12_alumnes_curs,ga18_assignatures"
." where ga43_codi_curs=ga12_codi_curs and ga43_alumne=ga12_id_alumne and ga12_id_alumne=ga11_id_alumne and ga43_codi_professor=ga04_codi_prof and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and"
." ga43_assignatura=ga18_codi_assignatura and ga43_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)"
." and ga43_enviat=0 and ga43_switch_enviar='1' order by alumne,codialumne";


//executem la consulta
$result = $conn->query($query);

//tot seguit deixem els comentaris com eviats

$query1="update ga43_comentaris_sessio set ga43_enviat='1' where ga43_enviat='0'";

$conn->query($query1);


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
                    . '<p> No s&#39;ha pogut enviar el comentari als responsables de l&#39;alumne: ' . $row['alumne'] . ' de ' . $row['nivell'] . '-' . $row['grup'] . ' perquè, a la base de dades, no hi consten adreces de correu</p>';

            //  $mail = new GestioMails("no_resposta@iesrocagrossa.cat", "xhb110693", "smtp.gmail.com");
            //$mail->afegeixAdreca("no_resposta@iesrocagrossa.cat");
            $mail->afegeixAdreca($row['mailprof']);
            $mail->afegeixCosHtml($cosHtml);
            $resultat = $mail->enviaMail();
            sleep(1);
            //esborrem les adreces
            $mail->netejaAdreces();
        } else {

                        
            $cosHtml = '<img src="cid:logo_roca">'
                    . '<h1 style="text-decoration: underline;">Notificació</h1>'
                    . '<p style="font-size: 20px;">L&#39;alumne/a: <strong>' . $row['alumne'] . '</strong> de <strong>' . $row['nivell'] . '</strong> grup <strong>' . $row['grup'] . '</strong> té un comentari amb les següents característiques:</p>'
                    . '<ul>'
                    . '<li style="font-size: 18px;"><strong>Data: </strong>' . $row['dia'] . '</li>'
                    . '<li style="font-size: 18px;"><strong>Hora: </strong>' . $row['hora'] . '</li>'
                    . '<li style="font-size: 18px;"><strong>Assignatura: </strong>' . $row['assignatura'] . '</li>'
                    . '<li style="font-size: 18px;"><strong>Professor/a: </strong>' . $row['professor'] . '</li>'
                    . '<li style="font-size: 18px;"><strong>Text: </strong>' . $row['textcoment'] . '</li>'                   
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


echo 'fi de la tramesa de comentaris als pares';

