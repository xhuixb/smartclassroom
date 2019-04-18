<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$codiProf = $_POST['codiProf'];
$nomsFitxers = explode("<#>", $_POST['nomsFitxers']);

$directori = "../../pdf/provisional/";

for ($i = 0; $i < count($nomsFitxers); $i++) {
    //esborrem els fitxers del servidor
    
    $fitxer = $directori . 'p' . $codiProf .'_'. $nomsFitxers[$i].'.pdf';
    unlink($fitxer);
     echo '<p class="btn-success">El fitxer ha estat esborrat amb èxit</p>';
}

echo '<p class="btn-success">El fitxers han estat esborrats amb èxit</p>';