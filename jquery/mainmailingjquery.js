/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function () {
    $('#summernote').summernote({

        height: 400,
        // height: 300, // set editor height

        //  minHeight: null, // set minimum height of editor
        //   maxHeight: null, // set maximum height of editor
        focus: true                  // set focus to editable area after initializing summernote
    });


});

$(document).ready(function () {
    $('#summernote1').summernote({
        height: 400,
        // height: 300, // set editor height

        //  minHeight: null, // set minimum height of editor
        //   maxHeight: null, // set maximum height of editor
        focus: true                  // set focus to editable area after initializing summernote
    });

    $('#summernote1').summernote('disable');
});


function carregaDadesMailing() {

    carregaDropGeneric('nivellDropdownMailing', 'SELECT distinct(ga35_nivell) as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga35_nivell=ga06_codi_nivell', 'Tots Niv');
   // carregaDropGeneric('grupDropdownMailing', 'SELECT ga07_codi_grup as codi, ga07_descripcio_grup as descripcio FROM ga07_grup', 'Tots Gr');


    //carreguem els mails del professor
    cercaMailingsProfe();
    //esborraAttachFiles();

}



function cercaMailingsProfe() {
    //no cal passar res

    var url = "php/cercaMalingsProfe.php";

    $.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {

            //rebem les dades
            $("#mailingsProfeDiv").html(data);

        }

    });
    return false;


}



function mostranivellDropdownMailing(element) {

    $("#butDropnivellDropdownMailing").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropnivellDropdownMailing").val($(element).attr('data-val'));

    var nivell=$(element).attr('data-val');
    
    if(nivell!=''){
        cercaGrupsNivell('grupDropdownMailing',nivell);
        
    }

}

function mostragrupDropdownMailing(element) {

    $("#butDropgrupDropdownMailing").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropgrupDropdownMailing").val($(element).attr('data-val'));

}




function afegeixAlumnes() {

    var url = "php/afegeixAlumnesMailing.php";
    var nivell = $("#butDropnivellDropdownMailing").val();
    var grup = $("#butDropgrupDropdownMailing").val();

    $.ajax({
        type: "POST",
        url: url,
        data: {"nivell": nivell, "grup": grup},
        success: function (data) {

            //rebem les dades
            $("#divTaulaAlumnesMailing").html(data);

        }

    });
    return false;


}

function passaAlumnesMail() {

    //obtenim la colecció de tots els alumnes ja afegits
    var fileresAfegides = $("#taulaAlumnesMailingBody").children();
    var codisAlumnes = [];

    for (var i = 0; i < fileresAfegides.length; i++) {
        codisAlumnes[i] = $($($(fileresAfegides[i])).children()[0]).attr('data-codi-alumne');
    }

    //obtenim els alumnes a afegir
    var fileresAlumnes = $("#costaulaAlumnesMailing").children();

    for (var i = 0; i < fileresAlumnes.length; i++) {
        //afegim cada filera a la taula d'enviaments
        //comprovarem si l'alumne ja està a la llista
        //obtenim el codi d'alumne
        var codiAlumne = $($($(fileresAlumnes[i])).children()[0]).attr('data-codi-alumne');

        debugger;
        if (codisAlumnes.indexOf(codiAlumne) == -1) {
            //no està a la llista i, per tant, l'afegim
            var fileraAfegir = $(fileresAlumnes[i]).clone();
            $(fileraAfegir).append('<td><input type="checkbox" value="" class="checkEsborra"></td>');
            $("#taulaAlumnesMailingBody").append(fileraAfegir);
        }



    }



}

function seleccionaTotsAlumnes() {
    //obtenim totes les fileres ja afegides
    if ($("#checkMarcaDesmarca").prop('checked') == true) {
        $(".checkEsborra").prop('checked', true);
    } else {
        $(".checkEsborra").prop('checked', false);

    }

}

function treuAlumnes() {
    //obtenim totes les fileres ja afegides
    var fileresAfegides = $("#taulaAlumnesMailingBody").children();

    //les recorreguem i si està marcada l'eliminem

    for (var i = 0; i < fileresAfegides.length; i++) {
        if ($($($($(fileresAfegides[i])).children()[3]).children()[0]).prop('checked') == true) {
            $(fileresAfegides[i]).remove();

        }
    }

}

function selectFileClick() {

    debugger;
    //pugem el fitxer al servidor
    var codiProf = $("#dadesCredencials").attr('data-codi-prof');
    var name = document.getElementById("file").files[0].name;
    var form_data = new FormData();

    var f = document.getElementById("file").files[0];
    var fsize = f.size || f.fileSize;

    //comprovem que la no s'hagi pujat ja el fitxer i que no superi la mida
    var esNou = true;
    var esPetit = true;
    var fileraFitxer = $(".fileraFitxer");
    var mida = 0;

    for (var i = 0; i < fileraFitxer.length; i++) {
        if ($(fileraFitxer[i]).text() == name) {
            esNou = false;

        }
        mida += parseInt($(fileraFitxer[i]).attr('data-mida'), 10);
    }

    mida += fsize;

    if (mida > 25000000) {
        esPetit = false;
    }

    if (esNou == false)
    {   //no és u fitxer nou
        alert("Aquest fitxer ja l'havies pujat abans");
    } else if (esPetit == false) {
        //es superen els 25 MB
        alert("No pots afegir aquest fitxer perquè se supera la mida màxima de 25 MB");

    } else
    {
        //podem pujar el fitxer
        $('#ressultUpload').html('<p class="btn-info">El fitxer està pujant: tingues paciència</p>');
        var contador = $("#taulaAttachedBody").attr('data-contador');

        form_data.append("file", document.getElementById('file').files[0]);
        form_data.append("codiProf", codiProf);
        form_data.append("nomFitxer", name);
        form_data.append("contador", contador);

        $("body").css("cursor", "progress");

        $.ajax({
            url: "php/uploadScripts/pujaAdjunt.php",
            method: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {

            },

            success: function (data)
            {

                $("#ressultUpload").html(data);

                //esborrem el fitxer que s'ha pujat
                $("#file").val('');

                $("#taulaAttachedBody").attr('data-contador', contador);

                //pengem el fitxer
                var fitxer = '<tr><td data-mida="' + fsize + '" class="fileraFitxer" data-nom="' + contador + '">' + name + '</td><td><center><input type="checkbox" value="" class="checkEsborra"></center></td></tr>';
                $("#taulaAttachedBody").append(fitxer);
                $("body").css("cursor", "default");
                //incrementem el contador
                contador = parseInt(contador, 10);
                contador++;
                $("#taulaAttachedBody").attr('data-contador', contador);


            }
        });
    }



}

function enviaMail() {
    //desem les dades a la taula
    //obtenim els alumnes
    alert("Es guardarà l'enviament que ha de ser aprovat per l'administrador.\nEls alumnes sense correu no es tindran en compte");

    var cont = 0;
    var codisAlumnes = [];
    var fileresAfegides = $("#taulaAlumnesMailingBody").children();
    var alumnesString = '';
    for (var i = 0; i < fileresAfegides.length; i++) {
        codisAlumnes[i] = $($(fileresAfegides[i]).children()[0]).attr('data-codi-alumne');
        if ($($(fileresAfegides[i]).children()[0]).hasClass('success')) {
            //la resta sí
            alumnesString += codisAlumnes[i];
            alumnesString += '-';
            cont++;

        }
    }

    if (cont > 0) {

        //si el darrer caràcter és un guió el traguem
        if (alumnesString.charAt(alumnesString.length - 1) == '-') {
            alumnesString = alumnesString.substr(0, alumnesString.length - 1);
        }


        //busquem els noms dels adjunts
        
        var nomsFitxers=[];
        var fileres=$(".fileraFitxer");
        
        for(var i=0;i<fileres.length;i++){
            nomsFitxers[i]=$(fileres[i]).text();
        }
        
        var nomsFitxerString=nomsFitxers.join('<#>');
        

        var cosMissatge = $('#summernote').summernote('code');

        //ho enviem per ajax

        var url = "php/desaMailing.php";

        $.ajax({
            type: "POST",
            url: url,
            data: {"alumnesString": alumnesString, "cosMissatge": cosMissatge,"nomsFitxerString":nomsFitxerString},
            //data: ("#form2").serialize,
            success: function (data) {
              //  $("#mailingsProfeDiv").html(data);
                //esborrem el contingut de les taules
                alert('Mailing creat!!');
                $("#taulaAlumnesMailingBody").html('');
                $("#costaulaAlumnesMailing").html('');
                //esborrem els fitxers adjunts
                $("#taulaAttachedBody").html('');
                $("#taulaAttachedBody").attr('data-contador','1');
                $("#ressultUpload").text('');
                $("#file").val('');
                cercaMailingsProfe();


            }

        });

        return false;
    } else {
        alert("no has seleccionat cap alumne\no els seleccionats no tenen adreça de correu");
    }


}

function mostraDadesMailing(element) {


    //obtenim el cos del missatge
    var missatge = $(element).attr('data-cos-mailing');

    var alumnes = $(element).attr('data-alumnes');
    
    var adjunts=$(element).attr('data-adjunts');

    adjunts=adjunts.replace(/<#>/g,'\n');

    var alumnesArray = alumnes.split("-");
    debugger;

    $("#fitxersAdjunts").html('Fitxers Adjunts<textarea class="form-control">'+adjunts+'</textarea>');
    $("#summernote1").summernote("code", missatge);

    //anem a buscar els noms dels alumnes
    var url = "php/cercaNomAlumnesMailing.php";

    $.ajax({
        type: "POST",
        url: url,
        data: {"alumnesArray": alumnesArray},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#alumnesMailingDetallDiv").html(data);

        }

    });

    return false;


}


function esborraMailingsProfe() {

    var confirmacio = confirm("Estàs segur d'esborrar aquestes comunicacions?");


    if (confirmacio == true) {
        //esborrem
        var mailingsProfes = $("#cosTaulaProfesMailing").children();
        var codisMailings = [];
        var cont = 0;


        for (var i = 0; i < mailingsProfes.length; i++) {
            //obtenim el check
            if ($($($(mailingsProfes[i]).children()[0]).children()[0]).prop('checked') == true) {
                //esborrem la filera

                codisMailings[cont] = $($($(mailingsProfes[i]).children()[0]).children()[0]).attr('data-codi-mailing');
                cont++;

            }

        }

        debugger;

        //esborrem els mailings de la base de dades
        if (codisMailings.length > 0) {
            var url = "php/esborraMailings.php";
            $.ajax({
                type: "POST",
                url: url,
                data: {"codisMailings": codisMailings},
                //data: ("#form2").serialize,
                success: function (data) {
                    cercaMailingsProfe();
                    //$("#mailingsProfeDiv").html(data);

                }

            });

            return false;

        }



    }

}


function adjuntaFitxers() {
    //guardem el codi del professor





}

function esborraAdjunt() {

    debugger;
    var fileresCheck = $(".checkEsborra");
    var fileres = $("#taulaAttachedBody").children();


    var nomsEsborrar = [];
    var conta = 0;


    for (var i = 0; i < fileresCheck.length; i++) {

        if ($(fileresCheck[i]).prop('checked') == true) {
            //si està marcat agafem el nom
            nomsEsborrar[conta] = $($(fileres[i]).children()[0]).attr('data-nom');
            conta++;
        }
    }

    if (conta > 0) {
        //anem al servidor a esborrar els fitxers
        var codiProf = $("#dadesCredencials").attr('data-codi-prof');
        var form_data = new FormData();


        //podem pujar el fitxer
        $('#ressultUpload').html('<p class="btn-info">Esborrant fitxers...</p>');

        //serialitzem l'array
        var serial_arr = nomsEsborrar.join("<#>");
        serial_arr = serial_arr.replace('(', '');
        serial_arr = serial_arr.replace('(', '');


        form_data.append("codiProf", codiProf);
        form_data.append("nomsFitxers", serial_arr);

        $("body").css("cursor", "progress");

        $.ajax({
            url: "php/uploadScripts/esborraAdjunt.php",
            method: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {

            },

            success: function (data)
            {

                $("#ressultUpload").html(data);
                //eliminem les fileres
                for (var i = 0; i < fileresCheck.length; i++) {

                    if ($(fileresCheck[i]).prop('checked') == true) {
                        //si està marcat agafem el nom
                        $(fileres[i]).remove();

                    }
                }



                $("body").css("cursor", "default");

            }
        });


    }



}

function esborraAttachFiles() {
    //sleep(2000);
    //$.get("php/uploadScripts/esborraAttachFiles.php");
    //var finestra=window.open("php/uploadScripts/esborraAttachFiles.php");
   
    

}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}


