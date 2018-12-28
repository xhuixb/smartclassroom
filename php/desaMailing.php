<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();
//$_SESSION['curs_actual'] = 3;
//$_SESSION['prof_actual'] = 0;
$alumnesString = $_POST['alumnesString'];
$cosMissatge = $_POST['cosMissatge'];
$nomsFitxerString=$_POST['nomsFitxerString'];

$missatgeSenseCometes = str_replace("'", "''", $cosMissatge);

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


$query = "INSERT INTO ga30_comunicacions (ga30_curs,ga30_codi_prof,ga30_alumnes,ga30_missatge,ga30_adjunts,ga30_estat, ga30_data_mailing)"
        . " VALUES (" . $_SESSION['curs_actual'] . "," . $_SESSION['prof_actual'] . ",'" . $alumnesString . "','" . $missatgeSenseCometes . "','".$nomsFitxerString."','0',date(now()))";

//echo $query;

$conn->query($query);

//obtenim el codi de mailing que acabem de crear
$query = "select max(ga30_id) as noumailing from ga30_comunicacions";

//executem la query
$result = $conn->query($query);

if (!$result)
    die($conn->error);

$row = $result->fetch_assoc();

$nouMailing = $row['noumailing'];


$result->close();


//movem els fitxers adjunts
//esborrem els fitxers antics
$files = glob('../pdf/provisional/p' . $_SESSION['prof_actual'] . '_*.pdf'); // get all file names
$cont = 1;
$fitxersNous=[];
foreach ($files as $file) { // iterate files
    if (is_file($file)) {
        //obtenim el nom
        $nomFitxer = basename($file);
        copy($file, '../pdf/mailings/m'.$nouMailing.'_'.$cont.'.pdf');    
        $fitxersNous[$cont]='m'.$nouMailing.'_'.$cont.'.pdf';
        $cont++;
        
    }
}

$fitxersNousJoin= join('<#>', $fitxersNous);
//modifico el mailing amb els noms del fitxers que s'ha desat realment


$query="update ga30_comunicacions set ga30_adjunts='".$fitxersNousJoin."' where ga30_id=".$nouMailing;

//executem la consulta
$conn->query($query);

$conn->close();