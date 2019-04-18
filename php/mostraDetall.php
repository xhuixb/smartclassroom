<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió

session_start();

$alumne = $_POST['alumne'];
$dataInicial = $_POST['dataInicial'];
$dataFinal = $_POST['dataFinal'];
$professor = $_POST['professor'];
$assignatura = $_POST['assignatura'];

//decidim si s'ha de filtar per data inicial
if ($dataInicial != "") {
    $dataInicialSql = date("Y-m-d", strtotime($dataInicial));
    $whereDataInicial = " and ga15_dia>='" . $dataInicialSql . "'";
} else {
    $whereDataInicial = "";
}

//decidim si s'ha de filtar per data inicial
if ($dataFinal != "") {
    $dataFinalSql = date("Y-m-d", strtotime($dataFinal));
    $whereDataFinal = " and ga15_dia<='" . $dataFinalSql . "'";
} else {
    $whereDataFinal = "";
}

//decidim si es filtra per professor
if ($professor != "") {

    $whereProfessor = " and ga15_codi_professor=" . $professor;
} else {
    $whereProfessor = "";
}

if ($assignatura != "") {
    $whereAssignatura = " and ga28_assignatura=" . $assignatura;
} else {
    $whereAssignatura = "";
}

//establim la connexió
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "select ga15_alumne as alumne, ga15_dia as dia,sum(ga15_check_absent) as abstotal,"
        . "(select sum(ga15_check_absent) from ga15_cont_presencia,ga28_cont_presencia_cap where ga28_codi_curs=ga15_codi_curs and ga28_professor=ga15_codi_professor and ga28_dia=ga15_dia and ga28_hora=ga15_hora_inici and ga15_check_absent=1 " . $whereDataInicial . $whereDataFinal . $whereProfessor . $whereAssignatura . " and ga15_check_justificat=1 and ga15_alumne=alumne and ga15_dia=dia and ga15_codi_curs=" . $_SESSION['curs_actual'] . " group by ga15_dia,ga15_alumne) absjusti,"
        . "(select sum(ga15_check_absent) from ga15_cont_presencia,ga28_cont_presencia_cap where ga28_codi_curs=ga15_codi_curs and ga28_professor=ga15_codi_professor and ga28_dia=ga15_dia and ga28_hora=ga15_hora_inici and ga15_check_absent=1 " . $whereDataInicial . $whereDataFinal . $whereProfessor . $whereAssignatura . " and ga15_check_justificat=0 and ga15_alumne=alumne and ga15_dia=dia and ga15_codi_curs=" . $_SESSION['curs_actual'] . " group by ga15_dia,ga15_alumne) absnojusti,"
        . "sum(ga15_check_retard) as retards"
        . " from ga15_cont_presencia,ga28_cont_presencia_cap where ga28_codi_curs=ga15_codi_curs and ga28_professor=ga15_codi_professor and ga28_dia=ga15_dia and ga28_hora=ga15_hora_inici and (ga15_check_absent=1 or ga15_check_retard=1) " . $whereDataInicial . $whereDataFinal . $whereProfessor . $whereAssignatura . " and ga15_alumne=" . $alumne . " and ga15_codi_curs=" . $_SESSION['curs_actual']
        . " group by ga15_dia,ga15_alumne order by ga15_dia desc";


//executem la query
//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

//construim capçalera de la taula
echo '<br>';
echo '<table id="taulaDetallAbsencies" class="table table-fixed">';
echo '<thead>';
echo '<tr>';
echo '<th>Dia</th>';
echo '<th><center>Abs. Totals</center></th>';
echo '<th><center>Justif</center></th>';
echo '<th><center>No justif</center></th>';
echo '<th><center>Retards</center></th>';
echo '</tr>';
echo '</thead>';

if ($result->num_rows > 0) {
    echo '<tbody id="cosTaulaDetallAbsencies">';
    $cont = 1;
    $contaDies = 0;
    $contaAbsTotals = 0;
    $contaJustificades = 0;
    $contaNoJustificades = 0;
    $contaRetards = 0;
    while ($row = $result->fetch_assoc()) {

        //calculem totals
        $contaDies++;
        $contaAbsTotals += $row['abstotal'];
        $contaJustificades += $row['absjusti'];
        $contaNoJustificades += $row['absnojusti'];
        $contaRetards += $row['retards'];
        //obtenim el detall
        $query1 = "select ga15_hora_inici as hora,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as professor, ga15_check_absent,ga15_check_retard,ga15_check_justificat,ga18_desc_assignatura as nomassig"
                . " from ga15_cont_presencia,ga04_professors,ga17_professors_curs,ga28_cont_presencia_cap,ga18_assignatures"
                . " where ga28_codi_curs=ga15_codi_curs and ga28_professor=ga15_codi_professor and ga28_dia=ga15_dia and ga28_hora=ga15_hora_inici and ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $alumne . " and ga15_dia='" . $row['dia'] . "'" . $whereProfessor . $whereAssignatura . " and (ga15_check_absent=1 or ga15_check_retard=1)"
                . " and ga15_codi_professor=ga17_codi_professor and ga15_codi_curs=ga17_codi_curs and ga17_codi_professor=ga04_codi_prof and ga28_assignatura=ga18_codi_assignatura order by hora";


        $result1 = $conn->query($query1);

        $detall = "";
        if ($result1->num_rows > 0) {

            while ($row1 = $result1->fetch_assoc()) {

                //decidim l'estat
                if ($row1['ga15_check_retard'] == 1) {
                    $estat = "Retard";
                } else if ($row1['ga15_check_justificat'] == 1) {
                    $estat = "Falta justificada";
                } else {
                    $estat = "Falta sense justificar";
                }

                $detall .= '<p><strong>Hora: </strong>' . $row1['hora'] . '</p>';
                $detall .= '<p><strong>Assignatura: </strong>' . $row1['nomassig'] . '</p>';
                $detall .= '<p><strong> Professor: </strong>' . $row1['professor'] . '</p>';
                $detall .= '<p><strong>Estat: </strong>' . $estat . '</p>';
                $detall .= '<hr>';
            }
        }
        $result1->close();


        echo '<tr>';
        echo '<td class="col-sm-4"><a href=#" data-toggle="collapse" data-target="#detallPerData' . $cont . '">' . $row['dia'] . '</a>';
        echo '<div id="detallPerData' . $cont . '" class="collapse">' . $detall . '</div></td>';
        echo '<td class="col-sm-2"><center>' . $row['abstotal'] . '</center></td>';
        echo '<td class="col-sm-2"><center>' . $row['absjusti'] . '</center></td>';
        echo '<td class="col-sm-2"><center>' . $row['absnojusti'] . '</center></td>';
        echo '<td class="col-sm-2"><center>' . $row['retards'] . '</center></td>';
        echo '</tr>';

        $cont++;
    }

    //resum

    echo '<tr>';
    echo '<td>Dies: ' .$contaDies . '</td>';   
    echo '<td class="col-sm-2"><center>' . $contaAbsTotals . '</center></td>';
    echo '<td class="col-sm-2"><center>' . $contaJustificades . '</center></td>';
    echo '<td class="col-sm-2"><center>' . $contaNoJustificades . '</center></td>';
    echo '<td class="col-sm-2"><center>' . $contaRetards . '</center></td>';
    echo '</tr>';
}

//tanquem cos i taula
echo '</tbody>';
echo '</table>';
$result->close();
$conn->close();
