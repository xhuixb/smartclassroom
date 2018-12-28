/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
});



function desaCanviPassword() {
    //comprovem que les dues contrasenyes noves són iguals
    var nova1 = $("#passwordNou1").val();
    var nova2 = $("#passwordNou2").val();


    if (nova1 == nova2) {
        var url = "php/desaCanviPassword.php";
        //procedim a canviar la contrasenya
        $.ajax({
            type: "POST",
            url: url,
            data: {"nova": nova1},
            //data: ("#form2").serialize,
            success: function (data) {
                $("#passwordNou1").val("");
                $("#passwordNou2").val("");
                alert("Canvi reeixit");

            }
        });
        return false;

    } else {
        alert("Contrasenyes no coincidents");

    }
}

function netejaCamps() {
    $("#passwordNou1").val("");
    $("#passwordNou2").val("");

}

function mostradivProfessorSessio(element) {
    $("#butDropdivProfessorSessio").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivProfessorSessio").val($(element).attr('data-val'));


}

function carregaProfes() {




}
function activaGuardia() {

    //esborrem el contingut de l'horari


    if ($("#esGuardia").prop('checked') == true) {
        $("#butDropdivProfessorSessio").prop('disabled', false);
        carregaDropGeneric('divProfessorSessio', "select ga17_codi_professor as codi,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio from ga04_professors,ga17_professors_curs where ga04_codi_prof=ga17_codi_professor and ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga04_suspes='0' order by descripcio", 'Tria professor');
        $(".cellahorari").html('');
    } else {
        $("#butDropdivProfessorSessio").prop('disabled', true);
        //esborrem el profe de guàrdia
        $("#butDropdivProfessorSessio").html('Tria professor ' + '<span class="caret">');
        $("#butDropdivProfessorSessio").val('');
        carregaSessions();
    }
}


function carregaSessions() {
    //mostrem el professor

    //posem la setmana actual
    //inicialitzem la data amb el primer i el darrer dia de la setmana

    var dilluns = trobaDilluns();

    dilluns = moment(dilluns, "YYYY-MM-DD");
    dilluns = dilluns.format("YYYY-MM-DD");

    var diumenge = trobaDiumenge();

    diumenge = moment(diumenge, "YYYY-MM-DD");
    diumenge = diumenge.format("YYYY-MM-DD");

    $("#setmanaSessio").val('de ' + dilluns.toString() + " a " + diumenge.toString());

    $("#setmanaSessio").attr('data-dataactual', dilluns.toString());



    recuperaSessions(dilluns, diumenge);


}

function recuperaSessions(dilluns, diumenge) {

    var url = "php/carregaSessionsSetmana.php";

    if ($("#esGuardia").prop('checked') == true) {
        //és guàrdia
        var profe = $("#butDropdivProfessorSessio").val();

    } else {
        //no és guàrdia
        var profe = $("#butDropdivProfessorSessio").val();
    }


    $("body").css('cursor', 'progress');
    debugger;
    $("#divSessionsSetmana").html('<h1 class="btn-info form-ontrol">Carregant sessions</h1><h2 class="btn-info form-ontrol">Tingues paciència!</h2>');
    $.ajax({
        type: "POST",
        url: url,
        data: {"dilluns": dilluns.toString(), "diumenge": diumenge.toString(), "profe": profe},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divSessionsSetmana").html(data);
            $("body").css('cursor', 'default');
        }

    });

    return false;

}


function setmanaEnrere() {


    var startdate = moment($("#setmanaSessio").attr('data-dataactual'), "YYYY-MM-DD");

    startdate = startdate.subtract(7, "days");
    startdate = startdate.format("YYYY-MM-DD");

    var enddate = moment(startdate, "YYYY-MM-DD");
    enddate = enddate.add(6, "days");
    enddate = enddate.format("YYYY-MM-DD");


    $("#setmanaSessio").val('de ' + startdate.toString() + " a " + enddate.toString());

    //tornem a posar l'atribut
    $("#setmanaSessio").attr('data-dataactual', startdate.toString());

    recuperaSessions(startdate, enddate);


}

function setmanaEndavant() {

    var startdate = moment($("#setmanaSessio").attr('data-dataactual'), "YYYY-MM-DD");

    startdate = startdate.add(7, "days");
    startdate = startdate.format("YYYY-MM-DD");

    var enddate = moment(startdate, "YYYY-MM-DD");
    enddate = enddate.add(6, "days");
    enddate = enddate.format("YYYY-MM-DD");


    $("#setmanaSessio").val('de ' + startdate.toString() + " a " + enddate.toString());
    //tornem a posar l'atribut
    $("#setmanaSessio").attr('data-dataactual', startdate.toString());

    recuperaSessions(startdate, enddate);
}

function trobaDilluns() {

    var dilluns = new Date();
    var dillunsDia = dilluns.getDay();

    dilluns.setDate(dilluns.getDate() - (dillunsDia - 1));

    return dilluns;

}

function trobaDiumenge() {
    var diumenge = new Date();
    var diumengeDia = diumenge.getDay();

    diumenge.setDate(diumenge.getDate() + (7 - diumengeDia));
    return diumenge;

}

function obreSessio(element) {
    //agafem les dades de la sessió
    //dia

    debugger;
    var cella = $($(element).parent()).parent();
    var columna = $(cella).index();
    var dia = $($($("#captaulaHorarisProfessor").children()[0]).children()[columna]).text();

    //hora
    var hora = $($($(cella).parent()).children()[0]).attr('data-horainici');
    //professor
    if ($("#butDropdivProfessorSessio").val() != '') {
        //prenem el codi del professor substituït
        var esguardia = '1';
        //el profe titular
        var profeCodi = $("#butDropdivProfessorSessio").val();
        var profeNom = $("#butDropdivProfessorSessio").text();


    } else {
        //és una sessió normal
        var profeCodi = $("#dadesCredencials").attr('data-codi-prof');
        var profeNom = $("#dadesCredencials").text().substring(29);
        var esguardia = '0';

    }



    window.open('mainSessionsHorari.php?dia=' + dia + "&hora=" + hora + "&profeCodi=" + profeCodi + "&profeNom=" + profeNom + "&esguardia=" + esguardia, '_self');

}

function carregaBarraMenu() {

    var url = "php/construeixMenu.php";

    debugger;

    $.ajax({
        type: "POST",
        url: url,
        data: {},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divMenuBar").html(data);

        }

    });

    return false;
}

function comprovaComunicacions() {

    var url = "php/comprovaComunicacions.php";

    debugger;

    $.ajax({
        type: "POST",
        url: url,
        data: {},

        success: function (data) {
            if (data === '1') {
                alert("Hi ha comunicacions pendents d'aprovar");
            }

        }

    });

    return false;

}

function editaProgramacio(element) {
    //recullim les dades de la capçalera


    var tram = $($(element).parent()).html();

    var tramArray = tram.split("<br>");

    var nivell = tramArray[1];
    var grup = tramArray[2];
    var assignatura = tramArray[3];
    var aula = tramArray[4];

    var columna = $($(element).parent()).index();

    var filera = $($($(element).parent()).parent()).index();

    var horaIniciFi = $($($(element).parent()).siblings()[0]).html();

    var horaIniciAtribut = $($($(element).parent()).siblings()[0]).attr('data-horainici');

    var horaIniciFiArray = horaIniciFi.split("<br>");

    var horaInici = horaIniciFiArray[0];
    var horaFi = horaIniciFiArray[1];
    debugger;
    //$('#summernote').summernote('code', tramArray[1]);
    var dia = $($($($($("#captaulaHorarisProfessor").children()[0]).children()[columna])).children()[0]).text();

    var taulaCapcalera = "";

    taulaCapcalera += '<div class="col-sm-8">';
    taulaCapcalera += '<table class="table table-fixed">';
    taulaCapcalera += '<tr>';
    taulaCapcalera += '<td class="col-sm-4">'
    taulaCapcalera += 'Data: ' + dia.substring(8) + '/' + dia.substring(5, 7) + '/' + dia.substring(0, 4);
    taulaCapcalera += '</td>'
    taulaCapcalera += '<td class="col-sm-4">'
    taulaCapcalera += nivell;
    taulaCapcalera += '</td>'
    taulaCapcalera += '</tr>';

    taulaCapcalera += '<tr>';
    taulaCapcalera += '<td class="col-sm-4">'
    taulaCapcalera += horaInici;
    taulaCapcalera += '</td>'
    taulaCapcalera += '<td class="col-sm-4">'
    taulaCapcalera += grup;
    taulaCapcalera += '</td>'
    taulaCapcalera += '</tr>';

    taulaCapcalera += '<tr>';
    taulaCapcalera += '<td class="col-sm-4">'
    taulaCapcalera += aula;
    taulaCapcalera += '</td>'
    taulaCapcalera += '<td class="col-sm-4">'
    taulaCapcalera += assignatura;
    taulaCapcalera += '</td>'
    taulaCapcalera += '</tr>';

    taulaCapcalera += '</table>';
    taulaCapcalera += '</div>';

    $("#capcaleraPrograma").html(taulaCapcalera);

    $("#capcaleraPrograma").attr('data-filera', filera);
    $("#capcaleraPrograma").attr('data-columna', columna);
    $("#capcaleraPrograma").attr('data-dia', dia);
    $("#capcaleraPrograma").attr('data-hora', horaIniciAtribut);

    if ($("#esGuardia").prop('checked') === true) {
        //és una guàrdia
        var profe = $("#butDropdivProfessorSessio").val();
    } else {
        var profe = "";
    }

    //anem a buscar la programació si cal
    if ($(element).attr('data-programacio') === '1') {
        //hi ha programacio
        var url = "php/cercaProgramacio.php";

        debugger;

        $.ajax({
            type: "POST",
            url: url,
            data: {"dia": dia, "hora": horaIniciAtribut, "profe": profe},

            success: function (data) {
                $('#summernote').summernote('code', data);
                if (profe === "") {

                    $('#summernote').summernote('enable');
                    $('#desaProgramadorSessions').prop('disabled', false);
                    $('#esborraProgramadorSessions').prop('disabled', false);
                } else {
                    $('#summernote').summernote('disable');
                    $('#desaProgramadorSessions').prop('disabled', true);
                    $('#esborraProgramadorSessions').prop('disabled', true);
                }
            }

        });

        return false;
    } else {
        //no n'hi ha
        $('#summernote').summernote('code', '');
        if (profe === "") {

            $('#summernote').summernote('enable');
            $('#desaProgramadorSessions').prop('disabled', false);
            $('#esborraProgramadorSessions').prop('disabled', false);
        } else {
            $('#summernote').summernote('disable');
            $('#desaProgramadorSessions').prop('disabled', true);
            $('#esborraProgramadorSessions').prop('disabled', true);

        }

    }



}

function desaProgramadorSessions() {
    var textProgramacio = $('#summernote').summernote('code');
    //recullim dia i hora
    var dia = $("#capcaleraPrograma").attr('data-dia');
    var hora = $("#capcaleraPrograma").attr('data-hora');

    var url = "php/desaProgramacio.php";

    debugger;

    $.ajax({
        type: "POST",
        url: url,
        data: {"textProgramacio": textProgramacio, "dia": dia, "hora": hora},

        success: function (data) {
            //$("#divSessionsSetmana").html(data);
            carregaSessions();
        }

    });

    return false;


}


function esborraProgramadorSessions() {

    var confirmacio = confirm("N'estàs segur d'esborrar la programació");
    if (confirmacio === true) {
        //recullim dia i hora
        var dia = $("#capcaleraPrograma").attr('data-dia');
        var hora = $("#capcaleraPrograma").attr('data-hora');

        var url = "php/esborraProgramacio.php";

        debugger;

        $.ajax({
            type: "POST",
            url: url,
            data: {"dia": dia, "hora": hora},

            success: function (data) {
                //$("#divSessionsSetmana").html(data);
                carregaSessions();
            }

        });

        return false;

    }

}

$(document).ready(function () {
    $('#summernote').summernote({

        height: 400,
        // height: 300, // set editor height

        //  minHeight: null, // set minimum height of editor
        //   maxHeight: null, // set maximum height of editor
        focus: true                  // set focus to editable area after initializing summernote
    });


});

