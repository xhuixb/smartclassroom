<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../../vendor/autoload.php';

if ($_FILES["file"]["name"] != '') {

    // move_uploaded_file($_FILES["file"]["tmp_name"],'../../pdf/xx');


    $handle = new upload($_FILES["file"]);
    //$nomFitxer = $_POST['nomFitxer'];
    $codiProf = $_POST['codiProf'];
    $contador = $_POST['contador'];
    $nomFitxer = $_FILES["file"]["name"];

    $path_parts = pathinfo($nomFitxer);

    $nomSenseExt = $path_parts['filename'];


    if ($handle->uploaded) {
        //echo 'p' . $codiProf . $nomSenseExt;
        $handle->file_new_name_body = 'p' . $codiProf .'_'. $contador;
        $handle->file_overwrite = true;

        $handle->process('../../pdf/provisional/');
        if ($handle->processed) {
            echo '<p class="btn-success">El fitxer ha estat pujat amb èxit</p>';
            $handle->clean();
        } else {
            echo 'error : ' . $handle->error;
        }
    }
}
