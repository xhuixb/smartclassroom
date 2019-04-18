/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function iniTooltip() {

    $('[data-tooltip="tooltip"]').tooltip();

}


$(document).ready(function () {

    $('#dataInicialResum').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });
});

$(document).ready(function () {
    $('#dataFinalResum').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });
});


function carregaEquipament(mode, filtre) {

    if (mode === '0') {
        carregaDropGeneric('divNivellPrestec', 'SELECT distinct(ga35_nivell) as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga35_nivell=ga06_codi_nivell', 'Tria Nivell');
    }
    var url = "php/carregaEquipament.php";

    $.ajax({
        type: "POST",
        url: url,
        data: {"filtre": filtre},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divTaulaEquipament").html(data);
            iniTooltip();

        }

    });


    return false;

}

function canviaEstatPrestec(element) {

    var estat = $(element).attr('data-estat');
    debugger;


    if ($("#radioTotal").prop('checked') === true) {
        //tots
        var filtre = '1';
    } else if ($("#radioDispo").prop('checked') === true) {
        //només disponibles
        var filtre = '2';

    } else {
        //nomes prestats
        var filtre = '3';
    }

    if (estat === '0') {
        //obrim el modal
        //jQuery.noConflict();
        $('#prestaEquipament').modal('show');
        var equipament = $($($(element).parent()).siblings()[0]).text();
        var codiEquipament = $($($(element).parent()).siblings()[0]).attr('data-codi');
        $("#nomEquipament").text(equipament);
        $("#nomEquipament").attr('data-codi', codiEquipament);

    } else {
        var confirmacio = confirm('Vols fer efectiva la devolució');
        if (confirmacio === true) {
            //fem efectiva la devolució
            debugger;
            var codiEquip = $($($(element).parent()).siblings()[0]).attr('data-codi');
            var url = "php/esborraPrestec.php";

            $.ajax({
                type: "POST",
                url: url,
                data: {"codiEquip": codiEquip},
                //data: ("#form2").serialize,
                success: function (data) {
                    //$("#divTaulaEquipament").html(data);
                    //buidem l'alumne i desabilitem desar   
                    if (data === '1') {
                        alert("El préstec ja ha estat retornat");
                    }

                    carregaEquipament('1', filtre);

                }

            });


            return false;
        }

    }
}

function mostradivNivellPrestec(element) {
    $("#butDropdivNivellPrestec").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivNivellPrestec").val($(element).attr('data-val'));

    var nivell = $(element).attr('data-val');

    if (nivell != '') {
        cercaGrupsNivell('divGrupPrestec', nivell);
    }
}

function mostradivGrupPrestec(element) {

    $("#butDropdivGrupPrestec").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivGrupPrestec").val($(element).attr('data-val'));

    habilitaDropPrestec();

}


function habilitaDropPrestec() {

    if ($('#butDropdivNivellPrestec').val() != '' && $('#butDropdivGrupPrestec').val() != '') {
        //habilitem el drop d'alumnes


        var nivell = $("#butDropdivNivellPrestec").val();
        var grup = $("#butDropdivGrupPrestec").val();

        //omplim el drop amb els alumnes
        carregaDropGeneric('divAlumnePrestec', "select ga12_id_alumne as codi,concat(ga11_cognom1,' ',ga11_cognom2,', ',ga11_nom) as descripcio from ga11_alumnes,ga12_alumnes_curs where ga12_codi_nivell=" + nivell + " and ga12_codi_grup=" + grup + " and ga12_id_alumne=ga11_id_alumne and ga12_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) order by descripcio", "Tria alumne");

    }

}


function mostradivAlumnePrestec(element) {
    debugger;
    $("#butDropdivAlumnePrestec").html($(element).text() + ' ' + '<span class="caret"></span>');
    $("#butDropdivAlumnePrestec").val($(element).attr('data-val'));
    $('#desaPrestec').prop('disabled', false);
}

function desaPrestec() {


    if ($("#radioTotal").prop('checked') === true) {
        //tots
        var filtre = '1';
    } else if ($("#radioDispo").prop('checked') === true) {
        //només disponibles
        var filtre = '2';

    } else {
        //nomes prestats
        var filtre = '3';
    }




    var codiEquip = $("#nomEquipament").attr('data-codi');
    var codiAlumne = $("#butDropdivAlumnePrestec").val();

    var url = "php/desaPrestec.php";

    $.ajax({
        type: "POST",
        url: url,
        data: {"codiEquip": codiEquip, "codiAlumne": codiAlumne},
        //data: ("#form2").serialize,
        success: function (data) {
            //$("#divTaulaEquipament").html(data);
            //buidem l'alumne i desabilitem desar
            if (data === '0') {
                $("#butDropdivAlumnePrestec").html('Tria alumne ' + '<span class="caret">');
                $("#butDropdivAlumnePrestec").val('');
                $('#desaPrestec').prop('disabled', true);
            } else {
                $("#butDropdivAlumnePrestec").html('Tria alumne ' + '<span class="caret">');
                $("#butDropdivAlumnePrestec").val('');
                $('#desaPrestec').prop('disabled', true);
                alert("Equipament ja prestat");
            }
            carregaEquipament(1, filtre);

        }

    });


    return false;

}

function tancaPrestec() {
    //desabilito grup i alumne
    $("#butDropdivAlumnePrestec").html('Tria alumne ' + '<span class="caret">');
    $("#butDropdivAlumnePrestec").val('');
    $("#butDropdivAlumnePrestec").prop('disabled', true);
    $("#butDropdivGrupPrestec").html('Tria Grup ' + '<span class="caret">');

    $("#butDropdivGrupPrestec").val('');
    $("#butDropdivGrupPrestec").prop('disabled', true);
    $('#desaPrestec').prop('disabled', true);
    $("#butDropdivNivellPrestec").html('Tria Grup ' + '<span class="caret">');
    $("#butDropdivNivellPrestec").val('');

}

function mostraDetallPrestecs(element) {
    //passem el nom de l'equip
    debugger;
    var equipament = $($($($(element).parent()).parent()).siblings()[0]).text();
    var codiEquipament = $($($($(element).parent()).parent()).siblings()[0]).attr('data-codi');
    $("#nomEquipDetall").text(equipament);
    $("#esborraPrestec").prop('disabled', true);

    var url = "php/mostraDetallPrestecs.php";

    $.ajax({
        type: "POST",
        url: url,
        data: {"codiEquipament": codiEquipament},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#detallPrestecEquip").html(data);



        }

    });


}

function comprovaEsborra() {

    var totalCheck = $(".checkEsborrar");

    var conta = 0

    for (var i = 0; i < totalCheck.length; i++) {
        if ($(totalCheck[i]).prop('checked') === true) {
            conta++;
        }
    }

    if (conta > 0) {
        //habilitem esborrar
        $("#esborraPrestec").prop('disabled', false);
    } else {
        //desabilitem esborrar
        $("#esborraPrestec").prop('disabled', true);
    }
}

function esborraPrestec() {
    var totalCheck = $(".checkEsborrar");

    if ($("#radioTotal").prop('checked') === true) {
        //tots
        var filtre = '1';
    } else if ($("#radioDispo").prop('checked') === true) {
        //només disponibles
        var filtre = '2';

    } else {
        //nomes prestats
        var filtre = '3';
    }



    debugger;

    var prestecs = [];
    var conta = 0;
    for (var i = 0; i < totalCheck.length; i++) {
        if ($(totalCheck[i]).prop('checked') === true) {
            prestecs[conta] = $(totalCheck[i]).attr('data-codi-prestec');
            conta++;
        }
    }

    debugger;
    if (conta > 0) {
        //hi ha prestecs per esborrar;
        var confirmacio = confirm("Estàs a punt d'esborrar préstecs. Vols continuar?");

        if (confirmacio === true) {
            var url = "php/esborraPrestecs.php";

            $.ajax({
                type: "POST",
                url: url,
                data: {"prestecs": prestecs},
                //data: ("#form2").serialize,
                success: function (data) {
                    if (data == '1') {
                        alert("Posa't en contacte amb l'administrador per esborrar aquest registre");
                    }
                    carregaEquipament('0', filtre);
                    //$("#divTaulaEquipament").html(data);


                }

            });


            return false;
        }
    }

}

function cercaResumPrestecs() {
    //agafem les dades necessàries
    //data inici, fi i criteri agrupació

    var dataIniciResum = $("#dataInicialResum").val();
    var dataFiResum = $("#dataFinalResum").val();

    if ($("#checkEquip").prop('checked') === true) {
        var codiAgrupacio = '0';

    } else if ($("#checkAlumne").prop('checked') === true) {
        var codiAgrupacio = '1';
    } else {
        var codiAgrupacio = '2';
    }
    var url = "php/cercaResumPrestecs.php";
    debugger;
    $.ajax({
        type: "POST",
        url: url,
        data: {"dataIniciResum": dataIniciResum, "dataFiResum": dataFiResum, "codiAgrupacio": codiAgrupacio},

        success: function (data) {
            $("#detallAgrupacioPrestecs").html(data);


        }

    });

    return false;


}



function filtraPerDispo() {
    //mirem l'opció triada
    if ($("#radioTotal").prop('checked') === true) {
        //tots
        carregaEquipament('0', '1');
    } else if ($("#radioDispo").prop('checked') === true) {
        //només disponibles
        carregaEquipament('0', '2');


    } else {
        //nomes prestats
        carregaEquipament('0', '3');
    }




}

