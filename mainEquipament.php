<?php
header("Cache-Control: no-cache");
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<html>
    <head>
        <title>Préstec d'equipaments</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        

        <script src="jquery/sessionsjquery.js"></script>
        <script src="jquery/funcionsjquery.js"></script>
        <script src="jquery/equipament.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <!--<link rel="stylesheet" href="/resources/demos/style.css">-->
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <!--<script src="https://code.jquery.com/jquery-2.2.4.js"></script>-->
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <script>
            $.widget.bridge('uibutton', $.ui.button);
            $.widget.bridge('uitooltip', $.ui.tooltip);
        </script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/equipament.css">

        <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.js"></script>

    </head>
    <body onload="controlaCredencials('dadesProfEquipament');carregaEquipament('0', '1');iniTooltip();">
        <div class="container-fluid">
            <div class="page-header" id="dadesProfEquipament">

            </div>   
        </div>

        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-2">
                    <button type="button" class="btn btn-info form-control" data-toggle="modal" data-target="#agrupacioPrestecs" data-tooltip="tooltip" data-placement="top" title="Estadístiques de préstecs"><span class="glyphicon glyphicon-list-alt"></span> Estadístiques</button>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <form>
                        <label class="radio-inline">
                            <input type="radio" id="radioTotal" onclick="filtraPerDispo();" name="optradio" checked>Tots
                        </label>
                        <label class="radio-inline">
                            <input type="radio" id="radioDispo" onclick="filtraPerDispo();" name="optradio">Disponibles
                        </label>
                        <label class="radio-inline">
                            <input type="radio" id="radioPrestats" onclick="filtraPerDispo();" name="optradio">Prestats
                        </label>
                    </form>
                </div>

            </div>
        </div>

        <br>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-8" >
                    <div id="divTaulaEquipament" class="container-fluid">

                    </div>
                </div>

            </div>
        </div>

        <div id="prestaEquipament" class="modal fade" role="dialog"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3 class="modal-title" id="nomEquipament"></h3>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label>Nivell</label>
                                    <div class="dropdown" id="divNivellPrestec">
                                        <!-- aquí posarem el dropdown de nivel -->
                                    </div>

                                </div>
                                <div class="col-sm-3" >
                                    <label>Grup</label>
                                    <div class="dropdown" id="divGrupPrestec">
                                        <button data-val="" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="butDropdivGrupPrestec" disabled>Tria Grup<span class="caret"></span></button>
                                        <!-- aquí posarem el dropdown de grup -->
                                    </div>
                                </div>
                                <div class="col-sm-6" >
                                    <label>Alumnes</label>
                                    <div class="dropdown" id="divAlumnePrestec">
                                        <button data-val="" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="butDropdivAlumnePrestec" disabled>Tria Alumne<span class="caret"></span></button>
                                        <!-- aquí posarem el dropdown de grup -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">                               
                        <button id="desaPrestec" type="button" class="btn btn-success" data-dismiss="modal" onclick="desaPrestec();" disabled><span class="glyphicon glyphicon-ok"></span>Presta</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="tancaPrestec();">Tanca</button>
                    </div>
                </div>

            </div>
        </div>
        <div id="detallPrestecs" class="modal fade" role="dialog"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3 class="modal-title" id="nomEquipDetall"></h3>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div id="detallPrestecEquip" class="col-sm-12">


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">                               
                        <button id="esborraPrestec" type="button" class="btn btn-danger" data-dismiss="modal" onclick="esborraPrestec();" disabled><span class="glyphicon glyphicon-trash"></span></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tanca</button>
                    </div>
                </div>

            </div>
        </div>

        <div id="agrupacioPrestecs" class="modal fade" role="dialog"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label>Data ini préstec</label>
                                    <input type='text' id='dataInicialResum' class="form-control"/>

                                </div>

                                <div class="col-sm-3">
                                    <label>Data fi préstec</label>
                                    <input type='text' id='dataFinalResum' class="form-control"/>

                                </div>
                                <div class="col-sm-3">
                                    <div class="radio">
                                        <label><input type="radio" name="optradio" id="checkEquip" checked>Agrupat per equip</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="optradio" id="checkAlumne">Agrupat per alumne</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="optradio" id="checkPresta">Agrupat per prestador</label>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <button type="button" class="btn btn-info" id="cercaResumPrestecs" onclick="cercaResumPrestecs();"><span class="glyphicon glyphicon-search"></span>Cerca</button>

                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div id="detallAgrupacioPrestecs" class="col-sm-6">


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">                               
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tanca</button>
                    </div>
                </div>
            </div>     
        </div>

    </body>
</html>
