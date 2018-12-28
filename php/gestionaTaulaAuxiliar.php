<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/MantenimentBasic.php';

$opcio = $_POST['opcio'];

switch ($opcio) {
    case '0';
        carregaTaulaAuxiliar();
        break;
    case '1';
        desaTaulaAuxiliar();
        break;
    case '2';
        esborraTaulaAuxiliar();
        break;
    case '3';
        construeixDetallDrop();
        break;
    default :
}

function carregaTaulaAuxiliar() {


    $taula = $_POST['taula'];
    $camps = $_POST['camps'];
    $amplades = $_POST['amplades'];
    $caption = $_POST['caption'];
    $campPrimari = $_POST['campPrimari'];
    $descr = $_POST['descr'];
    $tipusCamp = $_POST['tipusCamp'];
    //per substituir l'escapatori per la coma
    
    $dadesForana = str_replace('%2C', ',', $_POST['dadesForana']);
    $div = $_POST['div'];
    $campsCondicio = $_POST['campsCondicio'];
    $valorsCondicio = $_POST['valorsCondicio'];
    $ordenacio=$_POST['ordenacio'];

    MantenimentBasic::carregaTaula($taula, $camps, $caption, $amplades, $campPrimari, $descr, $tipusCamp, $dadesForana, $div, $campsCondicio, $valorsCondicio,$ordenacio);
}

function desaTaulaAuxiliar() {


    $mode = $_POST['mode'];
    $taula = $_POST['taula'];
    $camps = $_POST['camps'];
    $valors = $_POST['valors'];
    $campPrimari = $_POST['campPrimari'];
    $clauPrimaria = $_POST['clauPrimaria'];

    $resposta = MantenimentBasic::actualitzaTaula($mode, $taula, $camps, $valors, $campPrimari, $clauPrimaria);

    //echo $resposta;
    
    if ($resposta === false) {
        echo '0';
    } else {
        echo '1';
    }
}

function construeixDetallDrop() {
    $foranaTaula = $_POST['foranaTaula'];
    $foranaId = $_POST['foranaId'];
    $foranaCamp = $_POST['foranaCamp'];
    $caption = $_POST['caption'];
    $codiCamp = $_POST['codiCamp'];
    $dadaCamp = $_POST['dadaCamp'];
    $div = $_POST['div'];


    MantenimentBasic::construeixDetallDrop($foranaTaula, $foranaId, $foranaCamp, $caption, $codiCamp, $dadaCamp, $div);
}

function esborraTaulaAuxiliar() {
    $taula = $_POST['taula'];
    $codisEsborrat = $_POST['codisEsborrat'];
    $campPrimari = $_POST['campPrimari'];

    $resposta = MantenimentBasic::esborraTaula($taula, $codisEsborrat, $campPrimari);

    if ($resposta === false) {
        echo '0';
    } else {
        echo '1';
    }
}
