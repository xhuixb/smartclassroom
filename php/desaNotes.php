<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



require '../classes/Databases.php';

session_start();
//fem la connexió a la base de dades per totes les consultes que ens caldran
$conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);

//triem el charset de la cerca
mysqli_set_charset($conn,"utf8");

$notes = $_POST['notes'];
$notesInicials = $_POST['notesInicials'];
$comentaris = $_POST['comentaris'];
$comentarisInicials = $_POST['comentarisInicials'];
$nivell = $_POST['nivell'];
$grup = $_POST['grup'];
$comentarisGenerals = $_POST['comentarisGenerals'];
$comentarisGeneralsInicials = $_POST['comentarisGeneralsInicials'];
$plaLlengua = $_POST['plaLlengua'];
$plaLlenguaInicials = $_POST['plaLlenguaInicials'];


/* for ($i = 1; $i < count($comentaris); $i++) {
  for ($j = 0; $j <count($comentaris[1]); $j++) {


  echo $comentaris[$i][$j].'<br>';
  echo $comentarisInicials[$i][$j].'<br>';
  // echo $comentaris[$i][$j].'<br>';
  }

  } */


echo '<br>';
/* for ($x = 0; $x < count($notes); $x++) {
  for ($y = 0; $y < count($notes[0]); $y++) {

  echo $notes[$x][$y] . "-c" . $comentaris[$x][$y] . 'c-';
  }
  echo '<br>';
  } */

$actualitzacions = 0;

for ($x = 1; $x < count($notes); $x++) {
    for ($y = 1; $y < count($notes[0]); $y++) {
        //recorreguem totes les notes
        //obtenim la nota en text quan n'hi ha
        if ($notes[$x][$y] != '') {
            switch ($notes[$x][$y]) {

                case 0:
                case 1:
                case 2:
                case 3:
                case 4:
                    $notaString = "Insuficient";
                    break;
                case 5:
                    $notaString = "Suficient";
                    break;
                case 6:
                    $notaString = "Bé";
                    break;
                case 7:
                case 8:
                    $notaString = "Notable";
                    break;
                case 9:
                case 10:
                    $notaString = "Excel·lent";
                    break;
            }
        } else {
            $notaString = "";
        }

        //decidim si fem un insert,un delete un update o res

        
        if ($notes[$x][$y] == '' && $notesInicials[$x][$y] != '') {
            //abans hi havia i ara no delete    
            $sql = "delete from ga19_qualificacions_alumnes where ga19_codi_alumne=" . $notes[$x][0] . " and ga19_codi_assignatura=" . $notes[0][$y];
            //esborrem el pla de llengues i el comentari general

            $conn->query($sql);
            $actualitzacions++;
        } elseif ($notes[$x][$y] != '' && $notesInicials[$x][$y] == '') {
            //ara n'hi ha i abans no. Insert
            $comentaris[$x][$y] = str_replace("'", "''", $comentaris[$x][$y]);
            $sql = "insert into ga19_qualificacions_alumnes (ga19_curs,ga19_codi_alumne,ga19_codi_assignatura,ga19_qualificacio,ga19_comentari,ga19_nota_string) values((select ga03_codi_curs from ga03_curs where ga03_actual='1')," . $notes[$x][0] . "," . $notes[0][$y] . "," . $notes[$x][$y] . ",'" . $comentaris[$x][$y] . "','" . $notaString . "')";
           // echo $sql;
            
            $conn->query($sql);
            $actualitzacions++;
        } elseif ($notes[$x][$y] != '' && $notesInicials[$x][$y] != '') {
            //hi ha nota nova i vella
            //anem a veure si han canviat
          
            if ($notes[$x][$y] != $notesInicials[$x][$y]) {
                //la nota ha canviat s'ha de fer un update
                $comentaris[$x][$y] = str_replace("'", "''", $comentaris[$x][$y]);
                $sql = "update ga19_qualificacions_alumnes set ga19_qualificacio=" . $notes[$x][$y] . ", ga19_comentari='" . $comentaris[$x][$y] . "', ga19_nota_string='" . $notaString . "' "
                        . "where ga19_codi_alumne=" . $notes[$x][0] . " and ga19_codi_assignatura=" . $notes[0][$y];

                $conn->query($sql);
                $actualitzacions++;
            } else {
                //mirem si hi ha un nou comentari
              
                if ($comentaris[$x][$y] != $comentarisInicials[$x][$y]) {
                    //ha canviat el comentari i només modifiquem el comentari
                    $comentaris[$x][$y] = str_replace("'", "''", $comentaris[$x][$y]);
                    $sql = "update ga19_qualificacions_alumnes set ga19_comentari='" . $comentaris[$x][$y]
                            . "' where ga19_codi_alumne=" . $notes[$x][0] . " and ga19_codi_assignatura=" . $notes[0][$y];

                    $conn->query($sql);
                    $actualitzacions++;
                }
            }
        }
        //anem a comprovar el comentari general i el pla de llengües
        if ($y == 1) {
            //ho farem per la primera nota
            if ($comentarisGenerals[$x - 1] != $comentarisGeneralsInicials[$x - 1] || $plaLlengua[$x - 1] != $plaLlenguaInicials[$x - 1]) {
                //ha canviar el comentari general o el pla de llengües
                $plaLlengua[$x - 1] = str_replace("'", "''", $plaLlengua[$x - 1]);
                $comentarisGenerals[$x - 1] = str_replace("'", "''", $comentarisGenerals[$x - 1]);

                $sql = "update ga12_alumnes_curs set ga12_pla_llengues='" . $plaLlengua[$x - 1] . "',ga12_comentari='" . $comentarisGenerals[$x - 1] . "' "
                        . " where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_id_alumne=" . $notes[$x][0];
                //executem la query
                $conn->query($sql);
                $actualitzacions++;
            }
        }
    }
}


$missatge = "s'han actualitzat " . $actualitzacions . " qualificacions ";
//missatge amb les fileres actualitzades
//echo '<script type="text/javascript">alert("'.$missatge.'");</script>';

echo '<div class="alert alert-success">';
echo '<strong> S\'han actualitzat amb èxit ' . $actualitzacions . ' qualificacions </strong>';
echo '</div>';


$conn->close();
