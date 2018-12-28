<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
require '../classes/GestioMails.php';

//fem la connexió

session_start();
//$_SESSION['curs_actual'] = 3;
//$_SESSION['prof_actual'] = 0;

$nivell = $_POST['nivell'];
$grup = $_POST['grup'];
$hora = $_POST['hora'];
$dia = $_POST['dia'];
$profSubstituit = $_POST['profSubstituit'];
$nivellNom = $_POST['nivellNom'];
$grupNom = $_POST['grupNom'];
$nomProfe = $_POST['nomProfe'];


//els arrays
if (isset($_POST['alumnes'])) {
    //vol dir que hi ha alumnes
    $alumnes = $_POST['alumnes'];
    $alumnesNom = $_POST['alumnesNom'];
    $faltes = $_POST['faltes'];
    $tipusFaltaNom = $_POST['tipusFaltaNom'];
    $faltesmotius = $_POST['faltesmotius'];
    $checkPres = $_POST['checkPres'];
    $checkAbs = $_POST['checkAbs'];
    $checkRet = $_POST['checkRet'];
    $avisResponsables = $_POST['avisResponsables'];
    $avisTutors = $_POST['avisTutors'];
    $checkComunica = $_POST['checkComunica'];
}
$diaFormSql = date("Y-m-d", strtotime($dia));

//fem la connexió a la base de dades per totes les consultes que ens caldran
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

if ($profSubstituit == "") {
    $profeAssist = $_SESSION['prof_actual'];
} else {
    $profeAssist = $profSubstituit;
}


//esborrem les faltes d'ordre anteriors d'aquesta assistència
$query = "delete from ga31_faltes_ordre"
        . " where ga31_codi_curs=" . $_SESSION['curs_actual'] . " and "
        . "ga31_codi_professor=" . $profeAssist . " and "
        . "ga31_dia='" . $diaFormSql . "' and "
        . "ga31_hora_inici ='" . $hora . "' and "
        . "ga31_es_sessio=1";

//executem la query
$conn->query($query);

//esborrem l'assistència anterior detall
$query = "delete from ga15_cont_presencia "
        . "where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and "
        . "ga15_codi_professor=" . $profeAssist. " and "
        . "ga15_dia='" . $diaFormSql . "' and "
        . "ga15_hora_inici ='" . $hora . "'";

//executem la query
$conn->query($query);

////esborrem l'assistència anterior capçalera

$query = "delete from ga28_cont_presencia_cap "
        . "where ga28_codi_curs=" . $_SESSION['curs_actual'] . " and "
        . "ga28_professor=" . $profeAssist . " and "
        . "ga28_dia='" . $diaFormSql . "' and "
        . "ga28_hora='" . $hora . "'";

//executem la query
$conn->query($query);

//creem la capçalera de l'assitència nova
if (isset($_POST['alumnes'])) {

    if ($profSubstituit == "") {
        $query = "insert into ga28_cont_presencia_cap (ga28_codi_curs,ga28_professor,ga28_dia,ga28_hora) values "
                . "(" . $_SESSION['curs_actual'] . "," . $_SESSION['prof_actual'] . ",'" . $diaFormSql . "','" . $hora . "')";
    } else {
        $query = "insert into ga28_cont_presencia_cap (ga28_codi_curs,ga28_professor,ga28_dia,ga28_hora,ga28_is_guardia,ga28_prof_substituit) values "
                . "(" . $_SESSION['curs_actual'] . "," . $profSubstituit . ",'" . $diaFormSql . "','" . $hora . "','1'," . $_SESSION['prof_actual'] . ")";
    }




    $conn->query($query);
}
$mailSi = 0;

if (isset($_POST['alumnes'])) {
//creem la nova assistència
    //inicialitzem les comunicacions
    if ($profSubstituit == "") {
        $profeAssist = $_SESSION['prof_actual'];
    } else {
        $profeAssist = $profSubstituit;
    }

    for ($x = 0; $x < count($alumnes); $x++) {
        if ($faltes[$x] !== "") {
            //l'alumne té faltes d'ordre
            //substituiem els apostrofs

            $faltesmotius[$x] = str_replace("'", "''", $faltesmotius[$x]);
            $query = "insert into ga15_cont_presencia values(" . $_SESSION['curs_actual'] . "," . $alumnes[$x] . "," . $profeAssist . ",'" . $diaFormSql . "','" . $hora . "','" . $checkPres[$x] . "','" . $checkAbs[$x] . "','" . $checkRet[$x] . "','0','',now(),"
                    . "" . $faltes[$x] . ",1,'" . $faltesmotius[$x] . "',null,'" . $avisTutors[$x] . "','" . $avisResponsables[$x] . "','" . $checkComunica[$x] . "')";

            //inserim la falta d'ordre a la taula cooresponent
            $query2 = "INSERT INTO ga31_faltes_ordre (ga31_codi_curs,ga31_alumne,ga31_codi_professor,ga31_dia,ga31_hora_inici,ga31_tipus_falta,ga31_estat,ga31_motiu,ga31_es_sessio,ga31_just_tutor,ga31_just_resp,ga31_assignatura)"
                    . " VALUES (" . $_SESSION['curs_actual'] . "," . $alumnes[$x] . "," . $profeAssist . ",'" . $diaFormSql . "','" . $hora . "'," . $faltes[$x] . ",1,'" . $faltesmotius[$x] . "',1,'" . $avisTutors[$x] . "','" . $avisResponsables[$x] . "',null)";
            //anem a veure si enviem correu electrònic
            if ($avisResponsables[$x] != "0" || $avisTutors[$x] != "0") {
                if ($avisResponsables[$x] != "0" && $avisTutors[$x] == "0") {
                    $mailSi++;
                } elseif ($avisResponsables[$x] == "0" && $avisTutors[$x] != "0") {
                    $mailSi++;
                } else {
                    $mailSi += 2;
                }
            }
            $conn->query($query);
            $conn->query($query2);
        } else {

            //l'alumne no té faltes d'ordre
            $query = "insert into ga15_cont_presencia values(" . $_SESSION['curs_actual'] . "," . $alumnes[$x] . "," . $profeAssist . ",'" . $diaFormSql . "','" . $hora . "','" . $checkPres[$x] . "','" . $checkAbs[$x] . "','" . $checkRet[$x] . "','0','',now(),null,null,null,null,'0','0','" . $checkComunica[$x] . "')";
            $conn->query($query);
        }
    }
}

$conn->close();
echo '<br>';
echo '<div class="alert alert-success alert-dismissable fade in">';
echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
echo '<strong>Èxit!</strong> Asssitència actualitzada correctament.</div>';
echo '<div class="alert alert-warning alert-dismissable fade in">';
echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
echo 'Es faran: ' . $mailSi . ' comunicacions </div>';

