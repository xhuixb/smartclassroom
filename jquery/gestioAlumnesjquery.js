/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function carregaDadesInicials() {

    carregaDropGeneric('nivellAlumnesDropdown', 'SELECT distinct(ga35_nivell) as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga35_nivell=ga06_codi_nivell', 'Tria Nivell');
    carregaDropGeneric('tutorDropdown', "select ga17_codi_professor as codi, concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio from ga04_professors,ga17_professors_curs where ga17_codi_professor=ga04_codi_prof and ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual='1') order by descripcio", "Tria tutor")
    //  carregaDropGeneric('grupAlumnesDropdown', 'SELECT ga07_codi_grup as codi, ga07_descripcio_grup as descripcio FROM ga07_grup', 'Tria Grup');
    carregaDropGeneric('cotutorDropdown', "select ga17_codi_professor as codi, concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio from ga04_professors,ga17_professors_curs where ga17_codi_professor=ga04_codi_prof and ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual='1') order by descripcio", "Tria cotutor")
}



//és pel drop de canvi de grup
function mostratutorDropdown(element) {

    $("#butDroptutorDropdown").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDroptutorDropdown").val($(element).attr('data-val'));

}

function mostracotutorDropdown(element) {

    $("#butDropcotutorDropdown").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropcotutorDropdown").val($(element).attr('data-val'));

}


function mostranivellAlumnesDropdown(element) {
    debugger;
    $("#butDropnivellAlumnesDropdown").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropnivellAlumnesDropdown").val($(element).attr('data-val'));
    $("#divTaulaGestioAlumnes").html("");

    var nivell = $(element).attr('data-val');

    if (nivell != '') {
        cercaGrupsNivell('grupAlumnesDropdown', nivell)

    }

    habilitacercaAlumnes();

}

function mostragrupAlumnesDropdown(element) {

    $("#butDropgrupAlumnesDropdown").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropgrupAlumnesDropdown").val($(element).attr('data-val'));
    //esborrem taula
    $("#divTaulaGestioAlumnes").html("");
    habilitacercaAlumnes();

}

//és pel drop de canvi de grup
function mostragrupNouAlumnesDropdown(element) {

    $("#butDropgrupNouAlumnesDropdown").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropgrupNouAlumnesDropdown").val($(element).attr('data-val'));

}


function habilitacercaAlumnes() {

    if ($("#butDropnivellAlumnesDropdown").val() != "" && $("#butDropgrupAlumnesDropdown").val() != "") {
        //habilitem cerca
        $("#cercaAlumnesNivellGrup").prop("disabled", false);
        $("#plusAlumnesNivellGrup").prop("disabled", false);
        $("#assignaTutorNivellGrup").prop("disabled", false);

    } else {
        //habilitem cerca
        $("#cercaAlumnesNivellGrup").prop("disabled", true);
        $("#plusAlumnesNivellGrup").prop("disabled", true);
        $("#assignaTutorNivellGrup").prop("disabled", true);
    }
}

function cercaAlumnesNivellGrup() {


    var nivell = $("#butDropnivellAlumnesDropdown").val();
    var grup = $("#butDropgrupAlumnesDropdown").val();

    //primerament busquem el tutor del grup

    var url = "php/cercaTutorNivellGrup.php";

    $.ajax({
        type: "POST",
        url: url,
        data: {"nivell": nivell, "grup": grup},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divTutorNivellGrup").html(data);

        }

    });




    var url = "php/cercaAlumnesNivellGrup.php";


    $.ajax({
        type: "POST",
        url: url,
        data: {"nivell": nivell, "grup": grup},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divTaulaGestioAlumnes").html(data);
            $("#plusAlumnesNivellGrup").prop("disabled", false);
            $("#trashAlumnesNivellGrup").prop("disabled", false);

            //omplim el grup del modal per a canviar de grup
            carregaDropGeneric('grupNouAlumnesDropdown', "SELECT ga35_grup as codi, ga07_descripcio_grup as descripcio FROM ga07_grup,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual='1') and ga35_nivell=" + nivell + " and ga35_grup=ga07_codi_grup order by codi", 'Tria Grup');
        }

    });

    return false;

}

function desaAlumneNou(element) {

    var url = "php/desaAlumneNou.php";

    //agefem totes les dades
    var nom = $("#nomAlumneNou").val();
    var cognom1 = $("#cognom1AlumneNou").val();
    var cognom2 = $("#cognom2AlumneNou").val();
    var nivell = $("#butDropnivellAlumnesDropdown").val();
    var grup = $("#butDropgrupAlumnesDropdown").val();
    var mode = $("#capAlumne").attr('data-mode');
    var codiAlumne = $("#capAlumne").attr('data-codi-alumne');
    var mail1 = $("#mail1AlumneNou").val();
    var mail2 = $("#mail2AlumneNou").val();

    var mail1Vell = $("#mail1AlumneNou").attr('data-mail1-vell');
    var mail2Vell = $("#mail2AlumneNou").attr('data-mail2-vell');


    if ((isEmail(mail1) == false && mail1 != '') || (isEmail(mail2) == false && mail2 != '')) {
        //els emails no estan correctament formats
        alert("El format d'algun dels correus electrònics no és correcte");

    } else {

        //els emails estan correctament formats
        if ($("#checkAoAlta").prop('checked') == true) {
            var checkAoAlta = "1";
        } else {
            var checkAoAlta = "0";
        }

        if ($("#checkAaAlta").prop('checked') == true) {
            var checkAaAlta = "1";
        } else {
            var checkAaAlta = "0";
        }
        if ($("#checkUseeAlta").prop('checked') == true) {
            var checkUseeAlta = "1";
        } else {
            var checkUseeAlta = "0";
        }

        if ($("#checkComunicaAlta").prop('checked') == true) {
            var checkComunica = "1";
        } else {
            var checkComunica = "0";
        }


        debugger;

        $.ajax({
            type: "POST",
            url: url,
            data: {"nivell": nivell, "grup": grup, "nom": nom, "cognom1": cognom1, "cognom2": cognom2, "mode": mode, "codiAlumne": codiAlumne
                , "checkAoAlta": checkAoAlta, "checkAaAlta": checkAaAlta, "checkUseeAlta": checkUseeAlta, "checkComunica": checkComunica, "mail1": mail1, "mail2": mail2, "mail1Vell": mail1Vell, "mail2Vell": mail2Vell},
            //data: ("#form2").serialize,
            success: function (data) {

                //$("#divTaulaGestioAlumnes").html(data);
                debugger;
                //esborrem les dades del formulari modal
                $("#nomAlumneNou").val("");
                $("#cognom1AlumneNou").val("");
                $("#cognom2AlumneNou").val("");
                $("#checkAoAlta").prop('checked', false);
                $("#checkAaAlta").prop('checked', false);
                $("#checkUseeAlta").prop('checked', false);
                $("#mail1AlumneNou").val("");
                $("#mail2AlumneNou").val("");
                //recarreguem les dades

                cercaAlumnesNivellGrup();

            }

        });

        return false;
    }
}

function canviGrupAlumne(element) {
    //obtenim l'alumne
    debugger;
    var filera = $($(element).parent()).parent();
    var nom = $($(filera).children()[1]).attr('data-nom');
    var cognom1 = $($(filera).children()[1]).attr('data-cognom1');
    var cognom2 = $($(filera).children()[1]).attr('data-cognom2');
    var codiAlumne = $($(filera).children()[1]).attr('data-codi-alumne');


    //posem el nom
    $("#alumneCanviGrup").text(cognom1 + " " + cognom2 + ", " + nom);
    $("#alumneCanviGrup").attr('data-codi-alumne', codiAlumne);



}

function desaCanviAlumneNou(element) {
    //abans de tancar cal fer el canvi a la base de dades
    //ens cal el curs i l'alumne però el curs és una variable de sessió
    var nouGrup = $("#butDropgrupNouAlumnesDropdown").val();
    var codiAlumne = $("#alumneCanviGrup").attr('data-codi-alumne');

    var url = "php/desaCanviAlumneNou.php";

    debugger;
    $.ajax({
        type: "POST",
        url: url,
        data: {"nouGrup": nouGrup, "codiAlumne": codiAlumne},
        //data: ("#form2").serialize,
        success: function (data) {
            //recarreguem les dades        
            cercaAlumnesNivellGrup();


        }

    });

    return false;



}

function altaAlumne(element) {
    //desabilitem desa fins que es faci algun canvi
    $("#desaAlumneNou").prop('disabled', true);
    $("#nomAlumneNou").addClass('alert-danger');
    $("#cognom1AlumneNou").addClass('alert-danger');

    $("#capAlumne").text("Alta alumne");
    $("#capAlumne").attr('data-codi-alumne', "");
    //mode edició
    $("#capAlumne").attr('data-mode', "");
    $("#checkComunicaAlta").prop('checked', true);
}

function editaAlumne(element) {
    //obtenim les dades
    var filera = $($(element).parent()).parent();
    var nom = $($(filera).children()[1]).attr('data-nom');
    var cognom1 = $($(filera).children()[1]).attr('data-cognom1');
    var cognom2 = $($(filera).children()[1]).attr('data-cognom2');
    var mail1 = $($(filera).children()[2]).text();
    var mail2 = $($(filera).children()[3]).text();

    var codiAlumne = $($(filera).children()[1]).attr('data-codi-alumne');
    debugger;
    //posem les dades   
    //var prova = $($($($(filera).children()[4]).children()[0]).children(0)).prop('checked');

    if ($($(filera).children()[1]).attr('data-ao') == '1') {
        $("#checkAoAlta").prop('checked', true);
    } else {
        $("#checkAoAlta").prop('checked', false);
    }


    if ($($(filera).children()[1]).attr('data-aa') == '1') {
        $("#checkAaAlta").prop('checked', true);
    } else {
        $("#checkAaAlta").prop('checked', false);
    }

    if ($($(filera).children()[1]).attr('data-usee') == '1') {
        $("#checkUseeAlta").prop('checked', true);
    } else {
        $("#checkUseeAlta").prop('checked', false);
    }

    if ($($($($(filera).children()[4]).children()[0]).children()[0]).prop('checked') == true) {
        $("#checkComunicaAlta").prop('checked', true);
    } else {
        $("#checkComunicaAlta").prop('checked', false);
    }


    $("#capAlumne").text("Edita alumne");
    $("#capAlumne").attr('data-codi-alumne', codiAlumne);
    //mode edició
    $("#capAlumne").attr('data-mode', '1');
    $("#nomAlumneNou").val(nom);
    $("#cognom1AlumneNou").val(cognom1);
    $("#cognom2AlumneNou").val(cognom2);
    $("#mail1AlumneNou").val(mail1);
    $("#mail2AlumneNou").val(mail2);

    //guardem els mail vells
    $("#mail1AlumneNou").attr('data-mail1-vell', mail1);
    $("#mail2AlumneNou").attr('data-mail2-vell', mail2);


    //habilitem desa fins que es faci algun canvi
    $("#desaAlumneNou").prop('disabled', false);

    $("#nomAlumneNou").removeClass('alert-danger');
    $("#cognom1AlumneNou").removeClass('alert-danger');


}

function netejaCampsAlumne() {
    //esborrem les dades del formulari modal
    debugger;
    $("#nomAlumneNou").val("");
    $("#cognom1AlumneNou").val("");
    $("#cognom2AlumneNou").val("");
    $("#mail1AlumneNou").val("");
    $("#mail2AlumneNou").val("");
    $("#checkAoAlta").prop('checked', false);
    $("#checkAaAlta").prop('checked', false);
    $("#checkUseeAlta").prop('checked', false);

}

function trashAlumnesNivellGrup() {
    var confirmacio = confirm("N'estàs convençut de que vols esborrar aquests alumnes?");
    if (confirmacio === true) {
        var alumnes = [];
        var fileresSeleccionades = $($($(".checkBaixa:checked")).parent()).parent();

        //construim l'array amb els codis d'almunes
        var codisAlumnes = [];
        debugger;
        for (var i = 0; i < fileresSeleccionades.length; i++) {
            codisAlumnes[i] = $($(fileresSeleccionades[i]).children()[1]).attr('data-codi-alumne');
        }

        var url = "php/baixaAlumnesCurs.php";


        $.ajax({
            type: "POST",
            url: url,
            data: {"codisAlumnes": codisAlumnes},
            //data: ("#form2").serialize,
            success: function (data) {
                //recarreguem les dades        
                //$("#divTaulaGestioAlumnes").html(data);
                cercaAlumnesNivellGrup();

            }

        });

        return false;


    }


}

function usuariBuit() {
    if ($("#nomAlumneNou").val() != "" && $("#cognom1AlumneNou").val() != "") {
        //s'habilitat desar
        $("#desaAlumneNou").prop('disabled', false);
        $("#nomAlumneNou").removeClass('alert-danger');
        $("#cognom1AlumneNou").removeClass('alert-danger');
    } else {

        //es desabilitat desar
        $("#desaAlumneNou").prop('disabled', true);
        if ($("#nomAlumneNou").val() == "" && $("#cognom1AlumneNou").val() != "") {
            $("#nomAlumneNou").addClass('alert-danger');
            $("#cognom1AlumneNou").removeClass('alert-danger');
        } else if ($("#nomAlumneNou").val() != "" && $("#cognom1AlumneNou").val() == "") {
            $("#nomAlumneNou").removeClass('alert-danger');
            $("#cognom1AlumneNou").addClass('alert-danger');

        } else {
            $("#nomAlumneNou").addClass('alert-danger');
            $("#cognom1AlumneNou").addClass('alert-danger');

        }
    }

}

function mostraImatge(element) {

    //obtenim el codi de l'alumne
    var fileraAlumne = $($(element).parent()).parent();
    var codiAlumne = $($(fileraAlumne).children()[1]).attr('data-codi-alumne');
    var nomAlumne = $($(fileraAlumne).children()[1]).text();
    var cognom1Alumne = $($($(fileraAlumne).children()[2]).children()[1]).text();
    var cognom2Alumne = $($(fileraAlumne).children()[3]).text();

    var nomComplet = cognom1Alumne + ' ' + cognom2Alumne + ', ' + nomAlumne;
    //$("#divImatgeAlumne").attr('data-codi-alumne',codiAlumne);

    //esborren el ressultat anterior
    $('#ressultUpload').html('');

    //anem a buscar la imatge

    var url = "php/cercaImatgeAlumne.php";

    debugger;
    $.ajax({
        type: "POST",
        url: url,
        data: {"codiAlumne": codiAlumne, "nomComplet": nomComplet},
        //data: ("#form2").serialize,
        success: function (data) {
            //recarreguem les dades        
            $("#divImatgeAlumne").html(data);
            //refresquem la imatge
            $("#codiImatgeAlumne").attr("src", $("#codiImatgeAlumne").attr("src") + "?" + new Date().getTime());

        }

    });

    return false;



}

async function mostraFitxaAlumne(element) {
    var urlPhp = "php/creaFitxaAlumne.php";

    var alumne = $($(element).parent()).attr('data-codi-alumne');
    var professor = $("#dadesCredencials").attr('data-codi-prof');
    var urlXml = "xml/prof" + professor + "al" + alumne + ".xml";


    debugger;

    //var urlXml = "xml/fitxaAlumne.xml";

    $.get(urlPhp + "?alumne=" + alumne);
    await sleep(2000);
    //window.open(urlXml, "_self", "toolbar=no,scrollbars=yes,resizable=yes,width=" + screen.width + ",height=" + screen.height);
    var finestra = window.open(urlXml, "_blank");
    finestra.focus();

}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}



function uploadImage() {

    debugger;
    var name = document.getElementById("fileToUpload").files[0].name;
    var form_data = new FormData();

    var oFReader = new FileReader();
    oFReader.readAsDataURL(document.getElementById("fileToUpload").files[0]);
    var f = document.getElementById("fileToUpload").files[0];
    var fsize = f.size || f.fileSize;
    if (fsize > 10000000)
    {
        alert("La imatge que vols pujar és massa grossa");
    } else
    {
        $('#ressultUpload').html('<p>El fitxer està punjant: tingues paciència</p>');


        form_data.append("file", document.getElementById('fileToUpload').files[0]);
        form_data.append("codiAlumne", $("#alumneImatge").attr('data-codi-alumne'));
        form_data.append("nomComplet", $("#alumneImatge").text());
        $("body").css("cursor", "progress");

        $.ajax({
            url: "php/uploadScripts/provaUpload_1.php",
            method: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                $('#uploaded_image').html("<label class='text-success'>Image Uploading...</label>");
            },
            success: function (data)
            {
                //mostraImatge(this);
                $('#divImatgeAlumne').html(data);
                $('#ressultUpload').html('<p>Fitxer Pujat</p>');
                //refresquem la imaatge
                $("#codiImatgeAlumne").attr("src", $("#codiImatgeAlumne").attr("src") + "?" + new Date().getTime());
                $("body").css("cursor", "default");

            }
        });
    }

}

function esborraImage() {
    //obtenim el codi de l'alumne i el nom
    var codiAlumne = $("#alumneImatge").attr('data-codi-alumne');
    var nomComplet = $("#alumneImatge").text();


    url = "php/uploadScripts/removeUpload_1.php";

    $.ajax({
        type: "POST",
        url: url,
        data: {"codiAlumne": codiAlumne, "nomComplet": nomComplet},
        //data: ("#form2").serialize,
        success: function (data) {
            //recarreguem les dades        
            $("#divImatgeAlumne").html(data);
            $('#ressultUpload').html('<p>Fitxer Esborrat</p>');
            //refresquem la imatge
            $("#codiImatgeAlumne").attr("src", "imatges/alumnes/avatar.png?" + new Date().getTime());

        }

    });

    return false;

}

function assignaTutorNivellGrup() {
    //recollim el nivell i el grup

    debugger;
    var nivell = $("#butDropnivellAlumnesDropdown").val();
    var grup = $("#butDropgrupAlumnesDropdown").val();

    var url = "php/cercaTutor.php";


    $.ajax({
        type: "POST",
        url: url,
        data: {"nivell": nivell, "grup": grup},
        //data: ("#form2").serialize,
        success: function (data) {
            //recarreguem les dades        
            //rebem el tutor
            var tutor = data.split('<#>');
            //$("#tutorDropdown").html(tutor[0]+'-'+tutor[1]+'-')+tutor[2];

            if (tutor[0] === '1') {
                //sí que hi ha tutor assignat
                $("#butDroptutorDropdown").html(tutor[2] + ' ' + '<span class="caret">');
                $("#butDroptutorDropdown").val(tutor[1]);
            } else {

                //no hi hatutor assignat
                $("#butDroptutorDropdown").html('Tria tutor ' + '<span class="caret">');
                $("#butDroptutorDropdown").val('');


            }

        }

    });

    return false;

}


function desaTutor() {
    //agafem el tutor,nivell i grup
    var tutor = $("#butDroptutorDropdown").val();
    var nivell = $("#butDropnivellAlumnesDropdown").val();
    var grup = $("#butDropgrupAlumnesDropdown").val();


    //ho enviem al servidor
    var url = "php/desaTutor.php";


    $.ajax({
        type: "POST",
        url: url,
        data: {"nivell": nivell, "grup": grup, "tutor": tutor},
        //data: ("#form2").serialize,
        success: function (data) {
            //no cal fer res


        }

    });

    return false;


}

function esborraTutor() {
    //agafem el tutor,nivell i grup

    var nivell = $("#butDropnivellAlumnesDropdown").val();
    var grup = $("#butDropgrupAlumnesDropdown").val();


    //ho enviem al servidor
    var url = "php/esborraTutor.php";


    $.ajax({
        type: "POST",
        url: url,
        data: {"nivell": nivell, "grup": grup},
        //data: ("#form2").serialize,
        success: function (data) {
            //no cal fer res


        }

    });

    return false;


}


function assignaCotutor(element) {
    //passem l'alumne
    var filera = $($(element).parent()).parent();
    var nom = $($(filera).children()[1]).attr('data-nom');
    var cognom1 = $($(filera).children()[1]).attr('data-cognom1');
    var cognom2 = $($(filera).children()[1]).attr('data-cognom2');
    var codiAlumne = $($(filera).children()[1]).attr('data-codi-alumne');


    //posem el nom
    $("#alumneCotutor").text(cognom1 + " " + cognom2 + ", " + nom);
    $("#alumneCotutor").attr('data-codi-alumne', codiAlumne);

    //anem a buscar el cotutor
    //ho enviem al servidor
    var url = "php/cercaCotutor.php";


    $.ajax({
        type: "POST",
        url: url,
        data: {"codiAlumne": codiAlumne},
        //data: ("#form2").serialize,
        success: function (data) {
            var dataArray = data.split('<#>');

            if (dataArray[0] === '0') {
                //no hi ha cotutor
                $("#butDropcotutorDropdown").html('Tria cotutor ' + '<span class="caret">');
                $("#butDropcotutorDropdown").val('');

            } else {
                //hi ha cotutor              
                $("#butDropcotutorDropdown").html(dataArray[2] + ' ' + '<span class="caret">');
                $("#butDropcotutorDropdown").val(dataArray[1]);
            }

        }

    });

    return false;

}

function desaCotutor() {
    //agafem el codi de l'alumne
    var codiAlumne = $("#alumneCotutor").attr('data-codi-alumne');
    //agafem el codi tutor
    var codiCotutor = $("#butDropcotutorDropdown").val();
    var codiTutor = $($("#divTutorNivellGrup").children()[0]).attr('data-codi-tutor');
    if (codiCotutor === '') {
        alert("No has triat cap professor com a cotutor");

    } else if (codiTutor == codiCotutor) {
        //el cotutor no pot ser igual que el tutor
        alert("El cotutor no pot ser el mateix que el tutor");
    } else {
        var url = "php/desaCotutor.php";


        $.ajax({
            type: "POST",
            url: url,
            data: {"codiAlumne": codiAlumne, "codiCotutor": codiCotutor},
            //data: ("#form2").serialize,
            success: function (data) {
                cercaAlumnesNivellGrup();

            }

        });

        return false;
    }

}

function esborraCotutor() {
    //agafem el codi de l'alumne
    var codiAlumne = $("#alumneCotutor").attr('data-codi-alumne');

    var url = "php/esborraCotutor.php";

    $.ajax({
        type: "POST",
        url: url,
        data: {"codiAlumne": codiAlumne},
        //data: ("#form2").serialize,
        success: function (data) {
            cercaAlumnesNivellGrup();

        }

    });

    return false;


}