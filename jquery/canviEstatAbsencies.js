/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function carregaDadesInicialsCanviEstat() {
    //omplim els dropdown
    carregaDropGeneric('nivellDropdownCanviEstat', 'SELECT distinct(ga35_nivell) as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga35_nivell=ga06_codi_nivell', 'Tria Nivell');
    //carregaDropGeneric('horaDropdownCanviEstat', "SELECT ga10_hora_inici as codi, concat('de ',ga10_hora_inici,' a ',ga10_hora_fi) as descripcio from ga10_horaris_aula", 'Tram horari');
    carregaDropHoraris('horaDropdownCanviEstat');
    //carregaDropGeneric('grupDropdownCanviEstat', 'SELECT ga07_codi_grup as codi, ga07_descripcio_grup as descripcio FROM ga07_grup', 'Grup');

}

function mostrahoraDropdownCanviEstat(element) {

    //anem a veure si està desabilitat
    var pareElement = $(element).parent();

    if ($(pareElement).hasClass('disabled') == false) {
        $("#butDrophoraDropdownCanviEstat").html($(element).text() + ' ' + '<span class="caret">');
        $("#butDrophoraDropdownCanviEstat").val($(element).attr('data-val'));
        // $("#pprovasessio").text($("#butDropnivellDropdown").val() + '-' + $("#butDropnivellDropdown").text());
        habilitaCercaAlumnes();
    }
}

function mostranivellDropdownCanviEstat(element) {
    $("#butDropnivellDropdownCanviEstat").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropnivellDropdownCanviEstat").val($(element).attr('data-val'));
    
    var nivell=$(element).attr('data-val');
    
    if(nivell!=''){
        cercaGrupsNivell('grupDropdownCanviEstat',nivell)
        
    }
    
    habilitaCercaAlumnes();
}

function mostragrupDropdownCanviEstat(element) {
    $("#butDropgrupDropdownCanviEstat").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropgrupDropdownCanviEstat").val($(element).attr('data-val'));
    habilitaCercaAlumnes();

}

$(document).ready(function () {
    $('#dataCanviEstat').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });
});

function habilitaCercaAlumnes() {

    debugger;

    if ($("#butDropnivellDropdownCanviEstat").val() != '' && $("#butDropgrupDropdownCanviEstat").val() != '' && $("#butDrophoraDropdownCanviEstat").val() != '' && $("#dataCanviEstat").val() != '') {
        $("#cercaAssisCanviEstat").prop('disabled', false);
    } else {
        $("#cercaAssisCanviEstat").prop('disabled', true);
    }

    $("#divTaulaAlumnesCanviEstat").html("");
}

function cercaAsCanviEstat() {

    var nivell = $("#butDropnivellDropdownCanviEstat").val();
    var grup = $("#butDropgrupDropdownCanviEstat").val();
    var hora = $("#butDrophoraDropdownCanviEstat").val();
    var dia = $("#dataCanviEstat").val();

    if ($("#checkResum").prop('checked') == true) {
        var resum = 1;

    } else {

        var resum = 0;
    }

    var dataSplit = dia.split("/");
    dia = dataSplit[1] + "/" + dataSplit[0] + "/" + dataSplit[2];

    //ho enviem per ajax

    var url = "php/cercaAsCanviEstat.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"nivell": nivell, "grup": grup, "dia": dia, "hora": hora, "resum": resum},
        //data: ("#form2").serialize,
        success: function (data) {

            //rebem les dades
            $("#divTaulaAlumnesCanviEstat").html(data);

        }

    });
    return false;



}

function comprovaCheckEstat(element) {
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
    var posCheck2=0;
    var posCheck3=0;
    
    //esbrinem posicions a desmarcar
    if (posCheck1 === 3) {
        posCheck2 = 4;
        posCheck3 = 5;
    } else if (posCheck1 === 4) {
        posCheck2 = 3;
        posCheck3 = 5;

    } else {
        posCheck2 = 3;
        posCheck3 = 4;
    }

    //obtenime els elements a desmarcar
    //var taula = document.getElementById('taulaAlumnesCanviEstat');
    var taula=$("#taulaAlumnesCanviEstat");

    var ele1=$($(aviCheck).children()[posCheck2]).children()[0];
    var ele2=$($(aviCheck).children()[posCheck3]).children()[0];
    
    //var ele1 = taula.rows[filera + 1].cells[posCheck2].firstChild;
    
    //var ele2 = taula.rows[filera + 1].cells[posCheck3].firstChild;


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

function actualitzaEstat(element) {
    //obtenim les dades per a localitzar el registre d'assistència
    debugger;
    var hora = $("#butDrophoraDropdownCanviEstat").val();
    var dia = $("#dataCanviEstat").val();
    var filera = $($(element).parent()).parent();

    var alumne = $($(filera).children()[1]).attr('data-codi-alumne');
    var professor = $($(filera).children()[2]).attr('data-codi-prof');


    var diaSplit = dia.split("/");
    var dia = diaSplit[1] + "/" + diaSplit[0] + "/" + diaSplit[2];


    //ara obtenim les dades a actualitzar
    if ($($($(filera).children()[3]).children()[0]).prop('checked')) {
        var pos = 1
    } else if ($($($(filera).children()[4]).children()[0]).prop('checked')) {
        var pos = 2;
    } else if ($($($(filera).children()[5]).children()[0]).prop('checked')) {
        var pos = 3;
    }

    //obtenim la comunicacio

    if ($($($(filera).children()[6]).children()[0]).prop('checked')) {
        var checkComunica = 1;
    } else {
        var checkComunica = 0;
    }


    //ho enviem per ajax

    var url = "php/actualitzaEstat.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"alumne": alumne, "professor": professor, "dia": dia, "hora": hora, "pos": pos, "checkComunica": checkComunica},
        //data: ("#form2").serialize,
        success: function (data) {

            alert("Registre d'assistència actualitzat correctament");

        }

    });
    return false;



}