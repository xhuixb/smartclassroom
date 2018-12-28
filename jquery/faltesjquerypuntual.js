/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    $('.clockpicker').clockpicker();
});

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

$(document).ready(function () {
    $('#dataFaltaSingular').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });

});


function carregaDadesIncialsCCCpuntuals() {
    carregaDropGenericCheck('tipusFaltaDiv', 'SELECT ga22_codi_falta as codi, ga22_nom_falta as descripcio FROM ga22_tipus_falta', 'Tipus falta');
    carregaDropGeneric('nivellAlumneFalta', 'SELECT distinct(ga35_nivell) as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga35_nivell=ga06_codi_nivell', 'Tria nivell');
   // carregaDropGeneric('grupAlumneFalta', 'SELECT ga07_codi_grup as codi, ga07_descripcio_grup as descripcio FROM ga07_grup', 'Tria grup');
    carregaDropGeneric('tipusFaltaSingular', 'SELECT ga22_codi_falta as codi,ga22_nom_falta as descripcio FROM ga22_tipus_falta', 'Tria tipus');

}

function mostranivellAlumneFalta(element) {
    $("#butDropnivellAlumneFalta").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropnivellAlumneFalta").val($(element).attr('data-val'));

    var nivell=$(element).attr('data-val');
    if(nivell!=''){
        cercaGrupsNivell('grupAlumneFalta',nivell);
        
    }

    habilitaDropAlumne();
    habilitaDesaFalta();

}

function mostragrupAlumneFalta(element) {
    $("#butDropgrupAlumneFalta").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropgrupAlumneFalta").val($(element).attr('data-val'));

    habilitaDropAlumne();
    habilitaDesaFalta();
}

function mostraalumnesFalta(element) {
    $("#butDropalumnesFalta").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropalumnesFalta").val($(element).attr('data-val'));
    habilitaDesaFalta();

}


function mostratipusFaltaSingular(element) {
    $("#butDroptipusFaltaSingular").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDroptipusFaltaSingular").val($(element).attr('data-val'));
    habilitaDesaFalta();

}



function habilitaDropAlumne() {
    if ($('#butDropnivellAlumneFalta').val() != '' && $('#butDropgrupAlumneFalta').val() != '') {
        //habilitem el drop d'alumnes
        $('#alumnesFaltaButton').prop('disabled', false);

        var nivell = $("#butDropnivellAlumneFalta").val();
        var grup = $("#butDropgrupAlumneFalta").val();

        //omplim el drop amb els alumnes
        carregaDropGeneric('alumnesFalta', "select ga12_id_alumne as codi,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as descripcio from ga11_alumnes,ga12_alumnes_curs where ga12_codi_nivell=" + nivell + " and ga12_codi_grup=" + grup + " and ga12_id_alumne=ga11_id_alumne and ga12_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) order by descripcio", "Tria alumne");

    }

}

function habilitaDesaFalta() {

    //si tots els camps necessaris estan informats habilitem desa
    if ($('#butDropnivellAlumneFalta').val() != '' && $('#butDropgrupAlumneFalta').val() != ''
            && $("#butDropalumnesFalta").val() != '' && $("#butDroptipusFaltaSingular").val() != ''
            && $("#dataFaltaSingular").val() != '' && $("#rellotge").val() != '') {
        $("#desaFaltaPuntual").prop('disabled', false);
    }


}


function cercaFaltes() {
    var checksTipus = [];
    var dataInicial = $('#dataInicialFaltes').val();
    var dataFinal = $('#dataFinalFaltes').val();


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

    var url = "php/cercaFaltesPuntuals.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"checksTipus": checksTipus, "totesFaltes": totesFaltes, "dataInicial": dataInicial, "dataFinal": dataFinal},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divTaulaFaltes").html(data);
            //$("#taulaFaltes").tableHeadFixer();
            var rowCount = $('#taulaFaltes tr').length - 1;
            $("#totalFaltes").text("Registres cerca: " + rowCount);


        }

    });
    return false;


}

function mostraMotiu(element) {
    //posem l'alumne
    var filera = $($(element).parent()).parent();
    var cellaAlumne = $(filera).children()[0];


    //posem el nom de l'alumne
    $("#alumneMotiuFalta").text($(cellaAlumne).text());

    //posem el codi de la falta
    $("#alumneMotiuFalta").attr('data-codi-falta', $(cellaAlumne).attr('data-codi-falta'));

    //posem el motiu
    $("#motiuFaltaVisualitza").val($(element).attr('data-motiu'));

    if ($(element).attr('data-tutor') == '1') {
        $("#enviaCheckTutorEdit").prop('checked', true);
    } else {
        $("#enviaCheckTutorEdit").prop('checked', false);
    }


    if ($(element).attr('data-pares') == '1') {
        $("#enviaCheckParesEdit").prop('checked', true);
    } else {
        $("#enviaCheckParesEdit").prop('checked', false);
    }

}

function desaFaltaPuntual() {
    //prenem les dades necessàries per a crear la falta
    debugger;

    var alumne = $('#butDropalumnesFalta').val();

    var dia = $('#dataFaltaSingular').val();
    var hora = $('#rellotge').val();
    var tipus = $('#butDroptipusFaltaSingular').val();

    if ($('#enviaCheckPares').prop('checked') == true) {
        var enviaPares = 1;
    } else {
        var enviaPares = 0;
    }

    if ($('#enviaCheckTutor').prop('checked') == true) {
        var enviaTutor = 1;
    } else {
        var enviaTutor = 0;
    }

//posem les dates en format internacional
    if (dia != "") {

        var dataI = dia.split("/");
        dia = dataI[1] + "/" + dataI[0] + "/" + dataI[2];

    }

    var motiu = $('#motiuFaltaPuntual').val().replace(/"/g, "&quot;");

    //ho enviem per ajax al servidor
    var url = "php/desaFaltaPuntual.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"alumne": alumne, "dia": dia, "hora": hora, "tipus": tipus, "enviaPares": enviaPares, "enviaTutor": enviaTutor, "motiu": motiu},
        //data: ("#form2").serialize,
        success: function (data) {
            //refresquem la taula

            $("#divTaulaFaltes").html(data);
            netejaModalAlta();
            cercaFaltes();
        }

    });
    return false;




}

function netejaModalAlta() {
    $("#butDropnivellAlumneFalta").html('Tria nivell' + ' ' + '<span class="caret">');
    $("#butDropnivellAlumneFalta").val('');

    $("#butDropgrupAlumneFalta").html('Tria grup' + ' ' + '<span class="caret">');
    $("#butDropgrupAlumneFalta").val('');

    $("#alumnesFalta").html('<button id="alumnesFaltaButton" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" disabled>Tria alumne<span class="caret"></span></button>');

    $("#butDroptipusFaltaSingular").html('Tria tipus' + ' ' + '<span class="caret">');
    $("#butDroptipusFaltaSingular").val('');

    $('#dataFaltaSingular').val('');
    $('#rellotge').val('');

    $('#motiuFaltaPuntual').val('');

    $('#enviaCheckPares').prop('checked', true);

    $('#enviaCheckTutor').prop('checked', true);

    $("#desaFaltaPuntual").prop('disabled', true);

}

function desaEditFaltaPuntual() {
    //agafem el codi motiu i els check

    var codiFalta = $("#alumneMotiuFalta").attr('data-codi-falta');
    var motiu = $("#motiuFaltaVisualitza").val().replace(/"/g, "&quot;");

    if ($("#enviaCheckTutorEdit").prop('checked') == true) {
        var enviaTutor = 1;

    } else {

        var enviaTutor = 0;
    }

    if ($("#enviaCheckParesEdit").prop('checked') == true) {
        var enviaPares = 1;

    } else {

        var enviaPares = 0;
    }


    //ho enviem per ajax al servidor
    var url = "php/editaFaltaPuntual.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"codiFalta": codiFalta, "motiu": motiu, "enviaTutor": enviaTutor, "enviaPares": enviaPares},
        //data: ("#form2").serialize,
        success: function (data) {
            //refresquem la taula
            cercaFaltes();
        }

    });
    return false;


}

function esborraFaltaPuntual() {
    //agafem el codi de la falta a esborrar

    var resposta = confirm("N'estàs segur de voler esborrar aquesta falta d'ordre?");

    if (resposta == true) {
        var codiFalta = $("#alumneMotiuFalta").attr('data-codi-falta');

        //ho enviem per ajax al servidor
        var url = "php/esborraFaltaPuntual.php";
        $.ajax({
            type: "POST",
            url: url,
            data: {"codiFalta": codiFalta},
            //data: ("#form2").serialize,
            success: function (data) {
                //refresquem la taula
                alert("falta d'ordre esborrada");
                cercaFaltes();
            }

        });
        return false;
    }
}