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
        <script src="jquery/sessionsMassivesAlumne.js"></script>
        <script src="jquery/funcionsjquery.js"></script>
        <script src="jquery/sessionsjquery.js"></script>

        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <!--<script src="https://code.jquery.com/jquery-1.12.4.js"></script>-->
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="stylesheet" href="css/sessionsMassivesAlumne.css">

        <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.css" rel="stylesheet">
        <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.js"></script>
        
    </head>
    <body onload="controlaCredencials('sessionsMassivesAlumne');carregaDadesIniSessionsMassivesAlumne();">
        <div class="container-fluid">
            <div class="page-header" id="sessionsMassivesAlumne">

            </div>   
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-2" >
                    <label>Nivell</label>
                    <div class="dropdown" id="divNivellSessionsMassives">
                        <!-- aquí posarem el dropdown de nivel -->
                    </div>

                </div>
                <div class="col-sm-2" >
                    <label>Grup</label>
                    <div class="dropdown" id="divGrupSessionsMassives">
                        <button data-val="" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="butDropdivGrupSessionsMassives" disabled>Tria Grup<span class="caret"></span></button>
                        <!-- aquí posarem el dropdown de grup -->
                    </div>
                </div>
                <div class="col-sm-3" >
                    <label>Alumnes</label>
                    <div class="dropdown" id="divAlumnesSessionsMassives">
                        <button data-val="" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="butDropdivSessionsMassives" disabled>Tria Alumne<span class="caret"></span></button>
                        <!-- aquí posarem el dropdown de grup -->
                    </div>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-sm-2" >
                    <label>Data inicial</label>
                    <input type='text' id='dataInicialSessionsMassives' class="form-control" onchange="posaDataFinal();comprovaCamps();" readonly/>

                </div>
                <div class="col-sm-2" >
                    <label>Data Final</label>
                    <input type='text' id='dataFinalSessionsMassives' class="form-control" onchange="comprovaCamps()" readonly/>

                </div>

                <div class="col-sm-3" >
                    <br>
                    <form>
                        <label class="radio-inline">
                            <input type="radio" name="presenciaRadio" id="presentOption" class="presenciaRadioClass" disabled checked>Present
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="presenciaRadio" id="absentOption" class="presenciaRadioClass" disabled>Absent
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="presenciaRadio" id="retardOption" class="presenciaRadioClass" disabled>Retard
                        </label>
                    </form>
                </div>

            </div>
            <div class="row">
                <div class="col-sm-4" >
                    <br>
                    <button type="button" class="btn btn-success form-control" id="mostraMassives" onclick="mostraSessionsMassivesAlumne();" disabled>
                        <span class="glyphicon glyphicon-search"></span>
                    </button>
                </div>
                <div class="col-sm-3" >
                    <label for="comment">Comentari</label>
                    <textarea class="form-control" rows="1" id="comentari" disabled></textarea>

                </div>
            </div>

            <div class="row">
                <div class="col-sm-2" >

                    <button type="button" class="btn btn-success form-control" id="creaMassives" onclick="creaSessionsMassivesAlumne();" disabled>
                        <span class="glyphicon glyphicon-calendar"></span>Crea
                    </button>
                </div>
                <div class="col-sm-2" >


                    <button type="button" class="btn btn-danger form-control" id="esborraMassives" onclick="esbSessionsMassivesAlumne();" disabled >
                        <span class="glyphicon glyphicon-trash"></span>Esborra
                    </button>
                </div>

            </div>

        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-4" id="divTaulaSessionsAlumne" >
                    <!--aqui es mostraran les sessions de l'alumne-->
                </div>
            </div>
        </div>




        <br>
    </body>
</html>