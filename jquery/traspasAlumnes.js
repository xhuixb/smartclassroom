/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function carregaDadesTraspasAlumnes() {

    carregaDropGeneric('nivellDropOrigen', "select distinct(ga06_codi_nivell) as codi,(select ga06_descripcio_nivell from ga06_nivell where ga06_codi_nivell=codi) as descripcio from ga06_nivell,ga35_curs_nivell_grup where ga06_codi_nivell=ga35_nivell and ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual='1')", 'Tria nivell');
    carregaDropGeneric('cursDropDesti', "select ga03_codi_curs as codi,ga03_descripcio as descripcio from ga03_curs", 'Tria curs');
    //anem a carregar el curs origen

    var url = "php/carregaCursOrigen.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {},
        //data: ("#form2").serialize,
        success: function (data) {
            //rebem les dades
            $("#cursOrigenDiv").html(data);

        }

    });
    return false;


}

function mostranivellDropOrigen(element) {

    $("#butDropnivellDropOrigen").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropnivellDropOrigen").val($(element).attr('data-val'));
    comprovaDades();
    netejaDades();


}

function netejaDades() {
    $("#divTaulaAlumnesOrigen").html('');
    $("#traspassaAlumnes").prop('disabled', true);

}

function comprovaDades() {
    if ($("#butDropnivellDropOrigen").val() !== '' && $("#butDropnivellDropDesti").length > 0 && $("#butDropnivellDropDesti").val() !== '') {
        $("#cercaAlumnesOrigen").prop('disabled', false);
        //$("#traspassaAlumnes").prop('disabled', false);
    } else {
        $("#cercaAlumnesOrigen").prop('disabled', true);
        //$("#traspassaAlumnes").prop('disabled', true);
    }
}

function mostracursDropDesti(element) {
    var cursOrigen = $("#cursOrigen").attr('data-codi');

    //comprovem que és un curs diferent de l'origen
    if (cursOrigen !== $(element).attr('data-val')) {
        $("#butDropcursDropDesti").html($(element).text() + ' ' + '<span class="caret">');
        $("#butDropcursDropDesti").val($(element).attr('data-val'));
        var cursDesti = $("#butDropcursDropDesti").val();
        //carreguem els nivells corresponents al curs desti
        carregaDropGeneric('nivellDropDesti', "select distinct(ga06_codi_nivell) as codi,(select ga06_descripcio_nivell from ga06_nivell where ga06_codi_nivell=codi) as descripcio from ga06_nivell,ga35_curs_nivell_grup where ga06_codi_nivell=ga35_nivell and ga35_codi_curs=" + cursDesti, 'Tria nivell');
        netejaDades();

    } else {
        alert("Ull!! El destí no pot ser el mateix que l'origen");
    }
}

function mostranivellDropDesti(element) {

    $("#butDropnivellDropDesti").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropnivellDropDesti").val($(element).attr('data-val'));
    comprovaDades();
    netejaDades();
    $("#visualitzaAlumnesDesti").prop('disabled', false);
}



function cercaAlumnesOrigen() {
    //agafem les dades
    var cursOrigen = $("#cursOrigen").attr('data-codi');
    var nivellOrigen = $("#butDropnivellDropOrigen").val();
    var nivellDesti = $("#butDropnivellDropDesti").val();
    var cursDesti = $("#butDropcursDropDesti").val();


    var url = "php/cercaAlumnesOrigen.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"cursOrigen": cursOrigen, "nivellOrigen": nivellOrigen, "cursDesti": cursDesti, "nivellDesti": nivellDesti},
        //data: ("#form2").serialize,
        success: function (data) {
            //rebem les dades
            $("#divTaulaAlumnesOrigen").html(data);
            $("#traspassaAlumnes").prop('disabled', false);
            //comptem les fileres
            $("#numAlumnesCerca").text('Alumnes que es poden traspassar: ' + $("#taulaBodyAlumnesOrigen").children().length);
            $("#numAlumnesTraspas").text("Alumnes seleccionats: 0");
        }

    });
    return false;


}

function traspassaAlumnes() {
    //agafem les dades a traspassar
    var cursDesti = $("#butDropcursDropDesti").val();
    var nivellDesti = $("#butDropnivellDropDesti").val();

    var alumnesTraspas = [];

    var checkTraspas = $(".radioTraspas");
    var j = 0;
    for (var i = 0; i < checkTraspas.length; i++) {
        if ($(checkTraspas[i]).prop('checked') === true) {
            //s'ha de traspassar
            var alumneT = $($($($(checkTraspas[i]).parent()).parent()).children()[0]).attr('data-alumne');

            var columna = $($($(checkTraspas[i]).parent())).index();

            var grupT = $($($("#taulaCapAlumnesOrigen").children()[0]).children()[columna]).attr('data-codi-grup');

            alumnesTraspas[j] = {alumne: alumneT, grup: grupT};
            j++;

        }
    }

    debugger;
    if (alumnesTraspas.length > 0 || $("#esborraExistents").prop('checked') === true) {
        //no enviem al servidor
        var alumnesTraspasJSON = JSON.stringify(alumnesTraspas);
        var url = "php/traspassaAlumnes.php";
        if ($("#esborraExistents").prop('checked') === true) {
            var esborraExistents = '1';
        } else {
            var esborraExistents = '0';
        }


        $.ajax({
            type: "POST",
            url: url,
            data: {"cursDesti": cursDesti, "nivellDesti": nivellDesti, "alumnesTraspas": alumnesTraspasJSON, "esborraExistents": esborraExistents},
            //data: ("#form2").serialize,
            success: function (data) {
                //rebem les dades
                //$("#divTaulaAlumnesOrigen").html(data);
                alert("Trasàs fet correctament!");
                location.reload(true);

            }

        });
        return false;

    } else {
        alert('no hi ha dades a traspassar');
    }

}

function comptaAlumnes() {
    debugger;
    var elements = $(".radioTraspas");
    var conta = 0;
    for (var i = 0; i < elements.length; i++) {
        if ($(elements[i]).prop('checked') === true) {
            conta++;
        }
    }

    $("#numAlumnesTraspas").text("Alumnes seleccionats: " + conta);

}

function visualitzaAlumnesDesti() {
    var cursDesti = $("#butDropcursDropDesti").val();
    var nivellDesti = $("#butDropnivellDropDesti").val();

    $("#cursTraspassat").text($("#butDropcursDropDesti").text());
    $("#cursTraspassat").attr('data-curs', cursDesti);
    $("#nivellTraspassat").text($("#butDropnivellDropDesti").text());
    $("#nivellTraspassat").attr('data-nivell', nivellDesti);

    carregaDropGeneric('grupsTraspassatsDrop', 'select distinct(ga07_codi_grup) as codi,ga07_descripcio_grup as descripcio from ga07_grup,ga12_alumnes_curs where ga12_codi_grup=ga07_codi_grup and ga12_codi_curs=' + cursDesti + ' and ga12_codi_nivell=' + nivellDesti, 'Tria grup');
    $("#esborraAlumnesDesti").prop('disabled', true);
    $("#taulaAlumnesTraspassats").html('');
}

function esborraAlumnesDesti() {
    //recollim les dades necessàries
    //només cal el curs i els alumnes
    var curs = $("#cursTraspassat").attr('data-curs');

    var marcats = $(".checkEsborrar");

    var alumnes = [];
    var j = 0;

    for (var i = 0; i < marcats.length; i++) {
        if ($(marcats[i]).prop('checked') === true) {
            alumnes[j] = $($($(marcats[i]).parent()).siblings()[0]).attr('data-codi');
            j++;
        }
    }

    if (j > 0) {

        var url = "php/esborraAlumnesTraspassats.php";
        $.ajax({
            type: "POST",
            url: url,
            data: {"curs": curs, "alumnes": alumnes},
            //data: ("#form2").serialize,
            success: function (data) {
                //rebem les dades
                if (data === '0') {
                    //esborrem els elements
                    for (var i = 0; i < marcats.length; i++) {
                        if ($(marcats[i]).prop('checked') === true) {
                            //esborrem la filera
                            $($($(marcats[i]).parent()).parent()).remove();
                            
                        }
                    }

                }else{
                    alert("no s'han pogut esborrar els alumnes degut a referències existents");
                }

            }

        });
        return false;
    } else {
        alert("no hi ha alumnes a traspassar");

    }
}


function mostragrupsTraspassatsDrop(element) {
    $("#butDropgrupsTraspassatsDrop").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropgrupsTraspassatsDrop").val($(element).attr('data-val'));

    //mostrem els alumnes traspassats

    var cursDesti = $("#cursTraspassat").attr('data-curs');
    var nivellDesti = $("#nivellTraspassat").attr('data-nivell');
    var grupDesti = $("#butDropgrupsTraspassatsDrop").val();

    var url = "php/cercaAlumnesTraspassats.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"cursDesti": cursDesti, "nivellDesti": nivellDesti, "grupDesti": grupDesti},
        //data: ("#form2").serialize,
        success: function (data) {
            //rebem les dades
            $("#taulaAlumnesTraspassats").html(data);
            $("#esborraAlumnesDesti").prop('disabled', false);

        }

    });
    return false;



}