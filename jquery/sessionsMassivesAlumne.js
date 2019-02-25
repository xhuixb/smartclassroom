/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    $('#dataInicialSessionsMassives').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });
});

$(document).ready(function () {
    $('#dataFinalSessionsMassives').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });
});
function carregaDadesIniSessionsMassivesAlumne() {

    carregaDropGeneric('divNivellSessionsMassives', 'SELECT distinct(ga35_nivell) as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga35_nivell=ga06_codi_nivell', 'Tria Nivell');


}

function mostradivNivellSessionsMassives(element) {

    $("#butDropdivNivellSessionsMassives").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivNivellSessionsMassives").val($(element).attr('data-val'));

    var nivell = $(element).attr('data-val');

    if (nivell != '') {
        cercaGrupsNivell('divGrupSessionsMassives', nivell);
    }
    comprovaCamps();

}

function mostradivGrupSessionsMassives(element) {

    $("#butDropdivGrupSessionsMassives").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivGrupSessionsMassives").val($(element).attr('data-val'));

    habilitaDropAlumne();
    comprovaCamps();

}

function habilitaDropAlumne() {

    if ($('#butDropdivNivellSessionsMassives').val() != '' && $('#butDropdivGrupSessionsMassives').val() != '') {
        //habilitem el drop d'alumnes


        var nivell = $("#butDropdivNivellSessionsMassives").val();
        var grup = $("#butDropdivGrupSessionsMassives").val();

        //omplim el drop amb els alumnes
        carregaDropGeneric('divAlumnesSessionsMassives', "select ga12_id_alumne as codi,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as descripcio from ga11_alumnes,ga12_alumnes_curs where ga12_codi_nivell=" + nivell + " and ga12_codi_grup=" + grup + " and ga12_id_alumne=ga11_id_alumne and ga12_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) order by descripcio", "Tria alumne");
        //$('#cercaHorariAlumne').prop('disabled', false);

    }

}

function mostradivAlumnesSessionsMassives(element) {
    $("#butDropdivAlumnesSessionsMassives").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivAlumnesSessionsMassives").val($(element).attr('data-val'));



    comprovaCamps();

}

function comprovaCamps() {
    debugger;
    var nivell = $("#butDropdivNivellSessionsMassives").val();
    var grup = $("#butDropdivGrupSessionsMassives").val();
    var alumne = $("#butDropdivAlumnesSessionsMassives").val();

    var dataInici = $("#dataInicialSessionsMassives").val();
    var dataFinal = $("#dataFinalSessionsMassives").val();

    if (nivell != '' && grup != '' && alumne != '' && dataInici != '' && dataFinal != '') {

        $("#mostraMassives").prop('disabled', false);

        $(".presenciaRadioClass").prop('disabled', false);
        $("#comentari").prop('disabled', false);
    } else {
        $("#mostraMassives").prop('disabled', true);

        $(".presenciaRadioClass").prop('disabled', true);
        $("#comentari").prop('disabled', true);
    }
}


function mostraSessionsMassivesAlumne() {

    //recullim totes les dades
    debugger;
    var nivell = $("#butDropdivNivellSessionsMassives").val();
    var grup = $("#butDropdivGrupSessionsMassives").val();
    var alumne = $("#butDropdivAlumnesSessionsMassives").val();

    var dataInici = $("#dataInicialSessionsMassives").val();
    var dataFinal = $("#dataFinalSessionsMassives").val();

    var comentari = $("#comentari").val();

    if ($("#presentOption").prop('checked') === true) {
        var presencia = 1;
    } else if ($("#absentOption").prop('checked') === true) {
        var presencia = 2;
    } else {
        var presencia = 3;
    }


    var url = "php/mostraSessionsAlumne.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"nivell": nivell, "grup": grup, "alumne": alumne, "dataInici": dataInici, "dataFinal": dataFinal, "presencia": presencia, "comentari": comentari},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divTaulaSessionsAlumne").html(data);
            //activo els butons de crea i esborrar
            $("#creaMassives").prop('disabled', false);
            $("#esborraMassives").prop('disabled', false);
        }

    });
    return false;

}

function creaSessionsMassivesAlumne() {

    var from = $("#dataInicialSessionsMassives").val().split("/");
    var f = from[2].toString() + '-' + from[1].toString() + '-' + from[0].toString();
    var avui = new Date();
    var avuiString = avui.getFullYear().toString() + '-' + ("0" + (avui.getMonth() + 1)).slice(-2) + '-' + ("0" + avui.getDate()).slice(-2);

    if (f >= avuiString) {

        //agefem les dades de l'alumne
        var nivell = $("#butDropdivNivellSessionsMassives").val();
        var grup = $("#butDropdivGrupSessionsMassives").val();
        var alumne = $("#butDropdivAlumnesSessionsMassives").val();

        var dataInici = $("#dataInicialSessionsMassives").val();
        var dataFinal = $("#dataFinalSessionsMassives").val();

        var comentari = $("#comentari").val();



        if ($("#presentOption").prop('checked') === true) {
            var presencia = 1;
        } else if ($("#absentOption").prop('checked') === true) {
            var presencia = 2;
        } else {
            var presencia = 3;
        }

        var dataIniciDate = $.datepicker.parseDate('dd/mm/yy', dataInici);
        var dataFinalDate = $.datepicker.parseDate('dd/mm/yy', dataFinal);

        if (comentari !== '' && dataIniciDate <= dataFinalDate) {

            //iterarem per a cada sessió
            var fileresSessions = $("#cosTaulaSessionsAlumne").children();
            debugger;

            for (var i = 0; i < fileresSessions.length; i++) {
                var dataSessio = $($(fileresSessions[i]).children()[0]).text();

                var url = "php/creaSessionsMassivesAlumne.php";

                $.ajax({
                    type: "POST",
                    url: url,
                    data: {"dataSessio": dataSessio, "nivell": nivell, "grup": grup, "alumne": alumne, "comentari": comentari, "presencia": presencia, "i": i},
                    //data: ("#form2").serialize,
                    success: function (data) {
                        //$("#divProgressSessions").html(data);

                        var nouEstat = '<div class="progress"><div class="progress progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:100%">Sessions creades</div></div>';
                        $("#ses" + i).html(nouEstat);


                    }, async: false

                });
                if (i == fileresSessions.length - 1) {

                    alert("S'han  creat les sessions de " + (fileresSessions.length) + " dies ");
                    $("#creaMassives").prop('disabled', true);
                    $("#esborraMassives").prop('disabled', true);
                    $("#divTaulaSessionsAlumne").html('');
                    return false;
                }

            }

        } else {
            if (comentari === '') {
                alert('Cal posar comentari');
            } else {
                alert('Les dates són incorrectes');
            }
        }
    } else {
        alert('Data incorrecta');
    }
}



function esbSessionsMassivesAlumne() {
    //esborrarem les sessions d''aquest alumne en el cas que estiguin creades

    var from = $("#dataInicialSessionsMassives").val().split("/");
    var f = from[2].toString() + '-' + from[1].toString() + '-' + from[0].toString();
    var avui = new Date();
    var avuiString = avui.getFullYear().toString() + '-' + ("0" + (avui.getMonth() + 1)).slice(-2) + '-' + ("0" + avui.getDate()).slice(-2);

    if (f >= avuiString) {


        debugger;
        var nivell = $("#butDropdivNivellSessionsMassives").val();
        var grup = $("#butDropdivGrupSessionsMassives").val();
        var alumne = $("#butDropdivAlumnesSessionsMassives").val();


        //iterarem per a cada sessió
        var fileresSessions = $("#cosTaulaSessionsAlumne").children();
        debugger;
        for (var i = 0; i < fileresSessions.length; i++) {
            var dataSessio = $($(fileresSessions[i]).children()[0]).text();

            var url = "php/esborraSessionsMassivesAlumne.php";

            $.ajax({
                type: "POST",
                url: url,
                data: {"dataSessio": dataSessio, "nivell": nivell, "grup": grup, "alumne": alumne, "i": i},
                //data: ("#form2").serialize,
                success: function (data) {
                    //$("#divProgressSessions").html(data);
                    //mostrarem les sessions en el modal form
                    debugger;
                    //$("#divTaulaSessionsAlumne").html(data);
                    var nouEstat = '<div class="progress"><div class="progress progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:100%">Sessions esborrades</div></div>';
                    $("#ses" + i).html(nouEstat);

                }, async: false

            });
            if (i == fileresSessions.length - 1) {

                alert("S'han  esborrat les sessions de " + (fileresSessions.length) + " dies ");
                $("#creaMassives").prop('disabled', true);
                $("#esborraMassives").prop('disabled', true);
                $("#divTaulaSessionsAlumne").html('');
                return false;
            }

        }

    } else {
        alert('Data incorrecta');
    }
}

function posaDataFinal() {
    $("#dataFinalSessionsMassives").val($("#dataInicialSessionsMassives").val());

}

function esborraSessionsMassivesAlumne() {


}