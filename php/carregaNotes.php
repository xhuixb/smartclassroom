<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';

//obtenim el nivell i el grup que es arriben del document
$nivell = $_POST["nivell"];
$grup = $_POST['grup'];

//fem la connexió a la base de dades per totes les consultes que ens caldran
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

//primerament obtenim les assignatures d'aquest nivell

$query = "select ga18_desc_assignatura,ga18_desc_breu as descbreu,ga18_codi_assignatura as codi "
        . "from ga14_assignatura_nivell,ga18_assignatures where ga14_curs=(select ga03_codi_curs from ga03_curs where ga03_actual='1') and "
        . "ga18_codi_assignatura=ga14_codi_assignatura and ga14_nivell=" . $nivell .
        " order by ga14_ordre_butlleti,ga18_desc_assignatura";



//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);





//creem la taula
echo '<table id="taula2" class="table">';
//filera de capçalera
echo '<thead id="idheader">';
echo '<tr>';

echo '<th style="width:30px"></th>';
echo '<th style="width:250px">alumnes</th>';

if ($result->num_rows > 0) {
    // output data of each row
    $cont=0;
    while ($row = $result->fetch_assoc()) {
        //guardem també el codi d'assignatura
        echo '<th style="width:50px" id="tr'.$cont.'" data-value="' . $row['codi'] . '" data-nom="'.$row['ga18_desc_assignatura'].'">' . $row['descbreu'] . '</th>';
        $cont++;
    }
}

//tanqeum capçalera
echo '</tr>';
echo '</thead">';



$result->close();


$query = "select ga12_id_alumne,concat(ga11_cognom1,' ',ga11_cognom2,' ',ga11_nom) as alumne,ga12_pla_llengues as plallengues,ga12_comentari as comentgeneral,ga14_codi_assignatura,ga18_desc_breu,"
        . "(select ga19_qualificacio from ga19_qualificacions_alumnes where ga19_curs=(select ga03_codi_curs from ga03_curs where ga03_actual='1') and ga19_curs=ga12_codi_curs and ga19_codi_alumne=ga12_id_alumne and ga19_codi_assignatura =ga14_codi_assignatura) as nota, "
        ."(select ga19_comentari from ga19_qualificacions_alumnes where ga14_curs=(select ga03_codi_curs from ga03_curs where ga03_actual='1') and ga19_curs=ga12_codi_curs and ga19_codi_alumne=ga12_id_alumne and ga19_codi_assignatura =ga14_codi_assignatura) as comment"
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
    // output data of each row
    echo '<tbody>';
    echo '<tr>';
    $cont = 0;
    $alum=0;
    $col=0;
    while ($row = $result->fetch_assoc()) {
        if ($cont == 0) {
            $alumeneActual = $row['ga12_id_alumne'];
            $alumeneVell = $row['ga12_id_alumne'];
            //poso el primer alumne i el primer button de comentaris
            echo '<td style="width:30px"><button type="button" class="btn btn-info form-control" data-toggle="modal" data-target="#commentModalForm" onclick="carregaComments(this);">';
            echo '<span class="glyphicon glyphicon-pencil"></span></button></td>';
            
            
            
            echo '<td style="width:250px" id="al'.$alum.'" data-codi="' . $row['ga12_id_alumne'] . '" data-coment-general="'.$row['comentgeneral'].'" data-pla-llengues="'.$row['plallengues'].'" '
                    . 'data-coment-general-vell="'.$row['comentgeneral'].'" data-pla-llengues-vell="'.$row['plallengues'].'">' . $row['alumne'] . '</td>';
        } else {

            $alumeneActual = $row['ga12_id_alumne'];
        }

        $colorfons = "";
        //ha aprovat o suspès
        if ($row['nota'] == '') {
            //no té nota no fem res
        } elseif ($row['nota'] < 5) {
            //ha suspès color vermell
            $colorfons = "btn-warning";
        } else {
            //ha aprovat
            $colorfons = "btn-success";
        }


        if ($alumeneActual == $alumeneVell) {
            //continuem amb el mateix alumne
           
            echo '<td id="nota'.$cont.'" style="width:50px" data-comentari="'.$row['comment'].'" data-comentari-vell="'.$row['comment'].'" data-nota="'.$row['nota'].'">' .
                    '<input type="text" class="form-control input-sm '.$colorfons.'" style="font-weight: bold;color:black;width:40px;" value="' . $row['nota'] . '" onkeypress="return isNumber(event)" />' . '</td>';
            $col++;
        } else {
            //canviem d'alumne i per tant tanquem la filera i en comencen una altra
            //canviem l'alumne vell
            $alum++;
            $col=0;
            $alumeneVell = $alumeneActual;
            echo '</tr>';
            echo '<tr>';
            echo '<td style="width:30px"><button type="button" class="btn btn-info form-control" data-toggle="modal" data-target="#commentModalForm" onclick="carregaComments(this);">';
            echo '<span class="glyphicon glyphicon-pencil"></span></button></td>';            
            echo '<td style="width:250px" id="al'.$alum.'" data-codi="' . $row['ga12_id_alumne'] . '" data-coment-general="'.$row['comentgeneral'].'" data-pla-llengues="'.$row['plallengues'].'" '
                    . 'data-coment-general-vell="'.$row['comentgeneral'].'" data-pla-llengues-vell="'.$row['plallengues'].'">' . $row['alumne'] . '</td>';
            echo '<td id="nota'.$cont.'" style="width:50px" data-comentari="'.$row['comment'].'" data-comentari-vell="'.$row['comment'].'" data-nota="'.$row['nota'].'">' . 
                    '<input type="text" class="form-control input-sm '.$colorfons.'" style="font-weight: bold;color:black;width:40px;" value="' . $row['nota'] . '" onkeypress="return isNumber(event)" />' . '</td>';
            $col++;
        }
        $cont++;
    }

    //tanco la darrera filera
    echo '</td>';
    echo '</tbody>';
}
//tamquem taula
echo '</table>';
$result->close();
$conn->close();
