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
mysqli_set_charset($conn, "utf8");

$query = "select ga18_desc_assignatura as descripcio,ga18_desc_breu as descbreu,ga18_codi_assignatura as codi "
        . "from ga14_assignatura_nivell,ga18_assignatures where ga14_curs=(select ga03_codi_curs from ga03_curs where ga03_actual='1') and "
        . "ga18_codi_assignatura=ga14_codi_assignatura and ga14_nivell=" . $nivell .
        " order by ga14_ordre_butlleti";


//executem la consulta
$result = $conn->query($query);


if (!$result)
    die($conn->error);

//composem les assignatures
if ($result->num_rows > 0) {

    $assig = "";
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        $assig .= $row['descbreu'] . '-' . $row['descripcio'] . '&#13;&#10;';
    }
} else {
    $assig = "";
}

echo '<label for="assignatures">Llegenda:</label>';
echo '<textarea class="form-control" rows="20" id="assignatures" readonly>' . $assig . '</textarea>';

$result->close();
$conn->close();
