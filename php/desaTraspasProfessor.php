<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

session_start();

$switchHoraris = $_POST['switchHoraris'];
$codiProfOrigen = $_POST['codiProfOrigen'];
$codiProfDesti = $_POST['codiProfDesti'];


if ($switchHoraris == 'true') {
    //primer traspassem grups
    $relCodisGrups = [];
    $relCodisGrups = traspassaGrups($codiProfOrigen, $codiProfDesti);
    //ara traspassarem horaris
    traspassaHoraris($codiProfOrigen, $codiProfDesti, $relCodisGrups);
    echo 1;
} else {
    //només es traspassaran els grups
    traspassaGrups($codiProfOrigen, $codiProfDesti);
    echo 0;
}

function traspassaHoraris($codiProfOrigen, $codiProfDesti, $relCodisGrups) {
    //establim la connexió
    $conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
    if ($conn->connect_error)
        die($conn->connect_error);

    //triem el charset de la cerca
    mysqli_set_charset($conn, "utf8");

    //primerament recuperem els horaris del professor origen
    $query = "select ga26_codi_curs as curs,ga26_codi_professor as professor,ga26_dia_setmana as dia,ga26_hora_inici as hora,"
            . "ga26_nivell as nivell,ga26_grup as grup,ga26_tipus_grup as tipusgrup,ga26_is_lectiva as islectiva,ga26_tipus_carrec as tipuscarrec,"
            . "ga26_tipus_guardia as tipusguardia,ga26_codi_assignatura as assignatura,ga26_codi_aula as aula,ga26_es_guardia as esguardia,ga26_es_carrec as escarrec"
            . " from ga26_horaris_docents where ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_codi_professor=" . $codiProfOrigen;

    $result = $conn->query($query);

    if (!$result)
        die($conn->error);

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {
            //posarem nulls si cal en els camps que en poden tenir
            if ($row['nivell'] == '') {
                $nivell = 'null';
            } else {
                $nivell = $row['nivell'];
            }

            if ($row['grup'] == '') {
                $grup = 'null';
            } else {
                $grup = $row['grup'];
            }

            if ($row['tipuscarrec'] == '') {
                $tipusCarrec = 'null';
            } else {
                $tipusCarrec = $row['tipuscarrec'];
            }

            if ($row['tipusguardia'] == '') {
                $tipusGuardia = 'null';
            } else {
                $tipusGuardia = $row['tipusguardia'];
            }

            if ($row['assignatura'] == '') {
                $assignatura = 'null';
            } else {
                $assignatura = $row['assignatura'];
            }

            if ($row['aula'] == '') {
                $aula = 'null';
            } else {
                $aula = $row['aula'];
            }

            //si és un grup personal hem de recuperar el nou grup que s'ha creat
            if ($row['tipusgrup'] == '1') {
                $grup = $relCodisGrups[$row['grup']];
            }

            //fem la inserció del nou horari

            $query2 = "insert into ga26_horaris_docents values(" . $_SESSION['curs_actual'] . "," . $codiProfDesti . "," . $row['dia'] . ",'" . $row['hora'] . "',"
                    . $nivell . "," . $grup . "," . $row['tipusgrup'] . "," . $row['islectiva'] . "," . $tipusCarrec . "," . $tipusGuardia . "," . $assignatura . "," . $aula . ","
                    . $row['esguardia'] . "," . $row['escarrec'] . ")";

            $conn->query($query2);
        }
    }
}

function traspassaGrups($codiProfOrigen, $codiProfDesti) {

    //establim la connexió
    $conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
    if ($conn->connect_error)
        die($conn->connect_error);

    //triem el charset de la cerca
    mysqli_set_charset($conn, "utf8");

    //obtenim el darrer grup creat
    $query = "select max(ga23_codi_grup) as codiultim from ga23_grups_profes_cap";

    $result = $conn->query($query);

    if (!$result)
        die($conn->error);

    $row = $result->fetch_assoc();

    $codiUltim = $row['codiultim'];

    //obtenim els grups del profe origen
    $query = "select ga23_codi_grup as codi,ga23_codi_professor as professor,ga23_codi_nivell as nivell,ga23_nom_grup as nom,ga24_codi_alumne as alumne"
            . " from ga23_grups_profes_cap,ga24_grups_profes_det where ga23_codi_grup=ga24_codi_grup"
            . " and ga23_curs=" . $_SESSION['curs_actual'] . " and ga23_codi_professor=" . $codiProfOrigen;


    $result = $conn->query($query);

    if (!$result)
        die($conn->error);

    if ($result->num_rows > 0) {

        $conta = 0;
        $grupVell = '';
        $relCodisGrups = [];

        while ($row = $result->fetch_assoc()) {
            if ($grupVell != $row['codi']) {
                $canviGrup = true;
            } else {
                $canviGrup = false;
            }

            if ($canviGrup == true) {

                $codiUltim++;
                $contaAlumnes = 0;
                $relCodisGrups[$row['codi']] = $codiUltim;

                if ($conta == 0) {
                    $alumnesGrup = [];
                    $query1 = "insert into ga23_grups_profes_cap values(" . $codiUltim . "," . $_SESSION['curs_actual'] . "," . $codiProfDesti . "," . $row['nivell'] . ",'" . $row['nom'] . "')";
                    //executem la instrucció sql
                    $conn->query($query1);

                    //desem el primer alumne
                    $alumnesGrup[$contaAlumnes] = $row['alumne'];
                    $contaAlumnes++;

                    //si és el primer grup només creem la capçalera
                } else {
                    //abans de crear la capçalera creem el detall del grup vell
                    $query1 = "insert into ga24_grups_profes_det values ";
                    for ($i = 0; $i < count($alumnesGrup); $i++) {
                        if ($i == count($alumnesGrup) - 1) {
                            //l'ultim no li cal posar la coma
                            $query1 .= "(" . ($codiUltim - 1) . "," . $alumnesGrup[$i] . ")";
                        } else {
                            $query1 .= "(" . ($codiUltim - 1) . "," . $alumnesGrup[$i] . "),";
                        }
                    }

                    //executem la inserció 
                    $conn->query($query1);
                    //ara insereim la capçalera del següent grup
                    $query1 = "insert into ga23_grups_profes_cap values(" . $codiUltim . "," . $_SESSION['curs_actual'] . "," . $codiProfDesti . "," . $row['nivell'] . ",'" . $row['nom'] . "')";

                    $conn->query($query1);
                    //creem el array dels alumnes del nou grup
                    //desem el primer alumne
                    $alumnesGrup = [];
                    $alumnesGrup[$contaAlumnes] = $row['alumne'];
                    $contaAlumnes++;
                }
            } else {
                //continuem
                //guadem els alunnes del grup en un array
                $alumnesGrup[$contaAlumnes] = $row['alumne'];
                $contaAlumnes++;
            }
            $grupVell = $row['codi'];
            $conta++;
        }
        //desem els alumnes del darrer grup
        $query1 = "insert into ga24_grups_profes_det values ";
        for ($i = 0; $i < count($alumnesGrup); $i++) {
            if ($i == count($alumnesGrup) - 1) {
                //l'ultim no li cal posar la coma
                $query1 .= "(" . $codiUltim . "," . $alumnesGrup[$i] . ")";
            } else {
                $query1 .= "(" . $codiUltim . "," . $alumnesGrup[$i] . "),";
            }
        }

        //executem la inserció 
        $conn->query($query1);
    }
    $result->close();
    $conn->close();

    return $relCodisGrups;
}
?>
