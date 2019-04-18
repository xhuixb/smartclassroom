<html lang="ca" >
    <head>
        <title>Sessions a partir d'horari</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="css/sessionsHorari.css">
        <script src="js/sessions.js"></script>
        <script src="js/variablesGlobals.js"></script>
        <script src="jquery/funcionsjquery.js"></script>
      <!--  <script src="jquery/sessionsjquery.js"></script>-->
        <script src="jquery/sessionsHorarijquery.js"></script>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.js"></script>
    </head>
    <?php
    require 'classes/Databases.php';

    session_start();

    $dia = $_GET['dia'];
    $hora = $_GET['hora'];
    $profeCodi = $_GET['profeCodi'];
    $profeNom = $_GET['profeNom'];
    $esguardia = $_GET['esguardia'];


    $query = "select ga28_professor as professor,ga28_dia as dia,ga28_hora as hora,ga28_is_guardia as esguardia,"
            . "ga28_prof_substituit as codiprofsubs,"
            . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=codiprofsubs) as nomprofsubs,"
            . "ga28_aula as aula,"
            . "(select ga01_descripcio_aula from ga01_aula where ga01_codi_aula=aula) as nomAula,"
            . "ga28_nivell as nivell,"
            . "(select ga06_descripcio_nivell from ga06_nivell where ga06_codi_nivell=nivell) as nomNivell,"
            . "ga28_grup as grup,"
            . "(select ga07_descripcio_grup from ga07_grup where ga07_codi_grup=grup) as nomGrup,"
            . "(select ga23_nom_grup from ga23_grups_profes_cap where ga23_codi_grup=grup) as nomGrupProfe,"
            . "ga28_tipus_grup as tipusgrup,"
            . "ga28_assignatura as assignatura,"
            . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=assignatura) as nomAssignatura,"
            . "ga28_coment_general as comentari,ga28_estat as estat"
            . " from ga28_cont_presencia_cap"
            . " where ga28_codi_curs=" . $_SESSION['curs_actual'] . " and ga28_professor=" . $profeCodi . " and ga28_dia='" . $dia . "' and  ga28_hora='" . $hora . "'";


    $conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
    if ($conn->connect_error)
        die($conn->connect_error);

//triem el charset de la cerca
    mysqli_set_charset($conn, "utf8");

    $result = $conn->query($query);


    if (!$result)
        die($conn->error);



    if ($result->num_rows > 0) {


        //hi ha sessió
        $sessioNova = false;
        //només hi pot haver un registre perquè anem per clau primària

        $row = $result->fetch_assoc();
        $dadesSessio = [];
        $dadesSessio['professor'] = $row['professor'];
        $dadesSessio['dia'] = $row['dia'];
        $dadesSessio['hora'] = $row['hora'];
        $dadesSessio['aula'] = $row['aula'];
        $dadesSessio['nomAula'] = $row['nomAula'];
        $dadesSessio['nivell'] = $row['nivell'];
        $dadesSessio['nomNivell'] = $row['nomNivell'];
        $dadesSessio['tipusgrup'] = $row['tipusgrup'];
        if ($dadesSessio['tipusgrup'] == '0') {
            $dadesSessio['nomGrup'] = $row['nomGrup'];
        } else {
            $dadesSessio['nomGrup'] = $row['nomGrupProfe'];
        }
        $dadesSessio['grup'] = $row['grup'];
        $dadesSessio['assignatura'] = $row['assignatura'];
        $dadesSessio['nomAssignatura'] = $row['nomAssignatura'];
        $dadesSessio['esguardia'] = $row['esguardia'];
        if ($dadesSessio['esguardia'] == '1') {
            $dadesSessio['codiprofsubs'] = $row['codiprofsubs'];
            $dadesSessio['nomprofsubs'] = $row['nomprofsubs'];
        }
        $dadesSessio['comentari'] = $row['comentari'];
        $estat = $row['estat'];

        if ($estat == '0') {
            $provisional = false;
        } else {
            $provisional = true;
        }

        $result->close();

        //anem a buscar el nombre d'alumnes de la sessio

        $query = "select ga15_check_present as present,ga15_check_absent as absent,ga15_check_retard as retard from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_codi_professor=" . $profeCodi
                . " and ga15_dia='" . $dia . "' and ga15_hora_inici='" . $hora . "'";


        $result = $conn->query($query);


        if (!$result)
            die($conn->error);

        if ($result->num_rows > 0) {
            // output data of each row
            $totalAlum = 0;
            $presentAlum = 0;
            $absentAlum = 0;
            $retardAlum = 0;
            while ($row = $result->fetch_assoc()) {
                if ($row['present'] == '1') {
                    $presentAlum++;
                } else if ($row['absent'] == '1') {
                    $absentAlum++;
                } else {
                    $retardAlum++;
                }
                $totalAlum++;
            }
        }
        $result->close();
    } else {
        $sessioNova = true;
        $provisional = false;
        $day_num = date('w', strtotime($dia));

        $result->close();
        //no s'havia passat llista
        //anem a buscar l'horari que hem de gestionar
        $query = "select ga26_codi_professor as professor,ga26_dia_setmana as dia,ga26_hora_inici as hora,ga26_nivell as nivell,"
                . "(select ga06_descripcio_nivell from ga06_nivell where ga06_codi_nivell=nivell) as nomNivell,"
                . "ga26_grup as grup,"
                . "(select ga07_descripcio_grup from ga07_grup where ga07_codi_grup=grup) as nomGrup,"
                . "(select ga23_nom_grup from ga23_grups_profes_cap where ga23_codi_grup=grup) as nomGrupProfe,"
                . "ga26_tipus_grup as tipusgrup,ga26_is_lectiva,ga26_codi_assignatura as assignatura,"
                . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=assignatura) as nomAssignatura,"
                . "ga26_codi_aula as aula,"
                . "(select ga01_descripcio_aula from ga01_aula where ga01_codi_aula=aula) as nomAula"
                . " from ga26_horaris_docents"
                . " where ga26_codi_curs=" . $_SESSION['curs_actual'] . " and ga26_codi_professor=" . $profeCodi . " and ga26_dia_setmana=" . $day_num . " and ga26_hora_inici='" . $hora . "'";


        $result = $conn->query($query);


        if (!$result)
            die($conn->error);

        if ($result->num_rows > 0) {

            $row = $result->fetch_assoc();
            $dadesSessio = [];
            $dadesSessio['professor'] = $row['professor'];
            $dadesSessio['dia'] = $row['dia'];
            $dadesSessio['hora'] = $row['hora'];
            $dadesSessio['aula'] = $row['aula'];
            $dadesSessio['nomAula'] = $row['nomAula'];
            $dadesSessio['nivell'] = $row['nivell'];
            $dadesSessio['nomNivell'] = $row['nomNivell'];
            $dadesSessio['tipusgrup'] = $row['tipusgrup'];
            if ($dadesSessio['tipusgrup'] == '0') {
                $dadesSessio['nomGrup'] = $row['nomGrup'];
            } else {
                $dadesSessio['nomGrup'] = $row['nomGrupProfe'];
            }
            $dadesSessio['grup'] = $row['grup'];
            $dadesSessio['assignatura'] = $row['assignatura'];
            $dadesSessio['nomAssignatura'] = $row['nomAssignatura'];
            $dadesSessio['comentari'] = '';
            $dadesSessio['esguardia'] = '';
            $result->close();

            //anem a buscar els alumnes del grup
            if ($dadesSessio['tipusgrup'] == '0') {
                //grup classe
                $query = "select count(*) as conta from ga12_alumnes_curs where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_codi_nivell=" . $dadesSessio['nivell'] . " and ga12_codi_grup=" . $dadesSessio['grup'];
            } else {
                //grup personal
                $query = "select count(*) as conta from ga24_grups_profes_det where ga24_codi_grup=" . $dadesSessio['grup'];
            }
            $result = $conn->query($query);

            if (!$result)
                die($conn->error);

            $row = $result->fetch_assoc();

            $totalAlum = $row['conta'];
            $presentAlum = $row['conta'];
            $absentAlum = '0';
            $retardAlum = '0';

            $result->close();
        }
    }
    $conn->close();
    ?>

    <!-- <body onload="carregaComboNivell();carregaComboGrup();"> -->


    <body onload="carregaDadesSessio();controlaCredencials('formSessionsHorari')">

        <div class="container-fluid">
            <div class="page-header" id="formSessionsHorari">

            </div>   
        </div>
        <div class="container-fluid">

            <div class="row" >
                <div class="col-sm-1">
                    <button type="button" class="btn btn-success form-control" id="desaSessioHorari" onclick="desaSessioHorari();" data-tooltip="tooltip" title="Desa sessió">
                        <span class="glyphicon glyphicon-floppy-disk"></span>Desa
                    </button>
                </div>
                <div class="col-sm-1">
                    <button type="button" class="btn btn-info form-control" id="surtSessioHorari" onclick="surtSessioHorari();"data-tooltip="tooltip" title="Surt de la sessió">
                        <span class="glyphicon glyphicon-log-out"></span>Surt
                    </button>
                </div>
                <div class="col-sm-1">
                    <button type="button" class="btn btn-danger form-control" id="esborraSessioHorari" onclick="esborraSessioHorari();" <?php
                    if ($sessioNova == true) {
                        echo 'disabled';
                    }
                    ?> data-tooltip="tooltip" title="Esborra la sessió">
                        <span class="glyphicon glyphicon-trash"></span>Esborra
                    </button>
                </div>
                <div class="col-sm-1">
                    <?php
                    if ($sessioNova === true) {
                        echo '<center><label id="labelEstat" class="form-control btn-danger">Pendent</label></center>';
                    } elseif ($sessioNova === false && $provisional === false) {
                        echo '<center><label id="labelEstat" class="form-control btn-success">Passada</label></center>';
                    } else {
                        echo '<center><label id="labelEstat" class="form-control btn-warning">Provisional</label></center>';
                    }
                    ?>
                </div>

                <div class="col-sm-2">

                    <strong id="alumnesTotals"><?php echo "Tot: $totalAlum - Pres: $presentAlum - Abs: $absentAlum - Ret: $retardAlum" ?></strong>
                </div>

            </div>
            <div class="row" >
                <div class="col-sm-1">
                    <label>Data</label>
                    <input class="form-control" id="diaSessio" value="<?php echo $dia ?>" readonly/>
                </div>
                <div class="col-sm-1">
                    <label>Nivell</label>
                    <input class="form-control" id="nivellSessio" data-nivell="<?php echo $dadesSessio['nivell'] ?>" value="<?php echo $dadesSessio['nomNivell'] ?>" readonly/>
                </div>

                <div class="col-sm-2">
                    <label>Assignatura</label>
                    <input class="form-control" id="assignaturaSessio" data-assignatura="<?php echo $dadesSessio['assignatura'] ?>" value="<?php echo $dadesSessio['nomAssignatura'] ?>" readonly/>
                </div>
                <div class="col-sm-1">

                    <label>Guàrdia</label>
                    <div class="checkbox col-sm-12">
                        <input id="esGuardia" type="checkbox" value="1" 
                        <?php
                        if ($esguardia == '1' || $dadesSessio['esguardia'] == '1')
                            echo 'checked';
                        //($sessioNova == true && $esguardia == '1') || ($sessioNova == false && ($dadesSessio['esguardia'] == '1') || ($dadesSessio['esguardia'] == '0' && $esguardia == '1' && $estat = '1'))
                        ?> disabled>
                    </div>


                </div>
                <div class="col-sm-1">
                    <label>Comentari</label>
                    <button data-comentari="<?php echo $dadesSessio['comentari']; ?>" type="button" class="btn btn-info form-control" data-toggle="modal" data-target="#comentariModalForm" id="editaComentari" onclick="editaComentari();" data-tooltip="tooltip" title="Agegeix un comentari a la sessió">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </button>
                </div>

            </div>

            <div class="row" >
                <div class="col-sm-1">
                    <label>Hora</label>
                    <input class="form-control" id="horaSessio" value="<?php echo $hora ?>" readonly/>
                </div>


                <div class="col-sm-1">
                    <label>Aula</label>
                    <div id="divaulaSessio">
                        <button value="<?php echo $dadesSessio['aula'] ?>" class="btn btn-primary dropdown-toggle" 
                                type="button" data-toggle="dropdown" id="butDropaulaSessio" data-tooltip="tooltip" title="Tria aula"><?php
                                    if ($dadesSessio['aula'] == '') {
                                        echo 'Tria Aula';
                                    } else {
                                        echo $dadesSessio['nomAula'];
                                    }
                                    ?><span class="caret"></span></button>

                        <?php
                        //executem la consulta

                        $conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
                        if ($conn->connect_error)
                            die($conn->connect_error);

                        //triem el charset de la cerca
                        mysqli_set_charset($conn, "utf8");
                        $query = "select ga01_codi_aula as codi,ga01_descripcio_aula as descripcio from ga01_aula";

                        $result = $conn->query($query);


                        if (!$result)
                            die($conn->error);

                        echo '<ul class="dropdown-menu" id="dropdivaulaSessio">';

                        if ($result->num_rows > 0) {
                            // output data of each row
                            while ($row = $result->fetch_assoc()) {

                                echo '<li><a data-val="' . $row['codi'] . '" onclick="mostradivaulaSessio(this);">' . $row['descripcio'] . '</a></li>';
                            }
                        }

                        echo '</ul>';

                        $result->close();
                        $conn->close();
                        ?>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label>Grup</label>
                    <input class="form-control" id="grupSessio" data-tipus="<?php echo $dadesSessio['tipusgrup'] ?>" data-grup="<?php echo $dadesSessio['grup'] ?>" value="<?php echo $dadesSessio['nomGrup'] ?>" readonly/>
                </div>
                <div class="col-sm-2">
                    <label id="labelprofguardia" data-tipus-subs="<?php
                    //0 no hi ha profe de guàrdia
                    //1 el profe de guàrdia és el substituït
                    //2 el profe de guàrdia és el substitut
                    /* if ($esguardia == '1' && $sessioNova == true) {
                      //el profe substitut passa llista per primer cop
                      echo '1';
                      } elseif ($esguardia != '1' && $sessioNova == true) {
                      //el profe titular passa llista per primer cop
                      echo '0';
                      } elseif ($esguardia == '1' && $sessioNova == false) {

                      //$sessioNova == false && (($dadesSessio['esguardia'] == '1') || ($dadesSessio['esguardia'] == '0' && $esguardia == '1' && $estat = '1'))


                      if ($profeCodi != $_SESSION['prof_actual']) {
                      echo '1';
                      } else {
                      echo '2';
                      }
                      } else {
                      echo '0';
                      } */

                    if ($esguardia == '0') {
                        //és el profe titular.
                        if ($sessioNova == true) {
                            //crea una sessió nove
                            echo '0';
                        } elseif ($sessioNova == false && $dadesSessio['esguardia'] == '0') {
                            //revisa una sessió pròpia
                            echo '0';
                        } else {
                            //revisa una sessió feta per un substitut
                            echo '2';
                        }
                    } else {
                        //profe no titular  perquè és una guàrdia
                        echo '1';
                    }
                    ?>"><?php
                               if ($esguardia == '0') {
                                   //és el profe titular.
                                   if ($sessioNova == true) {
                                       //crea una sessió nove
                                       echo 'prof de guàrdia';
                                   } elseif ($sessioNova == false && $dadesSessio['esguardia'] == '0') {
                                       //revisa una sessió pròpia
                                       echo 'prof de guàrdia';
                                   } else {
                                       //revisa una sessió feta per un substitut
                                       echo 'prof substitut';
                                   }
                               } else {
                                   //profe no titular  perquè és una guàrdia
                                   echo 'prof substituït';
                               }







                               /* if ($esguardia == '1' && $sessioNova == true) {
                                 echo 'prof substituït';
                                 } elseif ($esguardia != '1' && $sessioNova == true) {
                                 echo 'prof de guàrdia';
                                 } elseif (($sessioNova == true && $esguardia == '1') || ($sessioNova == false && ($dadesSessio['esguardia'] == '1') || ($dadesSessio['esguardia'] == '0' && $esguardia == '1' && $estat = '1'))) {

                                 if ($profeCodi != $_SESSION['prof_actual']) {
                                 echo 'prof substituït';
                                 } else {
                                 echo 'prof substitut';
                                 }
                                 } else {
                                 echo 'prof de guàrdia';
                                 } */
                               ?>
                    </label>
                    <input class="form-control" id="profSubsSessio" data-codi="<?php
                    if ($esguardia == '0') {
                        //és el profe titular.
                        if ($sessioNova == true) {
                            //crea una sessió nove
                            echo '';
                        } elseif ($sessioNova == false && $dadesSessio['esguardia'] == '0') {
                            //revisa una sessió pròpia
                            echo '';
                        } else {
                            //revisa una sessió feta per un substitut
                            echo $dadesSessio['codiprofsubs'];
                        }
                    } else {
                        //profe no titular  perquè és una guàrdia
                        echo $profeCodi;
                    }






                    /*  if ($esguardia == '1' && $sessioNova == true) {
                      echo $profeCodi;
                      } elseif ($esguardia != '1' && $sessioNova == true) {
                      echo '';
                      } elseif ($sessioNova == false && ($dadesSessio['esguardia'] == '1') || ($sessioNova == false && ($dadesSessio['esguardia'] == '1') || ($dadesSessio['esguardia'] == '0' && $esguardia == '1' && $estat = '1'))) {

                      if ($profeCodi != $_SESSION['prof_actual']) {
                      echo $profeCodi;
                      } else {
                      echo $dadesSessio['codiprofsubs'];
                      }
                      } else {
                      echo '';
                      } */
                    ?>" value="<?php
                           if ($esguardia == '0') {
                               //és el profe titular.
                               if ($sessioNova == true) {
                                   //crea una sessió nove
                                   echo '';
                               } elseif ($sessioNova == false && $dadesSessio['esguardia'] == '0') {
                                   //revisa una sessió pròpia
                                   echo '';
                               } else {
                                   //revisa una sessió feta per un substitut
                                   echo $dadesSessio['nomprofsubs'];
                               }
                           } else {
                               //profe no titular  perquè és una guàrdia
                               echo $profeNom;
                           }





                           /* if ($esguardia == '1' && $sessioNova == true) {
                             echo $profeNom;
                             } elseif ($esguardia != '1' && $sessioNova == true) {
                             echo '';
                             } elseif ($sessioNova == false && ($dadesSessio['esguardia'] == '1') || ($sessioNova == false && ($dadesSessio['esguardia'] == '1') || ($dadesSessio['esguardia'] == '0' && $esguardia == '1' && $estat = '1'))) {
                             if ($profeCodi != $_SESSION['prof_actual']) {
                             echo $profeNom;
                             } else {
                             echo $dadesSessio['nomprofsubs'];
                             }
                             } else {
                             echo '';
                             } */

                           /* if ($esguardia == '1') {
                             echo $profeNom;
                             } else {
                             echo '';
                             } */
                           ?>" readonly/>
                </div>

            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-7">
                    <div id="divTaulaSessio" class="container-fluid">
                        <!-- aquí es construirà dinàmicament la taula per passar llista --> 
                        <?php
                        if ($sessioNova == true) {
                            //carreguem els alumnes
                            //construim capçalera de la taula
                            echo '<br>';
                            echo '<table id="taulaSessio" class="table">';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th><form class="form-inline"><input type="checkbox" value="" id="checkMarcaDesmarca" onclick="seleccionaTot();"><button type="button" class="btn btn-warning form-control" onclick="esborraAlumnes()" data-tooltip="tooltip" title="Treu alumnes de la sessió"><span class="glyphicon glyphicon-trash"></span></button></form></th>';
                            echo '<th>Nivell</th>';
                            echo '<th>Grup</th>';
                            echo '<th>Alumne</th>';
                            echo '<th>Pres</th>';
                            echo '<th>Abs</th>';
                            echo '<th>Ret</th>';
                            echo'<th><center>CCC</center></th>';
                            echo'<th><center><span class="glyphicon glyphicon-edit"></center></th>';
                            echo '<th><center><span class="glyphicon glyphicon-envelope"></center></span></th>';
                            echo '</tr>';
                            echo '</thead>';

                            //és una sessió nova i recullim les dades bàsicament de l'horari
                            if ($dadesSessio['tipusgrup'] == '0') {
                                //és un grup general
                                $query = "select ga12_id_alumne as codi, ga07_descripcio_grup as descrgrup,ga06_descripcio_nivell as descrnivell,concat(ga11_cognom1, ' ' ,ga11_cognom2 , ', ' , ga11_nom) as alumne,ga11_check_comunica as checkcomunica "
                                        . "from ga12_alumnes_curs ,ga11_alumnes,ga07_grup,ga06_nivell "
                                        . "where ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga12_codi_nivell=" . $dadesSessio['nivell'] . " and ga12_codi_grup=" . $dadesSessio['grup'] . " and ga12_id_alumne=ga11_id_alumne and ga12_codi_grup=ga07_codi_grup and ga06_codi_nivell=ga12_codi_nivell "
                                        . "order by alumne";
                            } else {
                                //és un grup personal d'un profe
                                $query = "select ga24_codi_alumne as codi,ga07_descripcio_grup as descrgrup,ga06_descripcio_nivell as descrnivell,concat(ga11_cognom1, ' ' ,ga11_cognom2 , ', ' , ga11_nom) as alumne, ga11_check_comunica as checkcomunica "
                                        . " from ga24_grups_profes_det,ga23_grups_profes_cap,ga11_alumnes,ga07_grup,ga12_alumnes_curs,ga06_nivell"
                                        . " where ga24_codi_grup=" . $dadesSessio['grup'] . " and ga12_codi_curs=" . $_SESSION['curs_actual'] . " and ga24_codi_grup=ga23_codi_grup and ga24_codi_alumne=ga11_id_alumne and ga24_codi_alumne=ga12_id_alumne and ga12_codi_grup=ga07_codi_grup and ga06_codi_nivell=ga12_codi_nivell order by alumne";
                            }

                            $conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
                            if ($conn->connect_error)
                                die($conn->connect_error);

                            //triem el charset de la cerca
                            mysqli_set_charset($conn, "utf8");

                            //vaig a buscar l'hora anterior
                            $queryHora = "select max(ga10_hora_inici) as horaanterior from ga10_horaris_aula where ga10_codi_curs=" . $_SESSION['curs_actual'] . " and ga10_hora_inici<'" . $hora . "' and ga10_es_descans='0'";
                            //executem la consulta
                            $resultHora = $conn->query($queryHora);

                            if (!$resultHora)
                                die($conn->error);


                            if ($resultHora->num_rows > 0) {
                                $horaAnterior = $resultHora->fetch_assoc()['horaanterior'];
                            } else {
                                $horaAnterior = '';
                            }

                            $resultHora->close();


                            //executem la consulta
                            $result = $conn->query($query);

                            if (!$result)
                                die($conn->error);

                            $conta = 1;
                            if ($result->num_rows > 0) {
                                echo '<tbody id="cosTaulaSessio" data-modifi="">';
                                while ($row = $result->fetch_assoc()) {
                                    //onstruim el cos de la taula
                                    //snem a veure si hi estava absent a l'hora anterior o s'havia passat llista
                                    if ($horaAnterior != '') {
                                        $querySesAnterior = "select ga15_codi_professor as profesessio,ga15_alumne as alumne,ga15_dia as dia, ga15_hora_inici as horainici,ga28_assignatura as assignatura,"
                                                . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=assignatura) as nomassignatura,"
                                                . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=profesessio) as nomcomplet,"
                                                . "(select ga15_check_absent from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=alumne and ga15_codi_professor=profesessio and ga15_dia=dia and ga15_hora_inici=horainici) as checkabsencia"
                                                . " from ga15_cont_presencia,ga28_cont_presencia_cap where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $row['codi'] . " and ga15_dia='" . $dia . "' and ga15_hora_inici='" . $horaAnterior . "' and ga28_codi_curs=ga15_codi_curs and ga28_dia=ga15_dia and ga28_hora=ga15_hora_inici and ga28_professor=ga15_codi_professor";

                                        //echo $querySesAnterior;
                                        $resultSesAnterior = $conn->query($querySesAnterior);
                                        if (!$resultSesAnterior)
                                            die($conn->error);

                                        if ($resultSesAnterior->num_rows > 0) {
                                            $eraAbsent = false;
                                            $contaSessions = 0;
                                            $sessionsAnteriors = [];
                                            while ($rowSesAnterior = $resultSesAnterior->fetch_assoc()) {
                                                if ($rowSesAnterior['checkabsencia'] == '1') {
                                                    //a la sessió anterior era absent
                                                    $sessionsAnteriors[$contaSessions] = $horaAnterior . '<%>' . $rowSesAnterior['nomassignatura'] . '<%>' . $rowSesAnterior['nomcomplet'];
                                                    $eraAbsent = true;
                                                    $contaSessions++;
                                                }
                                            }
                                            if ($eraAbsent == true) {
                                                $sessionsAnteriorsString = join('<#>', $sessionsAnteriors);
                                                $assistAnterior = '<a data-toggle="modal" data-target="#sessionsAnteriorsForm" onclick="carregaSessionsAnteriors(this)" data-sessions-anteriors="' . $sessionsAnteriorsString . '"><h6 class="absentAnterior">absent a la sessió anterior</h6></a>';
                                            } else {
                                                $assistAnterior = '';
                                            }
                                        } else {
                                            $assistAnterior = '<h6 class="absentAnterior">no té sessió anterior</h6>';
                                        }
                                        $resultSesAnterior->close();
                                    } else {
                                        $assistAnterior = '';
                                    }


                                    echo '<tr>';
                                    echo '<td><input type="checkbox" value="" class="checkEsborrar"></td>';
                                    echo '<td>' . $row['descrnivell'] . '</td>';
                                    echo '<td>' . $row['descrgrup'] . '</td>';
                                    echo '<td id="al' . $row['codi'] . '" data-tipus="" data-estat="" data-motiu="" data-num="" data-textfalta="" data-avisresponsables="0" data-avistutor="0" data-comentarialumne="" data-comentariavis="1"><a onclick="mostraFitxaAlumne(this)" data-tooltip="tooltip" title="Ves a la fitxa de l\'alumne">' . $conta . '-' . $row['alumne'] . '</a>' . $assistAnterior . '</td>';
                                    echo '<td><input type="checkbox" value="" checked class="checkAssist" onchange="comprovaCheck(this)"></td>';
                                    echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
                                    echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
                                    echo '<td><button type="button" class="btn form-control" data-toggle="modal" data-target="#faltesModalForm" onclick="carregaFaltesSessio(this)" data-tooltip="tooltip" title="Conductes contràries a la convivència">';
                                    echo '<span class="glyphicon glyphicon-pencil"></span>CCC</button></td>';
                                    echo '<td><button type="button" class="btn form-control" data-toggle="modal" data-target="#comentAlumneForm" onclick="carregaComentAlumne(this)" data-tooltip="tooltip" title="Comentaris de l\'alumne">';
                                    echo '<span class="glyphicon glyphicon-edit"></span></button></td>';
                                    if ($row['checkcomunica'] == '1') {
                                        echo '<td data-tooltip="tooltip" title="Comunicacions activades"><center><input type="checkbox" value="" checked disabled></center></td>';
                                    } else {
                                        echo '<td data-tooltip="tooltip" title="Comunicacions desactivades"><center><input type="checkbox" value="" disabled></center></td>';
                                    }
                                    echo '</tr>';
                                    $conta++;
                                }
                            }

                            //tanquem cos i taula
                            echo '</tbody>';
                            echo '</table>';
                            $result->close();
                            $conn->close();
                        } else {

                            //és una sessió que ja s'havia passat i recullim les dades de la taula 15
                            //carreguem els alumnes
                            //construim capçalera de la taula
                            echo '<br>';
                            echo '<table id="taulaSessio" class="table">';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th><form class="form-inline"><input type="checkbox" value="" id="checkMarcaDesmarca" onclick="seleccionaTot();"><button type="button" class="btn btn-warning form-control" onclick="esborraAlumnes()" data-tooltip="tooltip" title="Treu alumnes de la sessió"><span class="glyphicon glyphicon-trash"></span></button></form></th>';
                            echo '<th>Nivell</th>';
                            echo '<th>Grup</th>';
                            echo '<th>Alumne</th>';
                            echo '<th>Pres</th>';
                            echo '<th>Abs</th>';
                            echo '<th>Ret</th>';
                            echo'<th><center>CCC</center></th>';
                            echo '<th><center><span class="glyphicon glyphicon-edit"></span></center></th>';
                            echo '<th><center><span class="glyphicon glyphicon-envelope"></span></center></th>';
                            echo '</tr>';
                            echo '</thead>';

                            $query = "select ga15_alumne as codi, ga07_descripcio_grup as descrgrup, ga06_descripcio_nivell as descrnivell, concat(ga11_cognom1, ' ', ga11_cognom2, ', ', ga11_nom) as alumne,"
                                    . "ga15_check_comunica as checkcomunica,ga15_check_present, ga15_check_absent, ga15_check_retard, ga15_data_hora_darrera_mod,ga15_comentari as comentari,"
                                    . "(select ga31_tipus_falta from ga31_faltes_ordre where ga15_codi_curs = " . $_SESSION['curs_actual'] . " and ga31_alumne=codi and ga31_codi_professor=" . $profeCodi . " and ga31_dia='" . $dia . "' and ga31_hora_inici='" . $hora . "' and ga31_es_sessio=1) as tipusfalta,"
                                    . "(select ga31_estat from ga31_faltes_ordre where ga15_codi_curs = " . $_SESSION['curs_actual'] . " and ga31_alumne=codi and ga31_codi_professor=" . $profeCodi . " and ga31_dia='" . $dia . "' and ga31_hora_inici='" . $hora . "' and ga31_es_sessio=1) as estatfalta,"
                                    . "(select ga31_motiu from ga31_faltes_ordre where ga15_codi_curs = " . $_SESSION['curs_actual'] . " and ga31_alumne=codi and ga31_codi_professor=" . $profeCodi . " and ga31_dia='" . $dia . "' and ga31_hora_inici='" . $hora . "' and ga31_es_sessio=1) as motiufalta,"
                                    . "(select ga31_id from ga31_faltes_ordre where ga15_codi_curs = " . $_SESSION['curs_actual'] . " and ga31_alumne=codi and ga31_codi_professor=" . $profeCodi . " and ga31_dia='" . $dia . "' and ga31_hora_inici='" . $hora . "' and ga31_es_sessio=1) as numfalta,"
                                    . "(select ga22_nom_falta from ga22_tipus_falta where ga22_codi_falta = tipusfalta) as textfalta,"
                                    . "(select ga31_just_tutor from ga31_faltes_ordre where ga15_codi_curs = " . $_SESSION['curs_actual'] . " and ga31_alumne=codi and ga31_codi_professor=" . $profeCodi . " and ga31_dia='" . $dia . "' and ga31_hora_inici='" . $hora . "' and ga31_es_sessio=1) as justtutor,"
                                    . "(select ga31_just_resp from ga31_faltes_ordre where ga15_codi_curs = " . $_SESSION['curs_actual'] . " and ga31_alumne=codi and ga31_codi_professor=" . $profeCodi . " and ga31_dia='" . $dia . "' and ga31_hora_inici='" . $hora . "' and ga31_es_sessio=1) as justresp,"
                                    . "(select ga43_text from ga43_comentaris_sessio where ga43_codi_curs = " . $_SESSION['curs_actual'] . " and ga43_alumne=codi and ga43_codi_professor=" . $profeCodi . " and ga43_dia='" . $dia . "' and ga43_hora_inici='" . $hora . "' and ga43_es_sessio=1) as comentalumne,"
                                    . "(select ga43_switch_enviar from ga43_comentaris_sessio where ga43_codi_curs = " . $_SESSION['curs_actual'] . " and ga43_alumne=codi and ga43_codi_professor=" . $profeCodi . " and ga43_dia='" . $dia . "' and ga43_hora_inici='" . $hora . "' and ga43_es_sessio=1) as switchenviar"
                                    . " from ga15_cont_presencia, ga11_alumnes, ga07_grup, ga12_alumnes_curs, ga06_nivell"
                                    . " where ga15_codi_curs = " . $_SESSION['curs_actual'] . " and ga15_codi_professor = " . $profeCodi . " and ga15_dia = '" . $dia . "' and ga15_hora_inici = '" . $hora . " ' and ga15_codi_curs=ga12_codi_curs and ga15_alumne = ga11_id_alumne and"
                                    . " ga15_alumne = ga12_id_alumne and ga12_codi_grup = ga07_codi_grup and ga06_codi_nivell = ga12_codi_nivell order by alumne";


                            $conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
                            if ($conn->connect_error)
                                die($conn->connect_error);

                            //triem el charset de la cerca
                            mysqli_set_charset($conn, "utf8");

                            //vaig a buscar l'hora anterior
                            $queryHora = "select max(ga10_hora_inici) as horaanterior from ga10_horaris_aula where ga10_codi_curs=" . $_SESSION['curs_actual'] . " and ga10_hora_inici<'" . $hora . "' and ga10_es_descans='0'";
                            //executem la consulta
                            $resultHora = $conn->query($queryHora);

                            if (!$resultHora)
                                die($conn->error);


                            if ($resultHora->num_rows > 0) {
                                $horaAnterior = $resultHora->fetch_assoc()['horaanterior'];
                            } else {
                                $horaAnterior = '';
                            }

                            $resultHora->close();

                            //executem la consulta
                            $result = $conn->query($query);

                            if (!$result)
                                die($conn->error);

                            $conta = 1;
                            if ($result->num_rows > 0) {
                                echo '<tbody id="cosTaulaSessio" data-modifi="">';
                                while ($row = $result->fetch_assoc()) {
                                    //onstruim el cos de la taula
                                    //anem a veure si hi estava absent a l'hora anterior o s'havia passat llista
                                    if ($horaAnterior != '') {

                                        $querySesAnterior = "select ga15_codi_professor as profesessio,ga15_alumne as alumne,ga15_dia as dia, ga15_hora_inici as horainici,ga28_assignatura as assignatura,"
                                                . "(select ga18_desc_assignatura from ga18_assignatures where ga18_codi_assignatura=assignatura) as nomassignatura,"
                                                . "(select concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) from ga04_professors where ga04_codi_prof=profesessio) as nomcomplet,"
                                                . "(select ga15_check_absent from ga15_cont_presencia where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=alumne and ga15_codi_professor=profesessio and ga15_dia=dia and ga15_hora_inici=horainici) as checkabsencia"
                                                . " from ga15_cont_presencia,ga28_cont_presencia_cap where ga15_codi_curs=" . $_SESSION['curs_actual'] . " and ga15_alumne=" . $row['codi'] . " and ga15_dia='" . $dia . "' and ga15_hora_inici='" . $horaAnterior . "' and ga28_codi_curs=ga15_codi_curs and ga28_dia=ga15_dia and ga28_hora=ga15_hora_inici and ga28_professor=ga15_codi_professor";

                                        //echo $querySesAnterior.'<br>';
                                        $resultSesAnterior = $conn->query($querySesAnterior);
                                        if (!$resultSesAnterior)
                                            die($conn->error);

                                        if ($resultSesAnterior->num_rows > 0) {
                                            $eraAbsent = false;
                                            $contaSessions = 0;
                                            $sessionsAnteriors = [];
                                            while ($rowSesAnterior = $resultSesAnterior->fetch_assoc()) {
                                                if ($rowSesAnterior['checkabsencia'] == '1') {
                                                    //a la sessió anterior era absent
                                                    $sessionsAnteriors[$contaSessions] = $horaAnterior . '<%>' . $rowSesAnterior['nomassignatura'] . '<%>' . $rowSesAnterior['nomcomplet'];
                                                    $eraAbsent = true;
                                                    $contaSessions++;
                                                }
                                            }
                                            if ($eraAbsent == true) {
                                                $sessionsAnteriorsString = join('<#>', $sessionsAnteriors);
                                                $assistAnterior = '<a data-toggle="modal" data-target="#sessionsAnteriorsForm" onclick="carregaSessionsAnteriors(this)" data-sessions-anteriors="' . $sessionsAnteriorsString . '"><h6 class="absentAnterior">absent a la sessió anterior</h6></a>';
                                            } else {
                                                $assistAnterior = '';
                                            }
                                        } else {
                                            $assistAnterior = '<h6 class="absentAnterior">no té sessió anterior</h6>';
                                        }
                                        $resultSesAnterior->close();
                                    } else {
                                        $assistAnterior = '';
                                    }


                                    $colorbuto = "";
                                    if ($row['tipusfalta'] != "") {
                                        //'alumne té falta d'ordre
                                        $colorbuto = "btn-danger";
                                    } else {
                                        $colorbuto = "";
                                    }

                                    if ($row['comentalumne'] == '') {
                                        //no té avis
                                        $switchEnviar = "1";
                                        $botoBlau = "";
                                    } else {
                                        $switchEnviar = $row['switchenviar'];

                                        $botoBlau = "btn-info";
                                    }

                                    //construim el cos de la taula
                                    echo '<tr>';
                                    echo '<td><input type="checkbox" value="" class="checkEsborrar"></td>';
                                    echo '<td>' . $row['descrnivell'] . '</td>';
                                    echo '<td>' . $row['descrgrup'] . '</td>';
                                    if ($row['comentari'] != '') {
                                        $colorComent = 'style="color:orange"';
                                    } else {
                                        $colorComent = '';
                                    }
                                    //en aquesta cella posarem el codi de l'alumne i les dades de la possible falta d'ordre
                                    echo '<td id="al' . $row['codi'] . '" data-tipus="' . $row['tipusfalta'] . '" data-estat="' . $row['estatfalta'] . '" data-motiu="' . $row['motiufalta'] . '" data-num="' . $row['numfalta'] . '" data-textfalta="' . $row['textfalta'] . '" data-avisresponsables="' . $row['justresp'] . '" data-avistutor="' . $row['justtutor'] . '" data-comentarialumne="' . $row['comentalumne'] . '" data-comentariavis="' . $switchEnviar . '"><a onclick="mostraFitxaAlumne(this)" data-tooltip="tooltip" title="Ves a la fitxa de l\'alumne">' . $conta . '-' . $row['alumne'] . ' </a><a href="#" data-toggle="tooltip" data-titol="' . str_replace('"', '&quot;', $row['comentari']) . '" title="' . str_replace('"', '&quot;', $row['comentari']) . '"><span class="glyphicon glyphicon-pencil" ' . $colorComent . '></span></a>' . $assistAnterior . '</td>';



                                    if ($row['ga15_check_present'] === '1') {
                                        echo '<td><input type="checkbox" value="" checked class="checkAssist" onchange="comprovaCheck(this)"></td>';
                                        echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
                                        echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
                                    } elseif ($row['ga15_check_absent'] === '1') {
                                        echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
                                        echo '<td><input type="checkbox" value="" checked class="checkAssist" onchange="comprovaCheck(this)"></td>';
                                        echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
                                    } else {
                                        echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
                                        echo '<td><input type="checkbox" value="" class="checkAssist" onchange="comprovaCheck(this)"></td>';
                                        echo '<td><input type="checkbox" value="" checked class="checkAssist" onchange="comprovaCheck(this)"></td>';
                                    }

                                    echo '<td><button type="button" class="btn form-control ' . $colorbuto . '" data-toggle="modal" data-target="#faltesModalForm" onclick="carregaFaltesSessio(this)" data-tooltip="tooltip" title="Conductes contràries a la convivència">';
                                    echo '<span class="glyphicon glyphicon-pencil"></span>CCC</button></td>';
                                    echo '<td><button type="button" class="btn form-control ' . $botoBlau . '" data-toggle="modal" data-target="#comentAlumneForm" onclick="carregaComentAlumne(this)" data-tooltip="tooltip" title="Comentaris de l\'alumne">';
                                    echo '<span class="glyphicon glyphicon-edit"></span></button></td>';

                                    if ($row['checkcomunica'] == '1') {
                                        echo '<td data-tooltip="tooltip" title="Comunicacions activades"><center><input type="checkbox" value="" checked disabled></center></td>';
                                    } else {
                                        echo '<td data-tooltip="tooltip" title="Comunicacions desactivades"><center><input type="checkbox" value="" disabled></center></td>';
                                    }
                                    echo '</tr>';
                                    $conta++;
                                }
                            }


                            //tanquem cos i taula
                            echo '</tbody>';
                            echo '</table>';
                            $result->close();
                            $conn->close();
                        }
                        ?>

                    </div>


                </div>
            </div>
        </div>



        <div id="comentariModalForm" class="modal fade" role="dialog"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3 class="modal-title">Comentari sessió</h3>

                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <textarea class="form-control" rows="5" id="commentari"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button id="desaButtonComentari" type="button" class="btn btn-success" data-dismiss="modal" onclick="desaButtonComentari();">Desa</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tanca</button>
                    </div>
                </div>

            </div>
        </div>

        <div id="comentAlumneForm" class="modal fade" role="dialog"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                        <h3 class="modal-title" id="alumneComentCap" data-codiComentCap=""></h3>
                    </div>
                    <div class="modal-body">

                        <div class="container-fluid">
                            <label><input id="avisRespComent" type="checkbox" checked> Envia a pares</label>
                            <label><input id="totsAlumnesComent" type="checkbox"> A tots els alumnes</label>
                            <textarea class="form-control" rows="5" id="commentariAlumne"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button id="desaComentariAlumne" type="button" class="btn btn-success" data-dismiss="modal" onclick="desaComentariAlumne();">Desa</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tanca</button>
                    </div>
                </div>

            </div>
        </div>


        <div id="sessionsAnteriorsForm" class="modal fade" role="dialog"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3 class="modal-title" id="alumneSessionsAnteriors">Absències anteriors</h3>
                        <h4 class="modal-title" id="alumneAbsAnteriors" data-codi=""></h4>
                    </div>
                    <div class="modal-body">

                        <div id="sessionsAnteriorsDiv">

                        </div>

                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-default" data-dismiss="modal">Tanca</button>
                    </div>
                </div>

            </div>
        </div>




        <div id="faltesModalForm" class="modal fade" role="dialog"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" onclick="iniDropTipus();">&times;</button>
                        <h3 class="modal-title" id="alumneFalta" data-codi=""></h3>

                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-6">
                                    <p><strong>Assenyala la falta <input type="checkbox" value="" id="checkFalta" onchange="verificaFalta(this);"></strong></p>

                                    <div class="container" id="divTipusFalta">


                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <strong>Envia avís a:</strong>
                                    <div class="checkbox">
                                        <label><input id="avisResponsables" type="checkbox" checked>Responsables</label>
                                    </div>

                                    <div class="checkbox">
                                        <label><input id="avisTutor" type="checkbox" checked>Tutor</label>
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-sm-12">
                                    <p><strong>Motiu</strong></p>

                                    <textarea class="form-control" rows="3" id="motiuFalta"></textarea>

                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button id="desaButtonFaltes" type="button" class="btn btn-success" data-dismiss="modal" onclick="desaButtonFaltes();">Desa</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="iniDropTipus();">Tanca</button>
                    </div>
                </div>

            </div>
        </div>
        <script>
            $(document).ready(function () {
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>
    </body>
</html>



