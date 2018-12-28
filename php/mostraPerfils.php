<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$nomHtml = $_POST['nomHtml'];

//anem a buscar els perfils habilitats d'aquesta opció de menu
$xml = simplexml_load_file("../xml/configuracio/mainMenu.xml") or die("No es pot crear l'objecte");

$nodeNomHtml = $xml->xpath("//menuOption[@referencia='" . $nomHtml . "']");
if (count($nodeNomHtml) > 0) {
    //és un menu option
    $nodeStringHtml = $nodeNomHtml[0]->attributes()[1];
} else {
    $nodeNomHtml = $xml->xpath("//menuItem[@referencia='" . $nomHtml . "']");
    if (count($nodeNomHtml) > 0) {
        //es un menu item
        $nodeStringHtml = $nodeNomHtml[0]->attributes()[1];
    } else {
        //es un submenuitem
        $nodeNomHtml = $xml->xpath("//subMenuItem[@referencia='" . $nomHtml . "']");
        $nodeStringHtml = $nodeNomHtml[0]->attributes()[1];
    }
}

//convertim els permisos en un array
$nodeArrayHtml = explode('-', $nodeStringHtml);



//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


$query = "select ga39_codi_perfil as codiperfil, ga39_nom_perfil as nomperfil from ga39_perfils_usuaris";

$result = $conn->query($query);

echo '<table id="taulaPerfilsPossibles" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th class="col-sm-2">Traspàs</th>';
echo '<th class="col-sm-3">Perfils possibles</th>';
echo '</tr>';
echo '</thead>';

if (!$result)
    die($conn->error);

if ($result->num_rows > 0) {
    echo '<tbody id="costaulaPerfilsPossibles">';

    while ($row = $result->fetch_assoc()) {
        if (array_search($row['codiperfil'], $nodeArrayHtml) === false) {
            echo '<tr>';
            echo '<td class="col-sm-2"><input type="checkbox" class="permisosTraspas"/></td>';
            echo '<td class="col-sm-3" data-codiperfil="'.$row['codiperfil'].'">' . $row['nomperfil'] . '</td>';
            echo '</tr>';
        }
    }
    echo '</tbody>';
}
echo '</table>';

$result->close();
$conn->close();
