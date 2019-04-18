<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../../vendor/autoload.php';
require '../../classes/Databases.php';

session_start();



if ($_FILES["file"]["name"] != '') {

    // move_uploaded_file($_FILES["file"]["tmp_name"],'../../pdf/xx');

    $conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
    if ($conn->connect_error)
        die($conn->connect_error);

    //triem el charset de la cerca
    mysqli_set_charset($conn, "utf8");


    $handle = new upload($_FILES["file"]);
    //$nomFitxer = $_POST['nomFitxer'];
    $codiProf = $_SESSION['prof_actual'];
    $dia = $_POST['dia'];
    $hora = $_POST['hora'];
    $nomFitxer = $_FILES["file"]["name"];
    $ext = pathinfo($nomFitxer, PATHINFO_EXTENSION);

    $path_parts = pathinfo($nomFitxer);

    $nomSenseExt = $path_parts['filename'];

    //mirem si hi havia programació per aquesta sessió
    $query = "select count(*) as conta from ga44_programacio_sessio where ga44_codi_curs=" . $_SESSION['curs_actual'] . " and ga44_professor=" . $codiProf . " and ga44_dia='" . $dia . "' and ga44_hora='" . $hora . "'";

    $result = $conn->query($query);

    if (!$result)
        die($conn->error);
    $row = $result->fetch_assoc();

    if ($row['conta'] == '0') {
        //caldrà crear la programació de la sessió
        $query = "insert into ga44_programacio_sessio (ga44_codi_curs,ga44_professor,ga44_dia,ga44_hora)"
                . " values (" . $_SESSION['curs_actual'] . "," . $codiProf . ",'" . $dia . "','" . $hora . "')";

        $result = $conn->query($query);
        if (!$result)
            die($conn->error);
    }

    //busquem l´últim adjunt
    $query = "select max(ga47_id) as max from ga47_adjunts_programador";
    //executem la consulta
    $result = $conn->query($query);


    if (!$result)
        die($conn->error);
    $row = $result->fetch_assoc();
    $item = $row['max'] + 1;
    //inserim el registre a la base de dades
    $query = "insert into ga47_adjunts_programador (ga47_id,ga47_curs,ga47_professor,ga47_dia,ga47_hora,ga47_nom_intern,ga47_nom_extern)"
            . " values (" . $item . "," . $_SESSION['curs_actual'] . "," . $codiProf . ",'" . $dia . "','" . $hora . "','p_" . $item . "." . $ext . "','" . str_replace("'", "''", $nomFitxer) . "')";
    $result = $conn->query($query);

    if (!$result)
        die($conn->error);

    //pugem el fitxer al servidor
    if ($handle->uploaded) {
        //echo 'p' . $codiProf . $nomSenseExt;
        $handle->file_new_name_body = 'p_' . $item;
        $handle->file_overwrite = true;

        $handle->process('../../uploads/programacions/');
        if ($handle->processed) {
            echo '<p class="btn-success">El fitxer ha estat pujat amb èxit </p>';
            $handle->clean();
        } else {
            echo 'error : ' . $handle->error;
        }
    }


    $conn->close();
}

