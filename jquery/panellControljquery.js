/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//inicialitzem els tooltips
$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

function activaDatePicker() {
    $(document).ready(function () {
        $('.datePanellControl').datepicker({
            uiLibrary: 'bootstrap',
            dateFormat: 'dd/mm/yy'
        });
    });
}


$(document).ready(function () {
    $('#dataCanviEstat').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });


});

$(document).ready(function () {
    $('.dates-clau').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });


});


$(document).ready(function () {
    $('#dataIniciActivitat').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });


});


$(document).ready(function () {
    $("#menuTree").jstree({
    });
});



$(document).ready(function () {
    $('#summernoteDatesClau').summernote({

        height: 400,
        // height: 300, // set editor height

        //  minHeight: null, // set minimum height of editor
        //   maxHeight: null, // set maximum height of editor
        focus: true                  // set focus to editable area after initializing summernote
    });


});





function cercaFestius() {
    //anem a buscar els festius
    var url = "php/cercaFestius.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            //rebem les dades
            $("#divTaulaFestius").html(data);

        }

    });
    return false;

}

function comprovaFestiu() {
    debugger;

    if ($("#nouFestiu").val() != '') {
        $('#altaFestiu').prop('disabled', false);
    } else {
        $('#altaFestiu').prop('disabled', true);
    }
}

function altaFestiu() {

    //agafem les dates
    var datesFestives = $('.datesfestives');

    var datesFestivesArray = [];
    var festiuRepe = false;

    for (var i = 0; i < datesFestives.length; i++) {
        datesFestivesArray[i] = $(datesFestives[i]).text();

        if ($(datesFestives[i]).text() === $('#nouFestiu').val().substring(6) + '-' + $('#nouFestiu').val().substring(3, 5) + '-' + $('#nouFestiu').val().substring(0, 2)) {
            //el festiu ja existeix
            festiuRepe = true;
        }
    }

    if (festiuRepe === false) {
        //afegim la nova data


        datesFestivesArray[datesFestivesArray.length] = $('#nouFestiu').val().substring(6) + '-' + $('#nouFestiu').val().substring(3, 5) + '-' + $('#nouFestiu').val().substring(0, 2);
        debugger;
        //ordennem les dates
        datesFestivesArray.sort();

        //les covertim en un string
        var datesFestivesString = datesFestivesArray.join('<#>');

        //ho enviem al servidor
        var url = "php/altaFestius.php";
        $.ajax({
            type: "POST",
            url: url,
            data: {"datesFestivesString": datesFestivesString},
            success: function (data) {
                //esborrem camp
                $("#nouFestiu").val('');
                //refrequem les dates                    
                cercaFestius();

            }

        });
        return false;
    } else {

        alert('Aquesta data ja existeix');
    }


}

function esborraFestius() {
    //agafem les dates
    var datesFestives = $('.datesfestives');

    var datesFestivesArray = [];

    //només agafem els desmarcats
    var j = 0;
    for (var i = 0; i < datesFestives.length; i++) {

        if ($($($(datesFestives[i]).siblings()[0]).children()[0]).prop('checked') == false) {
            datesFestivesArray[j] = $(datesFestives[i]).text();
            j++;
        }
    }

    //ordennem les dates
    datesFestivesArray.sort();

    //les covertim en un string
    var datesFestivesString = datesFestivesArray.join('<#>');

    //ho enviem al servidor
    var url = "php/altaFestius.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"datesFestivesString": datesFestivesString},
        success: function (data) {
            //refrequem les dates           
            cercaFestius();

        }

    });
    return false;


}

function comprovaProfe() {

    debugger;
    $("#avisResposta").html('');

    //anem a veure si els horaris es poden traspassar
    if ($("#checkHoraris").prop('checked') === true && $("#butDropprofessorDropdownTraspas").val() != '') {
        var codiProfDesti = $("#butDropprofessorDropdownTraspas").val();

        //enviem per ajax
        var url = "php/comprovaHorariProfessor.php";
        $.ajax({
            type: "POST",
            url: url,
            data: {"codiProfDesti": codiProfDesti},
            success: function (data) {

                var resposta = data;

                if (data == '1') {
                    //no hi ha horaris
                    var respostaText = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Informació!</strong> El professor destí no té horaris i, per tant, es pot procedir al traspàs.</div>';
                    $("#avisResposta").html(respostaText);
                    $("#traspassaDadesDocent").prop('disabled', false);
                } else {
                    var respostaText = '<div class="alert alert-info alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Informació!</strong> El professor destí té horaris. Cal esborrar-los abans de fer el traspàs.</div>';
                    $("#avisResposta").html(respostaText);
                    $("#traspassaDadesDocent").prop('disabled', true);
                }

            }

        });
        return false;

    } else if ($("#butDropprofessorDropdownTraspas").val() != '') {
        //no s'han de traspassar horaris per tant es pot habilitar desa
        $("#traspassaDadesDocent").prop('disabled', false);
    }

}


function mostraprofessorDropdownTraspas(element) {

    $("#butDropprofessorDropdownTraspas").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropprofessorDropdownTraspas").val($(element).attr('data-val'));

    comprovaProfe();

}

function mostraProfessors() {
    //abans carraguem els professors del combo


    carregaDropGeneric("professorDropdownTraspas", "select ga17_codi_professor as codi,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio from ga04_professors,ga17_professors_curs where ga04_codi_prof=ga17_codi_professor and ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) order by descripcio", "Tria Professor");


    debugger;
    //mostrem tots els professors del curs actual
    var url = "php/mostraProfessors.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            //mostrem els professor
            $("#divTaulaProfes").html(data);

        }

    });
    return false;


}

function gestioProfessor(mode, element) {
    debugger;
    if (mode == '0') {
        //desabilitem desa per si de cas
        $("#desaDocent").prop('disabled', true);

        //netegem la informació que hi pogués haver
        $("#nomDocent").val('');
        $("#cognom1Docent").val('');
        $("#cognom2Docent").val('');
        $("#mailDocent").val('');
        $("#usuariDocent").val('');
        $("#contraDocent").val('');
        $("#nomDocent").addClass('alert-danger');
        $("#cognom1Docent").addClass('alert-danger');
        $("#usuariDocent").addClass('alert-danger');
        //fem visible el camp de password
        $("#contraDocent").prop('disabled', false);
        $("#titolPass").prop('disabled', false);
        $("#contraDocent").text('');
        //posem la dada d'inici d'activitat per defecta com a data del dia
        $("#dataIniciActivitat").prop('disabled', false);
        $("#dataIniciActivitat").datepicker("setDate", new Date());
        //model alta
        //posem el mode com atribut de la capçalera
        $("#titolCapcalera").attr('data-mode', mode);
        $("#titolCapcalera").text('Alta de professor');

    } else {
        //model edició
        //habilitem desa per si de cas
        $("#desaDocent").prop('disabled', false);
        $("#dataIniciActivitat").prop('disabled', true);
        //posem el mode com atribut de la capçalera
        $("#titolCapcalera").attr('data-mode', mode);
        $("#titolCapcalera").text('Edició de professor');

        //fem invisible el camp de password
        $("#contraDocent").prop('disabled', true);
        $("#titolPass").prop('disabled', true);
        $("#contraDocent").val('****');



        //passem les dades al modal
        var nom = $($($($(element).parent()).parent()).children()[0]).attr('data-nom');
        var cognom1 = $($($($(element).parent()).parent()).children()[0]).attr('data-cognom1');
        var codiprof = $($($($(element).parent()).parent()).children()[0]).attr('data-codiprof');
        var cognom2 = $($($($(element).parent()).parent()).children()[0]).attr('data-cognom2');
        var mail = $($($($(element).parent()).parent()).children()[1]).text();
        var login = $($($($(element).parent()).parent()).children()[2]).text();


        $("#nomDocent").val(nom);
        $("#cognom1Docent").val(cognom1);
        $("#cognom1Docent").attr('data-codiprof', codiprof);
        $("#cognom2Docent").val(cognom2);
        $("#mailDocent").val(mail);
        $("#usuariDocent").val(login);
        $("#nomDocent").removeClass('alert-danger');
        $("#cognom1Docent").removeClass('alert-danger');
        $("#usuariDocent").removeClass('alert-danger');
    }


}


function usuariBuit() {
    if ($("#nomDocent").val() != "" && $("#cognom1Docent").val() != "" && $("#usuariDocent").val() != '' && $("#contraDocent").val() != '') {
        //s'habilitat desar
        $("#desaDocent").prop('disabled', false);

    } else {
        //es desabilitat desar
        $("#desaDocent").prop('disabled', true);

    }

    //marquem com a requerit?
    if ($("#nomDocent").val() != '') {
        $("#nomDocent").removeClass('alert-danger');
    } else {
        $("#nomDocent").addClass('alert-danger');
    }

    if ($("#cognom1Docent").val() != '') {
        $("#cognom1Docent").removeClass('alert-danger');
    } else {
        $("#cognom1Docent").addClass('alert-danger');
    }

    if ($("#usuariDocent").val() != '') {
        $("#usuariDocent").removeClass('alert-danger');
    } else {
        $("#usuariDocent").addClass('alert-danger');
    }

    if ($("#contraDocent").val() != '') {
        $("#contraDocent").removeClass('alert-danger');
    } else {
        $("#contraDocent").addClass('alert-danger');
    }
}

function desaProfessor() {
    //agafem el mode per saber si es tracta d'una alta o una modificació
    var mode = $("#titolCapcalera").attr('data-mode');
    //agafem la resta de dades del professor
    var nom = $("#nomDocent").val();
    var cognom1 = $("#cognom1Docent").val();
    var cognom2 = $("#cognom2Docent").val();
    var mail = $("#mailDocent").val();
    var login = $("#usuariDocent").val();
    var password = $("#contraDocent").val();
    var codiProf = $("#cognom1Docent").attr('data-codiprof');
    var dataIniciActivitat = $("#dataIniciActivitat").val().toString();

    if (mode === '0') {
        //alta
        dataIniciActivitat = dataIniciActivitat.substr(6) + '-' + dataIniciActivitat.substr(3, 2) + '-' + dataIniciActivitat.substr(0, 2);
    }

    debugger;
    //ho enviem al script per fer l'accés a la base de dades
    var url = "php/gestionaProfessor.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"nom": nom, "cognom1": cognom1, "codiProf": codiProf, "cognom2": cognom2, "mail": mail, "login": login, "password": password, "mode": mode, "dataIniciActivitat": dataIniciActivitat},
        success: function (data) {
            //refresquem les dades
            //$("#divTaulaProfes").html(data);
            if (data === '1') {
                //login repetit
                alert("L'usuari està repetit");

            } else {
                mostraProfessors();
            }

        }

    });
    return false;


}

function traspassaProfessor(element) {
    //passem el professor
    var codiProf = $($($($(element).parent()).parent()).children()[0]).attr('data-codiprof');
    var nomProf = $($($($(element).parent()).parent()).children()[0]).text();
    $("#capcaleraProfessor").attr('data-codiprof', codiProf);
    $("#professorOrigen").html('<strong>Professor origen: </strong>' + nomProf);
    //esborrem l'avís de resposta
    $("#avisResposta").html('');
    //esborrem el profe
    $("#butDropprofessorDropdownTraspas").html('Tria Professor' + ' ' + '<span class="caret">');
    $("#butDropprofessorDropdownTraspas").val('');
    //desabilitem desa
    $("#traspassaDadesDocent").prop('disabled', true);
    //activem el check d'horaris
    $("#checkHoraris").prop('checked', true);


}

function desaTraspasProfessor() {
    //agafem el codi del professor origen
    var codiProfOrigen = $("#capcaleraProfessor").attr('data-codiprof');
    //agafem el professor destí
    var codiProfDesti = $("#butDropprofessorDropdownTraspas").val();

    if ($("#checkHoraris").prop('checked') === true) {
        var switchHoraris = true;

    } else {
        var switchHoraris = false;
    }

    //ho enviem al servidor
    var url = "php/desaTraspasProfessor.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"codiProfOrigen": codiProfOrigen, "codiProfDesti": codiProfDesti, "switchHoraris": switchHoraris},
        success: function (data) {
            // $("#divTaulaProfes").html(data);
            var resposta = data;
            if (resposta == 0) {
                alert("S'han traspassat correctament els grups");

            } else {
                alert("S'han traspassat correctament els grups i els horaris");
            }

            //var respostaText = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Success!</strong> Indicates a successful or positive action.</div>';
        }

    });
    return false;


}

function mostraPermisos() {

    var url = "php/mostraPermisos.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            $("#divPermisosTree").html(data);
        }

    });
    return false;

}


function editaPermisos(element) {
    //  alert('ximplet');
    var nomHtml = $($($(element).parent()).parent()).attr('data-referencia');
    var textMenu = $($($(element).parent()).parent()).text();
    var nivellMenu = $($($(element).parent()).parent()).attr('data-nivellmenu');

    //ho posem al modal form
    $("#capcaleraPermisos").text(textMenu);
    $("#capcaleraPermisos").attr('data-nomhtml', nomHtml);
    $("#capcaleraPermisos").attr('data-nivellmenu', nivellMenu);

    //anem a buscar els perfils d'aquest menu
    //primerament els perfils possibles
    var url = "php/mostraPerfils.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"nomHtml": nomHtml},
        success: function (data) {
            $("#perfilsPossibles").html(data);
        }

    });

    //ara els perfils habilitats

    var url = "php/mostraPerfilsHabilitats.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"nomHtml": nomHtml},
        success: function (data) {
            $("#pefilsHabilitats").html(data);
        }

    });

    return false;

}

function afegeixPerfils() {
    //agafem els elements seleccionats
    var perfilsPossibles = $(".permisosTraspas");
    var perfilsNous = [];
    var perfilsNousFilera = [];
    var j = 0;

    for (var i = 0; i < perfilsPossibles.length; i++) {
        if ($(perfilsPossibles[i]).prop('checked') === true) {
            $(perfilsPossibles[i]).prop('checked', false);
            //és un perfil que cal afegir
            var perfilNouFilera = $($(perfilsPossibles[i]).parent()).parent();
            perfilsNous[j] = $($(perfilNouFilera).children()[1]).attr('data-codiperfil');
            //afegim la filera dreta            

            //esborrem la filera esquerra
            $(perfilNouFilera).remove();
            //afegim a la dreta
            perfilsNousFilera[j] = perfilNouFilera;
            $($($(perfilsNousFilera[j]).children()[0]).children()[0]).removeClass('permisosTraspas');
            $($($(perfilsNousFilera[j]).children()[0]).children()[0]).addClass('permisosEnrere');

            j++;
        }
    }

    var contPerfilsNous = perfilsNous.length;

    //obtenime els perfils ja habilitats
    var perfilsHabilitats = $("#costaulaPerfilsHabilitats").children();

    for (var i = 0; i < perfilsHabilitats.length; i++) {
        perfilsNous[contPerfilsNous] = $($(perfilsHabilitats[i]).children()[1]).attr('data-codiperfil');
        contPerfilsNous++;
    }

    //afegime els perfils nous a la dreta
    for (var i = 0; i < perfilsNousFilera.length; i++) {
        $("#costaulaPerfilsHabilitats").append(perfilsNousFilera[i]);
    }


    debugger;
    //ordenem els perfils
    perfilsNous = perfilsNous.sort();
    //els convertim en string
    var perfilsNousString = perfilsNous.join('-');
    //modifiquem l'atribut del document xml



    var nomHtml = $("#capcaleraPermisos").attr('data-nomhtml');
    var nivellMenu = $("#capcaleraPermisos").attr('data-nivellmenu');


    var url = "php/modificaPerfils.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"perfilsNousString": perfilsNousString, "nomHtml": nomHtml, "nivellMenu": nivellMenu},
        success: function (data) {
            //$("#pefilsHabilitats").html(data);
        }

    });

    return false;


}

function treuPerfils() {

    var perfilsHabilitats = $(".permisosEnrere");
    var perfilsNous = [];
    var perfilsNousFilera = [];
    var j = 0;
    var k = 0;
    debugger;
    for (var i = 0; i < perfilsHabilitats.length; i++) {
        if ($(perfilsHabilitats[i]).prop('checked') === true) {
            $(perfilsHabilitats[i]).prop('checked', false);
            //és un perfil que cal treure
            var perfilNouFilera = $($(perfilsHabilitats[i]).parent()).parent();

            //esborrem la filera dreta
            $(perfilNouFilera).remove();
            //afegim filera esquerra
            perfilsNousFilera[k] = perfilNouFilera;
            $($($(perfilsNousFilera[k]).children()[0]).children()[0]).removeClass('permisosEnrere');
            $($($(perfilsNousFilera[k]).children()[0]).children()[0]).addClass('permisosTraspas');

            k++;


        } else {
            var perfilNouFilera = $($(perfilsHabilitats[i]).parent()).parent();
            perfilsNous[j] = $($(perfilNouFilera).children()[1]).attr('data-codiperfil');
            j++;
        }
    }

    //afegime els perfils nous a l'esquerra
    for (var i = 0; i < perfilsNousFilera.length; i++) {
        $("#costaulaPerfilsPossibles").append(perfilsNousFilera[i]);
    }



    //ordenem els perfils
    perfilsNous = perfilsNous.sort();
    //els convertim en string
    var perfilsNousString = perfilsNous.join('-');
    //modifiquem l'atribut del document xml


    var nomHtml = $("#capcaleraPermisos").attr('data-nomhtml');
    var nivellMenu = $("#capcaleraPermisos").attr('data-nivellmenu');


    var url = "php/modificaPerfils.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"perfilsNousString": perfilsNousString, "nomHtml": nomHtml, "nivellMenu": nivellMenu},
        success: function (data) {
            //$("#pefilsHabilitats").html(data);
        }

    });

    return false;

}

function esborraProfessor(element) {

    //demanem confirmació
    var resposta = confirm("N'estàs segur d'esborrar el professor?");

    if (resposta === true) {
        //agafem el codi del professor

        var codiProf = $($($(element).parent()).siblings()[0]).attr('data-codiprof');

        var url = "php/esborraProfessor.php";
        $.ajax({
            type: "POST",
            url: url,
            data: {"codiProf": codiProf},
            success: function (data) {
                if (data === '0') {
                    //s'ha pogut esborrar
                    $($($(element).parent()).parent()).remove();
                } else {
                    //no s'ha pogut esborrar
                    alert("El professor té referències. No es pot esborrar");
                }
            }

        });

        return false;
    }

}

function restauraContrasenya(element) {

    var nouPassword = prompt("Introdueix la nova contrasenya:", $($($(element).parent()).siblings()[0]).attr('data-password'));
    var codiProf = $($($(element).parent()).siblings()[0]).attr('data-codiprof');

    if (nouPassword != null && nouPassword != "") {
        //s'ha premut ok
        //es modifica la contrasenya
        var url = "php/restauraContrasenya.php";
        $.ajax({
            type: "POST",
            url: url,
            data: {"codiProf": codiProf, "nouPassword": nouPassword},
            success: function (data) {
                //actualitzem l'atribut
                $($($(element).parent()).siblings()[0]).attr('data-password', nouPassword);
            }

        });

        return false;



    }

}

function filtraDocent() {
    var criteriFiltre = $("#criteriFiltre").val();

    if (criteriFiltre !== '') {
        //cal filtrar
        var fileres = $("#costaulaDocents").children();

        for (var i = 0; i < fileres.length; i++) {
            var nomProf = $($(fileres[i]).children()[0]).text();

            if (nomProf.toLowerCase().indexOf(criteriFiltre.toLowerCase()) != -1) {

                $(fileres[i]).prop('hidden', false);

            } else {
                $(fileres[i]).prop('hidden', true);
            }

        }


    } else {
        //cal mostrar-les totes
        var fileres = $("#costaulaDocents").children();

        for (var i = 0; i < fileres.length; i++) {
            $(fileres[i]).prop('hidden', false);
        }

    }


}

function situacioProfessor(element) {

    var estat = $($($(element).parent()).siblings()[0]).attr('data-suspes');
    var codiProf = $($($(element).parent()).siblings()[0]).attr('data-codiprof');
    var nomProf = $($($(element).parent()).siblings()[0]).text();

    if (estat === '0') {
        $("#profCanviEstat").html("Estàs a punt de suspendre el professor:<br><strong>" + nomProf + '</strong>');
        $("#profCanviEstat").attr('data-estat', estat);
        $("#profCanviEstat").attr('data-codiprof', codiProf);
        $("#profCanviEstat").attr('data-filera', $($($(element).parent()).parent()).index());
        $("#canviEstatLiteral").text("Data suspensió");
        $("#dataCanviEstat").datepicker('setDate', new Date());
    } else {
        $("#profCanviEstat").attr('data-estat', estat);
        $("#profCanviEstat").attr('data-codiprof', codiProf);
        $("#profCanviEstat").attr('data-filera', $($($(element).parent()).parent()).index());
        $("#profCanviEstat").html("Estàs a punt d'activar el professor:<br><strong>" + nomProf + '</strong>');
        $("#canviEstatLiteral").text("Data activació");
        $("#dataCanviEstat").datepicker('setDate', new Date());
    }



}

function desaCanviEstatProf() {
    //agafem l'estat

    debugger;
    //agefem l0estat el codi del professor i la data
    var codiProf = $("#profCanviEstat").attr('data-codiprof');
    var estat = $("#profCanviEstat").attr('data-estat');
    var dataCanviEstat = $("#dataCanviEstat").val().toString();
    var dataCanviEstat1 = dataCanviEstat;
    dataCanviEstat = dataCanviEstat.substr(6) + '-' + dataCanviEstat.substr(3, 2) + '-' + dataCanviEstat.substr(0, 2);

    debugger;
    //agefem la filera per canviar l'online després de fer la modificació a la BBDD
    var indexFilera = $("#profCanviEstat").attr('data-filera');

    if (estat === "0") {
        var nouEstat = "1";
    } else {
        var nouEstat = "0";
    }


    var url = "php/canviEstatProfe.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"codiProf": codiProf, "nouEstat": nouEstat, "dataCanviEstat": dataCanviEstat},
        success: function (data) {
            //actualitzem l'atribut
            //$("#divTaulaProfes").html(data);
            if (data != '1' && data != '2') {
                //s'ha pogut fer le canvi d'estat
                if (nouEstat === '1') {
                    var filera = $("#costaulaDocents").children();
                    //canviarem les dades de l'online
                    $($(filera[indexFilera]).children()[0]).attr('data-suspes', '1');
                    $($($(filera[indexFilera]).children()[7]).children()[0]).removeClass('btn-success');
                    $($($(filera[indexFilera]).children()[7]).children()[0]).addClass('btn-danger');
                    $($($(filera[indexFilera]).children()[7]).children()[0]).text('Suspès');
                    $($(filera[indexFilera]).children()[8]).text(dataCanviEstat1);

                } else {
                    var filera = $("#costaulaDocents").children();
                    //canviarem les dades de l'online
                    $($(filera[indexFilera]).children()[0]).attr('data-suspes', '0');
                    $($($(filera[indexFilera]).children()[7]).children()[0]).removeClass('btn-danger');
                    $($($(filera[indexFilera]).children()[7]).children()[0]).addClass('btn-success');
                    $($($(filera[indexFilera]).children()[7]).children()[0]).text('Actiu');
                    $($(filera[indexFilera]).children()[8]).text(dataCanviEstat1);
                }
            } else {
                //no s'ha pogut fer el canvi d'estat
                if (data == '1') {
                    alert('Data activació incorrecta');
                } else {
                    alert('Data suspensió incorrecta');
                }
            }
        }

    });

    return false;


}

function resumBaseDades() {


    var url = "php/resumBaseDades.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            //actualitzem l'atribut
            $("#divBasedadestab").html(data);
        }

    });

    return false;


}

function carregaDatesClau() {
    var url = "php/carregaDatesClau.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            //actualitzem l'atribut
            $("#divTaulaDatesClau").html(data);
        }

    });

    return false;

}

function mostraDetallDataClau(element) {
    //passem les dates

    var dataClau = $($($(element).parent()).siblings()[1]).text();
    var dataIniciPubli = $($($(element).parent()).siblings()[2]).text();
    var dataFiPubli = $($($(element).parent()).siblings()[3]).text();

    $('#dataClau').datepicker("setDate", dataClau);
    $('#dataIniciPubli').datepicker("setDate", dataIniciPubli);
    $('#dataFiPubli').datepicker("setDate", dataFiPubli);

    //anem a buscar el text de la data clau

    var id = $($($(element).parent()).siblings()[1]).attr('data-id');
    $('#dataClau').attr('data-id', id);
    $('#dataClau').attr('data-mode', '1');

    var url = "php/carregaTextDataClau.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"id": id},
        success: function (data) {
            debugger;
            var missatge = data;
            //actualitzem l'atribut
            $("#summernoteDatesClau").summernote("code", missatge);
        }

    });

    return false;


}

function desaDadesClau() {
    //agafem les dades
    debugger;

    var datesCorrectes = true;

    var dataClau = $('#dataClau').val().toString();

    var dataInici = $('#dataIniciPubli').val().toString();
    var dataFi = $('#dataFiPubli').val().toString();




    if (dataClau !== '' && isValidDate(dataClau) === false) {
        datesCorrectes = false;
    }

    if (isValidDate(dataInici) === false) {
        datesCorrectes = false;
    } else if (isValidDate(dataFi) === false) {
        datesCorrectes = false;
    } else {
        //les dates són correctes anem a veure que inici<fi
        var dataIniciNor = dataInici.substr(6) + '-' + dataInici.substr(3, 2) + '-' + dataInici.substr(0, 2);
        var dataFiNor = dataFi.substr(6) + '-' + dataFi.substr(3, 2) + '-' + dataFi.substr(0, 2);

        if (dataIniciNor > dataFiNor) {
            datesCorrectes = false;
        }

    }



    if (datesCorrectes === true) {

        if (dataClau !== '') {
            dataClau = dataClau.substr(6) + '-' + dataClau.substr(3, 2) + "-" + dataClau.substr(0, 2);
        }
        if (dataInici !== '') {
            dataInici = dataInici.substr(6) + '-' + dataInici.substr(3, 2) + "-" + dataInici.substr(0, 2);
        }
        if (dataFi !== '') {
            dataFi = dataFi.substr(6) + '-' + dataFi.substr(3, 2) + "-" + dataFi.substr(0, 2);
        }

        var mode = $('#dataClau').attr('data-mode');

        var missatge = $("#summernoteDatesClau").summernote("code");
        var id = $('#dataClau').attr('data-id');
        var url = "php/desaDadesClau.php";
        $.ajax({
            type: "POST",
            url: url,
            data: {"dataClau": dataClau, "dataInici": dataInici, "dataFi": dataFi, "missatge": missatge, "id": id, "mode": mode},
            success: function (data) {

                //$("#divTaulaDatesClau").html(data);
                carregaDatesClau();
            }

        });

        return false;
    } else {
        alert("Dates incorrectes");
    }

}

function isValidDate(dateString)
{
    // First check for the pattern
    if (!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(dateString))
        return false;

    // Parse the date parts to integers
    var parts = dateString.split("/");
    var day = parseInt(parts[0], 10);
    var month = parseInt(parts[1], 10);
    var year = parseInt(parts[2], 10);

    // Check the ranges of month and year
    if (year < 1000 || year > 3000 || month == 0 || month > 12)
        return false;

    var monthLength = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    // Adjust for leap years
    if (year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
        monthLength[1] = 29;

    // Check the range of the day
    return day > 0 && day <= monthLength[month - 1];
}

function creaDataClau() {
    //esborrem la infommació que pogués haver al modal
    $('#dataClau').val('');
    $('#dataIniciPubli').val('');
    $('#dataFiPubli').val('');
    $("#summernoteDatesClau").summernote("code", '');

    //posem el mode
    $('#dataClau').attr('data-mode', '0');
    $('#dataClau').attr('data-id', '0');



}

function esborraDatesClau() {
    //anem a buscar els codis a esborrar
    var elements = $(".checkEsborrat");
    var codisDates = [];
    var cont = 0;

    for (var i = 0; i < elements.length; i++) {
        if ($(elements[i]).prop('checked') === true) {
            codisDates[cont] = $($($(elements[i]).parent()).siblings()[0]).attr('data-id');
            cont++;

        }

    }
    debugger;

    var url = "php/esborraDatesClau.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"codisDates": codisDates},
        success: function (data) {

            //$("#divTaulaDatesClau").html(data);
            carregaDatesClau();
        }

    });

    return false;


}

