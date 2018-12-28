<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

session_start();

$alumne = $_GET['alumne'];

//ens connectem a la base de dades
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");


//creem el document xml del tutor
$xmlFitxaAlumne = new DOMDocument('1.0', 'utf-8');


$xmlFitxaAlumne->preserveWhiteSpace = false;
$xmlFitxaAlumne->formatOutput = true;

$xslt = $xmlFitxaAlumne->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="../xslt/formataFitxaAlumne.xsl"');

$xmlFitxaAlumne->appendChild($xslt);

//creem l'arrel
$root = $xmlFitxaAlumne->createElement('alumne');
$xmlFitxaAlumne->appendChild($root);


//anem a buscarm, en primer lloc, les dades de l'alumne

$query = "select ga11_id_alumne as codialumne,ga11_nom as nomalumne,ga11_cognom1 as cognom1alumne,ga11_cognom2 as cognom2alumne, ga11_mail1 as mail1alumne,ga11_mail2 as mail2alumne,ga11_check_comunica as checkcomunica,"
        . "ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup,"
        . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors,ga29_tutors_curs,ga12_alumnes_curs where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga29_curs=ga12_codi_curs and ga12_id_alumne=codialumne and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga29_tutor=ga04_codi_prof) as nomtutor"
        . " from ga11_alumnes,ga06_nivell,ga07_grup,ga12_alumnes_curs"
        . " where ga12_codi_curs=".$_SESSION['curs_actual']." and ga12_id_alumne=" . $alumne . " and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and ga12_id_alumne=ga11_id_alumne";



//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);

//ens posem en les dades dels alumnes
$row = $result->fetch_assoc();

//recuperem les dades de l'alumne
$codiAlumne = $row['codialumne'];
$nomAlumne = $row['nomalumne'];
$cognom1Alumne = $row['cognom1alumne'];
$cognom2Alumne = $row['cognom2alumne'];
$nivell = $row['nivell'];
$grup = $row['grup'];
$mail1Alumne = $row['mail1alumne'];
$mail2Alumne = $row['mail2alumne'];
$checkComunica = $row['checkcomunica'];
$nomTutor = $row['nomtutor'];

//les posem al fitxer xml

$node = $xmlFitxaAlumne->createElement('codi', $codiAlumne);
$root->appendChild($node);

$node = $xmlFitxaAlumne->createElement('nom', $nomAlumne);
$root->appendChild($node);

$node = $xmlFitxaAlumne->createElement('cognom1', $cognom1Alumne);
$root->appendChild($node);

$node = $xmlFitxaAlumne->createElement('cognom2', $cognom2Alumne);
$root->appendChild($node);

$node = $xmlFitxaAlumne->createElement('nivell', $nivell);
$root->appendChild($node);

$node = $xmlFitxaAlumne->createElement('grup', $grup);
$root->appendChild($node);

$node = $xmlFitxaAlumne->createElement('mail1', $mail1Alumne);
$root->appendChild($node);

$node = $xmlFitxaAlumne->createElement('mail2', $mail2Alumne);
$root->appendChild($node);

$node = $xmlFitxaAlumne->createElement('tutor', $nomTutor);
$root->appendChild($node);

$node = $xmlFitxaAlumne->createElement('profsolici', $_SESSION['prof_actual']);
$root->appendChild($node);

if ($checkComunica == '1') {
    $node = $xmlFitxaAlumne->createElement('comunica', 'Sí');
} else {

    $node = $xmlFitxaAlumne->createElement('comunica', 'No');
}

$root->appendChild($node);


//anem a buscar la imatge
$files = glob("../imatges/alumnes/" . $codiAlumne . ".*");

if (count($files) > 0) {
    $imatge = $files[0];
    $node = $xmlFitxaAlumne->createElement('imatge', $imatge);
    $root->appendChild($node);
} else {
    $node = $xmlFitxaAlumne->createElement('imatge', "../imatges/alumnes/avatar.png");
    $root->appendChild($node);
}



//constriem el resul de faltes d'ordre

$query = "select ga22_nom_falta as tipusfalta, count(*)  as total from ga22_tipus_falta,ga31_faltes_ordre "
        . "where ga31_tipus_falta=ga22_codi_falta and ga31_alumne=" . $alumne . " and ga31_codi_curs=" . $_SESSION['curs_actual'] . " group by ga31_tipus_falta ";

//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $faltaOrdre = $xmlFitxaAlumne->createElement('faltaordre');

        $descrFalta = $xmlFitxaAlumne->createElement('descrfalta', $row['tipusfalta']);
        $faltaOrdre->appendChild($descrFalta);

        $numFaltes = $xmlFitxaAlumne->createElement('numfaltes', $row['total']);
        $faltaOrdre->appendChild($numFaltes);

        $root->appendChild($faltaOrdre);
    }
}

//busquem les faltes d'ordre de l'alumne
$query = "select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as professor, ga31_dia as dia,ga31_hora_inici as hora,ga22_nom_falta as tipusfalta,ga31_motiu as motiu"
        . " from ga04_professors,ga31_faltes_ordre,ga22_tipus_falta"
        . " where ga31_codi_professor=ga04_codi_prof and ga31_tipus_falta=ga22_codi_falta and ga31_alumne=" . $alumne . " and"
        . " ga31_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)"
        . " order by dia desc,hora asc";


//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);


if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $falta = $xmlFitxaAlumne->createElement('falta');
//afegim els elements

        $professor = $xmlFitxaAlumne->createElement('professor', $row['professor']);
        $falta->appendChild($professor);

        $dia = $xmlFitxaAlumne->createElement('dia', $row['dia']);
        $falta->appendChild($dia);

        $hora = $xmlFitxaAlumne->createElement('hora', $row['hora']);
        $falta->appendChild($hora);

        $tipusFalta = $xmlFitxaAlumne->createElement('tipusfalta', $row['tipusfalta']);
        $falta->appendChild($tipusFalta);

        $motiu = $xmlFitxaAlumne->createElement('motiu', $row['motiu']);
        $falta->appendChild($motiu);

//afegim la falta a l'arrel
        $root->appendChild($falta);
    }
}

//fem el resum de les absències

$query = "select substr(ga15_dia,1,4) as anyabsencia ,substr(ga15_dia,6,2) as mesabsencia,"
        . "(select count(*)  from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and ga15_check_absent='1' and substr(ga15_dia,6,2)=mesabsencia and substr(ga15_dia,1,4)=anyabsencia group by substr(ga15_dia,6,2),substr(ga15_dia,1,4) order by substr(ga15_dia,6,2) desc,substr(ga15_dia,1,4) desc) as totalabsencia,"
        . "(select count(*)  from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and ga15_check_absent='1' and ga15_check_justificat='1' and substr(ga15_dia,6,2)=mesabsencia and substr(ga15_dia,1,4)=anyabsencia group by substr(ga15_dia,6,2),substr(ga15_dia,1,4) order by substr(ga15_dia,6,2) desc,substr(ga15_dia,1,4) desc) as justificades,"
        . "(select count(*)  from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and ga15_check_absent='1' and ga15_check_justificat='0' and substr(ga15_dia,6,2)=mesabsencia and substr(ga15_dia,1,4)=anyabsencia group by substr(ga15_dia,6,2),substr(ga15_dia,1,4) order by substr(ga15_dia,6,2) desc,substr(ga15_dia,1,4) desc) as nojustificades,"
        . "(select count(*)  from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and ga15_check_retard='1' and substr(ga15_dia,6,2)=mesabsencia and substr(ga15_dia,1,4)=anyabsencia group by substr(ga15_dia,6,2),substr(ga15_dia,1,4)) as retards"
        . " from ga15_cont_presencia as prova where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and (ga15_check_absent='1' or ga15_check_retard='1') group by substr(ga15_dia,1,4),substr(ga15_dia,6,2) order by anyabsencia desc,mesabsencia desc;";


//$query = "select substr(ga15_dia,1,4) as anyabsencia ,substr(ga15_dia,6,2) mesabsencia,count(*) totalabsencia,"
//        . "(select count(*)  from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and ga15_check_absent='1' and ga15_check_justificat='1' and substr(ga15_dia,6,2)=mesabsencia and substr(ga15_dia,1,4)=anyabsencia group by substr(ga15_dia,6,2),substr(ga15_dia,1,4)) as justificades,"
//        . "(select count(*)  from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and ga15_check_absent='1' and ga15_check_justificat='0' and substr(ga15_dia,6,2)=mesabsencia and substr(ga15_dia,1,4)=anyabsencia group by substr(ga15_dia,6,2),substr(ga15_dia,1,4)) as nojustificades"
//        . " from ga15_cont_presencia as prova where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and ga15_check_absent='1' group by substr(ga15_dia,6,2),substr(ga15_dia,1,4) order by anyabsencia,mesabsencia;";
//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);


if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {
        switch ($row['mesabsencia']) {
            case '01':
                $mesText = 'Gener';
                break;
            case '02':
                $mesText = 'Febrer';
                break;
            case '03':
                $mesText = 'Març';
                break;
            case '04':
                $mesText = 'Abril';
                break;
            case '05':
                $mesText = 'Maig';
                break;
            case '06':
                $mesText = 'Juny';
                break;
            case '07':
                $mesText = 'Juliol';
                break;
            case '08':
                $mesText = 'Agost';
                break;
            case '09':
                $mesText = 'Setembre';
                break;
            case '10':
                $mesText = 'Octubre';
                break;
            case '11':
                $mesText = 'Novembre';
                break;
            case '12':
                $mesText = 'Desembre';
                break;
        }
        $absenciaResum = $xmlFitxaAlumne->createElement('absenciaresum');

        $anyResum = $xmlFitxaAlumne->createElement('any', $row['anyabsencia']);
        $absenciaResum->appendChild($anyResum);

        $mesResum = $xmlFitxaAlumne->createElement('mes', $mesText);
        $absenciaResum->appendChild($mesResum);

        //si és null posem un 0
        if ($row['totalabsencia'] == '') {
            $totalAbsencia = 0;
        } else {
            $totalAbsencia = $row['totalabsencia'];
        }

        //si és null posem un 0
        if ($row['justificades'] == '') {
            $justificades = 0;
        } else {
            $justificades = $row['justificades'];
        }

        //si és null posem un 0
        if ($row['nojustificades'] == '') {
            $nojustificades = 0;
        } else {
            $nojustificades = $row['nojustificades'];
        }

        //si és null posem un 0
        if ($row['retards'] == '') {
            $retards = 0;
        } else {
            $retards = $row['retards'];
        }


        $totalResum = $xmlFitxaAlumne->createElement('total', $totalAbsencia);
        $absenciaResum->appendChild($totalResum);

        $justifiResum = $xmlFitxaAlumne->createElement('justifi', $justificades);
        $absenciaResum->appendChild($justifiResum);

        $nojustifiResum = $xmlFitxaAlumne->createElement('nojustifi', $nojustificades);
        $absenciaResum->appendChild($nojustifiResum);

        $retardsResum = $xmlFitxaAlumne->createElement('retards', $retards);
        $absenciaResum->appendChild($retardsResum);

        $root->appendChild($absenciaResum);
    }
}


//busquem les absències

$query = "select ga15_dia as dia,ga15_hora_inici as hora,ga15_check_absent as absent,ga15_check_retard as retard,ga15_check_justificat as checkjustificat from ga15_cont_presencia "
        . "where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and (ga15_check_absent='1' or ga15_check_retard='1') order by dia desc,hora asc";

$result = $conn->query($query);

if (!$result)
    die($conn->error);


$canviDia = false;
$dia = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        if ($row['dia'] == $dia) {
            //continuem amb el mateix alumne
            $canviDia = false;
        } else {
            //alumne nou
            $canviDia = true;

            //afegim la falta a l'arrel
            if ($dia != '') {
                $root->appendChild($absencia);
            }
            $dia = $row['dia'];
        }

        if ($canviDia == true) {
            $absencia = $xmlFitxaAlumne->createElement('absencia');

            $grup = $xmlFitxaAlumne->createElement('dia', $row['dia']);
            $absencia->appendChild($grup);

            $hores = $xmlFitxaAlumne->createElement('hores');
            $absencia->appendChild($hores);

            $hora = $xmlFitxaAlumne->createElement('hora', $row['hora']);
            $hores->appendChild($hora);

            $absenCheck = $xmlFitxaAlumne->createElement('checkabs', $row['absent']);
            $hores->appendChild($absenCheck);

            $justifi = $xmlFitxaAlumne->createElement('justifi', $row['checkjustificat']);
            $hores->appendChild($justifi);

            $retard = $xmlFitxaAlumne->createElement('retard', $row['retard']);
            $hores->appendChild($retard);
        } else {

            $hores = $xmlFitxaAlumne->createElement('hores');
            $absencia->appendChild($hores);

            $hora = $xmlFitxaAlumne->createElement('hora', $row['hora']);
            $hores->appendChild($hora);

            $absenCheck = $xmlFitxaAlumne->createElement('checkabs', $row['absent']);
            $hores->appendChild($absenCheck);

            $justifi = $xmlFitxaAlumne->createElement('justifi', $row['checkjustificat']);
            $hores->appendChild($justifi);

            $retard = $xmlFitxaAlumne->createElement('retard', $row['retard']);
            $hores->appendChild($retard);
        }
    }

    //penjo l'últim
    $root->appendChild($absencia);
}



//desem el document xml
$xmlFitxaAlumne->save('../xml/prof' . $_SESSION['prof_actual'] . 'al' . $alumne . '.xml');
//$xmlFitxaAlumne->save('../xml/fitxaAlumne.xml');
//tanquem el resultest i la connexió
$result->close();
$conn->close();
