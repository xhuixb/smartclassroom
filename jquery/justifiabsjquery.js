/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

function carregaDadesInicialsAbsencies() {
    carregaDropGeneric('nivellAbsDropdown', 'SELECT distinct(ga35_nivell) as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga35_nivell=ga06_codi_nivell', 'Tria Nivell');
   // carregaDropGeneric('grupAbsDropdown', 'SELECT ga07_codi_grup as codi, ga07_descripcio_grup as descripcio FROM ga07_grup', 'Tria Grup');

}

function mostranivellAbsDropdown(element) {
    
    
    $("#butDropnivellAbsDropdown").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropnivellAbsDropdown").val($(element).attr('data-val'));
    $("#divTaulaJustiAbsencies").html("");
    
    var nivell=$(element).attr('data-val');
    if(nivell!=''){
        //carreguem els grups
        cercaGrupsNivell('grupAbsDropdown',nivell);
        
    }
    
    habilitacerca();

}

function mostragrupAbsDropdown(element) {

    $("#butDropgrupAbsDropdown").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropgrupAbsDropdown").val($(element).attr('data-val'));
    //esborrem taula
    $("#divTaulaJustiAbsencies").html("");
    habilitacerca();

}

function cercaAbsencies() {
    var url = "php/cercaAbsencies.php";
    var nivell = $("#butDropnivellAbsDropdown").val();
    var grup = $("#butDropgrupAbsDropdown").val();

    if ($("#NoJustiCheck").prop('checked') == true) {
        var checkNoJusti = '1';
    } else {

        var checkNoJusti = '0';
    }

    if ($("#SiJustiCheck").prop('checked') == true) {
        var checkJusti = '1';
    } else {

        var checkJusti = '0';
    }



    if ($("#orderAlumne").prop('checked') == true) {
        var checkOrderAlumne = '1';
    } else {

        var checkOrderAlumne = '0';
    }


    debugger;
    $.ajax({
        type: "POST",
        url: url,
        data: {"nivell": nivell, "grup": grup, "checkJusti": checkJusti, "checkNoJusti": checkNoJusti, "checkOrderAlumne": checkOrderAlumne},
        //data: ("#form2").serialize,
        success: function (data) {
            debugger;
            $("#divTaulaJustiAbsencies").html(data);

        }

    });


    return false;

}



function cercaAbsenciesNova(element) {


    var url = "php/cercaAbsenciesNova.php";
    var nivell = $("#butDropnivellAbsDropdown").val();
    var grup = $("#butDropgrupAbsDropdown").val();
    var dataInicial = $('#dataInicialAbsencies').val();
    var dataFinal = $('#dataFinalAbsencies').val();


    //posem les dates en format internacional
    if (dataInicial != "") {

        var dataI = dataInicial.split("/");
        dataInicial = dataI[1] + "/" + dataI[0] + "/" + dataI[2];

    }
    if (dataFinal != "") {
        var dataF = dataFinal.split("/");
        dataFinal = dataF[1] + "/" + dataF[0] + "/" + dataF[2];
    }



    debugger;

    if ($("#NoJustiCheck").prop('checked') == true) {
        var checkNoJusti = '1';
    } else {

        var checkNoJusti = '0';
    }

    if ($("#SiJustiCheck").prop('checked') == true) {
        var checkJusti = '1';
    } else {

        var checkJusti = '0';
    }


    if ($(element).attr('id') == 'cercaPerData') {
        var checkOrderAlumne = '0';
        $("#signeOrdenacio").html('<strong>Ordenat per data descendentment</strong>');

    } else if ($(element).attr('id') == 'cercaPerAlumne') {
        var checkOrderAlumne = '1';
        $("#signeOrdenacio").html('<strong>Ordenat per alumne alfatèticament</strong>');
    } else {
        var checkOrderAlumne = '1';
        $("#signeOrdenacio").html('<strong>Ordenat per alumne alfatèticament</strong>');

    }


    /* if ($("#orderAlumne").prop('checked') == true) {
     var checkOrderAlumne = '1';
     } else {
     
     var checkOrderAlumne = '0';
     }*/


    debugger;
    $.ajax({
        type: "POST",
        url: url,
        data: {"nivell": nivell, "grup": grup, "checkJusti": checkJusti, "checkNoJusti": checkNoJusti, "checkOrderAlumne": checkOrderAlumne,"dataInicial":dataInicial,"dataFinal":dataFinal},
        //data: ("#form2").serialize,
        success: function (data) {
            debugger;
            $("#divTaulaJustiAbsencies").html(data);

        }

    });


    return false;

}


function habilitacerca() {

    if ($("#butDropnivellAbsDropdown").val() != "" && $("#butDropgrupAbsDropdown").val() != "") {
        //habilitem cerca
        $("#cercaAbsencies").prop("disabled", false);
    } else {
        //habilitem cerca
        $("#cercaAbsencies").prop("disabled", true);
    }

}
function carregaJustifiAbs(element) {
    debugger;
    //la cella que conté el button
    var celaJustifi = $(element).parent();

    //la filera del button
    var filera = $(celaJustifi).parent();

    //columna del button
    var indexCol = $(celaJustifi).index();
    //la filera del button

    var indexRow = $(filera).index();

    //totes les celles del la filera
    var totesCelles = filera.children();

    //les dades que passarem al modal
    var codiAlumne = $(totesCelles[1]).attr('data-codi');
    var codiLinia = $(totesCelles[1]).attr('data-linia');
    var nomAlumne = $(totesCelles[1]).text();
    var codiJustifi = $(celaJustifi).attr('data-codi-justifi');
    var motiuJustifi = $(celaJustifi).attr('data-motiu-justifi').replace(/&quot;/g, '"');
    var profJustifi = $(celaJustifi).attr('data-codi-prof');
    var dia = $(totesCelles[0]).text();

    //cal anar a buscar l'hora a la capçalera
    var hora = $($($("#taulaJustifiAbsenciesHeader").children()[0]).children()[indexCol]).text().substring(0,5);;


    //var hora = $(totesCelles[1]).text();

    //passem les dades
    $("#alumneJustificacioAbsencia").text(nomAlumne);
    $("#dataHoraJustificacioAbsencia").text('Data: ' + dia + '  Hora: ' + hora);
    $("#alumneJustificacioAbsencia").attr('data-codialumne', codiAlumne);
    $("#alumneJustificacioAbsencia").attr('data-row', indexRow);
    $("#alumneJustificacioAbsencia").attr('data-col', indexCol);
    $("#alumneJustificacioAbsencia").attr('data-dia', dia);
    $("#alumneJustificacioAbsencia").attr('data-hora', hora);
    $("#alumneJustificacioAbsencia").attr('data-prof', profJustifi);



    $("#justifiAbsenciaVisualitza").val(motiuJustifi);
    $("#justifiAbsenciaVisualitza").attr('data-codimotiu', codiJustifi);

}

function desaJustificacio() {

    debugger;
    var url = "php/desaJustificacio.php";
    var codiJustifi = $("#justifiAbsenciaVisualitza").attr('data-codimotiu');
    var motiuJustifi = $("#justifiAbsenciaVisualitza").val().replace(/"/g, "&quot;");
    var alumne = $("#alumneJustificacioAbsencia").attr('data-codialumne');
    var dia = $("#alumneJustificacioAbsencia").attr('data-dia');
    var hora = $("#alumneJustificacioAbsencia").attr('data-hora');
    var prof = $("#alumneJustificacioAbsencia").attr('data-prof');
    var indexRow = $("#alumneJustificacioAbsencia").attr('data-row');
    var indexCol = $("#alumneJustificacioAbsencia").attr('data-col');

    if ($("#restaDiaCheck").prop('checked')) {
        var totDia = 1;

    } else {
        var totDia = 0;

    }

    if (motiuJustifi == "") {
        codiJustifi = "0";
    } else {
        codiJustifi = "1";
    }



    $.ajax({
        type: "POST",
        url: url,
        data: {"codiJustifi": codiJustifi, "motiuJustifi": motiuJustifi, "alumne": alumne, "dia": dia, "hora": hora, "prof": prof, "totDia": totDia},
        //data: ("#form2").serialize,

        success: function (data) {
           // $("#divTaulaJustiAbsencies").html(data);
            //actualitzem les noves dades
            //nou codi de justificació

            if (totDia == 0) {

                //només posem en vermell aquella hora

                var fileraModificar = $("#costaulaJustifiAbsencies").children()[indexRow];
                var cellaModificar = $(fileraModificar).children()[indexCol];

                $(cellaModificar).attr('data-codi-justifi', codiJustifi);
                $(cellaModificar).attr('data-motiu-justifi', motiuJustifi);

                if (codiJustifi == "0") {
                    $(cellaModificar).html('<button type="button" class="btn form-control btn-danger" data-toggle="modal" data-target="#absenciesModalForm" onclick="carregaJustifiAbs(this)"><span class="glyphicon glyphicon-pencil"></span>No justificat</button>');

                } else {
                    $(cellaModificar).html('<button type="button" class="btn form-control btn-success" data-toggle="modal" data-target="#absenciesModalForm" onclick="carregaJustifiAbs(this)"><span class="glyphicon glyphicon-pencil"></span>Justificat</button>');
                }
            } else {
                //posem en vermell totes les hores del dia
                var fileraModificar = $("#costaulaJustifiAbsencies").children()[indexRow];
                var cellesModificar = $(fileraModificar).children();

                //recorrem tots els elements
                for (var i = 2; i < cellesModificar.length; i++) {
                    //si és de la data del dia en qüestió, la posem en vermell

                    $(cellesModificar[i]).attr('data-codi-justifi', codiJustifi);
                    $(cellesModificar[i]).attr('data-motiu-justifi', motiuJustifi);

                    debugger;

                    if ($(cellesModificar[i]).children().length > 0) {

                        //si en la cella hi ha alguna absència
                        if (codiJustifi == "0") {
                            $(cellesModificar[i]).html('<button type="button" class="btn form-control btn-danger" data-toggle="modal" data-target="#absenciesModalForm" onclick="carregaJustifiAbs(this)"><span class="glyphicon glyphicon-pencil"></span>No justificat</button>');

                        } else {
                            $(cellesModificar[i]).html('<button type="button" class="btn form-control btn-success" data-toggle="modal" data-target="#absenciesModalForm" onclick="carregaJustifiAbs(this)"><span class="glyphicon glyphicon-pencil"></span>Justificat</button>');
                        }
                    }

                }

            }
        }

    });


    return false;


}

function ordenaPerAlumne(element) {

    debugger;
    var table, rows, switching, i, x, y, shouldSwitch;
    table = document.getElementById("taulaJustifiAbsencies");
    switching = true;
    /*Make a loop that will continue until
     no switching has been done:*/
    while (switching) {
        //start by saying: no switching is done:
        switching = false;
        rows = table.getElementsByTagName("TR");
        /*Loop through all table rows (except the
         first, which contains table headers):*/

        for (i = 1; i < (rows.length - 1); i++) {
            //start by saying there should be no switching:
            shouldSwitch = false;
            /*Get the two elements you want to compare,
             one from current row and one from the next:*/
            x = rows[i].getElementsByTagName("TD")[2];
            y = rows[i + 1].getElementsByTagName("TD")[2];
            //check if the two rows should switch place:
            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                //if so, mark as a switch and break the loop:
                shouldSwitch = true;
                break;
            }
        }
        if (shouldSwitch) {
            /*If a switch has been marked, make the switch
             and mark that a switch has been done:*/
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
        }
    }
}

