<?php

require '../../vendor/autoload.php';
require '../../classes/Databases.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

//obrim la plantilla
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load("../../docs/plantillaLlistatExcel.xltx");

$spreadsheet->setActiveSheetIndex(0);

//obtenim el nivell i el grup que es arriben del document
$nivell = $_GET['nivell'];
$grup = $_GET['grup'];
$nivellText = $_GET['nivellText'];
$grupText = $_GET['grupText'];


//fem la connexió a la base de dades per totes les consultes que ens caldran
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

//obtenim les assignatures

$query = "select ga18_desc_assignatura,ga18_desc_breu as descbreu,ga18_codi_assignatura as codi "
        . "from ga14_assignatura_nivell,ga18_assignatures where ga14_curs=(select ga03_codi_curs from ga03_curs where ga03_actual='1') and "
        . "ga18_codi_assignatura=ga14_codi_assignatura and ga14_nivell=" . $nivell .
        " order by ga14_ordre_butlleti,ga18_desc_assignatura";


//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);



$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, 4, $nivellText);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, 5, $grupText);

$assigNoms = array();

//posem la capçalera de les notes    
if ($result->num_rows > 0) {
    $filera = 0;
    $col = 0;
// Set document properties
// Add some data


    while ($row = $result->fetch_assoc()) {

        $assigNoms[$col] = $row['ga18_desc_assignatura'];
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col + 2, $filera + 9, $row['descbreu']);
        $col++;
    }
}



$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col + 2, $filera + 9, 'apro/susp');
$notes = $col;


//ara posem les notes


$query = "select ga12_id_alumne,concat(ga11_cognom1,' ',ga11_cognom2,' ',ga11_nom) as alumne,ga12_pla_llengues as plallengues,ga12_comentari as comentgeneral,ga14_codi_assignatura,ga18_desc_breu,"
        . "(select ga19_qualificacio from ga19_qualificacions_alumnes where ga19_curs=(select ga03_codi_curs from ga03_curs where ga03_actual='1') and ga19_curs=ga12_codi_curs and ga19_codi_alumne=ga12_id_alumne and ga19_codi_assignatura =ga14_codi_assignatura) as nota, "
        . "(select ga19_comentari from ga19_qualificacions_alumnes where ga14_curs=(select ga03_codi_curs from ga03_curs where ga03_actual='1') and ga19_curs=ga12_codi_curs and ga19_codi_alumne=ga12_id_alumne and ga19_codi_assignatura =ga14_codi_assignatura) as comment"
        . " from ga14_assignatura_nivell,ga18_assignatures,ga11_alumnes,ga12_alumnes_curs "
        . "where ga14_curs=(select ga03_codi_curs from ga03_curs where ga03_actual='1') and ga14_codi_assignatura=ga18_codi_assignatura and ga14_nivell=" . $nivell . " and ga14_curs=ga12_codi_curs and ga12_id_alumne=ga11_id_alumne and ga12_codi_nivell=ga14_nivell and ga12_codi_grup=" . $grup
        . " order by concat(ga11_cognom1,' ',ga11_cognom2,' ',ga11_nom),ga12_id_alumne,ga14_ordre_butlleti,ga18_desc_assignatura";


//executem la consulta
$result = $conn->query($query);

if (!$result)
    die($conn->error);
//ara obtenim les notes
//coloquem les notes en forma de taula


if ($result->num_rows > 0) {
    $filera = 0;
    $col = 0;
    $suspes = 0;
    $aprovat = 0;

// Set document properties
// Add some data


    while ($row = $result->fetch_assoc()) {


        if ($row['nota'] != "") {
            //hi ha nota
            if ($row['nota'] < 5) {
                //ha suspès
                $suspes++;
            } else {
                //ha aprovat
                $aprovat++;
            }
        }
        if ($col == 0) {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col+1, $filera + 10, $row['alumne']);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col + 2, $filera + 10, $row['nota']);
        } else {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col + 2, $filera + 10, $row['nota']);
        }
        //mirem si hem arribat al final de les notes d'un alumne
        if ($col == $notes - 1) {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col + 3, $filera + 10, $aprovat . "/" . $suspes);
            //posem els totals
            $col = 0;
            $filera++;
            $aprovat = 0;
            $suspes = 0;
        } else {
            $col++;
        }
    }
}

//posem les assignatures per signar
$columna = 0;
$fila = 0;
for ($i = 0; $i < count($assigNoms); $i++) {

    if ($i % 4 == 0) {
        $fila++;
        $columna = 0;
    } else {
        $columna++;
    }
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2 + $columna * 4, $fila * 4 + $filera + 9, $assigNoms[$i]);
}


$result->close();
$conn->close();



// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('acta');


// Redirect output to a clientâ€™s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="acta.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;

