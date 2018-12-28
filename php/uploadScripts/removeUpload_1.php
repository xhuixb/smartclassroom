<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$codiAlumne = $_POST['codiAlumne'];
$nomComplet = $_POST['nomComplet'];

$directori = '../../imatges/alumnes/';
//busquem el fitxers
$files = glob("../../imatges/alumnes/" . $codiAlumne . ".*");

//esborrem el primer perquè no n'hi pot haver cap més
if (count($files) > 0) {
    unlink($files[0]);
}
//unlink($directori . $codiAlumne . '.JPG');
//reconstruim el div

echo '<h4 id="alumneImatge" data-codi-alumne="' . $codiAlumne . '">' . $nomComplet . '</h4>';
//echo '<p>'.$codiAlumne.'file uploaded</p>';
echo '<img id="codiImatgeAlumne" src="imatges/alumnes/avatar.png" width="180 px" height="180 px"/>';
echo '<p>Selecciona la imatge de perfil</p>';
echo '<input type="file" name="fileToUpload" id="fileToUpload" accept="image/*">';
echo '<button type="button" class="btn btn-success form-control" onclick="uploadImage()" >Desa</button>';
echo '<button type="button" class="btn btn-danger form-control" onclick="esborraImage()" >Esborra</button>';

