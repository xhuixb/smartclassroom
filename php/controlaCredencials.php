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
        $pareNode2 = $node2[0]->xpath("..");


        $node2Caption = $pareNode2[0]->attributes()['caption'] . ' > ' . $node2[0]->attributes()['caption'];
    } else {
        $node2Caption = '';
    }

    if (count($node3) > 0) {
        $pareNode3 = $node3[0]->xpath("..");
        $pareNode3Caption = $pareNode3[0]->attributes()['caption'];
        $aviNode3 = $pareNode3[0]->xpath("..");
        $aviNode3Caption = $aviNode3[0]->attributes()['caption'];

        $node3Caption = $aviNode3Caption . ' > ' . $pareNode3Caption . ' > ' . $node3[0]->attributes()['caption'];
    } else {
        $node3Caption = '';
    }

    if ($node1Caption != '') {
        $caption = $node1Caption;
    } elseif ($node2Caption != '') {
        $caption = $node2Caption;
    } elseif ($node3Caption != '') {
        $caption = $node3Caption;
    } else {
        $caption = "Principal";
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


    //anem a buscar les notificacions
    //primer els aniversaris
    $query = "select count(*) as conta from ga04_professors,ga17_professors_curs"
            . " where ga17_codi_curs=" . $_SESSION['curs_actual'] . " and ga04_suspes='0' and ( (month(ga04_data_naixement)=month(now()) and day(ga04_data_naixement)=day(now())) or (month(ga04_data_naixement)=month(now()- INTERVAL 1 DAY) and day(ga04_data_naixement)=day(now()- INTERVAL 1 DAY)) or (month(ga04_data_naixement)=month(now()+ INTERVAL 1 DAY) and day(ga04_data_naixement)=day(now()+ INTERVAL 1 DAY))  )"
            . " and ga04_codi_prof=ga17_codi_professor";


    $result = $conn->query($query);


    if (!$result)
        die($conn->error);

    $row = $result->fetch_assoc();

    $conta = (int) $row['conta'];

    //ara les dates clau
    $query = "select count(*) as conta from ga41_dates_clau where ga41_curs=".$_SESSION['curs_actual']." and ga41_data_inici_publi<=date(now()) and ga41_data_fi_publi>=date(now()) ";

    $result = $conn->query($query);


    if (!$result)
        die($conn->error);

    $row = $result->fetch_assoc();

    $conta += (int) $row['conta'];


    if ($conta === 0) {
        $colorButton = "btn-info";
        $textButton = "Informacions(0)";
    } else {
        $colorButton = "btn-success";
        $textButton = "Informacions(" . $conta . ")";
    }


    echo '<div class="container-fluid">';
    echo '<div class="row">';
    echo '<div class="col-sm-5" >';
    echo '<h4 id="dadesCredencials" data-uploadsize="'.ini_get('upload_max_filesize').'" data-postsize="'.ini_get('post_max_size').'" data-codi-prof="' . $_SESSION['prof_actual'] . '" data-admin="' . $_SESSION['admin'] . '"><strong>Curs: </strong>' . $_SESSION['nom_curs_actual'] . '<strong>&emsp;Docent: </strong>' . $_SESSION['nom_prof_actual'] . '</h4>';
    echo '<h4>' . '<strong>Perfil: </strong>' . join('-', $perfil) . '</h4>';
    echo '<h4><strong>Menu: </strong>' . $caption . ' <a href="main.html"><span class="glyphicon glyphicon-log-out"></span></a></h4>';
    echo '</div>';
    echo '<div class="col-sm-2">';
    echo '<button type="button" class="btn ' . $colorButton . ' form-control" data-toggle="modal" data-target="#notificacionsModal" onclick="carregaNotificacions(this);">';
    echo '<span class="glyphicon glyphicon-info-sign">' . $textButton . '</span>';
    echo '</button>';
    echo '</div>';
    echo '<div class="col-sm-2 col-sm-offset-3">';
    echo '<img class="img-responsive" src="imatges/logo_Rocagrossa.jpg" width="150">';
    echo '</div>';

    echo '</div>';
    echo '</div>';

    echo '<div id="notificacionsModal" class="modal fade" role="dialog"  data-backdrop="static" data-keyboard="false">';
    echo '<div class="modal-dialog modal-lg">';


    echo '<div class="modal-content" >';
    echo '<div class="modal-header">';
    echo '<button type="button" class="close" data-dismiss="modal">&times;</button>';
    echo '<h3 class="modal-title">Noficacions</h3>';
    echo '</div>';
    echo '<div class="modal-body">';
    echo '<div class="container-fluid">';
    echo '<div class="row">';
    echo '<div id="summernoteNotifi"><p>Edita el text</p></div>';

    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '<div class="modal-footer">';

    echo '<button type="button" class="btn btn-default" data-dismiss="modal">Tanca</button>';
    echo '</div>';
    echo '</div>';

    echo '</div>';
    echo '</div>';

    //
    $conn->close();
} else {
    echo 'forbidden';
    //echo $_SESSION['prof_actual'];
}


