/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    $('#dataInicialFaltes').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });


});

$(document).ready(function () {
    $('#dataFinalFaltes').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });
});
function carregaDadesIncialsCCC() {
    carregaDropGeneric('nivellDropdownFaltes', 'SELECT distinct(ga35_nivell) as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga35_nivell=ga06_codi_nivell', 'Tria Nivell');
    //carregaDropGeneric('grupDropdownFaltes', 'SELECT ga07_codi_grup as codi, ga07_descripcio_grup as descripcio FROM ga07_grup', 'Tria Grup');
    carregaDropGeneric("professorDropdownFaltes", "select ga17_codi_professor as codi,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio from ga04_professors,ga17_professors_curs where ga04_codi_prof=ga17_codi_professor and ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) order by descripcio", "Tria Professor");
    carregaDropGenericCheck('tipusFaltaDiv', 'SELECT ga22_codi_falta as codi, ga22_nom_falta as descripcio FROM ga22_tipus_falta', 'Tipus Falta');

    //inicialitzem la data amb el primer i el darrer dia de la setmana
    var dilluns = new Date();
    var dillunsDia = dilluns.getDay();

    var diumenge = new Date();
    var diumengeDia = diumenge.getDay();

    dilluns.setDate(dilluns.getDate() - (dillunsDia - 1));
    diumenge.setDate(diumenge.getDate() + (7 - diumengeDia))


    $("#dataInicialFaltes").val($.datepicker.formatDate('dd/mm/yy', dilluns));
    $("#dataFinalFaltes").val($.datepicker.formatDate('dd/mm/yy', diumenge));



}

$(document).ready(function () {

    $("#cercaFaltes").click(function () {

        debugger;
        var checksEstats = [];
        var checksTipus = [];

        //preparem la informació per a fer la cerca
        var alumneFalta = $("#alumneFalta").val();
        var profFalta = $("#butDropprofessorDropdownFaltes").val();
        var nivellFalta = $("#butDropnivellDropdownFaltes").val();
        var grupFalta = $("#butDropgrupDropdownFaltes").val();
        var dataInicial = $('#dataInicialFaltes').val();
        var dataFinal = $('#dataFinalFaltes').val();



        if ($("#cercaCheckImposada").prop('checked') == true) {
            checksEstats[0] = $("#cercaCheckImposada").val();
        } else {
            checksEstats[0] = 0;
        }

        if ($("#cercaCheckRevisada").prop('checked') == true) {
            checksEstats[1] = $("#cercaCheckRevisada").val();
        } else {
            checksEstats[1] = 0;
        }

        if ($("#cercaCheckExpedientada").prop('checked') == true) {
            checksEstats[2] = $("#cercaCheckExpedientada").val();
        } else {
            checksEstats[2] = 0;
        }

        if ($("#cercaCheckAmnistiada").prop('checked') == true) {
            checksEstats[3] = $("#cercaCheckAmnistiada").val();
        } else {
            checksEstats[3] = 0;
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



        //tipus de faltes per les quals buscar

        var tipusFaltes = $(".tipusCheck");
        debugger;
        var totesFaltes = true;
        //comptador de faltes a buscar
        var j = 0;

        for (var i = 0; i < tipusFaltes.length; i++) {
            if ($(tipusFaltes[i]).prop('checked')) {
                //s'ha de buscar per aquest tipus
                checksTipus[j] = $(tipusFaltes[i]).val();
                j++;
            } else {
                totesFaltes = false;
            }

        }



        debugger;

        var url = "php/cercaFaltes.php";
        $('body').css('cursor','progress');
        $.ajax({
            type: "POST",
            url: url,
            data: {"alumneFalta": alumneFalta, "profFalta": profFalta, "nivellFalta": nivellFalta, "grupFalta": grupFalta, "checksEstats": checksEstats, "totesFaltes": totesFaltes, "checksTipus": checksTipus, "dataInicial": dataInicial, "dataFinal": dataFinal},
            //data: ("#form2").serialize,
            success: function (data) {
                $("#divTaulaFaltes").html(data);
                $('body').css('cursor','default');
                //$("#taulaFaltes").tableHeadFixer();
                var rowCount = $('#taulaFaltes tr').length - 1;
                $("#totalFaltes").text("Registres cerca: " + rowCount);


            }

        });
        return false;
    });
});

function mostraprofessorDropdownFaltes(element) {
    $("#butDropprofessorDropdownFaltes").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropprofessorDropdownFaltes").val($(element).attr('data-val'));

}

function mostranivellDropdownFaltes(element) {
    $("#butDropnivellDropdownFaltes").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropnivellDropdownFaltes").val($(element).attr('data-val'));
    
    var nivell=$(element).attr('data-val');
    
    
    if(nivell!=''){
        //omplim el grup
        cercaGrupsNivell('grupDropdownFaltes',nivell);
    }

}

function mostragrupDropdownFaltes(element) {
    $("#butDropgrupDropdownFaltes").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropgrupDropdownFaltes").val($(element).attr('data-val'));

}
function canviEstatFalta(element) {
    //posem l'alumne
    var filera = $($(element).parent()).parent();
    var cellaAlumne = $(filera).children()[1];




    //posem l'alumne
    $("#alumneCanviEstatFalta").text($(cellaAlumne).text());
    //posem l'estat
    var estat = $($(filera).children()[8]).attr('data-estat');

    $("#formEstatFalta").attr('data-estat-form', estat);

    if (estat == 1) {
        $("#checkFaltaImposada").prop("checked", true);

    } else if (estat == 2) {
        $("#checkFaltaRevisada").prop("checked", true);

    } else if (estat == 3) {
        $("#checkFaltaExpedientada").prop("checked", true);
    } else if (estat == 4) {

        $("#checkFaltaAmnistiada").prop("checked", true);
    }

    var codialumne = $($(filera).children()[1]).attr('data-codi-al');
    var codifalta=$($(filera).children()[1]).attr('data-codi-falta');
    var codiprof = $($(filera).children()[4]).attr('data-codi-prof');
    var dia = $($(filera).children()[5]).text();
    var hora = $($(filera).children()[6]).text();


    //posem la resta de dades com a atributs del formulari
    $("#formEstatFalta").attr('data-codi-falta',codifalta);
    $("#formEstatFalta").attr('data-estat-form', estat);
    $("#formEstatFalta").attr('data-alumne-form', codialumne);
    $("#formEstatFalta").attr('data-prof-form', codiprof);
    $("#formEstatFalta").attr('data-dia-form', dia);
    $("#formEstatFalta").attr('data-hora-form', hora);
    $("#formEstatFalta").attr('data-num_filera', $(filera).index());



}

function desaCanviEstat() {
    //obtenin el nou estat
    var estat = 0;
    var textestat = "";
    if ($("#checkFaltaImposada").is(':checked')) {
        estat = 1;
        textestat = "Imposada";
    } else if ($("#checkFaltaRevisada").is(':checked')) {

        estat = 2;
        textestat = "Revisada";
    } else if ($("#checkFaltaExpedientada").is(':checked')) {
        estat = 3;
        textestat = "Expedientada";

    } else if ($("#checkFaltaAmnistiada").is(':checked')) {

        estat = 4
        textestat = "Amnistiada";
    }
    debugger;

    //ara ens caldrà la resta de dades per a modificar la falta

    var codialumne = $("#formEstatFalta").attr('data-alumne-form');
    var codiprof = $("#formEstatFalta").attr('data-prof-form');
    var dia = $("#formEstatFalta").attr('data-dia-form');
    var hora = $("#formEstatFalta").attr('data-hora-form');
    var numfilera = $("#formEstatFalta").attr('data-num_filera');
    var codifalta=$("#formEstatFalta").attr('data-codi-falta');

    //enviem les dades a php

    var url = "php/desaCanviEstat.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"codialumne": codialumne, "codiprof": codiprof, "dia": dia, "hora": hora, "estat": estat,"codifalta":codifalta},
        //data: ("#form2").serialize,
        success: function (data) {
            //canviem la filera
            //trobem la filera a modificar
            filera = $("#cosTaulaFaltes").children()[numfilera];

            //modifiquem l'estat
            $($(filera).children()[8]).text(textestat);
            $($(filera).children()[8]).attr('data-estat', estat);

        }

    });
    return false;



}

function mostraMotiu(element) {
    //posem l'alumne
    var filera = $($(element).parent()).parent();
    var cellaAlumne = $(filera).children()[1];



    $("#alumneMotiuFalta").text($(cellaAlumne).text());

    //posem el motiu
    $("#motiuFaltaVisualitza").val($(element).attr('data-motiu'));


}
