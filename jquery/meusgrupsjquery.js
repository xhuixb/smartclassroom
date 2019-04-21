/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */




$(document).ready(function () {
    $('[data-tooltip="tooltip"]').tooltip();
});

function carregaDadesInicialsGrups() {
    carregaDropGeneric('nivellGrupGralDropdown', 'SELECT distinct(ga35_nivell) as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga35_nivell=ga06_codi_nivell', 'Tria Nivell');
    carregaGrupsProfes('0');
    carregaDropGenericAdmin("professorDropdownGrups", "select ga17_codi_professor as codi,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio from ga04_professors,ga17_professors_curs where ga04_codi_prof=ga17_codi_professor and ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) order by descripcio", "Tria Professor");

}

function mostranivellGrupGralDropdown(element) {
    debugger;
    $("#butDropnivellGrupGralDropdown").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropnivellGrupGralDropdown").val($(element).attr('data-val'));

    var nivell = $(element).attr('data-val');

    if (nivell != '') {
        cercaGrupsNivell('grupGralDropdown', nivell)

    }

    habilitaDesaGrupGral();


}


function mostragrupGralDropdown(element) {

    $("#butDropgrupGralDropdown").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropgrupGralDropdown").val($(element).attr('data-val'));

    habilitaDesaGrupGral();

}

function habilitaDesaGrupGral() {
    debugger;
    var nivell = $("#butDropnivellGrupGralDropdown").val();
    var grup = $("#butDropgrupGralDropdown").val();
    var nomGrup = $("#nomGrupGral").val();

    if (nivell !== '' && grup !== '' && nomGrup !== '') {
        $("#creaGrupGral").prop('disabled', false);
    } else {
        $("#creaGrupGral").prop('disabled', true);
    }

}

function mostraprofessorDropdownGrups(element) {
    $("#butDropprofessorDropdownGrups").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropprofessorDropdownGrups").val($(element).attr('data-val'));

    //esborrem els grups del professor i els memebres i mostrem el nous grups
    $("#divAlumnesMeuGrup").html('');
    $("tr.btn-info").removeClass('btn-info');
    //amaga desa
    $("#desaAlumnesGrupProfe").css('visibility', 'hidden');
    carregaGrupsProfes($(element).attr('data-val'));


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



function carregaGrupsProfes(profe) {
    var url = "php/carregaGrupsProfes.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"profe": profe},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divTaulaMeusGrups").html(data);
            $('[data-tooltip="tooltip"]').tooltip();

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

    var nivell = $("#butDropnivellGrupProfe").val();

    debugger;
    if (nivell === "") {

        alert("has d'intruduir el nivell");
    } else {
        //enviem dades dades al servidor
        var nivell = $("#butDropnivellGrupProfe").val();
        var nomGrup = $("#nomGrupProfe").val().replace(/"/g, "&quot;");

        if (nomGrup !== '') {
            var url = "php/desaAltaGrup.php";
            var profe = $("#butDropprofessorDropdownGrups").val();
            debugger;

            $.ajax({
                type: "POST",
                url: url,
                data: {"nivell": nivell, "nomGrup": nomGrup, "profe": profe},
                //data: ("#form2").serialize,
                success: function (data) {
                    //refresquem els grups
                    //$("#altaGrupProfe").html(data);
                    debugger;

                    carregaGrupsProfes($("#butDropprofessorDropdownGrups").val());
                    //avis d'alta
                    alert("Alta feta amb èxit");
                    $("#butDropnivellGrupProfe").html("Tria Nivell" + ' ' + '<span class="caret">');
                    $("#butDropnivellGrupProfe").val("");
                    $("#nomGrupProfe").val("");
                    $("#altaGrupProfe").collapse("hide");
                    $("#divAlumnesMeuGrup").html('');
                    $("#desaAlumnesGrupProfe").css('visibility', 'hidden');
                }

            });
            return false;
        } else {
            alert("El nom del grup no pot ser buit");
        }
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
                        } else if (resposta == 1) {
                            alert("S'han esborrat tots els grups");
                        } else {
                            alert("Alguns grups no s'han esborrat perquè tenien horaris associats");
                        }
                        carregaGrupsProfes($("#butDropprofessorDropdownGrups").val());
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

    //desactivem la filera activa
    $("tr.btn-info").removeClass('btn-info');

    //activem la filera
    var filera = $($(element).parent()).parent();

    $(filera).addClass('btn-info');



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

        if (isselected === true) {
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
            $("tr.btn-info").removeClass('btn-info');
            //amaga desa
            $("#desaAlumnesGrupProfe").css('visibility', 'hidden');
            //style="visibility: hidden;"

        }

    });

    debugger;

    return false;



}

function editaNomGrup(element) {
    //tregguem read only

    if ($($(element).siblings()[0]).prop('readonly') === true) {

        $($(element).siblings()[0]).prop('readonly', false);
        $($(element).siblings()[0]).focus();
    } else {
        debugger;
        $($(element).siblings()[0]).prop('readonly', true);
        var nouNom = $($(element).siblings()[0]).val().replace(/"/g, "&quot;");
        ;
        var codiGrup = $($($($(element).parent()).parent()).siblings()[1]).text();

        $(element).prop('readonly', true);

        //fem la comunicació amb el servidor
        var url = "php/modiNomGrup.php";

        //ara ja podem enviar les dades al servidor per a fer la consulta

        $.ajax({
            type: "POST",
            url: url,
            data: {"nouNom": nouNom, "codiGrup": codiGrup},
            //data: ("#form2").serialize,
            success: function (data) {
                alert("S'ha modificat el nom del grup");
            }

        });



        return false;

    }


}

function modiNomGrup(element) {
    debugger;
    var nouNom = $(element).val().replace(/"/g, "&quot;");
    var codiGrup = $($($($(element).parent()).parent()).siblings()[1]).text();

    $(element).prop('readonly', true);

    //fem la comunicació amb el servidor
    var url = "php/modiNomGrup.php";

    //ara ja podem enviar les dades al servidor per a fer la consulta

    $.ajax({
        type: "POST",
        url: url,
        data: {"nouNom": nouNom, "codiGrup": codiGrup},
        //data: ("#form2").serialize,
        success: function (data) {
            alert("S'ha modificat el nom del grup");
        }

    });

    debugger;

    return false;


}

function carregaDropGenericAdmin(div, query, caption) {
    var url = "php/carregaDropGenericAdmin.php";
    debugger;
    $.ajax({
        type: "POST",
        url: url,
        data: {"div": div, "query": query, "caption": caption},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#" + div).html(data);

        }

    });


    return false;

}

function obreModalCopiaGrupGral() {

    //posem el codi del profe i el profe a la capçalera del modal
    $("#profeNouGrupGral").text('Professor destí: ' + $("#butDropprofessorDropdownGrups").text());
    $("#profeNouGrupGral").attr('data-profe', $("#butDropprofessorDropdownGrups").val());

    //netegem les dades que hi poguessin haver
    $("#butDropnivellGrupGralDropdown").html('Tria nivell' + ' ' + '<span class="caret">');
    $("#butDropnivellGrupGralDropdown").val('');

    $("#butDropgrupGralDropdown").prop('disabled', true);
    $("#butDropgrupGralDropdown").html('Tria grup' + ' ' + '<span class="caret">');
    $("#butDropgrupGralDropdown").val('');

    $("#nomGrupGral").val('');
    $("#creaGrupGral").prop('disabled', true);

}



function creaGrupGral() {


    var confirmacio = confirm("Estàs a punt de crear un grup personal a partir d'un grup classe");

    if (confirmacio === true) {
        //agafem la informació que els caldra per crear el grup personal a partir del general
        var nivell = $("#butDropnivellGrupGralDropdown").val();
        var grup = $("#butDropgrupGralDropdown").val();
        var profe = $("#profeNouGrupGral").attr('data-profe');
        var nomGrup = $("#nomGrupGral").val().replace(/"/g, "&quot;");

        var url = "php/creaGrupGral.php";
        debugger;
        $.ajax({
            type: "POST",
            url: url,
            data: {"nivell": nivell, "grup": grup, "profe": profe, "nomGrup": nomGrup},
            //data: ("#form2").serialize,
            success: function (data) {
                //alert(data);
                alert("S'ha creat un nou grup personal");
                carregaGrupsProfes($("#butDropprofessorDropdownGrups").val());
                $("#divAlumnesMeuGrup").html('');
                $("#desaAlumnesGrupProfe").css('visibility', 'hidden');
            }

        });


        return false;


    }

}

function duplicaGrupProfe(element) {
    //s'obre el moddal del grup personal duplicat
    //posem el professor del grup que es vol duplicar
    $("#profeDupGrupPer").text('Professor destí: ' + $("#butDropprofessorDropdownGrups").text());
    $("#profeDupGrupPer").attr('data-profe', $("#butDropprofessorDropdownGrups").val());
    //posem el grup que es vol duplicar
    $("#origenDupGrupPer").text('Grup Origen: ' + $($($(element).parent()).siblings()[1]).text() + '-' + $($($($($(element).parent()).siblings()[3]).children()[0]).children()[0]).val());
    $("#origenDupGrupPer").attr('data-codi-grup', $($($(element).parent()).siblings()[1]).text());
    //netegem el grup que hi pogués haver
    $("#nomGrupPersonal").val('');
    $("#creaGrupPer").prop('disabled', true);


}

function creaGrupPer() {
    //es crea el grup personal duplicat
    var confirmacio = confirm("Estàs a punt de duplicar un grup personal");

    if (confirmacio === true) {
        //agafem la informació que els caldra per crear el grup personal a partir del general     
        var profe = $("#profeDupGrupPer").attr('data-profe');
        var nomGrup = $("#nomGrupPersonal").val().replace(/"/g, "&quot;");
        var grupOrigen = $("#origenDupGrupPer").attr('data-codi-grup');

        var url = "php/creaGrupPer.php";
        debugger;
        $.ajax({
            type: "POST",
            url: url,
            data: {"profe": profe, "nomGrup": nomGrup, "grupOrigen": grupOrigen},
            //data: ("#form2").serialize,
            success: function (data) {
                //alert(data);
                alert("S'ha creat un nou grup personal");
                carregaGrupsProfes($("#butDropprofessorDropdownGrups").val());
                $("#divAlumnesMeuGrup").html('');
                $("#desaAlumnesGrupProfe").css('visibility', 'hidden');
            }

        });


        return false;


    }


}

function habilitaDesaGrupPer() {
    if ($("#nomGrupPersonal").val() !== '') {
        $("#creaGrupPer").prop('disabled', false);
    } else {
        $("#creaGrupPer").prop('disabled', true);
    }
}