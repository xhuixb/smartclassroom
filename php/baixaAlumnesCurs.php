<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();
if (isset($_POST['codisAlumnes'])) {
    $codisAlumnes = $_POST['codisAlumnes'];
    //establim la connexió
    $conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
    if ($conn->connect_error) {
        die($conn->connect_error);
    }
//triem el charset de la cerca
    mysqli_set_charset($conn, "utf8");


    for ($x = 0; $x < count($codisAlumnes); $x++) {
        //esborrem, en primer lloc, els grups on l'alumne està inclós
        $query = "delete ga24_grups_profes_det from ga23_grups_profes_cap,ga24_grups_profes_det where ga24_codi_alumne=" . $codisAlumnes[$x]
                . " and ga23_codi_grup=ga24_codi_grup and ga23_curs=" . $_SESSION['curs_actual'];
     

        $conn->query($query);

        //ara donem de baixa l'alumne del curs
        $query = "delete from ga12_alumnes_curs where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_id_alumne=" . $codisAlumnes[$x];
       

        $conn->query($query);
    }
}
