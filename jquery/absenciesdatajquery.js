/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/*$(function () {
 $("#dataInicialAbsencies").datepicker($.datepicker.regional[ "fr" ]);
 $("#locale").on("change", function () {
 $("#datepicker").datepicker("option",
 "ca");
 });
 });*/

$(document).ready(function () {
    $('#dataInicialAbsencies').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });
});

$(document).ready(function () {
    $('#dataFinalAbsencies').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });
});



/*$(function () {
 $("#dataFinalAbsencies").datepicker($.datepicker.regional[ "fr" ]);
 $("#locale").on("change", function () {
 $("#datepicker").datepicker("option",
 "ca");
 });
 });*/

function carregaDadesAbsData() {
    carregaDropGeneric('nivellDropdownAbsencies', 'SELECT distinct(ga35_nivell) as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga35_nivell=ga06_codi_nivell', 'Tria Nivell');
    //carregaDropGeneric('grupDropdownAbsencies', 'SELECT ga07_codi_grup as codi, ga07_descripcio_grup as descripcio FROM ga07_grup', 'Tria Grup');
    carregaDropGeneric('profDropdownAbsencies', "select ga17_codi_professor as codi,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio from ga04_professors,ga17_professors_curs where ga04_codi_prof=ga17_codi_professor and ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) order by descripcio", 'Tria Professor');

}

function mostraprofDropdownAbsencies(element) {

    $("#butDropprofDropdownAbsencies").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropprofDropdownAbsencies").val($(element).attr('data-val'));

}

function mostraassigDropdownAbsencies(element) {

    $("#butDropassigDropdownAbsencies").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropassigDropdownAbsencies").val($(element).attr('data-val'));

}


function mostranivellDropdownAbsencies(element) {

    $("#butDropnivellDropdownAbsencies").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropnivellDropdownAbsencies").val($(element).attr('data-val'));

    var nivell = $(element).attr('data-val');

    if (nivell != '') {
        cercaGrupsNivell('grupDropdownAbsencies', nivell);
        cercaAssigNivell('assigDropdownAbsencies', nivell);
    }

}

function mostragrupDropdownAbsencies(element) {

    $("#butDropgrupDropdownAbsencies").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropgrupDropdownAbsencies").val($(element).attr('data-val'));

}

function cercaAbsRet() {
    //agafem les dades per aplicar els criteris de cerca
    var nivell = $("#butDropnivellDropdownAbsencies").val();
    var grup = $("#butDropgrupDropdownAbsencies").val();
    var dataInicial = $("#dataInicialAbsencies").val();
    var dataFinal = $("#dataFinalAbsencies").val();
    var professor = $("#butDropprofDropdownAbsencies").val();
    var assignatura = $("#butDropassigDropdownAbsencies").val();

    if ($("#nomesAbsents").prop('checked') === true) {
        var nomesAbsents = '1';
    } else {
        var nomesAbsents = '0';
    }

    if ($("#nomesRetards").prop('checked') === true) {
        var nomesRetards = '1';
    } else {
        var nomesRetards = '0';
    }

    debugger;

    var dataInicialUniversal = '';
    var dataFinalUniversal = '';

    //posem les dates en format internacional
    if (dataInicial != "") {

        var dataI = dataInicial.split("/");
        dataInicial = dataI[1] + "/" + dataI[0] + "/" + dataI[2];
        dataInicialUniversal = dataI[2] + '-' + dataI[1] + '-' + dataI[0];

    }
    if (dataFinal != "") {
        var dataF = dataFinal.split("/");
        dataFinal = dataF[1] + "/" + dataF[0] + "/" + dataF[2];
        dataFinalUniversal = dataF[2] + '-' + dataF[1] + '-' + dataF[0];
    }

    //ho enviemt tot per ajax

    if (dataFinalUniversal !== '' && dataInicialUniversal !== '' && dataFinalUniversal < dataInicialUniversal) {
        //data final anterior a inicial
        alert('Dates incorrectes');

    } else {
        $("body").css("cursor", "progress");
        var url = "php/cercaAbsRet_1.php";
        $.ajax({
            type: "POST",
            url: url,
            data: {"nivell": nivell, "grup": grup, "dataInicial": dataInicial, "dataFinal": dataFinal, "professor": professor, "assignatura": assignatura, "nomesAbsents": nomesAbsents, "nomesRetards": nomesRetards},
            //data: ("#form2").serialize,
            success: function (data) {

                //rebem les dades
                $("#divTaulaAbsenciesData").html(data);
                $("#taulaAbsenciesRetards").tableHeadFixer();
                $("body").css("cursor", "default");

            }

        });
        return false;
    }

}



function absenciesToPDF() {
    //agafem les dades per aplicar els criteris de cerca
    var nivell = $("#butDropnivellDropdownAbsencies").val();
    var grup = $("#butDropgrupDropdownAbsencies").val();
    var dataInicial = $("#dataInicialAbsencies").val();
    var dataFinal = $("#dataFinalAbsencies").val();
    var professor = $("#butDropprofDropdownAbsencies").val();
    var nivellText = $("#butDropnivellDropdownAbsencies").text();
    var grupText = $("#butDropgrupDropdownAbsencies").text();
    var professorNom = $("#butDropprofDropdownAbsencies").text();

    debugger;

    if ($("#nomesAbsents").prop('checked') === true) {
        var nomesAbsents = '1';
    } else {
        var nomesAbsents = '0';
    }

    if ($("#nomesRetards").prop('checked') === true) {
        var nomesRetards = '1';
    } else {
        var nomesRetards = '0';
    }



    //posem les dates en format internacional
    if (dataInicial != "") {

        var dataI = dataInicial.split("/");
        dataInicial = dataI[1] + "/" + dataI[0] + "/" + dataI[2];

    }
    if (dataFinal != "") {
        var dataF = dataFinal.split("/");
        dataFinal = dataF[1] + "/" + dataF[0] + "/" + dataF[2];
    }
    var url = "php/reports/tcpdf2.php";

    window.open(url + "?nivell=" + nivell + "&grup=" + grup + "&dataInicial=" + dataInicial + "&dataFinal=" + dataFinal + "&professor=" + professor + "&nivellText=" + nivellText + "&grupText=" + grupText + "&professorNom=" + professorNom + "&nomesAbsents=" + nomesAbsents + "&nomesRetards=" + nomesRetards);


}

function mostraDetall(element) {
    //obtenim el client
    var alumne = $($(element).parent()).attr('data-codi-alumne');
    var nomalumne = $($(element).parent()).text();
    var dataInicial = $("#dataInicialAbsencies").val();
    var dataFinal = $("#dataFinalAbsencies").val();
    var professor = $("#butDropprofDropdownAbsencies").val();
    var assignatura = $("#butDropassigDropdownAbsencies").val();

//posem les dates en format internacional
    if (dataInicial != "") {

        var dataI = dataInicial.split("/");
        dataInicial = dataI[1] + "/" + dataI[0] + "/" + dataI[2];

    }
    if (dataFinal != "") {
        var dataF = dataFinal.split("/");
        dataFinal = dataF[1] + "/" + dataF[0] + "/" + dataF[2];
    }


    //posem l'alumne al modal
    $("#alumneAbsenciaRetard").text(nomalumne);

    //ho enviem per ajax

    var url = "php/mostraDetall.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"alumne": alumne, "dataInicial": dataInicial, "dataFinal": dataFinal, "professor": professor, "assignatura": assignatura},
        //data: ("#form2").serialize,
        success: function (data) {

            //rebem les dades
            $("#divTaulaDetallAbsencies").html(data);
            //activem els butons d'accions massives
            $("#creaMassives").prop('disabled', false);
            $("#esborraMassives").prop('disabled', false);

        }

    });
    return false;


}