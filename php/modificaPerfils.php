<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$perfilsNousString = $_POST['perfilsNousString'];
$nivellMenu = $_POST['nivellMenu'];
$nomHtml = $_POST['nomHtml'];


//anem a buscar els perfils habilitats d'aquesta opció de menu
$xml = simplexml_load_file("../xml/configuracio/mainMenu.xml") or die("No es pot crear l'objecte");

if ($nivellMenu === '0') {
    //menu option
    $node = $xml->xpath("//menuOption[@referencia='" . $nomHtml . "']");
    $node[0]->attributes()['permis'] = $perfilsNousString;
    $xml->saveXML('../xml/configuracio/mainMenu.xml');
}

if ($nivellMenu === '1') {
    //menu item

    $node = $xml->xpath("//menuItem[@referencia='" . $nomHtml . "']");
    $node[0]->attributes()['permis'] = $perfilsNousString;
    $xml->saveXML('../xml/configuracio/mainMenu.xml');
}

if ($nivellMenu === '2') {
    //submenuItem 
    $node = $xml->xpath("//subMenuItem[@referencia='" . $nomHtml . "']");
    $node[0]->attributes()['permis'] = $perfilsNousString;
    $xml->saveXML('../xml/configuracio/mainMenu.xml');
}