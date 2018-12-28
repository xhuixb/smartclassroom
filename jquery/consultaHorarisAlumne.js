/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function carregaDadesIniHorAlumne() {
    carregaDropGeneric('divNivellHorari', 'SELECT distinct(ga35_nivell) as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga35_nivell=ga06_codi_nivell', 'Tria Nivell');

}

function mostradivNivellHorari(element) {

    $("#butDropdivNivellHorari").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivNivellHorari").val($(element).attr('data-val'));

    var nivell = $(element).attr('data-val');

    if (nivell != '') {
        cercaGrupsNivell('divGrupHorari', nivell);
    }


}

function mostradivGrupHorari(element) {

    $("#butDropdivGrupHorari").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivGrupHorari").val($(element).attr('data-val'));

    habilitaDropAlumne();

}


function mostradivAlumnesHorari(element) {
    $("#butDropdivAlumnesHorari").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivAlumnesHorari").val($(element).attr('data-val'));

}


function habilitaDropAlumne() {
    if ($('#butDropdivNivellHorari').val() != '' && $('#butDropdivGrupHorari').val() != '') {
        //habilitem el drop d'alumnes


        var nivell = $("#butDropdivNivellHorari").val();
        var grup = $("#butDropdivGrupHorari").val();

        //omplim el drop amb els alumnes
        carregaDropGeneric('divAlumnesHorari', "select ga12_id_alumne as codi,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as descripcio from ga11_alumnes,ga12_alumnes_curs where ga12_codi_nivell=" + nivell + " and ga12_codi_grup=" + grup + " and ga12_id_alumne=ga11_id_alumne and ga12_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) order by descripcio", "Tria alumne");
        $('#cercaHorariAlumne').prop('disabled', false);
    }

}



function cercaHorariAlumne(div) {
    var url = "php/consultaHorariAlumne.php";

    var codiAlumne = $("#butDropdivAlumnesHorari").val();
    var nivell = $("#butDropdivNivellHorari").val();
    var grup = $("#butDropdivGrupHorari").val();
    var aula = '';

    $.ajax({
        type: "POST",
        url: url,
        data: {"nivell": nivell, "grup": grup, "codiAlumne": codiAlumne, "aula": aula},
        //data: ("#form2").serialize,
        success: function (data) {
            debugger;
            $("#" + div).html(data);

        }

    });

    var ximplet="";

    return false;

}

$(document).ready(function () {

    $('[data-toggle="tooltip"]').tooltip();
});

function provaTooltip(element) {

    debugger;

    $(element).attr('title', 'Assignatura: ' + $(element).attr('data-assig') + '\n' + 'Professor: ' + $(element).attr('data-prof') + '\n' + 'Nivell: ' + $(element).attr('data-nivell') + '\n' + 'Grup: ' + $(element).attr('data-grup') + '\n' + 'Aula: ' + $(element).attr('data-aula'));

}

function mostraDetallHorari(element) {

    //capçalera
    switch ($(element).attr('data-dia')) {
        case '1':
            var diaText = "Dilluns";
            break;
        case '2':
            var diaText = "Dimarts";
            break;
        case '3':
            var diaText = "Dimecres";
            break;
        case '4':
            var diaText = "Dijous";
            break;
        case '5':
            var diaText = "Divendres";
            break;

    }

    $("#capcaleraTram").html('<strong>' + diaText + '</strong>' + ' de : <strong>' + $(element).attr('data-horainici') + '</strong> a: <strong>' + $(element).attr('data-horafi') + '</strong>');


    //detall
    $("#assigDetall").html('Assignatura: ' + '<strong>' + $(element).attr('data-assig') + '</strong>');
    $("#profeDetall").html('Professor: ' + '<strong>' + $(element).attr('data-prof') + '</strong>');
    $("#nivellDetall").html('Nivell: ' + '<strong>' + $(element).attr('data-nivell') + '</strong>');
    $("#grupDetall").html('Grup: ' + '<strong>' + $(element).attr('data-grup') + '</strong>');
    $("#aulaDetall").html('Aula: ' + '<strong>' + $(element).attr('data-aula') + '</strong>');


    //anem a buscar els membres del grup
    var codiGrup = $(element).attr('data-codi-grup');
    var tipusGrup = $(element).attr('data-tipus-grup');
    var codiNivell = $(element).attr('data-codi-nivell');

    var url = "php/alumnesPerGrup.php";

    $.ajax({
        type: "POST",
        url: url,
        data: {"codiGrup": codiGrup, "tipusGrup": tipusGrup, "codiNivell": codiNivell},
        //data: ("#form2").serialize,
        success: function (data) {
            debugger;
            $("#divAlumnesPerGrup").html(data);

        }

    });


    return false;

}

