/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var myVar;

$(document).ready(function () {
    $('#dataSessionsMassives').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });
});


function carregaDadesIniSessionsMassives() {
    carregaDropGeneric('nivellSessionsMassives', 'SELECT distinct(ga35_nivell) as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga35_nivell=ga06_codi_nivell', 'Tria Nivell');

}

function mostranivellSessionsMassives(element) {

    $("#butDropnivellSessionsMassives").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropnivellSessionsMassives").val($(element).attr('data-val'));

    //esborrem el que hi havia abans
    $("#divAlumnesSessio").html('');

    var nivell = $(element).attr('data-val');

    if (nivell != '') {
        cercaGrupsNivell('grupSessionsMassives', nivell);

    }
    comprovaDades();

}

function mostragrupSessionsMassives(element) {

    $("#butDropgrupSessionsMassives").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropgrupSessionsMassives").val($(element).attr('data-val'));

    //esborrem el que hi havia abans
    $("#divAlumnesSessio").html('');

    comprovaDades();
}


function cercaSessionsMassives() {
    //anem a busar els alumnes del nivell grup
    //agafem nivell grup i data
    var nivell = $("#butDropnivellSessionsMassives").val();
    var grup = $("#butDropgrupSessionsMassives").val();

    var dataSessio = $("#dataSessionsMassives").val();


    var url = "php/alumnesSessioMassiva.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"nivell": nivell, "grup": grup, "dataSessio": dataSessio},
        //data: ("#form2").serialize,
        success: function (data) {

            //rebem les dades
            $("#divAlumnesSessio").html(data);
            //activem els butons d'accions massives
            $("#creaMassives").prop('disabled', false);
            $("#esborraMassives").prop('disabled', false);
        }

    });
    return false;


}

function comprovaDades() {


    if ($('#dataSessionsMassives').val() != '' && $("#butDropnivellSessionsMassives").val() != '' && $("#butDropgrupSessionsMassives").val() != '') {
        //habilitem cerca
        $('#cercaSesMassives').prop('disabled', false);

    }

}


function comprovaCheckMassiu(element) {

    //trobem la filera
    debugger;
    //obtenimn la cella
    var pareCheck = $(element).parent();
    //index de la cella
    var posCheck1 = $(pareCheck).index();
    //filera
    var aviCheck = $(pareCheck).parent();
    //index de la filera (amb capçalera inclosa
    var filera = $(aviCheck).index();

    //esbrinem posicions a desmarcar
    if (posCheck1 == 2) {
        var posCheck2 = 3;
        var posCheck3 = 4;
    } else if (posCheck1 == 3) {
        var posCheck2 = 2;
        var posCheck3 = 4;

    } else {
        var posCheck2 = 2;
        var posCheck3 = 3;
    }

    //obtenime els elements a desmarcar
    var taula = document.getElementById('taulaAlumnesGrup');

    var ele1 = taula.rows[filera + 1].cells[posCheck2].firstChild;
    var ele2 = taula.rows[filera + 1].cells[posCheck3].firstChild;


    if ($(element).prop('checked')) {
        //canviem l'atribut de la resta
        //desmarquem la resta
        $(ele1).prop('checked', false);
        $(ele2).prop('checked', false);


    } else {

        //ens han desmarcat el check per tant el tornem a marcar
        $(element).prop('checked', true);

    }



}






function creaSessionsMassives() {
    //agafem el nivell i el grup
    //mode esborrar accions massives
    //abans de res comprovem la data
    var from = $("#dataSessionsMassives").val().split("/");
    var f = from[2].toString() + '-' + from[1].toString() + '-' + from[0].toString();
    var avui = new Date();
    var avuiString = avui.toISOString().substr(0, 10);

    debugger;


    if (f >= avuiString) {

        $("#creaSessionsMassives").attr('disabled', false);
        $("#esborraSessionsMassives").attr('disabled', true);

        var mode = '2';

        var nivell = $("#butDropnivellSessionsMassives").val();
        var grup = $("#butDropgrupSessionsMassives").val();
        var dataSessio = $("#dataSessionsMassives").val();

        dataSessio = dataSessio.substring(6) + '-' + dataSessio.substring(3, 5) + '-' + dataSessio.substring(0, 2);
        //agafem els alumnes
        var alumnes = [];

        //estat = 1 present =2 absent = 3 retard
        var fileres = $("#cosTaulaAlumnesGrup").children();

        for (var i = 0; i < fileres.length; i++) {
            var columnes = $(fileres[i]).children();
            alumnes[i] = $(columnes[1]).attr('data-codi-alumne');
            if ($($(columnes[2]).children()[0]).prop('checked') == true) {
                alumnes[i] = $(columnes[1]).attr('data-codi-alumne') + '<#>1';
            } else if ($($(columnes[3]).children()[0]).prop('checked') == true) {
                alumnes[i] = $(columnes[1]).attr('data-codi-alumne') + '<#>2';
            } else {
                alumnes[i] = $(columnes[1]).attr('data-codi-alumne') + '<#>3';
            }

        }

        //posem el grup classe i la data a la capçalera
        $("#grupClasse").text($("#butDropnivellSessionsMassives").text() + '-' + $("#butDropgrupSessionsMassives").text());
        $("#dataSessions").text($("#dataSessionsMassives").val());
        //guardem la data de la sessió en format sql
        $("#dataSessions").attr('data-sessio', dataSessio);
        $("#relacioSessions").attr("data-mode", mode)


        debugger;
        jQuery.noConflict();
        $('#sessionsMassivesModalForm').modal('show');
        //enviem les dades al servidor per ajax

        var url = "php/cercaSessionsMassives.php";
        $.ajax({
            type: "POST",
            url: url,
            data: {"nivell": nivell, "grup": grup, "alumnes": alumnes, "dataSessio": dataSessio, "mode": mode},
            //data: ("#form2").serialize,
            success: function (data) {

                //mostrarem les sessions en el modal form
                $("#relacioSessions").html(data);

            }

        });
        return false;

    } else {
        alert('Data incorrecta');
    }
}



function esborraSessionsMassives() {
    //agafem el nivell i el grup
    //mode esborrar accions massives
    //comproem les dates
    var from = $("#dataSessionsMassives").val().split("/");
    var f = from[2].toString() + '-' + from[1].toString() + '-' + from[0].toString();
    var avui = new Date();
    var avuiString = avui.toISOString().substr(0, 10);

    debugger;

    if (f >= avuiString) {



        $("#creaSessionsMassives").attr('disabled', true);
        $("#esborraSessionsMassives").attr('disabled', false);
        var mode = '1';

        var nivell = $("#butDropnivellSessionsMassives").val();
        var grup = $("#butDropgrupSessionsMassives").val();
        var dataSessio = $("#dataSessionsMassives").val();

        dataSessio = dataSessio.substring(6) + '-' + dataSessio.substring(3, 5) + '-' + dataSessio.substring(0, 2);
        //agafem els alumnes
        var alumnes = [];

        //estat = 1 present =2 absent = 3 retard
        var fileres = $("#cosTaulaAlumnesGrup").children();

        for (var i = 0; i < fileres.length; i++) {
            var columnes = $(fileres[i]).children();
            alumnes[i] = $(columnes[1]).attr('data-codi-alumne');
            if ($($(columnes[2]).children()[0]).prop('checked') == true) {
                alumnes[i] = $(columnes[1]).attr('data-codi-alumne') + '<#>1';
            } else if ($($(columnes[3]).children()[0]).prop('checked') == true) {
                alumnes[i] = $(columnes[1]).attr('data-codi-alumne') + '<#>2';
            } else {
                alumnes[i] = $(columnes[1]).attr('data-codi-alumne') + '<#>3';
            }

        }

        //posem el grup classe i la data a la capçalera
        $("#grupClasse").text($("#butDropnivellSessionsMassives").text() + '-' + $("#butDropgrupSessionsMassives").text());
        $("#dataSessions").text($("#dataSessionsMassives").val());
        //guardem la data de la sessió en format sql
        $("#dataSessions").attr('data-sessio', dataSessio);
        $("#relacioSessions").attr("data-mode", mode)


        debugger;
        jQuery.noConflict();
        $('#sessionsMassivesModalForm').modal('show');
        //enviem les dades al servidor per ajax

        var url = "php/cercaSessionsMassives.php";
        $.ajax({
            type: "POST",
            url: url,
            data: {"nivell": nivell, "grup": grup, "alumnes": alumnes, "dataSessio": dataSessio, "mode": mode},
            //data: ("#form2").serialize,
            success: function (data) {

                //mostrarem les sessions en el modal form
                $("#relacioSessions").html(data);

            }

        });
        return false;

    } else {
        alert('Data incorrecta');
    }
}



function seleccionaSessions() {
    if ($("#checkEsborraSessions").prop('checked') === true) {
        //marquem tots els check
        var checksSessions = $('.sessionsSeleccionades');
        for (var i = 0; i < checksSessions.length; i++) {
            if ($(checksSessions[i]).prop('disabled') === false) {
                $(checksSessions[i]).prop('checked', true);
            }
        }

    } else {
        //desmarquem tot els checks
        var checksSessions = $('.sessionsSeleccionades');
        for (var i = 0; i < checksSessions.length; i++) {
            if ($(checksSessions[i]).prop('disabled') === false) {
                $(checksSessions[i]).prop('checked', false);
            }
        }

    }

}

function gestionaSessionsTriades() {
    //s'esborren les sessions marcades amb el checkbox
    var mode = $("#relacioSessions").attr("data-mode");

    var checksSessions = $('.sessionsSeleccionades');
    var max = 0;
    for (var i = 0; i < checksSessions.length; i++) {
        if ($(checksSessions[i]).prop('checked') === true) {
            max++;
        }
    }


    if (max > 0) {
        if (mode == '1') {
            var confirmacio = confirm("Estàs a punt d'esborar " + max + " sessions. Vols continuar?");
            if (confirmacio === true) {
                //posem en format loading
                $("#prova").attr('id', 'loadingDiv');
                $("#areaTreball").css("opacity", "0.4");
                //posem el check actual a la primera sessió marcada
                var checksSessions = $('.sessionsSeleccionades');

                for (var i = 0; i < checksSessions.length; i++) {
                    if ($(checksSessions[i]).prop('checked') === true) {
                        $(checksSessions[i]).attr('data-actual', '1');
                        $("#taulaSessionsMassives").attr('data-ordre', '1');
                        //només ens interessa el primer
                        break;
                    }
                }

                myVar = setInterval(esborraSessio, 2000);

            }
          

            
        } else {
            if ($("#comentSessio").val() !== '') {
                //anem a crear les sessions

                var confirmacio = confirm("Estàs a punt de crear " + max + " sessions. Vols continuar?");
                if (confirmacio === true) {
                    //posem en format loading
                    $("#prova").attr('id', 'loadingDiv');
                    $("#areaTreball").css("opacity", "0.4");
                    //posem el check actual a la primera sessió marcada
                    var checksSessions = $('.sessionsSeleccionades');

                    for (var i = 0; i < checksSessions.length; i++) {
                        if ($(checksSessions[i]).prop('checked') === true) {
                            $(checksSessions[i]).attr('data-actual', '1');
                            $("#taulaSessionsMassives").attr('data-ordre', '1');
                            //només ens interessa el primer
                            break;
                        }
                    }

                    myVar = setInterval(creaSessio, 2000);

                }


            } else {
                alert("Cal posar algun comentari per dur a terme l'acció")
            }
        }
    } else {
        alert("No s'ha triat cap sessió");
    }
}

function esborraSessio() {
    //anem a buscar els alumnes
 
    var checksSessions = $('.sessionsSeleccionades');
    var max = 0;
    for (var i = 0; i < checksSessions.length; i++) {
        if ($(checksSessions[i]).prop('checked') === true) {
            max++;
        }
    }

    var fileresAlumnes = $("#cosTaulaAlumnesGrup").children();
    var alumnesSessioMassiva = [];
    var mode = 1;

    //faltam la data les hores i els profes
    var dataSessio = $("#dataSessions").attr('data-sessio');

    var horaProfe;

    //anem a buscar la sessió que toca tractar
    var fileraActual = $("[data-actual=1]");

    //és la filera a tractar
    //l'atribut data actual està al check
    //només n'hi pot haver un
    var hora = $($($(fileraActual[0]).parent()).siblings()[0]).text();
    var profe = $($($(fileraActual[0]).parent()).siblings()[4]).attr('data-codiprof');

    horaProfe = hora + '<#>' + profe;


    var url = "php/gestionaSessionsTriades.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"dataSessio": dataSessio, "horaProfe": horaProfe, "mode": mode},
        //data: ("#form2").serialize,

        success: function (data) {


            var numSessioActual = parseInt($("#taulaSessionsMassives").attr('data-ordre'));

            var percent = (numSessioActual * 100 / max).toFixed(2);

            var nouEstat = "";

            nouEstat += '<div class="progress">';
            nouEstat += '<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="' + percent + '" aria-valuemin="0" aria-valuemax="100" style="width:' + percent + '%">';
            nouEstat += percent + '% processat';
            nouEstat += '</div>';
            nouEstat += '</div >';


            $("#divProgressSessions").html(nouEstat);
            if (parseInt($("#taulaSessionsMassives").attr('data-ordre')) < max) {
                //encara queden més sessions per a tractar
                //cal canviar la filera a tractar
                var numFilera = $($($(fileraActual[0]).parent()).parent()).index();
                var totesFileres = $($($(fileraActual[0]).parent()).parent()).siblings();
                //traguem l'anterior

                $(fileraActual[0]).removeAttr('data-actual');

                for (var i = 0; i < totesFileres.length; i++) {
                    var estatCheck = $($($(totesFileres[i]).children()[0]).children()[0]).prop('checked');
                    var indexActual = $(totesFileres[i]).index();

                    if (estatCheck === true && indexActual > numFilera) {
                        //és la següent filera a marcar
                        $($($(totesFileres[i]).children()[0]).children()[0]).attr('data-actual', '1');



                        var ordre = parseInt($("#taulaSessionsMassives").attr('data-ordre')) + 1;
                        $("#taulaSessionsMassives").attr('data-ordre', ordre.toString());

                        break;
                    }

                }


            } else {
                //ja s'han tractat totes
                clearInterval(myVar);
                $("#loadingDiv").attr('id', 'prova');
                $("#areaTreball").css("opacity", "1");
                alert('Procés acabat');

            }


        }, async: true

    });

    return false;

}


function creaSessio() {
    //anem a buscar els alumnes
   
    var checksSessions = $('.sessionsSeleccionades');
    var max = 0;
    for (var i = 0; i < checksSessions.length; i++) {
        if ($(checksSessions[i]).prop('checked') === true) {
            max++;
        }
    }
    var fileresAlumnes = $("#cosTaulaAlumnesGrup").children();
    var alumnesSessioMassiva = [];
    var mode = 0;

    for (var i = 0; i < fileresAlumnes.length; i++) {
        var alumne = $($(fileresAlumnes[i]).children()[1]).attr('data-codi-alumne');

        if ($($($(fileresAlumnes[i]).children()[2]).children()[0]).prop('checked') === true) {
            var presencia = 1;

        } else if ($($($(fileresAlumnes[i]).children()[3]).children()[0]).prop('checked') === true) {
            var presencia = 2;

        } else {
            var presencia = 3;
        }

        alumnesSessioMassiva[i] = alumne + '<#>' + presencia;
    }

    //agafem les dades necessàries per crear la sessió
    //faltam la data les hores i els profes
    var dataSessio = $("#dataSessions").attr('data-sessio');
    var dadesSessio;
    //anem a buscar la sessió que toca tractar
    var fileraActual = $("[data-actual=1]");

    //és la filera a tractar
    //l'atribut data actual està al check
    //només n'hi pot haver un
    var hora = $($($(fileraActual[0]).parent()).siblings()[0]).text();
    var profe = $($($(fileraActual[0]).parent()).siblings()[4]).attr('data-codiprof');
    var aula = $($(fileraActual[0]).parent()).attr('data-aula');
    var nivell = $($($(fileraActual[0]).parent()).siblings()[2]).attr('data-nivell');
    var grup = $($($(fileraActual[0]).parent()).siblings()[3]).attr('data-grup');
    var tipusGrup = $($($(fileraActual[0]).parent()).siblings()[3]).attr('data-tipusgrup');
    var assignatura = $($(fileraActual[0]).parent()).attr('data-assignatura');
    var comentari = $("#comentSessio").val();
    var estatSessio = $($($(fileraActual[0]).parent()).siblings()[5]).attr('data-estat');

    dadesSessio = profe + '<#>' + dataSessio + '<#>' + hora + '<#>' + aula + '<#>' + nivell + '<#>' + grup + '<#>' + tipusGrup + '<#>' + assignatura + '<#>' + comentari + '<#>' + estatSessio;

    debugger;

    //enviem les dades al servidor
    var url = "php/gestionaSessionsTriades.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"dadesSessio": dadesSessio, "mode": mode, "alumnesSessioMassiva": alumnesSessioMassiva},
        //data: ("#form2").serialize,

        success: function (data) {


            var numSessioActual = parseInt($("#taulaSessionsMassives").attr('data-ordre'));

            var percent = (numSessioActual * 100 / max).toFixed(2);

            var nouEstat = "";

            nouEstat += '<div class="progress">';
            nouEstat += '<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="' + percent + '" aria-valuemin="0" aria-valuemax="100" style="width:' + percent + '%">';
            nouEstat += percent + '% processat';
            nouEstat += '</div>';
            nouEstat += '</div >';


            $("#divProgressSessions").html(nouEstat);
            if (parseInt($("#taulaSessionsMassives").attr('data-ordre')) < max) {
                //encara queden més sessions per a tractar
                //cal canviar la filera a tractar
                var numFilera = $($($(fileraActual[0]).parent()).parent()).index();
                var totesFileres = $($($(fileraActual[0]).parent()).parent()).siblings();
                //traguem l'anterior

                $(fileraActual[0]).removeAttr('data-actual');

                for (var i = 0; i < totesFileres.length; i++) {
                    var estatCheck = $($($(totesFileres[i]).children()[0]).children()[0]).prop('checked');
                    var indexActual = $(totesFileres[i]).index();

                    if (estatCheck === true && indexActual > numFilera) {
                        //és la següent filera a marcar
                        $($($(totesFileres[i]).children()[0]).children()[0]).attr('data-actual', '1');



                        var ordre = parseInt($("#taulaSessionsMassives").attr('data-ordre')) + 1;
                        $("#taulaSessionsMassives").attr('data-ordre', ordre.toString());

                        break;
                    }

                }


            } else {
                //ja s'han tractat totes
                clearInterval(myVar);
                $("#loadingDiv").attr('id', 'prova');
                $("#areaTreball").css("opacity", "1");
                alert('Procés acabat');

            }


        }, async: true

    });

    return false;

}


function esborraAlumnesTaula() {
    var fileres = $(".checkEsborrar");

    for (var i = 0; i < fileres.length; i++) {
        if ($(fileres[i]).prop('checked') === true) {
            //si està marcat l'eliminarem
            $($($(fileres[i]).parent()).parent()).remove();
        }
    }



}