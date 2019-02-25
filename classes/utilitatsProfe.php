<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function comprovaProfeActiu($dataSessio, $profe, $conn) {



    $actiu = false;
    $dataSessioDate = date_create_from_format('Y-m-d', $dataSessio);

    $queryProfe = "select ga42_data_inici as datainici,ga42_data_fi as datafi from ga42_registre_activitat where ga42_professor=" . $profe;

    $result1 = $conn->query($queryProfe);

    if (!$result1)
        die($conn->error);

    if ($result1->num_rows > 0) {
        while ($row1 = $result1->fetch_assoc()) {

            $dataIniciDate = date_create_from_format('Y-m-d', $row1['datainici']);
            $diffDataInici = (int) date_diff($dataSessioDate, $dataIniciDate)->format("%R%a");

            if ($diffDataInici <= 0) {
                //si la diferència és >=0 la sessió és posteriro a la data d'inici
                //anem a veure la data de fi no fos cas que ens passessim

                if ($row1['datafi'] == '') {
                    //no hi ha data de fi per tant estem dins del tram horari
                    $actiu = true;
                } else {
                    $dataFiDate = date_create_from_format('Y-m-d', $row1['datafi']);
                    $diffDataFi = (int) date_diff($dataSessioDate, $dataFiDate)->format("%R%a");
                    if ($diffDataFi >= 0) {
                        //estem dis del tran
                        $actiu = true;
                    }
                }
            }
        }
    }

    $result1->close();
    return $actiu;
}
