<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../classes/Databases.php';
//fem la connexió


$div = $_POST['div'];


$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
if ($conn->connect_error)
    die($conn->connect_error);

//triem el charset de la cerca
mysqli_set_charset($conn, "utf8");

$query = "SELECT ga10_hora_inici as codi,concat('de ',ga10_hora_inici,' a ',ga10_hora_fi) as descripcio,ga10_es_descans as esdescans,ga34_descripcio_tram as descrtram FROM ga10_horaris_aula,ga34_trams_descripcio where ga34_codi_tram=ga10_tipus_horari and ga10_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) order by ga10_hora_inici";

//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

echo '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="butDrop' . $div . '">Tram horari<span class="caret"></span></button>';
echo '<ul class="dropdown-menu" id="drop' . $div . '" style="height: 250px; overflow: auto">';

$tramvell = '';

if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {

        if ($tramvell == $row['descrtram']) {
            //continuem al mateix tram
            $canvitram = false;
            $tramvell = $row['descrtram'];
        } else {
            //canviem de tram
            $canvitram = true;
            $tramvell = $row['descrtram'];
        }

        if ($canvitram == true) {

            echo '<li  class="disabled"><a><u><strong>' . $row['descrtram'] . '</strong></u></a></li>';
            echo '<li><a data-val="' . $row['codi'] . '" onclick="mostra' . $div . '(this);">' . $row['descripcio'] . '</a></li>';
        } else {

            if ($row['esdescans'] == '0') {
                echo '<li><a data-val="' . $row['codi'] . '" onclick="mostra' . $div . '(this);">' . $row['descripcio'] . '</a></li>';
            } else {
                //es lleure 
                echo '<li  class="disabled"><a class="btn-success" data-val="' . $row['codi'] . '" onclick="mostra' . $div . '(this);">' . $row['descripcio'] . '(L)</a></li>';
            }
        }
    }
}

echo '</ul>';


$result->close();
$conn->close();
