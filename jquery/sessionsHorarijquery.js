/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function carregaDadesSessio() {
    debugger;
    var codiGrup = $("#butDropgrupSessio").val();
    //carregaDropGeneric('divaulaSessio',"select ga01_codi_aula as codi,ga01_descripcio_aula as descripcio from ga01_aula","Tria Aula");
    carregaDropGeneric('divTipusFalta', 'SELECT ga22_codi_falta as codi,ga22_nom_falta as descripcio FROM ga22_tipus_falta', 'Tipus de falta');
}

function mostradivaulaSessio(element) {
    $("#butDropaulaSessio").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropaulaSessio").val($(element).attr('data-val'));


}

function mostradivTipusFalta(element) {
    $("#butDropdivTipusFalta").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivTipusFalta").val($(element).attr('data-val'));


}

function seleccionaTot() {

    if ($("#checkMarcaDesmarca").prop("checked") == true) {
        $(".checkEsborrar").prop("checked", true);

    } else {
        $(".checkEsborrar").prop("checked", false);

    }
}

function esborraAlumnes() {


    debugger;
    var fileres = $("#cosTaulaSessio > tr");

    for (var i = 0; i < fileres.length; i++) {
        //obtenim el check d'esborrar
        var cellaCheck = $(fileres[i]).children();
        var check = $(cellaCheck[0]).children();

        if ($(check[0]).prop('checked')) {
            //s'ha d'esborrar
            $(fileres[i]).remove();
        }

    }

    recomptar();
}


function comprovaCheck(element) {

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

    //esbrinem posicions a desmarcar
    if (posCheck1 == 4) {
        var posCheck2 = 5;
        var posCheck3 = 6;
    } else if (posCheck1 == 5) {
        var posCheck2 = 4;
        var posCheck3 = 6;

    } else {
        var posCheck2 = 4;
        var posCheck3 = 5;
    }

    //obtenime els elements a desmarcar
    var taula = document.getElementById('taulaSessio');

    var ele1 = taula.rows[filera + 1].cells[posCheck2].firstChild;
    var ele2 = taula.rows[filera + 1].cells[posCheck3].firstChild;


    if ($(element).prop('checked')) {
        //canviem l'atribut de la resta
        //desmarquem la resta
        $(ele1).prop('checked', false);
        $(ele2).prop('checked', false);


    } else {

        //ens han desmarcat el check per tant el tornem a marcar
        $(element).prop('checked', true);

    }

    //recomptem
    recomptar();
}

function recomptar() {
    var elements = $(".checkAssist");

    var presencia = 0;
    var absencia = 0;
    var retard = 0;

    for (var i = 0; i < elements.length; i++) {
        if ($(elements[i]).prop('checked') === true) {
            //si està seleccionam hem de mirar de quina columna és
            var columna = $(elements[i]).parent();
            if ($(columna).index() === 4) {
                //presencia
                presencia++;
            } else if ($(columna).index() === 5) {
                //absencia
                absencia++;
            } else {
                //retard
                retard++;
            }
        }

    }

    var total = presencia + absencia + retard;

    $("#alumnesTotals").text("Tot: " + total + " - Pres: " + presencia + " - Abs: " + absencia + " - Ret: " + retard);
}


function carregaFaltesSessio(element) {
    //  carregaDropGeneric('divTipusFalta', 'SELECT ga22_codi_falta as codi,ga22_nom_falta as descripcio FROM ga22_tipus_falta', 'Tipus de falta');
    //seleccionem alumne
    debugger;

    var filera = $($(element).parent()).parent();
    var alumne = $(filera).children()[3];

    var tipus = $(alumne).attr('data-tipus');
    var textfalta = $(alumne).attr('data-textfalta');
    var motiuFalta = $(alumne).attr('data-motiu').replace(/&quot;/g, '"');
    var alumneCodi = $(alumne).attr("id");
    var alumneNom = $($(alumne).children()[0]).text();

    //passem l'alumne
    $("#alumneFalta").text(alumneNom);
    $("#alumneFalta").attr("data-codi", alumneCodi);

    //passem les notificacions
    var avisResp = $(alumne).attr('data-avisresponsables');
    var avisTutor = $(alumne).attr('data-avistutor');

    if (avisResp == '0') {
        //s'avisa al tutor
        $("#avisResponsables").prop('checked', false);
    } else {
        $("#avisResponsables").prop('checked', true);

    }

    if (avisTutor == '0') {
        //s'avisa al tutor
        $("#avisTutor").prop('checked', false);
    } else {
        $("#avisTutor").prop('checked', true);

    }

    if (tipus !== "") {

        //es marca la falta
        $("#checkFalta").prop('checked', true);
        //posem al drop el tipus de falta, el motiu i es marca el check conforme hi ha una falta
        $("#butDropdivTipusFalta").text(textfalta);
        $("#butDropdivTipusFalta").val(tipus);
        $("#motiuFalta").val(motiuFalta);
        $("#butDropdivTipusFalta").prop("disabled", false);


    } else {

        $("#butDropdivTipusFalta").prop("disabled", true);
        $("#avisResponsables").prop('checked', true);
        $("#avisTutor").prop('checked', true);

    }


}

function iniDropTipus() {
    //quan es tanca el diàleg modal s'inicialitza el drop de tipus de falta
    $("#butDropdivTipusFalta").html('Tipus de falta' + ' ' + '<span class="caret">');
    $("#butDropdivTipusFalta").val("");
    $("#motiuFalta").val("");
    $("#checkFalta").prop('checked', false);
    $("#butDropdivTipusFalta").prop("disabled", true);

}

function verificaFalta(element) {
    debugger;
    if ($(element).prop('checked') === false) {
        //s'ha desactivat el check eliminem la falta d'ordre
        $("#butDropdivTipusFalta").html('Tipus de falta' + ' ' + '<span class="caret">');
        $("#butDropdivTipusFalta").val("");
        $("#butDropdivTipusFalta").prop("disabled", true);
        $("#motiuFalta").val("");


    } else {
        //s'ha activat el check
        $("#butDropdivTipusFalta").prop("disabled", false);

    }


}


function desaButtonFaltes() {
    debugger;
    //es desen les dades de
    var codiAlumne = $("#alumneFalta").attr("data-codi");
    var alumne = $("#" + codiAlumne);
    //passo els valors a la graella principal de sessions
    var nouTipus = $("#butDropdivTipusFalta").val();
    var nouMotiu = $("#motiuFalta").val().replace(/"/g, "&quot;");
    var nouTextFalta = $("#butDropdivTipusFalta").text();
    if ($("#avisResponsables").prop('checked') == true) {
        var avisResponsables = '1';

    } else {
        var avisResponsables = '0';
    }

    if ($("#avisTutor").prop('checked') == true) {
        var avisTutor = '1';

    } else {
        var avisTutor = '0';
    }

    //tipus de falta, motiu i avisos
    $(alumne).attr("data-tipus", nouTipus);
    $(alumne).attr("data-motiu", nouMotiu);
    $(alumne).attr("data-textfalta", nouTextFalta);
    $(alumne).attr("data-avisresponsables", avisResponsables);
    $(alumne).attr("data-avistutor", avisTutor);


    //faig el mateix que quan tanco sense desar
    $("#butDropdivTipusFalta").html('Tipus de falta' + ' ' + '<span class="caret">');
    $("#butDropdivTipusFalta").val("");
    $("#motiuFalta").val("");
    $("#checkFalta").prop('checked', false);
    $("#butDropdivTipusFalta").prop("disabled", true);

}

function carregaDropGeneric(div, query, caption) {
    var url = "php/carregaDropGeneric.php";
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

function desaSessioHorari() {
    //hem d'agafar totes les dades
    //professor
    var codiProf = $("#profSubsSessio").attr("data-codi");
    //dia
    var dia = $("#diaSessio").val();
    //hora
    var hora = $("#horaSessio").val();
    //es guardia
    if ($("#esGuardia").prop('checked') == true) {
        var esguardia = '1';
    } else {
        var esguardia = '0';
    }

    if ($("#labelEstat").hasClass('btn-danger')) {
        //sessió nova
        var sessioNova = true;
    } else {
        var sessioNova = false;
    }


    //debugger;

    var tipusSubs = $("#labelprofguardia").attr('data-tipus-subs');

    //aula
    $("#butDropaulaSessio").val();
    //nivell
    var nivell = $("#nivellSessio").attr('data-nivell');
    //grup
    var grup = $("#grupSessio").attr('data-grup');
    var tipusGrup = $("#grupSessio").attr('data-tipus');
    //assignatura
    var assignatura = $("#assignaturaSessio").attr('data-assignatura');

    var aula = $("#butDropaulaSessio").val();

    var comentari = $("#editaComentari").attr('data-comentari');
    //i ara les dades dels alumnes
    var fileres = $("#cosTaulaSessio").children();



    var dadesGeneralsSessio = [];

    for (var i = 0; i < fileres.length; i++) {
        var cellaAlumne = $(fileres[i]).children()[3];
        var codiAlumne = $(cellaAlumne).attr('id');
        var tipusFalta = $(cellaAlumne).attr('data-tipus');
        var motiu = $(cellaAlumne).attr('data-motiu');
        var avisResp = $(cellaAlumne).attr('data-avisresponsables');
        var avisTutor = $(cellaAlumne).attr('data-avistutor');
        var comentariAlumne = $(cellaAlumne).attr('data-comentarialumne');
        var enviaComentari = $(cellaAlumne).attr('data-comentariavis');

        var checkPresent = $($(fileres[i]).children()[4]).children()[0];
        var checkAbsent = $($(fileres[i]).children()[5]).children()[0];
        var checkRetard = $($(fileres[i]).children()[6]).children()[0];
        var checkComunica = $($($(fileres[i]).children()[9]).children()[0]).children()[0];


        if ($(checkPresent).prop('checked') == true) {
            var checkPre = '1';
        } else {
            var checkPre = '0';
        }

        if ($(checkAbsent).prop('checked') == true) {
            var checkAbs = '1';
        } else {
            var checkAbs = '0';
        }

        if ($(checkRetard).prop('checked') == true) {
            var checkRet = '1';
        } else {
            var checkRet = '0';
        }

        if ($(checkComunica).prop('checked') == true) {
            var checkCom = '1';
        } else {
            var checkCom = '0';
        }
        debugger;
        if (sessioNova === false) {
            var comentAssistencia = $($(cellaAlumne).children()[1]).attr('data-titol');
        } else {
            var comentAssistencia = '';
        }


        var fileraDades = [];
        fileraDades[0] = codiAlumne;
        fileraDades[1] = tipusFalta;
        fileraDades[2] = motiu;
        fileraDades[3] = avisResp;
        fileraDades[4] = avisTutor;
        fileraDades[5] = checkPre;
        fileraDades[6] = checkAbs;
        fileraDades[7] = checkRet;
        fileraDades[8] = checkCom;
        fileraDades[9] = comentariAlumne;
        fileraDades[10] = enviaComentari;
        fileraDades[11] = comentAssistencia;

        dadesGeneralsSessio[i] = fileraDades.join('<#>');


    }


    //enviem les dades al servidor
    var url = "php/desaSessioHorari.php";

    $.ajax({
        type: "POST",
        url: url,
        data: {"codiProf": codiProf, "dia": dia, "hora": hora, "esguardia": esguardia, "nivell": nivell, "grup": grup, "tipusgrup": tipusGrup, "assignatura": assignatura, "aula": aula, "dadesGeneralsSessio": dadesGeneralsSessio, "tipusSubs": tipusSubs, "comentari": comentari},
        //data: ("#form2").serialize,
        success: function (data) {
            //$("#divTaulaSessio").html(data);
            surtSessioHorari();
        }

    });


    return false;



}

function esborraSessioHorari() {
    //professor
    debugger;
    var pregunta = confirm("N'èstàs segur d'esborra les dades d'assistència i totes les dades associades");

    if (pregunta == true) {

        var codiProf = $("#profSubsSessio").attr("data-codi");
        //dia
        var dia = $("#diaSessio").val();
        //hora
        var hora = $("#horaSessio").val();

        var tipusSubs = $("#labelprofguardia").attr('data-tipus-subs');

        if ($("#esGuardia").prop('checked') == true) {
            var esguardia = '1';
        } else {
            var esguardia = '0';
        }

        var url = "php/esborraSessioHorari.php";
        debugger;
        $.ajax({
            type: "POST",
            url: url,
            data: {"codiProf": codiProf, "dia": dia, "hora": hora, "esguardia": esguardia, "tipusSubs": tipusSubs},
            //data: ("#form2").serialize,
            success: function (data) {
                //$("#divTaulaSessio").html(data);
                surtSessioHorari();
            }

        });


        return false;
    }

}

function surtSessioHorari() {
    window.open('main.html', '_self');

}

function editaComentari() {
    var comentari = $("#editaComentari").attr('data-comentari').replace(/&quot;/g, '"');
    //el posem al text area
    $("#commentari").val(comentari);

}

function desaButtonComentari() {
    //guardem el nou comentari
    $("#editaComentari").attr('data-comentari', $("#commentari").val().replace(/"/g, "&quot;"));


}


async function mostraFitxaAlumne(element) {
    debugger;

    var altWindow = parseInt(window.screen.availHeight * 2 / 3);
    var ampleWindow = parseInt(window.screen.availWidth * 2 / 3);

    var urlPhp = "php/creaFitxaAlumne.php";

    var alumne = $($(element).parent()).attr('id').substring(2);
    var professor = $("#dadesCredencials").attr('data-codi-prof');
    var urlXml = "xml/prof" + professor + "al" + alumne + ".xml";

    //var urlXml = "xml/fitxaAlumne.xml";

    $.get(urlPhp + "?alumne=" + alumne);
    await sleep(2000);
    //window.open(urlXml, "_self", "toolbar=no,scrollbars=yes,resizable=yes,width=" + screen.width + ",height=" + screen.height);
    var finestra = window.open(urlXml, "", "width=" + ampleWindow + ",height=" + altWindow);
    finestra.focus();

}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function desaComentariAlumne() {
    //desem el comentari


    if ($("#totsAlumnesComent").prop('checked') === false) {
        //el comentari és només per un alumne
        var alumne = $("#alumneComentCap").attr('data-codicomentcap');
        var comentari = $("#commentariAlumne").val().replace(/"/g, "&quot;");

        $("#" + alumne).attr('data-comentarialumne', comentari);

        if ($("#avisRespComent").prop('checked') === true) {
            $("#" + alumne).attr('data-comentariavis', '1');
        } else {
            $("#" + alumne).attr('data-comentariavis', '0');
        }
    } else {
        //el comentari és per tots els alumnes
        var fileres = $("#cosTaulaSessio").children();
        debugger;
        var comentari = $("#commentariAlumne").val().replace(/"/g, "&quot;");
        for (var i = 0; i < fileres.length; i++) {

            var codiAlumne = $($(fileres[i]).children()[3]).attr('id');
            $("#" + codiAlumne).attr('data-comentarialumne', comentari);
            if ($("#avisRespComent").prop('checked') === true) {
                $("#" + codiAlumne).attr('data-comentariavis', '1');
            } else {
                $("#" + codiAlumne).attr('data-comentariavis', '0');
            }


        }

    }

}


function carregaComentAlumne(element) {

    //a tots els alumnes desabilitat
    $("#totsAlumnesComent").prop('checked', false);

    //passem les dades
    var filera = $($(element).parent()).parent();
    var alumne = $(filera).children()[3];
    var alumneCodi = $(alumne).attr("id");
    var comentariAlumne = $(alumne).attr('data-comentarialumne').replace(/&quot;/g, '"');
    ;
    var enviaComentSwitch = $(alumne).attr('data-comentariavis');
    var alumneNom = $($(alumne).children()[0]).text();


    $("#alumneComentCap").text(alumneNom);
    $("#alumneComentCap").attr('data-codiComentCap', alumneCodi);
    $("#commentariAlumne").val(comentariAlumne);

    if (enviaComentSwitch == "1") {
        //marquem el checkbox
        $("#avisRespComent").prop('checked', true);
    } else {
        //no el marquem
        $("#avisRespComent").prop('checked', false);
    }

}

function carregaSessionsAnteriors(element) {

    //passem l'alumne
    $("#alumneAbsAnteriors").text($($(element).siblings()[0]).text());

    //agafem les sessions anteriors
    var sessionsAnteriors = $(element).attr('data-sessions-anteriors');

    var sessionsAnteriorsArray = sessionsAnteriors.split('<#>');



    var taula = '<table class="table">';
    taula += '<thead>';
    taula += '<tr>';
    taula += '<th class="col-sm-2">Hora</td>';
    taula += '<th class="col-sm-4">Assignatura</td>';
    taula += '<th class="col-sm-4">Professsor</td>';
    taula += '</tr>';
    taula += '</thead>';

    taula += '<tbody>';
    for (var i = 0; i < sessionsAnteriorsArray.length; i++) {
        var sessionsAnteriorsElements = sessionsAnteriorsArray[i].split('<%>');
        taula += '<tr>';
        taula += '<td class="col-sm-2">' + sessionsAnteriorsElements[0] + '</td>';
        taula += '<td class="col-sm-4">' + sessionsAnteriorsElements[1] + '</td>';
        taula += '<td class="col-sm-4">' + sessionsAnteriorsElements[2] + '</td>';
        taula += '</tr>';


    }
    taula += '</tbody>';
    taula += '</table>';


    $('#sessionsAnteriorsDiv').html(taula);

}