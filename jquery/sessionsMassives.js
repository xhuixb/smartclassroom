/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


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

function esborraSessionsMassives() {
    //agafem el nivell i el grup
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

    debugger;
    jQuery.noConflict();
    $('#sessionsMassivesModalForm').modal('show');
    //enviem les dades al servidor per ajax

    var url = "php/cercaSessionsMassives.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"nivell": nivell, "grup": grup, "alumnes": alumnes, "dataSessio": dataSessio},
        //data: ("#form2").serialize,
        success: function (data) {

            //mostrarem les sessions en el modal form
            $("#relacioSessions").html(data);

        }

    });
    return false;


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

function esborraSessionsTriades() {
    //s'esborren les sessions marcades amb el checkbox
    var checksSessions = $('.sessionsSeleccionades');
    var conta = 0;
    for (var i = 0; i < checksSessions.length; i++) {
        if ($(checksSessions[i]).prop('checked') === true) {
            conta++;
        }
    }


    if (conta > 0) {
        var confirmacio = confirm("Estàs a punt d'esborar " + conta + " sessions. Vols continuar");
        if (confirmacio === true) {
            //agafem les dades necessàries per esborra la sessió
            //faltam la data les hores i els profes
            var dataSessio = $("#dataSessions").attr('data-sessio');
            var horaProfe = [];
            var checksSessions = $('.sessionsSeleccionades');
            var conta = 0;
            for (var i = 0; i < checksSessions.length; i++) {
                if ($(checksSessions[i]).prop('checked') === true) {
                    var hora = $($($(checksSessions[i]).parent()).siblings()[0]).text();
                    var profe = $($($(checksSessions[i]).parent()).siblings()[3]).attr('data-codiprof');

                    horaProfe[conta] = hora + '<#>' + profe;
                    conta++;
                }
            }

        }
        debugger;

        for (var i = 0; i < horaProfe.length; i++) {

            var url = "php/esborraSessionsTriades.php";
            $.ajax({
                type: "POST",
                url: url,
                data: {"dataSessio": dataSessio, "horaProfe": horaProfe[i],"conta":i,"max":horaProfe.length},
                //data: ("#form2").serialize,
                success: function (data) {
                    $("#divProgressSessions").html(data);
                    //mostrarem les sessions en el modal form

                }

            });
            if (i == horaProfe.length - 1) {
                return false;
            }
        }
    }
}