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
        <title>Consulta d'absències i retards per data</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <script src="js/variablesGlobals.js"></script>
        <script src="jquery/sessionsMassives.js"></script>
        <script src="jquery/funcionsjquery.js"></script>
        <script src="jquery/sessionsjquery.js"></script>

        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <!--<script src="https://code.jquery.com/jquery-1.12.4.js"></script>-->
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="stylesheet" href="css/sessionsMassives.css">

        <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.css" rel="stylesheet">
        <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.js"></script>
    </head>
    <body onload="controlaCredencials('sessionsMassives');carregaDadesIniSessionsMassives();">

        <div class="container-fluid">
            <div class="page-header" id="sessionsMassives">

            </div>   
        </div>

        <br>
        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-4" >
                    <div id="divProgressSessions" class="container-fluid">
                        <!-- aquí la progress bar--> 


                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-1" >
                    <label>Nivell</label>
                    <div class="dropdown" id="nivellSessionsMassives">
                        <!-- posarem el dropdown de nivell -->
                    </div>
                    <label>Grup</label>
                    <div class="dropdown" id="grupSessionsMassives">
                        <button data-val="" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="butDropgrupSessionsMassives" disabled>Tria Grup<span class="caret"></span></button>

                    </div>  
                </div>
                <div class="col-sm-2" >
                    <label>Dia</label>
                    <input type='text' id='dataSessionsMassives' class="form-control" onchange="comprovaDades()"/>
                    <label></label>
                    <button type="button" class="btn btn-info form-control" id="cercaSesMassives" onclick="cercaSessionsMassives();" disabled>
                        <span class="glyphicon glyphicon-search"></span>Cerca
                    </button>
                </div>

                <div class="col-sm-1" >
                    <br>
                    <button type="button" class="btn btn-success form-control" id="creaMassives" onclick="creaSessionsMassives();" disabled>
                        <span class="glyphicon glyphicon-calendar"></span>Crea
                    </button>
                    <br>
                    <br>
                    <button type="button" class="btn btn-danger form-control" id="esborraMassives" onclick="esborraSessionsMassives();" data-toggle="tooltip" title="S'esborraran totes les sessions marcades amb el checkbox. Per tant, també es pot esborrar l'assistència d'altres alumnes" disabled >
                        <span class="glyphicon glyphicon-trash"></span>Esborra
                    </button>
                </div>



            </div>


            <div class="row">
                <div class="col-sm-7" >
                    <div id="divAlumnesSessio" class="container-fluid">
                        <!-- aquí es mostraran els alumnes d'una sessió--> 
                    </div>
                </div>
            </div>


        </div>

        <div id="sessionsMassivesModalForm" class="modal fade" role="dialog"  data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Sessions a gestionar</h4>
                        <h5 class="modal-title" id="grupClasse"></h5>
                        <h5 class="modal-title" id="dataSessions"></h5>
                    </div>
                    <div class="modal-body" id="relacioSessions">
                        <div class="container-fluid col-sm-12">
                            <div class="row">

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="creaSessionsMassives" type="button" class="btn btn-success" data-dismiss="modal" onclick="gestionaSessionsTriades();"><span class="glyphicon glyphicon-ok"></span></button>
                        <button id="esborraSessionsMassives" type="button" class="btn btn-danger" data-dismiss="modal" onclick="gestionaSessionsTriades();"><span class="glyphicon glyphicon-trash"></span></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tanca</button>
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
