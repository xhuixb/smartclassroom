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

$query = "select concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga15_alumne as codialumne,ga11_mail1 as mail1,ga11_mail2 as mail2,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,ga15_dia as dia,ga15_hora_inici as hora,"
        . "(select ga04_mail from ga04_professors,ga12_alumnes_curs,ga29_tutors_curs where ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga12_id_alumne=codialumne and ga29_tutor=ga04_codi_prof and ga29_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)) as mailtutor, "
        . "(select ga18_desc_assignatura from ga18_assignatures,ga28_cont_presencia_cap where ga28_codi_curs=ga15_codi_curs and ga28_dia=ga15_dia and ga28_hora=ga15_hora_inici and ga28_professor=ga15_codi_professor and ga28_assignatura=ga18_codi_assignatura) as assignatura,"
        . "(select ga01_descripcio_aula from ga01_aula,ga28_cont_presencia_cap where ga28_codi_curs=ga15_codi_curs and ga28_dia=ga15_dia and ga28_hora=ga15_hora_inici and ga28_professor=ga15_codi_professor and ga28_aula=ga01_codi_aula) as aula,"
        . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=ga15_codi_professor) as professor"
        . " from ga11_alumnes,ga12_alumnes_curs,ga15_cont_presencia,ga06_nivell,ga07_grup"
        . " where ga15_codi_curs=ga12_codi_curs and ga15_alumne=ga12_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and ga12_id_alumne=ga11_id_alumne and ga15_dia=date(now()) and ga15_hora_inici<TIME_FORMAT(now(),'%H:%i')"
        . " and ga15_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual='1') and ga15_check_absent='1' and ga15_check_comunica='1'"
        . " order by alumne,codialumne,ga15_hora_inici";

//echo $query;

//executem query
$result = $conn->query($query);

if (!$result)
    die($conn->error);


if ($result->num_rows > 0) {

    $mail = new GestioMails("no_resposta@iesrocagrossa.cat", "xhb110693", "smtp.gmail.com");

    $cont = 0;
    $alumVell = 0;
    $hores = '';
    $horesArray = [];
    $profeArray = [];
    $assignaturaArray = [];
    $aulaArray = [];
    $alumVellNom = '';
    $alumVellNivell = '';
    $alumVellGrup = '';
    $alumVellMail1 = '';
    $alumVellMail2 = '';
    $alumVellMailTutor = '';
    $contaFaltesAlumne = 0;

    while ($row = $result->fetch_assoc()) {
        $alumActual = $row['codialumne'];
        $alumActualNom = $row['alumne'];
        $alumActualNivell = $row['nivell'];
        $alumActualGrup = $row['grup'];
        $alumActualMail1 = $row['mail1'];
        $alumActualMail2 = $row['mail2'];
        $alumActualMailTutor = $row['mailtutor'];


        if ($alumActual == $alumVell) {
            $nouAlumne = false;

            $hores .= $row['hora'] . '-';
            $horesArray[$contaFaltesAlumne] = $row['hora'];
            $profeArray[$contaFaltesAlumne] = $row['professor'];
            $assignaturaArray[$contaFaltesAlumne] = $row['assignatura'];
            $aulaArray[$contaFaltesAlumne] = $row['aula'];
            $contaFaltesAlumne++;
        } else {
            if ($cont != 0) {
                $nouAlumne = true;

                //creem la taula del detall
                $creaTaula = '<table style="border: 1px solid black;border-collapse: collapse;">';
                $creaTaula .= '<tr>';
                $creaTaula .= '<th style="border: 1px solid black;border-collapse: collapse;text-align: left;padding: 5px;">Hora</th>';
                $creaTaula .= '<th style="border: 1px solid black;border-collapse: collapse;text-align: left;padding: 5px;">Assignatura</th>';
                $creaTaula .= '<th style="border: 1px solid black;border-collapse: collapse;text-align: left;padding: 5px;">Professor</th>';
                $creaTaula .= '<th style="border: 1px solid black;border-collapse: collapse;text-align: left;padding: 5px;">Aula</th>';
                $creaTaula .= "</tr>";


                for ($i = 0; $i < $contaFaltesAlumne; $i++) {
                    $creaTaula .= '<tr>';
                    $creaTaula .= '<td style="border: 1px solid black;border-collapse: collapse;padding: 5px;">' . $horesArray[$i] . '</td>';
                    $creaTaula .= '<td style="border: 1px solid black;border-collapse: collapse;padding: 5px;">' . $assignaturaArray[$i] . '</td>';
                    $creaTaula .= '<td style="border: 1px solid black;border-collapse: collapse;padding: 5px;">' . $profeArray[$i] . '</td>';
                    $creaTaula .= '<td style="border: 1px solid black;border-collapse: collapse;padding: 5px;">' . $aulaArray[$i] . '</td>';
                    $creaTaula .= '</tr>';
                }

                $contaFaltesAlumne = 0;

                $creaTaula .= '</table>';

                $cosHtml = '';
                $cosHtml .= '<img src="cid:logo_roca">'
                        . '<h1 style="text-decoration: underline;">Comunicació d&#39;absència</h1>'
                        . '<p style="font-size: 20px;">L&#39;alumne/a: <strong>' . $alumVellNom . '</strong> de <strong>' . $alumVellNivell . '</strong> grup <strong>' . $alumVellGrup . '</strong></p>'
                        . '<p>Ha faltat a classe sense avisar el dia: <strong>' . date("d/m/Y") . '</strong> les següents hores: </p>'
                        . '<br>'
                        . $creaTaula
                        . '<p style="font-size: 18px;">Atentament</p> ';
               


                //enviem el correu;

                if ($alumVellMail1 != "") {
                    $mail->afegeixAdreca($alumVellMail1);
                }

                if ($alumVellMail2 != "") {
                    $mail->afegeixAdreca($alumVellMail2);
                }

                if ($alumVellMail1 == "" && $alumVellMail2 == "") {
                    //  $mail->afegeixAdreca("no_resposta@iesrocagrossa.cat");
                    $mail->afegeixAdreca($alumVellMailTutor);
                }


                $mail->afegeixCosHtml($cosHtml);
                $resultat = $mail->enviaMail();

                sleep(1);

                //esborrem les adreces
                $mail->netejaAdreces();

                echo $alumVellNom . '<br>';


                $alumVell = $alumActual;
                $alumVellNom = $alumActualNom;
                $alumVellNivell = $alumActualNivell;
                $alumVellGrup = $alumActualGrup;
                $alumVellMail1 = $alumActualMail1;
                $alumVellMail2 = $alumActualMail2;
                $alumVellMailTutor = $alumActualMailTutor;

                $hores = '';
                $hores .= $row['hora'] . '-';
                $horesArray = [];
                $profeArray = [];
                $assignaturaArray = [];
                $aulaArray = [];
                $horesArray[$contaFaltesAlumne] = $row['hora'];
                $profeArray[$contaFaltesAlumne] = $row['professor'];
                $assignaturaArray[$contaFaltesAlumne] = $row['assignatura'];
                $aulaArray[$contaFaltesAlumne] = $row['aula'];
                $contaFaltesAlumne++;
            } else {

                $alumVell = $alumActual;
                $alumVellNom = $alumActualNom;
                $alumVellNivell = $alumActualNivell;
                $alumVellGrup = $alumActualGrup;
                $alumVellMail1 = $alumActualMail1;
                $alumVellMail2 = $alumActualMail2;
                $alumVellMailTutor = $alumActualMailTutor;

                $horesArray[$contaFaltesAlumne] = $row['hora'];
                $profeArray[$contaFaltesAlumne] = $row['professor'];
                $assignaturaArray[$contaFaltesAlumne] = $row['assignatura'];
                $aulaArray[$contaFaltesAlumne] = $row['aula'];
                $contaFaltesAlumne++;


                $hores .= $row['hora'] . '-';
            }
        }
        $cont++;
    }
    //l'últimm alumne
    //creem la taula del detall
    $creaTaula = '<table style="border: 1px solid black;border-collapse: collapse;">';
    $creaTaula .= '<tr>';
    $creaTaula .= '<th style="border: 1px solid black;border-collapse: collapse;text-align: left;padding: 5px;">Hora</th>';
    $creaTaula .= '<th style="border: 1px solid black;border-collapse: collapse;text-align: left;padding: 5px;">Assignatura</th>';
    $creaTaula .= '<th style="border: 1px solid black;border-collapse: collapse;text-align: left;padding: 5px;">Professor</th>';
    $creaTaula .= '<th style="border: 1px solid black;border-collapse: collapse;text-align: left;padding: 5px;">Aula</th>';
    $creaTaula .= "</tr>";


    for ($i = 0; $i < $contaFaltesAlumne; $i++) {
        $creaTaula .= '<tr>';
        $creaTaula .= '<td style="border: 1px solid black;border-collapse: collapse;padding: 5px;">' . $horesArray[$i] . '</td>';
        $creaTaula .= '<td style="border: 1px solid black;border-collapse: collapse;padding: 5px;">' . $assignaturaArray[$i] . '</td>';
        $creaTaula .= '<td style="border: 1px solid black;border-collapse: collapse;padding: 5px;">' . $profeArray[$i] . '</td>';
        $creaTaula .= '<td style="border: 1px solid black;border-collapse: collapse;padding: 5px;">' . $aulaArray[$i] . '</td>';
        $creaTaula .= '</tr>';
    }


    $creaTaula .= '</table>';

    $cosHtml = '';
    $cosHtml .= '<img src="cid:logo_roca">'
            . '<h1 style="text-decoration: underline;">Comunicació d&#39;absència</h1>'
            . '<p style="font-size: 20px;">L&#39;alumne/a: <strong>' . $alumVellNom . '</strong> de <strong>' . $alumVellNivell . '</strong> grup <strong>' . $alumVellGrup . '</strong></p>'
            . '<p>Ha faltat a classe sense avisar el dia: <strong>' . date("d/m/Y") . '</strong> les següents hores: </p>'
            . '<br>'
            . $creaTaula
            . '<p style="font-size: 18px;">Atentament</p> ';

  
    //enviem l'últim correu;
    if ($alumVellMail1 != "") {
        $mail->afegeixAdreca($alumVellMail1);
    }

    if ($alumVellMail2 != "") {
        $mail->afegeixAdreca($alumVellMail2);
    }

    if ($alumVellMail1 == "" && $alumVellMail2 == "") {
        //  $mail->afegeixAdreca("no_resposta@iesrocagrossa.cat");
        $mail->afegeixAdreca($alumVellMailTutor);
    }

    $mail->afegeixCosHtml($cosHtml);
    $resultat = $mail->enviaMail();


    echo $alumVellNom . '<br>';
}


$result->close();
$conn->close();
