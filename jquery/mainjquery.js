/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    $('[data-tooltip="tooltip"]').tooltip();

});


function desaCanviPassword() {
    //comprovem que les dues contrasenyes noves són iguals
    var nova1 = $("#passwordNou1").val();
    var nova2 = $("#passwordNou2").val();


    if (nova1 == nova2) {
        var url = "php/desaCanviPassword.php";
        //procedim a canviar la contrasenya
        $.ajax({
            type: "POST",
            url: url,
            data: {"nova": nova1},
            //data: ("#form2").serialize,
            success: function (data) {
                $("#passwordNou1").val("");
                $("#passwordNou2").val("");
                alert("Canvi reeixit");

            }
        });
        return false;

    } else {
        alert("Contrasenyes no coincidents");

    }
}

function netejaCamps() {
    $("#passwordNou1").val("");
    $("#passwordNou2").val("");

}

function mostradivProfessorSessio(element) {
    $("#butDropdivProfessorSessio").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivProfessorSessio").val($(element).attr('data-val'));


}

function carregaProfes() {




}
function activaGuardia() {

    //esborrem el contingut de l'horari


    if ($("#esGuardia").prop('checked') == true) {
        $("#butDropdivProfessorSessio").prop('disabled', false);
        carregaDropGeneric('divProfessorSessio', "select ga17_codi_professor as codi,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio from ga04_professors,ga17_professors_curs where ga04_codi_prof=ga17_codi_professor and ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga04_suspes='0' order by descripcio", 'Tria professor');
        $(".cellahorari").html('');
    } else {
        $("#butDropdivProfessorSessio").prop('disabled', true);
        //esborrem el profe de guàrdia
        $("#butDropdivProfessorSessio").html('Tria professor ' + '<span class="caret">');
        $("#butDropdivProfessorSessio").val('');
        carregaSessions();
    }
}


function carregaSessions() {
    //mostrem el professor

    //posem la setmana actual
    //inicialitzem la data amb el primer i el darrer dia de la setmana

    var dilluns = trobaDilluns();

    dilluns = moment(dilluns, "YYYY-MM-DD");
    dilluns = dilluns.format("YYYY-MM-DD");

    var diumenge = trobaDiumenge();

    diumenge = moment(diumenge, "YYYY-MM-DD");
    diumenge = diumenge.format("YYYY-MM-DD");

    $("#setmanaSessio").val('de ' + dilluns.toString() + " a " + diumenge.toString());

    $("#setmanaSessio").attr('data-dataactual', dilluns.toString());



    recuperaSessions(dilluns, diumenge);


}

function recuperaSessions(dilluns, diumenge) {
    debugger;
    var url = "php/carregaSessionsSetmana.php";

    if ($("#esGuardia").prop('checked') == true) {
        //és guàrdia
        var profe = $("#butDropdivProfessorSessio").val();

    } else {
        //no és guàrdia
        var profe = $("#butDropdivProfessorSessio").val();
    }


    $("body").css('cursor', 'progress');
    debugger;
    $("#prova").attr('id', 'loadingDiv');

    //$("#divSessionsSetmana").html('<h1 class="btn-info form-ontrol">Carregant sessions</h1><h2 class="btn-info form-ontrol">Tingues paciència!</h2>');
    $.ajax({
        type: "POST",
        url: url,
        data: {"dilluns": dilluns.toString(), "diumenge": diumenge.toString(), "profe": profe},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divSessionsSetmana").html(data);
            $("body").css('cursor', 'default');
            $("#loadingDiv").attr('id', 'prova');
        }

    });

    return false;

}


function setmanaEnrere() {


    var startdate = moment($("#setmanaSessio").attr('data-dataactual'), "YYYY-MM-DD");

    startdate = startdate.subtract(7, "days");
    startdate = startdate.format("YYYY-MM-DD");

    var enddate = moment(startdate, "YYYY-MM-DD");
    enddate = enddate.add(6, "days");
    enddate = enddate.format("YYYY-MM-DD");


    $("#setmanaSessio").val('de ' + startdate.toString() + " a " + enddate.toString());

    //tornem a posar l'atribut
    $("#setmanaSessio").attr('data-dataactual', startdate.toString());

    recuperaSessions(startdate, enddate);


}

function setmanaEndavant() {

    var startdate = moment($("#setmanaSessio").attr('data-dataactual'), "YYYY-MM-DD");

    startdate = startdate.add(7, "days");
    startdate = startdate.format("YYYY-MM-DD");

    var enddate = moment(startdate, "YYYY-MM-DD");
    enddate = enddate.add(6, "days");
    enddate = enddate.format("YYYY-MM-DD");


    $("#setmanaSessio").val('de ' + startdate.toString() + " a " + enddate.toString());
    //tornem a posar l'atribut
    $("#setmanaSessio").attr('data-dataactual', startdate.toString());

    recuperaSessions(startdate, enddate);
}

function trobaDilluns() {

    var dilluns = new Date();
    var dillunsDia = dilluns.getDay();

    dilluns.setDate(dilluns.getDate() - (dillunsDia - 1));

    return dilluns;

}

function trobaDiumenge() {
    var diumenge = new Date();
    var diumengeDia = diumenge.getDay();

    diumenge.setDate(diumenge.getDate() + (7 - diumengeDia));
    return diumenge;

}

function obreSessio(element) {
    //agafem les dades de la sessió
    //dia

    debugger;
    var cella = $($(element).parent()).parent();
    var columna = $(cella).index();
    var dia = $($($("#captaulaHorarisProfessor").children()[0]).children()[columna]).text();

    //hora
    var hora = $($($(cella).parent()).children()[0]).attr('data-horainici');
    //professor
    if ($("#butDropdivProfessorSessio").val() != '') {
        //prenem el codi del professor substituït
        var esguardia = '1';
        //el profe titular
        var profeCodi = $("#butDropdivProfessorSessio").val();
        var profeNom = $("#butDropdivProfessorSessio").text();


    } else {
        //és una sessió normal
        var profeCodi = $("#dadesCredencials").attr('data-codi-prof');
        var profeNom = $("#dadesCredencials").text().substring(29);
        var esguardia = '0';

    }



    window.open('mainSessionsHorari.php?dia=' + dia + "&hora=" + hora + "&profeCodi=" + profeCodi + "&profeNom=" + profeNom + "&esguardia=" + esguardia, '_self');

}

function carregaBarraMenu() {

    var url = "php/construeixMenu.php";

    debugger;

    $.ajax({
        type: "POST",
        url: url,
        data: {},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divMenuBar").html(data);

        }

    });

    return false;
}

function comprovaComunicacions() {

    var url = "php/comprovaComunicacions.php";

    debugger;

    $.ajax({
        type: "POST",
        url: url,
        data: {},

        success: function (data) {
            if (data === '1') {
                alert("Hi ha comunicacions pendents d'aprovar");
            }

        }

    });

    return false;

}

function editaProgramacio(element) {
    //recullim les dades de la capçalera

    if ($("#esGuardia").prop('checked') === true) {
        //és una guàrdia
        //no es poden esborrar els fitxers pujats ni pujar-ne més
        var canUpload = "disabled";
    } else {
        var canUpload = "";
    }
    var tram = $($(element).parent()).html();

    var tramArray = tram.split("<br>");

    var nivell = tramArray[1];
    var grup = tramArray[2];
    var assignatura = tramArray[3];
    var aula = tramArray[4];

    var columna = $($(element).parent()).index();

    var filera = $($($(element).parent()).parent()).index();

    var horaIniciFi = $($($(element).parent()).siblings()[0]).html();

    var horaIniciAtribut = $($($(element).parent()).siblings()[0]).attr('data-horainici');

    var horaIniciFiArray = horaIniciFi.split("<br>");

    var horaInici = horaIniciFiArray[0];
    var horaFi = horaIniciFiArray[1];
    debugger;
    //$('#summernote').summernote('code', tramArray[1]);
    var dia = $($($($($("#captaulaHorarisProfessor").children()[0]).children()[columna])).children()[0]).text();

    var taulaCapcalera = "";

    taulaCapcalera += '<div class="row">';
    taulaCapcalera += '<div class="col-sm-6">';
    taulaCapcalera += '<table class="table table-fixed">';
    taulaCapcalera += '<tr>';
    taulaCapcalera += '<td class="col-sm-3">'
    taulaCapcalera += 'Data: ' + dia.substring(8) + '/' + dia.substring(5, 7) + '/' + dia.substring(0, 4);
    taulaCapcalera += '</td>';
    taulaCapcalera += '<td class="col-sm-3">';
    taulaCapcalera += nivell;
    taulaCapcalera += '</td>';
    taulaCapcalera += '</tr>';

    taulaCapcalera += '<tr>';
    taulaCapcalera += '<td class="col-sm-3">';
    taulaCapcalera += horaInici;
    taulaCapcalera += '</td>';
    taulaCapcalera += '<td class="col-sm-3">';
    taulaCapcalera += grup;
    taulaCapcalera += '</td>';
    taulaCapcalera += '</tr>';

    taulaCapcalera += '<tr>';
    taulaCapcalera += '<td class="col-sm-3">';
    taulaCapcalera += aula;
    taulaCapcalera += '</td>';
    taulaCapcalera += '<td class="col-sm-3">';
    taulaCapcalera += assignatura;
    taulaCapcalera += '</td>';
    taulaCapcalera += '</tr>';

    taulaCapcalera += '</table>';
    taulaCapcalera += '</div>';
    taulaCapcalera += '<div class="col-sm-6">';
    taulaCapcalera += '<div>'
    taulaCapcalera += '<strong>adjunts</strong>';
    taulaCapcalera += '</div>';
    taulaCapcalera += '<div id="cosTaulaAdjunts">';


    taulaCapcalera += '</div>';
    taulaCapcalera += '<div>';
    taulaCapcalera += '<input type="file" id="file" ' + canUpload + ' onchange="triaFitxerClick()">';
    taulaCapcalera += '</div>';
    taulaCapcalera += '<div id="ressultUpload">';
    taulaCapcalera += '</div>';
    taulaCapcalera += '</div>';
    taulaCapcalera += '</div>';



    $("#capcaleraPrograma").html(taulaCapcalera);

    $("#capcaleraPrograma").attr('data-filera', filera);
    $("#capcaleraPrograma").attr('data-columna', columna);
    $("#capcaleraPrograma").attr('data-dia', dia);
    $("#capcaleraPrograma").attr('data-hora', horaIniciAtribut);

    if ($("#esGuardia").prop('checked') === true) {
        //és una guàrdia
        var profe = $("#butDropdivProfessorSessio").val();
    } else {
        var profe = "";
    }

    //anem a buscar la programació si cal
    if ($(element).attr('data-programacio') === '1') {
        //hi ha programacio
        var url = "php/cercaProgramacio.php";

        debugger;

        $.ajax({
            type: "POST",
            url: url,
            data: {"dia": dia, "hora": horaIniciAtribut, "profe": profe},

            success: function (data) {
                $('#summernote').summernote('code', data);
                if (profe === "") {

                    $('#summernote').summernote('enable');
                    $('#desaProgramadorSessions').prop('disabled', false);
                    $('#esborraProgramadorSessions').prop('disabled', false);
                } else {
                    $('#summernote').summernote('disable');
                    $('#desaProgramadorSessions').prop('disabled', true);
                    $('#esborraProgramadorSessions').prop('disabled', true);
                }
            }

        });

        //anem a veure si hi ha adjunts

        cercaAdjunts(dia, horaIniciAtribut, profe);

        return false;
    } else {
        //no n'hi ha
        $('#summernote').summernote('code', '');
        if (profe === "") {

            $('#summernote').summernote('enable');
            $('#desaProgramadorSessions').prop('disabled', false);
            $('#esborraProgramadorSessions').prop('disabled', false);
        } else {
            $('#summernote').summernote('disable');
            $('#desaProgramadorSessions').prop('disabled', true);
            $('#esborraProgramadorSessions').prop('disabled', true);

        }

    }



}

function cercaAdjunts(dia, horaIniciAtribut, profe) {

    var url = "php/cercaAdjuntsProgra.php";

    debugger;

    $.ajax({
        type: "POST",
        url: url,
        data: {"dia": dia, "hora": horaIniciAtribut, "profe": profe},

        success: function (data) {
            $("#cosTaulaAdjunts").html(data);

        }

    });
}

function desaProgramadorSessions() {
    var textProgramacio = $('#summernote').summernote('code');
    //recullim dia i hora
    var dia = $("#capcaleraPrograma").attr('data-dia');
    var hora = $("#capcaleraPrograma").attr('data-hora');

    var url = "php/desaProgramacio.php";

    debugger;

    $.ajax({
        type: "POST",
        url: url,
        data: {"textProgramacio": textProgramacio, "dia": dia, "hora": hora},

        success: function (data) {
            //$("#divSessionsSetmana").html(data);
            carregaSessions();
        }

    });

    return false;


}


function esborraProgramadorSessions() {

    var confirmacio = confirm("N'estàs segur d'esborrar la programació");
    if (confirmacio === true) {
        //recullim dia i hora
        var dia = $("#capcaleraPrograma").attr('data-dia');
        var hora = $("#capcaleraPrograma").attr('data-hora');

        var url = "php/esborraProgramacio.php";

        debugger;

        $.ajax({
            type: "POST",
            url: url,
            data: {"dia": dia, "hora": hora},

            success: function (data) {
                //$("#divSessionsSetmana").html(data);
                carregaSessions();
            }

        });

        return false;

    }

}

$(document).ready(function () {
    $('#summernote').summernote({

        height: 400,
        // height: 300, // set editor height

        //  minHeight: null, // set minimum height of editor
        //   maxHeight: null, // set maximum height of editor
        focus: true                  // set focus to editable area after initializing summernote
    });


});


function triaFitxerClick() {

    var codiProf = $("#dadesCredencials").attr('data-codi-prof');
    var name = document.getElementById("file").files[0].name;
    var form_data = new FormData();

    var f = document.getElementById("file").files[0];
    var fsize = f.size || f.fileSize;

    //comprovem la mida
    var uploadFile = $("#dadesCredencials").attr('data-uploadsize').toString();
    var postSize = $("#dadesCredencials").attr('data-postsize').toString();

    uploadFile = uploadFile.substr(0, uploadFile.length - 1);
    postSize = postSize.substr(0, postSize.length - 1);
    var midaMin = Math.min(uploadFile, postSize);

    var midaMinBytes = midaMin * 1024 * 1024;

    if (fsize < midaMinBytes) {
        //procedim a pujar el fitxer
        if ($("#esGuardia").prop('checked') === true) {
            //és una guàrdia
            var profe = $("#butDropdivProfessorSessio").val();
        } else {
            var profe = "";
        }

        var dia = $("#capcaleraPrograma").attr('data-dia');
        var horaIniciAtribut = $("#capcaleraPrograma").attr('data-hora');


        //podem pujar el fitxer
        $('#ressultUpload').html('<p class="btn-info">El fitxer està pujant: tingues paciència</p>');

        form_data.append("file", document.getElementById('file').files[0]);
        form_data.append("nomFitxer", name);
        form_data.append("dia", dia);
        form_data.append("hora", horaIniciAtribut);


        $("body").css("cursor", "progress");

        $.ajax({
            url: "php/uploadScripts/pujaAdjuntProgramacio.php",
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

                //refresquem les dades
                cercaAdjunts(dia, horaIniciAtribut, profe);
                $("body").css("cursor", "default");


            }
        });

        return false;

    } else {
        $('#ressultUpload').html('<p class="btn-danger">El fitxer és massa gran. La mida màxima és de: ' + midaMin + 'MB</p>');
    }
}

function downloadAdjunt(element) {


}

function esborraAdjunt(element) {

    var confirmacio = confirm('Segur que vols esborrar el fitxer adjunt?');

    if (confirmacio === true) {

        //el codi del registre a esborrar
        var codi = $(element).attr('data-codi');
        var form_data = new FormData();
        form_data.append("codi", codi);

        var dia = $("#capcaleraPrograma").attr('data-dia');
        var horaIniciAtribut = $("#capcaleraPrograma").attr('data-hora');

        if ($("#esGuardia").prop('checked') === true) {
            //és una guàrdia
            var profe = $("#butDropdivProfessorSessio").val();
        } else {
            var profe = "";
        }


        $("body").css("cursor", "progress");

        $.ajax({
            url: "php/uploadScripts/esborraAdjuntProgramacio.php",
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

                //refresquem les dades
                cercaAdjunts(dia, horaIniciAtribut, profe);
                $("body").css("cursor", "default");


            }
        });

    }
}


function carregaSesReplicables(element) {
    //obtenim les sessions replicable
    //ens cal dia i hora

    var hora = $($($($($(element).parent()).parent()).parent()).siblings()[0]).attr('data-horainici');
    var index = $($($($(element).parent()).parent()).parent()).index();

    var dia = $($($($("#captaulaHorarisProfessor").children()[0]).children()[index]).children()[0]).text();
    //mirem si és una guàrdia
    if ($("#esGuardia").prop('checked') === true) {
        //és una guàrdia
        var profe = $("#butDropdivProfessorSessio").val();
    } else {
        var profe = "";
    }

    var url = "php/carregaSesReplicables.php";

    debugger;

    $.ajax({
        type: "POST",
        url: url,
        data: {"dia": dia, "hora": hora, "profe": profe},

        success: function (data) {
            $("#divSessionsRepli").html(data);


        }

    });

    return false;


}

function desaReplicaSessions() {
    //anem a buscar les hores de les sessions a replicar
    var sessionsAReplicar = $(".sesAReplicar");
    var horesReplicar = [];
    var conta = 0;
    for (var i = 0; i < sessionsAReplicar.length; i++) {
        if ($(sessionsAReplicar).prop('checked') === true) {
            horesReplicar[conta] = $($($(sessionsAReplicar[i]).parent()).siblings()[0]).text();
            conta++;
        }

    }

    //anem a buscar les dades de la sessió original
    var dia = $("#cosTaulaSessionsReplicables").attr('data-dia');
    var hora = $("#cosTaulaSessionsReplicables").attr('data-hora');
    var profe = $("#cosTaulaSessionsReplicables").attr('data-profe');

    if (horesReplicar.length > 0) {
        //hi ha sessions per a replicar
        //enviem les dades al servidor
        var url = "php/desaReplicaSessions.php";

        debugger;

        $.ajax({
            type: "POST",
            url: url,
            data: {"dia": dia, "hora": hora, "profe": profe, "horesReplicar": horesReplicar},

            success: function (data) {
                //tornem a carregar les sessions

                alert('Replicades :' + data + ' sessions');
                carregaSessions();

            }
        });

        return false;

    } else {
        alert("No has marcat cap sessió per a replicar");
    }

}