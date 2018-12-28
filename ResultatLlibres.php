<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>
            table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            td, th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }

            tr:nth-child(even) {
                background-color: #dddddd;
            }
        </style>

    </head>
    <body>
        <?php
        // put your code here
        //adjuntem les dades de la connexió
        require 'classes/Databases.php';

        echo "ximplet";
        //fem la connexió
        $conn = new mysqli(Databases::$host, Databases::$user, Databases::$password, Databases::$dbase);
        if ($conn->connect_error)
            die($conn->connect_error);

        //obtenim els valors extrems del codi
        $codiInicial=$_POST['codiInicial'];
        $codiFinal=$_POST['codiFinal'];
        
        
        //creem la consulta
        $query = "select bi01_id as codi, "
                . "bi01_titol as titol, "
                . "(select bi04_nom_complet from bi04_autors where bi04_id=bi01_autor) as autor, "
                . "(select bi05_nom from bi05_editorials where bi05_id=bi01_editorial) as editorial "
                . "from bi01_llibres where bi01_id>=".$codiInicial." and bi01_id<=".$codiFinal;

        //executem la consulta
        $result = $conn->query($query);
        if (!$result)
            die($conn->error);

        //obtenim les dades
        $numFileres = $result->num_rows;

        echo '<table style="width:100%">';
        echo '<tr>';
        echo '<th>codi</th>';
        echo '<th>titol</th>';
        echo '<th>autor</th>';
        echo '<th>editorial</th>';
        echo '</tr>';

        for ($i = 0; $i < $numFileres; $i++) {
            echo '<tr>';



            $result->data_seek($i);
            echo '<td>' . $result->fetch_assoc()['codi'] . '</td>';
            $result->data_seek($i);
            echo '<td>' . $result->fetch_assoc()['titol'] . '</td>';
            $result->data_seek($i);
            echo '<td>' . $result->fetch_assoc()['autor'] . '</td>';
            $result->data_seek($i);
            echo '<td>' . $result->fetch_assoc()['editorial'] . '</td>';
            echo '</tr>';
        }

        echo '</table>';

        $result->close();
        $conn->close();
        ?>
    </body>
</html>
