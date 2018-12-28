<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../../vendor/autoload.php';

if ($_FILES["file"]["name"] != '') {

    $codiAlumne = $_POST['codiAlumne'];
    $nomComplet = $_POST['nomComplet'];

    $handle = new upload($_FILES["file"]);
    $nomFitxer=$_FILES["file"]["name"];


    if ($handle->uploaded) {
        $handle->file_new_name_body = $codiAlumne;
        $handle->image_resize = true;
        $handle->image_x = 180;
        $handle->image_y = 180;
        $handle->file_overwrite = true;
        $handle->image_convert = 'JPG';
        //$handle->image_ratio_y = true;
        $handle->process('../../imatges/alumnes/');
        if ($handle->processed) {
            echo '<h4 id="alumneImatge" data-codi-alumne="'.$codiAlumne.'">'.$nomComplet.'</h4>';
            //echo '<p>'.$codiAlumne.'file uploaded</p>';
            echo '<img id="codiImatgeAlumne" src="imatges/alumnes/'.$handle->file_dst_name.'" width="180 px" height="180 px"/>';
            echo '<p>Selecciona la imatge de perfil</p>';
            echo '<input type="file" name="fileToUpload" id="fileToUpload" accept="image/*">';
            echo '<button type="button" class="btn btn-success form-control" onclick="uploadImage()" >Desa</button>';
            echo '<button type="button" class="btn btn-danger form-control" onclick="esborraImage()" >Esborra</button>';

            $handle->clean();
        } else {
            echo 'error : ' . $handle->error;
        }
        
    }
}
