/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function carregaDadesInicialsGrups() {

    carregaGrupsProfes();


}

function filtraTaula() {
    var criteriFiltre = $("#criteriFiltre").val();
    var checkMembres = $("#nomesMembres").prop('checked');


    if (criteriFiltre != '' && checkMembres == false) {
        //seleccionem segons el filtre i independent de si son membres
        var fileres = $("#costaulaAlumnesMeuGrup").children();
        for (var i = 0; i < fileres.length; i++) {
            var nomAlumne = $($(fileres[i]).children()[2]).text();
            if (nomAlumne.toLowerCase().indexOf(criteriFiltre.toLowerCase()) != -1) {

                $(fileres[i]).prop('hidden', false);

            } else {
                $(fileres[i]).prop('hidden', true);
            }

        }

    } else if (criteriFiltre == '' && checkMembres == false) {
        //no hi ha filtre ni ckeck de membres es selecciona tot
        var fileres = $("#costaulaAlumnesMeuGrup").children();
        for (var i = 0; i < fileres.length; i++) {
            $(fileres[i]).prop('hidden', false);

        }

    } else if (criteriFiltre == '' && checkMembres == true) {
        //seleccionem només els membres
        var fileres = $("#costaulaAlumnesMeuGrup").children();
        for (var i = 0; i < fileres.length; i++) {

            //anem a buscar el check
            var esMembre = $($($(fileres[i]).children()[0]).children()[0]).prop('checked');
            if (esMembre == true) {
                $(fileres[i]).prop('hidden', false);
            } else {
                $(fileres[i]).prop('hidden', true);
            }


        }

    } else {
        //hi ha criteri i només els membres
        var fileres = $("#costaulaAlumnesMeuGrup").children();
        for (var i = 0; i < fileres.length; i++) {
            var nomAlumne = $($(fileres[i]).children()[2]).text();
            var esMembre = $($($(fileres[i]).children()[0]).children()[0]).prop('checked');
            if (nomAlumne.toLowerCase().indexOf(criteriFiltre.toLowerCase()) != -1 && esMembre == true) {

                $(fileres[i]).prop('hidden', false);

            } else {
                $(fileres[i]).prop('hidden', true);
            }

        }

    }



}

function filtraMembres() {
    filtraTaula();

}

function carregaGrupsProfes() {
    var url = "php/carregaGrupsProfes.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divTaulaMeusGrups").html(data);
        }

    });

    //ommplim el dropdown de nivells
    carregaDropGeneric('nivellGrupProfe', 'SELECT distinct(ga35_nivell) as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga35_nivell=ga06_codi_nivell', 'Tria nivell');



    return false;


}

function mostranivellGrupProfe(element) {

    $("#butDropnivellGrupProfe").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropnivellGrupProfe").val($(element).attr('data-val'));
}

function tancaCollapseAltaGrup() {
    $("#butDropnivellGrupProfe").html("Tria Nivell" + ' ' + '<span class="caret">');
    $("#butDropnivellGrupProfe").val("");
    $("#nomGrupProfe").val("");
    $("#altaGrupProfe").collapse("hide");

}

function desaAltaGrup() {
    //agafem el nivell



    debugger;
    if (nivell == "") {

        alert("has d'intruduir el nivell");
    } else {
        //enviem dades dades al servidor
        var nivell = $("#butDropnivellGrupProfe").val();
        var nomGrup = $("#nomGrupProfe").val();
        var url = "php/desaAltaGrup.php";

        debugger;

        $.ajax({
            type: "POST",
            url: url,
            data: {"nivell": nivell, "nomGrup": nomGrup},
            //data: ("#form2").serialize,
            success: function (data) {
                //refresquem els grups
                //$("#altaGrupProfe").html(data);
                carregaGrupsProfes();
                //avis d'alta
                alert("Alta feta amb èxit");
                $("#butDropnivellGrupProfe").html("Tria Nivell" + ' ' + '<span class="caret">');
                $("#butDropnivellGrupProfe").val("");
                $("#nomGrupProfe").val("");
                $("#altaGrupProfe").collapse("hide");

            }

        });
        return false;

    }

}

//esborrem els grups d'un profe marcats
$(document).ready(function () {

    $("#esborraGrupProfe").click(function () {

        //obtenim els grups que cal esborrar
        //primer els fills
        var fileres = $("#cosTaulaMeusGrups").children();

        var grups = [];
        var j = 0;
        for (i = 0; i < fileres.length; i++) {
            var isselected = $($($($(fileres[i]).children()[0])).children()[0]).prop('checked');

            if (isselected == true) {
                var codigrup = $($($(fileres[i]).children()[1])).text();
                grups[j] = codigrup;
                j++;
            }
        }


        var url = "php/esborraGrupsProfe.php";

        if (j > 0) {
            var confirmacio = confirm("Estàs segur de voler esborrar els grups amb tots els seus membres")
            if (confirmacio == true) {
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {"grups": grups},
                    //data: ("#form2").serialize,
                    success: function (data) {
                        //refresquem els grups
                        var resposta = data;
                        if (resposta == 0) {
                            alert("<h1>No hi havia grups per esborrar</h1>");
                        } else if(resposta==1) {
                            alert("S'han esborrat tots els grups");
                        }else{
                            alert("Alguns grups no s'han esborrat perquè tenien horaris associats");
                        }
                        carregaGrupsProfes();
                        $("#divAlumnesMeuGrup").html("");
                    }

                });

                return false;
            }
        }
    });
});


//mostrem els alumnes d'un grup concret
function cercaAlumnesGrupProfe(element) {
    //farà falta el nivell i el codi del grup
    var filera = $($(element).parent()).parent();
    debugger;
    //el grup és el primer element de la filera
    var grup = $(filera.children()[1]).text();
    //el nivell és l'atribut del segon element de la filera
    var codinivell = $(filera.children()[2]).attr('data-nivell');

    var url = "php/cercaAlumnesGrupProfe.php";

    //ara ja podem enviar les dades al servidor per a fer la consulta

    $.ajax({
        type: "POST",
        url: url,
        data: {"grup": grup, "codinivell": codinivell},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divAlumnesMeuGrup").html(data);
            $("#taulaAlumnesMeuGrup").tableHeadFixer();
            $("#desaAlumnesGrupProfe").css("visibility", "visible");

        }

    });

    debugger;

    return false;

}

function desaAlumnesGrupProfe() {
    //s'han d'obtenir les fileres marcades

    var fileres = $("#costaulaAlumnesMeuGrup").children();
    var alumnes = [];
    var j = 0;

    for (var i = 0; i < fileres.length; i++) {
        //obtenime el codi d'alumne
        var codialumne = $($(fileres[i]).children()[2]).attr('data-codialumne');
        //obtenim l'estat de selecció

        var isselected = $($($($(fileres[i]).children()[0])).children()[0]).prop('checked');

        if (isselected == true) {
            alumnes[j] = codialumne;
            j++;
        }

    }

    debugger;
    //obtenim el grup a modificar
    var grupprofe = $("#costaulaAlumnesMeuGrup").attr('data-codigrup');

    //fem la comunicació amb el servidor
    var url = "php/desaAlumnesGrupProfe.php";

    //ara ja podem enviar les dades al servidor per a fer la consulta

    $.ajax({
        type: "POST",
        url: url,
        data: {"grupprofe": grupprofe, "alumnes": alumnes},
        //data: ("#form2").serialize,
        success: function (data) {
            alert("Grup modificat correctament");
            $("#divAlumnesMeuGrup").html(data);
        }

    });

    debugger;

    return false;



}
