<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../classes/Databases.php';

session_start();

$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);

if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


if (isset($_POST['usuari'])) {
    $query = "select ga17_codi_professor as codiprof,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as nomprofessor,ga03_codi_curs as codicurs,ga03_descripcio as nomcurs,ga17_es_admin as admin"
            . " from ga04_professors,ga17_professors_curs,ga03_curs"
            . " where ga04_login='" . $_POST['usuari'] . "' and ga04_password='" . $_POST['password'] . "' and ga04_codi_prof=ga17_codi_professor and ga17_codi_curs=ga03_codi_curs and"
            . " ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga04_suspes='0'";

    //executem query
    //executem la consulta
    $result = $conn->query($query);

    if (!$result)
        die($conn->error);

    if ($result->num_rows > 0) {
        //s'ha trobat l'usuari
        $row = $result->fetch_assoc();
        $_SESSION['prof_actual'] = $row['codiprof'];
        $_SESSION['nom_prof_actual'] = $row['nomprofessor'];
        $_SESSION['curs_actual'] = $row['codicurs'];
        $_SESSION['nom_curs_actual'] = $row['nomcurs'];
        $_SESSION['admin']=$row['admin'];
    
        //registrem l'inici de sessió
        //creem el registre de l'usuari
        $query = "insert into ga25_log_usuaris (ga25_codi_usuari,ga25_inici_sessio,ga25_fi_sessio) values(" . $_SESSION['prof_actual'] . ",now(),null)";


        $conn->query($query);

        //recuperem el número de sessió creat
        $query = "select max(ga25_codi_usuari) as darrerasessio from ga25_log_usuaris";
        $result = $conn->query($query);

        if (!$result)
            die($conn->error);

        $row = $result->fetch_assoc();
        $_SESSION['darrerasessio'] = $row['darrerasessio'];
        //informem la taula d'usuaris del darrer acces

        $query = "update ga04_professors set ga04_darrer_acces=now() where ga04_codi_prof=" . $_SESSION['prof_actual'];

        $conn->query($query);

        //netegem les dades de l'usuari
        //primer dels mailings no fets
        $files = glob('../pdf/provisional/p' . $_SESSION['prof_actual'] . '_*.pdf'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file))
                unlink($file); // delete file
        }
        
        //ara les fitxer d'alumnes
        
        $files = glob('../xml/prof' . $_SESSION['prof_actual'] .'*.xml'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file))
                unlink($file); // delete file
        }
        
        
    } else {
        echo 'No';
    }
}

$result->close();
$conn->close();

