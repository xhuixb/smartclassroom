<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$dia = $_POST['dia'];
$hora = $_POST['hora'];
$profe = $_POST['profe'];
$horesReplicar = $_POST['horesReplicar'];

for ($i = 0; $i < count($horesReplicar); $i++) {
    //creem la capçalera
    $query = "INSERT INTO ga28_cont_presencia_cap"
            . "(ga28_codi_curs,"
            . "ga28_professor,"
            . "ga28_dia,"
            . "ga28_hora,"
            . "ga28_is_guardia,"
            . "ga28_prof_substituit,"
            . "ga28_aula,"
            . "ga28_nivell,"
            . "ga28_grup,"
            . "ga28_tipus_grup,"
            . "ga28_assignatura,"
            . "ga28_coment_general,"
            . "ga28_estat)"
            . "select ga28_codi_curs,ga28_professor,ga28_dia,'" . $horesReplicar[$i] . "',ga28_is_guardia,ga28_prof_substituit,ga28_aula,ga28_nivell,ga28_grup,ga28_tipus_grup,ga28_assignatura,ga28_coment_general,ga28_estat from ga28_cont_presencia_cap"
            . " where ga28_codi_curs=" . $_SESSION['curs_actual'] . " and ga28_professor=" . $profe . " and ga28_dia='" . $dia . "' and ga28_hora='" . $hora . "'";

    $result = $conn->query($query);


    if (!$result)
        die($conn->error);

    $query = "INSERT INTO ga15_cont_presencia"
            . "(ga15_codi_curs,"
            . "ga15_alumne,"
            . "ga15_codi_professor,"
            . "ga15_dia,"
            . "ga15_hora_inici,"
            . "ga15_check_present,"
            . "ga15_check_absent,"
            . "ga15_check_retard,"
            . "ga15_check_justificat,"
            . "ga15_motiu_justificat,"
            . "ga15_data_hora_darrera_mod,"
            . "ga15_tipus_falta,"
            . "ga15_estat,"
            . "ga15_motiu,"
            . "ga15_num_falta,"
            . "ga15_just_tutor,"
            . "ga15_just_resp,"
            . "ga15_check_comunica,"
            . "ga15_comentari)"
            . "SELECT ga15_codi_curs,"
            . "ga15_alumne,"
            . "ga15_codi_professor,"
            . "ga15_dia,"
            . "'" . $horesReplicar[$i] . "',"
            . "ga15_check_present,"
            . "ga15_check_absent,"
            . "ga15_check_retard,"
            . "ga15_check_justificat,"
            . "ga15_motiu_justificat,"
            . "now(),"
            . "ga15_tipus_falta,"
            . "ga15_estat,"
            . "ga15_motiu,"
            . "ga15_num_falta,"
            . "ga15_just_tutor,"
            . "ga15_just_resp,"
            . "ga15_check_comunica,"
            . "ga15_comentari"
            . " FROM ga15_cont_presencia"
            . " WHERE ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_codi_professor=" . $profe . " and ga15_dia='" . $dia . "' and ga15_hora_inici='" . $hora . "'";

    $result = $conn->query($query);


    if (!$result)
        die($conn->error);
}

echo count($horesReplicar);

$conn->close();
