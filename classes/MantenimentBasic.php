<?php

require 'Databases.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mantenimentBasic
 *
 * @author xhuix
 */
class MantenimentBasic {

    //put your code here
    public static function carregaTaula($taula, $camps, $alias, $amplades, $campPrimari, $descr, $tipusCamp, $dadesForana, $div, $campsCondicio, $valorsCondicio, $ordenacio) {


        //establim la connexió
        $conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
        if ($conn->connect_error)
            die($conn->connect_error);

        //triem el charset de la cerca
        mysqli_set_charset($conn, "utf8");

        $comptaForanes = 0;
        $taulaForanes = [];
        $joinForanes = [];
        $campForanes = [];


        //preparem laquery tenint en compte que hi poden haver joins
        for ($i = 0; $i < count($dadesForana) && $dadesForana[0] !== ''; $i++) {

            $dadesSplitForanes = explode('<#>', $dadesForana[$i]);
            $taulaForanes[$comptaForanes] = $dadesSplitForanes[0];
            $joinForanes[$comptaForanes] = $dadesSplitForanes[1];
            $campForanes[$comptaForanes] = $dadesSplitForanes[2];
            $comptaForanes++;
        }



        $query = "select ";

        $campsJoin = join(',', $camps);

        //engantxem els camps foranis de join
        if (count($campForanes) > 0) {

            $campsJoin = $campsJoin . ',' . join(',', $campForanes);
        }

        $query .= $campsJoin . " from " . $taula;


        //enganxem les taules foranes
        if (count($taulaForanes) > 0) {

            $query = $query . ',' . join(',', $taulaForanes);
        }

        //condició where foranes
        $hihaWhere = false;
        $whereForanes = '';
        $comptaForanesBis = 0;
        //enganxem la condició de join
        for ($i = 0; $i < count($camps); $i++) {
            if ($tipusCamp[$i] === '2') {
                //és clau forana
                if ($comptaForanesBis == 0) {
                    $hihaWhere = true;
                    //primera forana
                    $whereForanes = $whereForanes . " where " . $camps[$i] . " = " . $joinForanes[$comptaForanesBis];
                } else {
                    $whereForanes = $whereForanes . " and " . $camps[$i] . " = " . $joinForanes[$comptaForanesBis];
                }
                $comptaForanesBis++;
            }
        }

        //condicions
        for ($i = 0; $i < count($campsCondicio); $i++) {
            if ($campsCondicio[$i] !== '') {
                if ($i === 0 && $hihaWhere === false) {
                    //no hi havia joins cal posar el where
                    $whereForanes = $whereForanes . " where " . $campsCondicio[$i] . " = '" . $valorsCondicio[$i] . "'";
                } else {
                    $whereForanes = $whereForanes . " and " . $campsCondicio[$i] . " = '" . $valorsCondicio[$i] . "'";
                }
            }
        }


        //ordenació
        if ($ordenacio[1] === '0') {
            //ascendent
            $ordenaClausula = " order by " . $camps[(int) $ordenacio[0]] . " asc ";
        } else {
            //descendent
            $ordenaClausula = " order by " . $camps[(int) $ordenacio[0]] . " desc ";
        }



        $query .= $whereForanes . $ordenaClausula;

        $query;
        //executem la consulta
        $result = $conn->query($query);


        if (!$result)
            die($conn->error);

        $totalAmple = 0;
        //calculem l'amplada total de la taula
        foreach ($amplades as $ample) {
            $totalAmple += $ample;
        }

        //construim capçalera de la taula        
        echo '<br>';
        //filera dels botons d'alta i baixa
        echo '<div class="row">';

        echo '<div class="col-sm-1" >';
        echo '<button type="button" class="btn btn-success form-control" data-toggle="modal" data-target="#taulesAuxiliarsModal" onclick="nouItem(&#39;' . $taula . '&#39;,&#39;' . $descr . '&#39;);comprovaCamps(&#39;' . $div . '&#39;,&#39;relacioCamps&#39;)">';
        echo '<span class="glyphicon glyphicon-plus"></span>';
        echo '</button>';
        echo '</div>';
        echo '<div class="col-sm-1" >';
        echo '<button type="button" class="btn btn-danger form-control" onclick="esborraItems(&#39;' . $taula . '&#39;,&#39;' . $descr . '&#39;);">';
        echo '<span class="glyphicon glyphicon-trash"></span>';
        echo '</button>';
        echo '</div>';



        echo '</div>';
        echo '<div class="col-sm-' . ($totalAmple + 2) . '">';
        echo '<div class="row">';

        echo '<table id="taula' . $taula . '" class="table table-fixed" data-camp-primari="' . $campPrimari . '">';
        echo '<thead>';
        echo '<tr>';
        //icona d'esborrar
        echo '<th class="col-sm-1"><span class="glyphicon glyphicon-trash"></span></th>';
        for ($i = 0; $i < count($alias); $i++) {

            if ($i === (int) $ordenacio[0]) {
                if ($ordenacio[1] === '0') {
                    $ordenacioH = '<span class="glyphicon glyphicon-arrow-up"></span>';
                } else {
                    $ordenacioH = '<span class="glyphicon glyphicon-arrow-down"></span>';
                }
            } else {
                $ordenacioH = '';
            }

            if ($tipusCamp[$i] === '0' || $tipusCamp[$i] === '3') {
                echo '<th class="col-sm-' . $amplades[$i] . '"><a onclick="ordenaCerca(this,&#39;' . $div . '&#39;)">' . $alias[$i] . '</a>' . $ordenacioH . '</th>';
            } else if ($tipusCamp[$i] === '1') {
                echo '<th class="col-sm-' . $amplades[$i] . '"><a onclick="ordenaCerca(this,&#39;' . $div . '&#39;)">' . $alias[$i] . '</a>' . $ordenacioH . '</th>';
            } else if ($tipusCamp[$i] === '2') {
                echo '<th class="col-sm-' . $amplades[$i] . '"><a onclick="ordenaCerca(this,&#39;' . $div . '&#39;)">' . $alias[$i] . '</a>' . $ordenacioH . '</th>';
            }
        }
        echo '<th class="col-sm-1"><center><span class="glyphicon glyphicon-pencil"></span></center></th>';
        echo '</tr>';
        echo '</thead>';


        if ($result->num_rows > 0) {
            $cont = 0;
            $numCampsFornanis = 0;
            echo '<tbody id="cosTaula' . $taula . '">';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                //columna del check
                echo '<td class="col-sm-1"><input type="checkbox" class="checkEsborrar"></td>';
                for ($i = 0; $i < count($camps); $i++) {
                    if ($tipusCamp[$i] === '0' || $tipusCamp[$i] === '3') {
                        if ($tipusCamp[$i] === '0') {
                            echo '<td class="col-sm-' . $amplades[$i] . '">' . $row[$camps[$i]] . '</td>';
                        } else {
                            //girem la data
                            echo '<td class="col-sm-' . $amplades[$i] . '">' . substr($row[$camps[$i]], 8) . '/' . substr($row[$camps[$i]], 5, 2) . '/' . substr($row[$camps[$i]], 0, 4) . '</td>';
                        }
                    } else if ($tipusCamp[$i] === '1') {
                        if ($row[$camps[$i]] === '1') {
                            $esChecked = 'checked';
                        } else {
                            $esChecked = '';
                        }
                        echo '<td class="col-sm-' . $amplades[$i] . '"><center><input type="checkbox" class="check' . $camps[$i] . '" ' . $esChecked . ' disabled></center></td>';
                    } else if ($tipusCamp[$i] === '2') {
                        //és una clau forana pertant caldrà agafar el campforani                        
                        echo '<td class="col-sm-' . $amplades[$i] . '" data-codiforana="' . $row[$camps[$i]] . '">' . $row[$campForanes[$numCampsFornanis]] . '</td>';
                        $numCampsFornanis++;
                    }
                }
                //columne de manteniment
                echo '<td class="col-sm-1"><button type="button" class="btn btn-info form-control" data-toggle="modal" data-target="#taulesAuxiliarsModal" onclick="editaItem(&#39;' . $taula . '&#39;,&#39;' . $descr . '&#39;,this);comprovaCamps(&#39;' . $div . '&#39;,&#39;relacioCamps&#39;)"><span class="glyphicon glyphicon-pencil"></span></button></td>';
                echo '</tr>';
                $numCampsFornanis = 0;
            }
            //tanquem cos
            echo '</tbody>';
        }

        //tanquem taula

        echo '</table>';
        echo '</div>';
        echo '</div>';
        $result->close();
        $conn->close();
    }

    public static function actualitzaTaula($mode, $taula, $camps, $valors, $campPrimari, $clauPrimaria) {
        //establim la connexió
        $conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
        if ($conn->connect_error)
            die($conn->connect_error);

        //triem el charset de la cerca
        mysqli_set_charset($conn, "utf8");

        if ($mode == '0') {

            for ($i = 0; $i < count($valors); $i++) {
                $valors[$i] = str_replace("'", "''", $valors[$i]);
            }

            //és una inserció
            $query = "insert into " . $taula;

            $campsString = " (" . join(',', $camps) . ") ";
            $valorsString = " values ('" . join("','", $valors) . "')";


            //$valorsString= str_replace("'", "''",$valorsString);
            $query = $query . $campsString . $valorsString;


            $resposta = $conn->query($query);
            if ($resposta === false) {
                //hi ha registres relacionats a altres taules
                $conn->close();
                return false;
            } else {
                $conn->close();
                return true;
            }
        } else {
            //és una modificació
            $query = " update " . $taula . " set ";
            for ($i = 0; $i < count($camps); $i++) {
                $valors[$i] = str_replace("'", "''", $valors[$i]);
                if ($i < count($camps) - 1) {
                    $liniaCamp = $camps[$i] . "=" . "'" . $valors[$i] . "',";
                } else {
                    $liniaCamp = $camps[$i] . "=" . "'" . $valors[$i] . "'";
                }
                $query .= $liniaCamp;
            }

            $query = $query . " where " . $campPrimari . " = " . $clauPrimaria;
            //executem query

            $resposta = $conn->query($query);



            if ($resposta === false) {
                //hi ha registres relacionats a altres taules
                $conn->close();
                return false;
            } else {
                $conn->close();
                return true;
            }
        }
    }

    public static function esborraTaula($taula, $codisEsborrat, $campPrimari) {
        //establim la connexió
        $conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
        if ($conn->connect_error)
            die($conn->connect_error);

        //triem el charset de la cerca
        mysqli_set_charset($conn, "utf8");

        $query = "delete from " . $taula . " where " . $campPrimari . " in (" . join(",", $codisEsborrat) . ")";

        //executem query
        $resposta = $conn->query($query);

        if ($resposta === false) {
            //hi ha registres relacionats a altres taules
            $conn->close();
            return false;
        } else {
            $conn->close();
            //modifiquem el fitxer xml de confiurarió
            if ($taula === 'ga39_perfils_usuaris') {
                modificaXmlPermisos($codisEsborrat);
            }


            return true;
        }
    }

    public static function construeixDetallDrop($foranaTaula, $foranaId, $foranaCamp, $caption, $codiCamp, $dadaCamp, $div) {
        if ($codiCamp === '') {
            $textVisible = $caption;
            $valorVisible = '';
            $esObligatoriEstil = 'alert-danger';
        } else {
            $textVisible = $dadaCamp;
            $valorVisible = $codiCamp;
            $esObligatoriEstil = '';
        }


        $conn = new mysqli(Databases2::$host, Databases2::$user, Databases2::$password, Databases2::$dbase);
        if ($conn->connect_error)
            die($conn->connect_error);

        echo '<br>';
        echo '<div class="dropdown">';
        echo '<button class="btn btn-primary dropdown-toggle ' . $esObligatoriEstil . '" type="button" data-toggle="dropdown" id="buttonDropRef' . $foranaTaula . '" value="' . $valorVisible . '">' . $textVisible . '<span class="caret"></span></button>';
        echo '<ul class="dropdown-menu" style="height: 250px; overflow: auto">';


//triem el charset de la cerca
        mysqli_set_charset($conn, "utf8");

        $query = "select " . $foranaId . " as codi, " . $foranaCamp . " as descripcio from " . $foranaTaula . " order by " . $foranaCamp;

        $result = $conn->query($query);

        if (!$result)
            die($conn->error);

        if ($result->num_rows > 0) {
            // output data of each row

            while ($row = $result->fetch_assoc()) {

                echo '<li><a data-val="' . $row['codi'] . '" onclick="mostraDropReferencia(&#39;' . $foranaTaula . '&#39;,this);comprovaCamps(&#39;' . $div . '&#39;,&#39;relacioCamps&#39;)">' . $row['descripcio'] . '</a></li>';
            }
        }
        echo '</ul></div><br>';
        $result->close();
        $conn->close();
    }

}

function modificaXmlPermisos($codisEsborrat) {

    $xml = simplexml_load_file("../xml/configuracio/mainMenu.xml") or die("No es pot crear l'objecte");

    //nodel de nivell 0
    $node = $xml->xpath("//menuOption");

    for ($i = 0; $i < count($node); $i++) {
        $referencia = $node[$i]->attributes()['referencia'];
        if ($referencia !== '') {
            //hi ha referència per tant hi han permisos
            $pernisos = $node[$i]->attributes()['permis'];
            $permisosSplit = explode('-', $pernisos);
            for ($j = 0; $j < count($permisosSplit); $j++) {
                for ($k = 0; $k < count($codisEsborrat); $k++) {
                    if ($permisosSplit[$j] === $codisEsborrat[$k]) {
                        unset($permisosSplit[$j]);
                    }
                }
            }
            $permisosSplitJoin = join('-', $permisosSplit);
            $node[$i]->attributes()['permis'] = $permisosSplitJoin;
        }
    }

    //nodes de nivell 1
    $node = $xml->xpath("//menuItem");

    for ($i = 0; $i < count($node); $i++) {
        $referencia = $node[$i]->attributes()['referencia'];
        if ($referencia !== '') {
            //hi ha referència per tant hi han permisos
            $pernisos = $node[$i]->attributes()['permis'];
            $permisosSplit = explode('-', $pernisos);
            for ($j = 0; $j < count($permisosSplit); $j++) {
                for ($k = 0; $k < count($codisEsborrat); $k++) {
                    if ($permisosSplit[$j] === $codisEsborrat[$k]) {
                        unset($permisosSplit[$j]);
                    }
                }
            }
            $permisosSplitJoin = join('-', $permisosSplit);
            $node[$i]->attributes()['permis'] = $permisosSplitJoin;
        }
    }


    //nodes de nivell 2
    $node = $xml->xpath("//subMenuItem");

    for ($i = 0; $i < count($node); $i++) {
        $referencia = $node[$i]->attributes()['referencia'];
        if ($referencia !== '') {
            //hi ha referència per tant hi han permisos
            $pernisos = $node[$i]->attributes()['permis'];
            $permisosSplit = explode('-', $pernisos);
            for ($j = 0; $j < count($permisosSplit); $j++) {
                for ($k = 0; $k < count($codisEsborrat); $k++) {
                    if ($permisosSplit[$j] === $codisEsborrat[$k]) {
                        unset($permisosSplit[$j]);
                    }
                }
            }
            $permisosSplitJoin = join('-', $permisosSplit);
            $node[$i]->attributes()['permis'] = $permisosSplitJoin;
        }
    }


    $xml->saveXML('../xml/configuracio/mainMenu.xml');
}
