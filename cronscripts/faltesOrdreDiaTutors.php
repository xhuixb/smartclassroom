<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
require '../classes/GestioMails.php';

//fem la connexió
//esborrem els fitxers del directori
$files = glob('../xml/reports' . date('Y-m-d') . '/*'); // get all file names
foreach ($files as $file) { // iterate files
    if (is_file($file))
        unlink($file); // delete file
}


if (file_exists('../xml/reports' . date('Y-m-d'))) {
    rmdir('../xml/reports' . date('Y-m-d'));
}
mkdir('../xml/' . $date = 'reports' . date('Y-m-d'));



$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//constuïm la query

$query = "select ga31_alumne as codialumne, concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup, concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as professor, ga31_dia as dia,ga31_hora_inici as hora,ga22_nom_falta as tipusfalta,ga31_motiu as motiu"
        . " from ga11_alumnes,ga04_professors,ga31_faltes_ordre,ga22_tipus_falta,ga07_grup,ga06_nivell,ga12_alumnes_curs"
        . " where ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne and ga31_codi_professor=ga04_codi_prof and ga31_tipus_falta=ga22_codi_falta and ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and"
        . " ga31_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga31_enviada=0 order by nivell,grup";

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);
//creem el xml
$xmlDoc = new DOMDocument('1.0', 'utf-8');

$xmlDoc->preserveWhiteSpace = false;
$xmlDoc->formatOutput = true;

$xslt = $xmlDoc->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="../../xslt/formataDireccio.xsl"');

$xmlDoc->appendChild($xslt);

//creem l'arrel
$root = $xmlDoc->createElement('dades');
$xmlDoc->appendChild($root);

if ($result->num_rows > 0) {


    $cont = 0;

    while ($row = $result->fetch_assoc()) {
        //creem el node de la falta
        if ($cont == 0) {
            //abans de la primera inciència posem la data
            $dataInciencia = $xmlDoc->createElement('data', date("d/m/Y"));
            $root->appendChild($dataInciencia);
        }

        $cont++;

        $falta = $xmlDoc->createElement('falta');
        //afegim els elements
        $codiAlumne = $xmlDoc->createElement('codialumne', $row['codialumne']);
        $falta->appendChild($codiAlumne);

        $alumne = $xmlDoc->createElement('alumne', $row['alumne']);
        $falta->appendChild($alumne);

        $nivell = $xmlDoc->createElement('nivell', $row['nivell']);
        $falta->appendChild($nivell);

        $grup = $xmlDoc->createElement('grup', $row['grup']);
        $falta->appendChild($grup);

        $professor = $xmlDoc->createElement('professor', $row['professor']);
        $falta->appendChild($professor);

        $dia = $xmlDoc->createElement('dia', $row['dia']);
        $falta->appendChild($dia);

        $hora = $xmlDoc->createElement('hora', $row['hora']);
        $falta->appendChild($hora);

        $tipusFalta = $xmlDoc->createElement('tipusfalta', $row['tipusfalta']);
        $falta->appendChild($tipusFalta);

        $motiu = $xmlDoc->createElement('motiu', $row['motiu']);
        $falta->appendChild($motiu);


        //afegim la falta a l'arrel
        $root->appendChild($falta);
    }
}

$result->close();


//ara les absències
$query = "select ga15_alumne as codialumne, concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup, ga15_dia as dia,ga15_hora_inici as hora"
        . " from ga11_alumnes,ga15_cont_presencia,ga07_grup,ga06_nivell,ga12_alumnes_curs"
        . " where ga15_codi_curs=ga12_codi_curs and ga15_alumne=ga12_id_alumne and ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and"
        . " ga15_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga15_check_absent=1"
        . " and ga15_dia=date(now()) order by nivell,grup,alumne,codialumne,hora";

//echo $query;
//executem la consulta
$result1 = $conn->query($query);


if (!$result1)
    die($conn->error);
$numAlumne = 0;
$canviAlumne = false;
if ($result1->num_rows > 0) {
    while ($row = $result1->fetch_assoc()) {

        if ($row['codialumne'] == $numAlumne) {
            //continuem amb el mateix alumne
            $canviAlumne = false;
        } else {
            //alumne nou
            $canviAlumne = true;

            //afegim la falta a l'arrel
            if ($numAlumne != 0) {
                $root->appendChild($absencia);
            }
            $numAlumne = $row['codialumne'];
        }



        if ($canviAlumne == true) {
            $absencia = $xmlDoc->createElement('absencia');

            //afegim els elements
            $codiAlumne = $xmlDoc->createElement('codialumne', $row['codialumne']);
            $absencia->appendChild($codiAlumne);


            $alumne = $xmlDoc->createElement('alumne', $row['alumne']);
            $absencia->appendChild($alumne);



            $nivell = $xmlDoc->createElement('nivell', $row['nivell']);
            $absencia->appendChild($nivell);


            $grup = $xmlDoc->createElement('grup', $row['grup']);
            $absencia->appendChild($grup);

            $hores = $xmlDoc->createElement('hores');
            $absencia->appendChild($hores);


            $hora = $xmlDoc->createElement('hora', $row['hora']);
            $hores->appendChild($hora);
        } else {

            $hores = $xmlDoc->createElement('hores');
            $absencia->appendChild($hores);

            $hora = $xmlDoc->createElement('hora', $row['hora']);
            $hores->appendChild($hora);
        }
    }
    //penjo l'últim
    $root->appendChild($absencia);
}

//tanquem
$result1->close();

echo 'desem el document de direcció';

$xmlDoc->save('../xml/reports' . date('Y-m-d') . '/incDiariesDireccio.xml');

//preparem les comunicacions amb els tutors
//esborrem la taula auxiliar anterior
$query = "DROP TABLE IF EXISTS gapr01_faltes_ordre";

$result = $conn->query($query);

if (!$result)
    die($conn->error);

//la tornem a crear
$query = "CREATE TABLE gapr01_faltes_ordre "
        . "(gapr01_codi_alumne INT NULL,"
        . "gapr01_alumne VARCHAR(200) NULL,"
        . "gapr01_nivell VARCHAR(45) NULL,"
        . "gapr01_grup VARCHAR(45) NULL,"
        . "gapr01_professor VARCHAR(200) NULL,"
        . "gapr01_dia DATE NULL,"
        . "gapr01_hora VARCHAR(5) NULL,"
        . "gapr01_nom_falta VARCHAR(45) NULL,"
        . "gapr01_motiu TEXT NULL,"
        . "gapr01_codi_tutor INT NULL,"
        . "gapr01_nom_tutor VARCHAR(200) NULL,"
        . "gapr01_mail_tutor VARCHAR(200) NULL)";


$result = $conn->query($query);

if (!$result)
    die($conn->error);

//inserem les absències dels tutors
$query = "insert into gapr01_faltes_ordre select ga31_alumne as codialumne, concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup, concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as professor, ga31_dia as dia,ga31_hora_inici as hora,ga22_nom_falta as tipusfalta,ga31_motiu as motiu,"
        . "(select ga29_tutor from ga12_alumnes_curs,ga29_tutors_curs where ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga12_id_alumne=ga31_alumne and ga29_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)) as coditutor,"
        . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors,ga12_alumnes_curs,ga29_tutors_curs where ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga12_id_alumne=ga31_alumne and ga29_tutor=ga04_codi_prof and ga29_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)) as nomtutor,"
        . "(select ga04_mail from ga04_professors,ga12_alumnes_curs,ga29_tutors_curs where ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga12_id_alumne=ga31_alumne and ga29_tutor=ga04_codi_prof and ga29_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)) as mailtutor"
        . " from ga11_alumnes,ga04_professors,ga31_faltes_ordre,ga22_tipus_falta,ga07_grup,ga06_nivell,ga12_alumnes_curs"
        . " where ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne and ga12_id_alumne=ga11_id_alumne and ga31_codi_professor=ga04_codi_prof and ga31_tipus_falta=ga22_codi_falta and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and"
        . " ga31_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)"
        . " and ga31_enviada=0 order by coditutor,alumne,codialumne";



$result = $conn->query($query);

if (!$result)
    die($conn->error);

//inserim les absències dels cotutors
$query = "insert into gapr01_faltes_ordre select ga31_alumne as codialumne, concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup, concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as professor, ga31_dia as dia,ga31_hora_inici as hora,ga22_nom_falta as tipusfalta,ga31_motiu as motiu,"
        . "ga12_cotutor as coditutor,"
        . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=coditutor) as nomtutor,"
        . "(select ga04_mail from ga04_professors where ga04_codi_prof=coditutor) as mailtutor"
        . " from ga11_alumnes,ga04_professors,ga31_faltes_ordre,ga22_tipus_falta,ga07_grup,ga06_nivell,ga12_alumnes_curs"
        . " where ga31_codi_curs=ga12_codi_curs and ga31_alumne=ga12_id_alumne and ga12_cotutor is not null and ga12_cotutor <>'' and ga12_id_alumne=ga11_id_alumne and ga31_codi_professor=ga04_codi_prof and ga31_tipus_falta=ga22_codi_falta and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and"
        . " ga31_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)"
        . " and ga31_enviada=0 order by coditutor,alumne,codialumne";

$result = $conn->query($query);

if (!$result)
    die($conn->error);


//agafem les absències ordenades per tutor o cotutor
$query = "SELECT gapr01_codi_alumne as codialumne,"
        . "gapr01_alumne as alumne,"
        . "gapr01_nivell as nivell,"
        . "gapr01_grup as grup,"
        . "gapr01_professor as professor,"
        . "gapr01_dia as dia,"
        . "gapr01_hora as hora,"
        . "gapr01_nom_falta as tipusfalta,"
        . "gapr01_motiu as motiu,"
        . "gapr01_codi_tutor as coditutor,"
        . "gapr01_nom_tutor as nomtutor,"
        . "gapr01_mail_tutor as mailtutor"
        . " FROM gapr01_faltes_ordre order by coditutor,alumne,codialumne";
$result = $conn->query($query);

if (!$result)
    die($conn->error);

echo '<br>';


//crerem els documents xml per cada tutor amb les absències

$xmlDocTutors = [];
$mailsTutors = [];

if ($result->num_rows > 0) {

    $codiTutorRef = 0;
    $nouTutor = false;

    while ($row = $result->fetch_assoc()) {


        $codiTutor = $row['coditutor'];
        if ($codiTutor != $codiTutorRef) {
            //canvi de tutor
            if ($codiTutorRef != 0) {
                //    $xmlDocTutors[$codiTutorRef]->save('../xml/reports' . date('Y-m-d') . '/reportTutor' . date('Y-m-d') . '_' . $codiTutorRef . '.xml');
            }
            $nouTutor = true;
            $codiTutorRef = $codiTutor;
        } else {
            //continuem amb el mateix tutor
            $nouTutor = false;
        }


        if ($nouTutor == true) {

            //desem el mail del tutor en una array
            $mailsTutors[$row['coditutor']] = $row['mailtutor'];


            //creem el document xml del tutor
            $xmlDocTutors[$row['coditutor']] = new DOMDocument('1.0', 'utf-8');


            $xmlDocTutors[$row['coditutor']] = new DOMDocument('1.0', 'utf-8');
            $xmlDocTutors[$row['coditutor']]->preserveWhiteSpace = false;
            $xmlDocTutors[$row['coditutor']]->formatOutput = true;

            $xslt = $xmlDocTutors[$row['coditutor']]->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="../../xslt/formataTutors.xsl"');

            $xmlDocTutors[$row['coditutor']]->appendChild($xslt);

            //creem l'arrel
            $root = $xmlDocTutors[$row['coditutor']]->createElement('dades');
            $xmlDocTutors[$row['coditutor']]->appendChild($root);


            //abans de la primera falta posem la data
            $dataInciencia = $xmlDocTutors[$row['coditutor']]->createElement('data', date("d/m/Y"));
            $root->appendChild($dataInciencia);

            //i ara posem el nom del tutor
            $nomTutor = $xmlDocTutors[$row['coditutor']]->createElement('tutor', $row['nomtutor']);
            $root->appendChild($nomTutor);


            //afegim la resta de dades
            //afegim els elements

            $falta = $xmlDocTutors[$row['coditutor']]->createElement('falta');
            //afegim els elements
            $codiAlumne = $xmlDocTutors[$row['coditutor']]->createElement('codialumne', $row['codialumne']);
            $falta->appendChild($codiAlumne);

            $alumne = $xmlDocTutors[$row['coditutor']]->createElement('alumne', $row['alumne']);
            $falta->appendChild($alumne);

            $nivell = $xmlDocTutors[$row['coditutor']]->createElement('nivell', $row['nivell']);
            $falta->appendChild($nivell);

            $grup = $xmlDocTutors[$row['coditutor']]->createElement('grup', $row['grup']);
            $falta->appendChild($grup);

            $professor = $xmlDocTutors[$row['coditutor']]->createElement('professor', $row['professor']);
            $falta->appendChild($professor);

            $dia = $xmlDocTutors[$row['coditutor']]->createElement('dia', $row['dia']);
            $falta->appendChild($dia);

            $hora = $xmlDocTutors[$row['coditutor']]->createElement('hora', $row['hora']);
            $falta->appendChild($hora);

            $tipusFalta = $xmlDocTutors[$row['coditutor']]->createElement('tipusfalta', $row['tipusfalta']);
            $falta->appendChild($tipusFalta);

            $motiu = $xmlDocTutors[$row['coditutor']]->createElement('motiu', $row['motiu']);
            $falta->appendChild($motiu);

            //afegim la falta a l'arrel
            $root->appendChild($falta);
        } else {
            //només afegim la falta

            $falta = $xmlDocTutors[$row['coditutor']]->createElement('falta');
            //afegim els elements
            $codiAlumne = $xmlDocTutors[$row['coditutor']]->createElement('codialumne', $row['codialumne']);
            $falta->appendChild($codiAlumne);

            $alumne = $xmlDocTutors[$row['coditutor']]->createElement('alumne', $row['alumne']);
            $falta->appendChild($alumne);

            $nivell = $xmlDocTutors[$row['coditutor']]->createElement('nivell', $row['nivell']);
            $falta->appendChild($nivell);

            $grup = $xmlDocTutors[$row['coditutor']]->createElement('grup', $row['grup']);
            $falta->appendChild($grup);

            $professor = $xmlDocTutors[$row['coditutor']]->createElement('professor', $row['professor']);
            $falta->appendChild($professor);

            $dia = $xmlDocTutors[$row['coditutor']]->createElement('dia', $row['dia']);
            $falta->appendChild($dia);

            $hora = $xmlDocTutors[$row['coditutor']]->createElement('hora', $row['hora']);
            $falta->appendChild($hora);

            $tipusFalta = $xmlDocTutors[$row['coditutor']]->createElement('tipusfalta', $row['tipusfalta']);
            $falta->appendChild($tipusFalta);

            $motiu = $xmlDocTutors[$row['coditutor']]->createElement('motiu', $row['motiu']);
            $falta->appendChild($motiu);

            //afegim la falta a l'arrel
            $root->appendChild($falta);
        }
    }
    //$xmlDocTutors[$codiTutorRef]->save('../xml/reports' . date('Y-m-d') . '/reportTutor' . date('Y-m-d') . '_' . $codiTutorRef . '.xml');
}

$result->close();

//esborrem la taula auxiliar anterior
$queryTutors = "DROP TABLE IF EXISTS gapr02_absencies";

$resultTutors = $conn->query($queryTutors);

if (!$resultTutors)
    die($conn->error);

//la tornem a crear

$queryTutors = "CREATE TABLE gapr02_absencies ("
        . "gapr02_codi_tutor INT NULL,"
        . "gapr02_mail_tutor VARCHAR(200) NULL,"
        . "gapr02_nom_tutor VARCHAR(200) NULL);";

//executem la consulta
$resultTutors = $conn->query($queryTutors);


if (!$resultTutors)
    die($conn->error);


//abans que res, obtenim els codis dels tutors afectats per les faltes d'assistència
$queryTutors = "insert into gapr02_absencies select distinct(ga29_tutor) as coditutor,(select ga04_mail from ga04_professors where ga04_codi_prof=coditutor) as mailtutor,"
        . "(select concat(ga04_cognom1,' ',ga04_cognom2,',',ga04_nom) from ga04_professors where ga04_codi_prof=coditutor) as nomtutor"
        . " from ga11_alumnes,ga15_cont_presencia,ga07_grup,ga06_nivell,ga12_alumnes_curs,ga29_tutors_curs"
        . " where ga15_codi_curs=ga12_codi_curs and ga15_alumne=ga12_id_alumne and ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and ga29_curs=ga12_codi_curs and ga29_nivell=ga12_codi_nivell and ga29_grup=ga12_codi_grup and"
        . " ga15_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga15_check_absent=1 and ga15_dia=date(now())"
        . " order by ga29_tutor";

//echo $queryTutors;
//executem la consulta
$resultTutors = $conn->query($queryTutors);


if (!$resultTutors)
    die($conn->error);

//ara els codis dels cotutors

$queryTutors = "insert into gapr02_absencies select distinct(ga12_cotutor) as coditutor,"
        . "(select ga04_mail from ga04_professors where ga04_codi_prof=coditutor) as mailtutor,"
        . "(select concat(ga04_cognom1,' ',ga04_cognom2,',',ga04_nom) from ga04_professors where ga04_codi_prof=coditutor) as nomtutor"
        . " from ga15_cont_presencia,ga12_alumnes_curs"
        . " where ga15_codi_curs=ga12_codi_curs and ga15_alumne=ga12_id_alumne and ga12_cotutor<>'' and ga12_cotutor is not null"
        . " and ga15_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga15_check_absent=1 and ga15_dia=date(now())"
        . " order by coditutor";

//echo $queryTutors;
//executem la consulta
$resultTutors = $conn->query($queryTutors);


if (!$resultTutors)
    die($conn->error);


//recuperem els tutors 
$queryTutors = "select distinct(gapr02_codi_tutor) as coditutor,gapr02_mail_tutor as mailtutor,gapr02_nom_tutor as nomtutor from gapr02_absencies order by coditutor";

//executem la consulta
$resultTutors = $conn->query($queryTutors);


if (!$resultTutors)
    die($conn->error);



if ($resultTutors->num_rows > 0) {

    //esborrem la taula anterior
    $query = "DROP TABLE IF EXISTS gapr03_absencies_detall";

    //executem la consulta
    $result = $conn->query($query);


    if (!$result)
        die($conn->error);

    //creem la taula auxiliar
    $query = "CREATE TABLE gapr03_absencies_detall("
            . "gapr03_codi_alumne INT NULL,"
            . "gapr03_alumne VARCHAR(200) NULL,"
            . "gapr03_codi_tutor INT NULL,"
            . "gapr03_nom_tutor VARCHAR(200) NULL,"
            . "gapr03_mail_tutor VARCHAR(200) NULL,"
            . "gapr03_nivell VARCHAR(45) NULL,"
            . "gapr03_grup VARCHAR(45) NULL,"
            . "gapr03_dia DATE NULL,"
            . "gapr03_hora VARCHAR(5) NULL);";


    //executem la consulta
    $result = $conn->query($query);


    if (!$result)
        die($conn->error);

    $query = "ALTER TABLE gapr03_absencies_detall ADD INDEX gapr03_tutor (gapr03_codi_tutor ASC);";

    //executem la consulta
    $result = $conn->query($query);

    if (!$result)
        die($conn->error);


    while ($rowTutors = $resultTutors->fetch_assoc()) {
        //comprovem si el document del tutors ja s'havia creat
        //desem el mail del tutor en una array
        $mailsTutors[$rowTutors['coditutor']] = $rowTutors['mailtutor'];
        if (array_key_exists($rowTutors['coditutor'], $xmlDocTutors) == false) {


            //en cas que no s'hagi creat en creem el document i l'arrel            
            //echo $rowTutors['coditutor'] . '<br>';
            $xmlDocTutors[$rowTutors['coditutor']] = new DOMDocument('1.0', 'utf-8');


            $xmlDocTutors[$rowTutors['coditutor']] = new DOMDocument('1.0', 'utf-8');
            $xmlDocTutors[$rowTutors['coditutor']]->preserveWhiteSpace = false;
            $xmlDocTutors[$rowTutors['coditutor']]->formatOutput = true;

            $xslt = $xmlDocTutors[$rowTutors['coditutor']]->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="../../xslt/formataTutors.xsl"');

            $xmlDocTutors[$rowTutors['coditutor']]->appendChild($xslt);

            //creem l'arrel
            $root = $xmlDocTutors[$rowTutors['coditutor']]->createElement('dades');
            $xmlDocTutors[$rowTutors['coditutor']]->appendChild($root);

            //abans de la primera absencia posem la data
            $dataAbsencia = $xmlDocTutors[$rowTutors['coditutor']]->createElement('data', date("d/m/Y"));
            $root->appendChild($dataAbsencia);

            //i ara posem el nom del tutor
            $nomTutor = $xmlDocTutors[$rowTutors['coditutor']]->createElement('tutor', $rowTutors['nomtutor']);
            $root->appendChild($nomTutor);

            //$xmlDocTutors[$rowTutors['coditutor']]->save('../xml/reports' . date('Y-m-d') . '/reportTutor' . date('Y-m-d') . '_' . $rowTutors['coditutor'] . '.xml');
            //anem a buscar les absències d'aquest tutor
            $query = "insert into gapr03_absencies_detall select ga15_alumne as codialumne, concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,"
                    . "(select ga29_tutor from ga12_alumnes_curs,ga29_tutors_curs where ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga12_id_alumne=ga15_alumne and ga29_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)) as coditutor,"
                    . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors,ga12_alumnes_curs,ga29_tutors_curs where ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga12_id_alumne=ga15_alumne and ga29_tutor=ga04_codi_prof and ga29_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)) as nomtutor,"
                    . "(select ga04_mail from ga04_professors,ga12_alumnes_curs,ga29_tutors_curs where ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga12_id_alumne=ga15_alumne and ga29_tutor=ga04_codi_prof and ga29_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)) as mailtutor,"
                    . "ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup, ga15_dia as dia,ga15_hora_inici as hora"
                    . " from ga11_alumnes,ga15_cont_presencia,ga07_grup,ga06_nivell,ga12_alumnes_curs"
                    . " where ga15_codi_curs=ga12_codi_curs and ga15_alumne=ga12_id_alumne and ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and"
                    . " ga15_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga15_check_absent=1 and ga15_dia=date(now())"
                    . " and (select ga29_tutor from ga12_alumnes_curs,ga29_tutors_curs where ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga12_id_alumne=ga15_alumne and ga29_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1))=" . $rowTutors['coditutor']
                    . " order by alumne,codialumne,hora";

            //echo $query;
            //i les absències del cotutor
            //executem la consulta
            $result = $conn->query($query);

            if (!$result)
                die($conn->error);
            echo '2';
            $query = "insert into gapr03_absencies_detall select ga15_alumne as codialumne, concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga12_cotutor as coditutor,"
                    . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=coditutor) as nomtutor,"
                    . "(select ga04_mail from ga04_professors where ga04_codi_prof=coditutor) as mailtutor,"
                    . "ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup, ga15_dia as dia,ga15_hora_inici as hora"
                    . " from ga11_alumnes,ga15_cont_presencia,ga07_grup,ga06_nivell,ga12_alumnes_curs"
                    . " where ga15_codi_curs=ga12_codi_curs and ga15_alumne=ga12_id_alumne and ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup"
                    . " and ga15_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga15_check_absent=1 and ga15_dia=date(now()) and ga12_cotutor=" . $rowTutors['coditutor']
                    . " order by alumne,codialumne,hora;";

            $result = $conn->query($query);

            if (!$result)
                die($conn->error);

            echo '3';
            //recullim només les absències del tutot/cotutor
            $query = "SELECT gapr03_codi_alumne as codialumne,gapr03_alumne as alumne,"
                    . "gapr03_codi_tutor as coditutor,gapr03_nom_tutor as nomtutor,gapr03_mail_tutor as mailtutor,"
                    . "gapr03_nivell as nivell,gapr03_grup as grup,gapr03_dia as dia,gapr03_hora as hora FROM gapr03_absencies_detall"
                    . " where gapr03_codi_tutor=" . $rowTutors['coditutor'] . " order by alumne,codialumne,hora";

            //executem
            $result1 = $conn->query($query);


            if (!$result1)
                die($conn->error);

            $numAlumne = 0;
            $canviAlumne = false;
            if ($result1->num_rows > 0) {
                while ($row = $result1->fetch_assoc()) {

                    if ($row['codialumne'] == $numAlumne) {
                        //continuem amb el mateix alumne
                        $canviAlumne = false;
                    } else {
                        //alumne nou
                        $canviAlumne = true;

                        //afegim la falta a l'arrel
                        if ($numAlumne != 0) {
                            $root->appendChild($absencia);
                        }
                        $numAlumne = $row['codialumne'];
                    }



                    if ($canviAlumne == true) {
                        $absencia = $xmlDocTutors[$row['coditutor']]->createElement('absencia');

                        //echo $numAlumne;
                        //afegim els elements
                        $codiAlumne = $xmlDocTutors[$row['coditutor']]->createElement('codialumne', $row['codialumne']);
                        $absencia->appendChild($codiAlumne);


                        $alumne = $xmlDocTutors[$row['coditutor']]->createElement('alumne', $row['alumne']);
                        $absencia->appendChild($alumne);



                        $nivell = $xmlDocTutors[$row['coditutor']]->createElement('nivell', $row['nivell']);
                        $absencia->appendChild($nivell);


                        $grup = $xmlDocTutors[$row['coditutor']]->createElement('grup', $row['grup']);
                        $absencia->appendChild($grup);

                        $hores = $xmlDocTutors[$row['coditutor']]->createElement('hores');
                        $absencia->appendChild($hores);


                        $hora = $xmlDocTutors[$row['coditutor']]->createElement('hora', $row['hora']);
                        $hores->appendChild($hora);
                    } else {

                        $hores = $xmlDocTutors[$row['coditutor']]->createElement('hores');
                        $absencia->appendChild($hores);

                        $hora = $xmlDocTutors[$row['coditutor']]->createElement('hora', $row['hora']);
                        $hores->appendChild($hora);
                    }
                }
                //penjo l'últim
                $root->appendChild($absencia);
            }

//tanquem
        } else {

            echo '4';
            //ja hi havien faltes
            //$xmlDocTutors[$rowTutors['coditutor']]->save('../xml/reports' . date('Y-m-d') . '/reportTutor' . date('Y-m-d') . '_' . $rowTutors['coditutor'] . '.xml');
            //anem a buscar les absències d'aquest tutor
            $query = "insert into gapr03_absencies_detall select ga15_alumne as codialumne, concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,"
                    . "(select ga29_tutor from ga12_alumnes_curs,ga29_tutors_curs where ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga12_id_alumne=ga15_alumne and ga29_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)) as coditutor,"
                    . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors,ga12_alumnes_curs,ga29_tutors_curs where ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga12_id_alumne=ga15_alumne and ga29_tutor=ga04_codi_prof and ga29_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)) as nomtutor,"
                    . "(select ga04_mail from ga04_professors,ga12_alumnes_curs,ga29_tutors_curs where ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga12_id_alumne=ga15_alumne and ga29_tutor=ga04_codi_prof and ga29_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1)) as mailtutor,"
                    . "ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup, ga15_dia as dia,ga15_hora_inici as hora"
                    . " from ga11_alumnes,ga15_cont_presencia,ga07_grup,ga06_nivell,ga12_alumnes_curs"
                    . " where ga15_codi_curs=ga12_codi_curs and ga15_alumne=ga12_id_alumne and ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup and"
                    . " ga15_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga15_check_absent=1 and ga15_dia=date(now())"
                    . " and (select ga29_tutor from ga12_alumnes_curs,ga29_tutors_curs where ga12_codi_curs=ga29_curs and ga12_codi_nivell=ga29_nivell and ga12_codi_grup=ga29_grup and ga12_id_alumne=ga15_alumne and ga29_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1))=" . $rowTutors['coditutor']
                    . " order by alumne,codialumne,hora";

            //i les absències del cotutor
            //executem la consulta
            $result = $conn->query($query);

            if (!$result)
                die($conn->error);
            echo '5';
            $query = "insert into gapr03_absencies_detall select ga15_alumne as codialumne, concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as alumne,ga12_cotutor as coditutor,"
                    . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=coditutor) as nomtutor,"
                    . "(select ga04_mail from ga04_professors where ga04_codi_prof=coditutor) as mailtutor,"
                    . "ga06_descripcio_nivell as nivell,ga07_descripcio_grup as grup, ga15_dia as dia,ga15_hora_inici as hora"
                    . " from ga11_alumnes,ga15_cont_presencia,ga07_grup,ga06_nivell,ga12_alumnes_curs"
                    . " where ga15_codi_curs=ga12_codi_curs and ga15_alumne=ga12_id_alumne and ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga06_codi_nivell and ga12_codi_grup=ga07_codi_grup"
                    . " and ga15_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga15_check_absent=1 and ga15_dia=date(now()) and ga12_cotutor=" . $rowTutors['coditutor']
                    . " order by alumne,codialumne,hora;";

            $result = $conn->query($query);

            if (!$result)
                die($conn->error);

            echo '6';
            //recullim només les absències del tutot/cotutor
            $query = "SELECT gapr03_codi_alumne as codialumne,gapr03_alumne as alumne,"
                    . "gapr03_codi_tutor as coditutor,gapr03_nom_tutor as nomtutor,gapr03_mail_tutor as mailtutor,"
                    . "gapr03_nivell as nivell,gapr03_grup as grup,gapr03_dia as dia,gapr03_hora as hora FROM gapr03_absencies_detall"
                    . " where gapr03_codi_tutor=" . $rowTutors['coditutor'] . " order by alumne,codialumne,hora";


            //executem la consulta
            $result1 = $conn->query($query);


            if (!$result1)
                die($conn->error);
            $numAlumne = 0;
            $canviAlumne = false;

            $arrel = $xmlDocTutors[$rowTutors['coditutor']]->documentElement;


            if ($result1->num_rows > 0) {
                while ($row = $result1->fetch_assoc()) {
                    // $arrel = $xmlDocTutors[$row['coditutor']]->documentElement;
                    if ($row['codialumne'] == $numAlumne) {
                        //continuem amb el mateix alumne
                        $canviAlumne = false;
                    } else {
                        //alumne nou
                        $canviAlumne = true;

                        //afegim la falta a l'arrel
                        if ($numAlumne != 0) {
                            $arrel->appendChild($absencia);
                        }
                        $numAlumne = $row['codialumne'];
                    }



                    if ($canviAlumne == true) {
                        $absencia = $xmlDocTutors[$row['coditutor']]->createElement('absencia');

                        //echo $numAlumne;
                        //afegim els elements
                        $codiAlumne = $xmlDocTutors[$row['coditutor']]->createElement('codialumne', $row['codialumne']);
                        $absencia->appendChild($codiAlumne);


                        $alumne = $xmlDocTutors[$row['coditutor']]->createElement('alumne', $row['alumne']);
                        $absencia->appendChild($alumne);



                        $nivell = $xmlDocTutors[$row['coditutor']]->createElement('nivell', $row['nivell']);
                        $absencia->appendChild($nivell);


                        $grup = $xmlDocTutors[$row['coditutor']]->createElement('grup', $row['grup']);
                        $absencia->appendChild($grup);

                        $hores = $xmlDocTutors[$row['coditutor']]->createElement('hores');
                        $absencia->appendChild($hores);


                        $hora = $xmlDocTutors[$row['coditutor']]->createElement('hora', $row['hora']);
                        $hores->appendChild($hora);
                    } else {

                        $hores = $xmlDocTutors[$row['coditutor']]->createElement('hores');
                        $absencia->appendChild($hores);

                        $hora = $xmlDocTutors[$row['coditutor']]->createElement('hora', $row['hora']);
                        $hores->appendChild($hora);
                    }
                }
                //penjo l'últim
                $arrel->appendChild($absencia);
            }
        }
        $result1->close();
    }
}

//desem tots els fitxers

echo 'desem els documents dels tutors';
foreach ($xmlDocTutors as $clau => $valor) {
    $valor->save('../xml/reports' . date('Y-m-d') . '/reportTutor' . date('Y-m-d') . '_' . $clau . '.xml');
}

//enviem els correus electrònics


echo 'enviem els documents del tutors';
$contaMails = 0;

//si hi ha almenys un mail per enviar s'envien


if (count($mailsTutors > 0)) {
    $mail = new GestioMails("no_resposta@iesrocagrossa.cat", "xhb110693", "smtp.gmail.com");
}

foreach ($mailsTutors as $codiTutorMail => $mailStringTutor) {
    echo $mailStringTutor . '<br>';

    $cosHtml = '<img src="cid:logo_roca">'
            . '<p>Benvolgut company, </p>'
            . '<p>Adjuntem el link per visualitzar un report amb les incidències de la teva tutoria d&#39;avui</p>'
            . '<a href="insrocagrossa.cat/plataforma/xml/reports' . date('Y-m-d') . '/reportTutor' . date('Y-m-d') . '_' . $codiTutorMail . '.xml">Prem aquest link per visualitzar la informació</a>"';


    $mail->afegeixAdreca($mailStringTutor);
    $mail->afegeixCosHtml($cosHtml);
    $resultat = $mail->enviaMail();

    sleep(1);

    //netegem l'adreça
    $mail->netejaAdreces();

    $contaMails++;
}



$resultTutors->close();

//guardem els fitxers

$conn->close();


