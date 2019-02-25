<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$nouEstat = $_POST['nouEstat'];
$codiProf = $_POST['codiProf'];
$dataCanviEstat = $_POST['dataCanviEstat'];


$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//actualitzem els registres d'activitat
if ($nouEstat == '0') {
    //ara està actiu
    //s'ha de crear un nou registre
    //comprovem abans que la data sigui posterior a l'anterior registre
    $query = "select count(*) as conta from ga42_registre_activitat where ga42_professor=" . $codiProf . " and ga42_data_inici>='" . $dataCanviEstat . "'";
    $result = $conn->query($query);


    if (!$result)
        die($conn->error);

    $row = $result->fetch_assoc();

    if ($row['conta'] == '0') {
        //no hi ha cap data d'inici superior es pot procedir a l'activació
        $query = "update ga04_professors set ga04_suspes='" . $nouEstat . "' where ga04_codi_prof=" . $codiProf;

        $conn->query($query);
        //creem el nou registre d'activitat
        $query = "insert into ga42_registre_activitat values (" . $codiProf . ",'" . $dataCanviEstat . "',null)";

        $conn->query($query);
    } else {
        //la data d'activaciò no és correcta
        echo '1';
    }
} else {
    //ara està inactiu
    //comprovem que la data de fi és posterior o igual a la data d'inici
    $query = "select count(*) as conta from ga42_registre_activitat where ga42_professor=" . $codiProf . " and ga42_data_fi is null and ga42_data_inici<='" . $dataCanviEstat . "'";
    
    $result = $conn->query($query);


    if (!$result)
        die($conn->error);

    $row = $result->fetch_assoc();

    if ($row['conta'] == '1') {
        //si troba un registre podem fer el canvi d'estat
        $query = "update ga04_professors set ga04_suspes='" . $nouEstat . "' where ga04_codi_prof=" . $codiProf;

        $conn->query($query);
        
        $query = "update ga42_registre_activitat set ga42_data_fi='" . $dataCanviEstat . "' where ga42_professor=" . $codiProf . " and ga42_data_fi is null";
         $conn->query($query);
        
    }else{
        //la data de suspensió no és correcta
        echo '2';
    }

   
}



$conn->close();
