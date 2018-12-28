/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function carregaDadesIniHorAula() {
    carregaDropGeneric('divAulaHorari', 'SELECT ga01_codi_aula as codi, ga01_descripcio_aula as descripcio FROM ga01_aula', 'Tria Aula');

}

function mostradivAulaHorari(element) {

    $("#butDropdivAulaHorari").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivAulaHorari").val($(element).attr('data-val'));
    cercaHorariAlumne('horarisConsultaAula');

}




function cercaHorariAlumne(div) {
    var url = "php/consultaHorariAlumne.php";

    var codiAlumne = '';
    var nivell = '';
    var grup = '';
    var aula = $("#butDropdivAulaHorari").val();

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

