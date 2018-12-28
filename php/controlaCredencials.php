<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../classes/Databases.php';

session_start();

$urlRef = $_POST['urlRef'];

if (isset($_SESSION['prof_actual'])) {

    //anem a buscar el caption del menu
    $xml = simplexml_load_file("../xml/configuracio/mainMenu.xml") or die("No es pot crear l'objecte");
    //$xml = simplexml_load_string("../xml/configuracio/provaCarrega.xml") or die("No es pot crear l'objecte");
    $node1 = $xml->xpath("//menuOption[@referencia='" . $urlRef . "']");
    $node2 = $xml->xpath("//menuItem[@referencia='" . $urlRef . "']");
    $node3 = $xml->xpath("//subMenuItem[@referencia='" . $urlRef . "']");

    if (count($node1) > 0) {
        $node1Caption = $node1[0]->attributes()['caption'];
    } else {
        $node1Caption = '';
    }

    if (count($node2) > 0) {
        //anem a buscar el pare
        $pareNode2=$node2[0]->xpath("..");
      
        
        $node2Caption = $pareNode2[0]->attributes()['caption'].' > '.$node2[0]->attributes()['caption'];
    } else {
        $node2Caption = '';
    }

    if (count($node3) > 0) {
        $pareNode3=$node3[0]->xpath("..");
        $pareNode3Caption=$pareNode3[0]->attributes()['caption'];
        $aviNode3=$pareNode3[0]->xpath("..");
        $aviNode3Caption=$aviNode3[0]->attributes()['caption'];
        
        $node3Caption = $aviNode3Caption.' > '.$pareNode3Caption.' > '.$node3[0]->attributes()['caption'];
    } else {
        $node3Caption = '';
    }

    if ($node1Caption != '') {
        $caption = $node1Caption;
    } elseif ($node2Caption != '') {
        $caption = $node2Caption;
    } elseif($node3Caption != '') {
        $caption = $node3Caption;
    }else{
        $caption="Principal";
    }

    //establim la connexió
    $conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
    if ($conn->connect_error)
        die($conn->connect_error);

    //triem el charset de la cerca
    mysqli_set_charset($conn, "utf8");

    //anem a buscar els perfils
    $query = "select ga39_nom_perfil as perfil from ga39_perfils_usuaris,ga40_perfils_usuaris_rel where ga39_codi_perfil=ga40_codi_perfil and ga40_codi_usuari=" . $_SESSION['prof_actual'];

    $result = $conn->query($query);


    if (!$result)
        die($conn->error);

    $perfil = [];
    $cont = 0;

    if (!$result)
        die($conn->error);

    if ($result->num_rows > 0) {
        // desen els perfils del profe en un array

        while ($row = $result->fetch_assoc()) {
            $perfil[$cont] = $row['perfil'];
            $cont++;
        }
    }



    echo '<div class="container-fluid">';
    echo '<div class="row">';
    echo '<div class="col-sm-10" >';
    echo '<h4 id="dadesCredencials" data-codi-prof="' . $_SESSION['prof_actual'] . '" data-admin="' . $_SESSION['admin'] . '"><strong>Curs: </strong>' . $_SESSION['nom_curs_actual'] . '<strong>&emsp;Docent: </strong>' . $_SESSION['nom_prof_actual'] . '</h4>';
    echo '<h4>' . '<strong>Perfil: </strong>' . join('-', $perfil) . '</h4>';
    echo '<h4><strong>Menu: </strong>' . $caption . ' <a href="main.html"><span class="glyphicon glyphicon-log-out"></span></a></h4>';
    echo '</div>';

    echo '<div class="col-sm-2">';
    echo '<img class="img-responsive" src="imatges/logo_Rocagrossa.jpg" width="150">';
    echo '</div>';

    echo '</div>';
    echo '</div>';
} else {
    echo 'forbidden';
    //echo $_SESSION['prof_actual'];
}