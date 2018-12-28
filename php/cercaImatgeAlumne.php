<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$codiAlumne = $_POST['codiAlumne'];
$nomComplet=$_POST['nomComplet'];

//anem a buscar la imatge
$files = glob("../imatges/alumnes/" . $codiAlumne . ".*");

echo '<h4 id="alumneImatge" data-codi-alumne="'.$codiAlumne.'">'.$nomComplet.'</h4>';

if (count($files) > 0) {

    echo '<img id="codiImatgeAlumne" src="' .substr($files[0],3) . '" width="180 px" height="180 px"/>';
} else {

    echo '<img id="codiImatgeAlumne" src="imatges/alumnes/avatar.png" width="180 px" height="180 px"/>';
}
echo '<p>Selecciona la imatge de perfil</p>';
echo '<input type="file" name="fileToUpload" id="fileToUpload" accept="image/*">';
echo '<button type="button" class="btn btn-success form-control" onclick="uploadImage()" >Desa</button>';
echo '<button type="button" class="btn btn-danger form-control" onclick="esborraImage()" >Esborra</button>';
