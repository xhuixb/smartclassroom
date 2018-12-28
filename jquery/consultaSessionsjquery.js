/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function () {


    $('#dataSessio').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });


});

function verificaData() {
    if ($('#dataSessio').val() !== '') {
        $("#cercaSessions").prop('disabled', false);
    } else {
        $("#cercaSessions").prop('disabled', true);
    }
}

function cercaSessionsData() {
    //agafo les dates
    var dataSessio = $('#dataSessio').val();

    dataSessio = dataSessio.substring(6) + '-' + dataSessio.substring(3, 5) + '-' + dataSessio.substring(0, 2);
    var url = "php/consultaSessions.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"dataSessio": dataSessio},
        success: function (data) {
            //rebem les dades
            $("#divTaulaSessions").html(data);

        }

    });
    return false;




}

function mostraSessioDetall(element) {

    //passsem les dades al modal
    $("#assigDetall").html('Assignatura: ' + '<strong>' + $(element).attr('data-assig-detall') + '</strong>');
    $("#profeDetall").html('Professor: ' + '<strong>' + $(element).attr('data-profe-detall') + '</strong>');
    $("#nivellDetall").html('Nivell: ' + '<strong>' + $(element).attr('data-nivell-detall') + '</strong>');
    $("#grupDetall").html('Grup: ' + '<strong>' + $(element).attr('data-grup-detall') + '</strong>');
    $("#aulaDetall").html('Aula: ' + '<strong>' + $(element).attr('data-aula-detall') + '</strong>');
    $("#profeGuardiaDetall").html('Substitut: ' + '<strong>' + $(element).attr('data-nomprofsubs') + '</strong>');
    if ($(element).attr('data-proftit') != '') {
        $("#estatSessio").html('<strong class="btn-success">Llista passada</strong>');
    } else {
        $("#estatSessio").html('<strong class="btn-danger">Llista pendent</strong>');
    }

    debugger;
    //anem a buscar els alumnes de la sessió o de l'horari
    var profe = $(element).attr('data-codiprof');
    var dataSessio = $('#dataSessio').val();
    dataSessio = dataSessio.substring(6) + '-' + dataSessio.substring(3, 5) + '-' + dataSessio.substring(0, 2);
    var hora = $(element).attr('data-hora');
    var profSessio = $(element).attr('data-proftit');
    var grup=$(element).attr('data-grup');
    var tipusGrup=$(element).attr('data-tipusgrup');
    var nivell=$(element).attr('data-nivell');

    var url = "php/mostraAlumnesSessio.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"profe": profe, "dataSessio": dataSessio, "hora": hora, "profSessio": profSessio,"grup":grup,"tipusGrup":tipusGrup,"nivell":nivell},
        success: function (data) {
            //rebem les dades
            $("#divAlumnesPerGrup").html(data);

        }

    });
    return false;


}