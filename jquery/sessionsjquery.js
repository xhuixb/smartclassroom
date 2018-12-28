/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function () {
    $('#dataAssist').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });
});

//controlem la fletxa del collapse
$(document).on("show.bs.collapse", ".collapse", function (event) {
    $("#collapseState").html('<span class="glyphicon glyphicon-menu-up"></span>');
    event.stopPropagation();
});

$(document).on("hide.bs.collapse", ".collapse", function (event) {
    $("#collapseState").html('<span class="glyphicon glyphicon-menu-down"></span>');
    event.stopPropagation();
});


$(document).ready(function () {

    $("#drophoraDropdown a").click(function () {
        debugger;
        //$("#pprovasessio").text('ximplet');
        $("#butDrophoraDropdown").text($(this).text());
        $("#butDrophoraDropdown").val($(this).attr('data-val'));

    });

});

function mostradivTipusFalta(element) {

    $("#butDropdivTipusFalta").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivTipusFalta").val($(element).attr('data-val'));
}

function mostragrupDropdownProfSubst(element) {
    $("#butDropgrupDropdownProfSubst").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropgrupDropdownProfSubst").val($(element).attr('data-val'));

}

function mostragrupDropdownSuport(element) {
    //carrega els alumnes del nivell i grup

    $("#butDropgrupDropdownSuport").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropgrupDropdownSuport").val($(element).attr('data-val'));

    //enviem les dades per ajax per a obtener els alumnes del nivell grup
    var nivell = $("#butDropnivellDropdown").val();
    var nivellText = $("#butDropnivellDropdown").text();
    var grup = $("#butDropgrupDropdownSuport").val();
    var grupText = $("#butDropgrupDropdownSuport").text();

    var url = "php/cercaAlumnesGrup.php";

    //obtenim els codis dels alumnes
    var fileres = $("#cosTaulaAssist").children();
    var alumnes = [];
    var hihaAlumnes = false;


    for (var i = 0; i < fileres.length; i++) {
        var celles = $(fileres[i]).children();
        alumnes[i] = $(celles[3]).attr('id').substring(2);
        hihaAlumnes = true;

    }

    if (hihaAlumnes == false) {

        alumnes[0] = "";
    }


    debugger;

    $.ajax({
        type: "POST",
        url: url,
        data: {"nivell": nivell, "grup": grup, "grupText": grupText, "alumnes": alumnes, "nivellText": nivellText},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divTaulaAssistGrup").html(data);

        }

    });


    return false;

}


function mostrahoraDropdown(element) {

    //anem a veure si està desabilitat
    var pareElement = $(element).parent();

    if ($(pareElement).hasClass('disabled') == false) {

        $("#butDrophoraDropdown").html($(element).text() + ' ' + '<span class="caret">');
        $("#butDrophoraDropdown").val($(element).attr('data-val'));
        // $("#pprovasessio").text($("#butDropnivellDropdown").val() + '-' + $("#butDropnivellDropdown").text());
        habilitaDesa(element);

    }
}

function mostranivellDropdown(element) {
    $("#butDropnivellDropdown").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropnivellDropdown").val($(element).attr('data-val'));

    debugger;

    var nivell = $(element).attr('data-val');
    habilitaDesa(element);
    if (nivell != '') {

        var profe = $("#profSubstTriat").attr("data-codi");

        //anem a buscar els grups
        $('#butDropgrupDropdown').prop('disabled', false);
        //carregaDropGrupsGrupsProfes('grupDropdown','Tria grup')
        //anem a buscar els grups d'aquest nivell
        var url = "php/cercaGrupsProfes.php";
        debugger;
        $.ajax({
            type: "POST",
            url: url,
            data: {"nivell": nivell, "profe": profe},
            //data: ("#form2").serialize,
            success: function (data) {
                $("#grupDropdown").html(data);

            }

        });


        return false;


    }




}

function cercaGrupsNivell(div, nivell) {
    var url = "php/cercaGrupsNivell.php";
    debugger;
    $.ajax({
        type: "POST",
        url: url,
        data: {"div": div, "nivell": nivell},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#" + div).html(data);

        }

    });


    return false;


}

function cercaAssigNivell(div, nivell) {
    var url = "php/cercaAssigNivell.php";
    debugger;
    $.ajax({
        type: "POST",
        url: url,
        data: {"div": div, "nivell": nivell},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#" + div).html(data);

        }

    });


    return false;


}




function mostragrupDropdown(element) {
    $("#butDropgrupDropdown").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropgrupDropdown").val($(element).attr('data-val'));
    //agafem el tipus de grup de l'element que es selecciona
    var tipusgrup = $(element).attr('data-tipusgrup');

    $("#butDropgrupDropdown").attr('data-tipusgrup', tipusgrup);

    // $("#pprovasessio").text($("#butDropnivellDropdown").val() + '-' + $("#butDropnivellDropdown").text());
    habilitaDesa(element);

}

function habilitaDesa(element) {

    debugger;

    if ($("#butDropnivellDropdown").val() != '' && $("#butDropgrupDropdown").val() != '' && $("#butDrophoraDropdown").val() != '' && $("#dataAssist").val() != '') {
        $("#cercaAssist").prop('disabled', false);
    } else {
        $("#cercaAssist").prop('disabled', true);
    }
    $("#taulaAssistGrup").remove();
    $("#taulaAssist").remove();
    $("#divTaulaAssitSuport").prop('hidden', true);
    $("#desaAssist").prop('disabled', true);
    $("#actualitzacio").val("");
    // $("#profSubstTriat").val("");
    //$("#profSubstTriat").attr('data-codi','');
    //$("#isGuardiaCheck").prop('checked',false);




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



function carregaDropHoraris(div) {
    var url = "php/carregaDropHoraris.php";
    debugger;
    $.ajax({
        type: "POST",
        url: url,
        data: {"div": div},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#" + div).html(data);

        }

    });


    return false;



}


function carregaDropGenericCheck(div, query, caption) {
    var url = "php/carregaDropGenericCheck.php";
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



function carregaDropGrupsGrupsProfes(div, caption) {

    var url = "php/carregaDropGrupsGrupsProfes.php";
    debugger;
    $.ajax({
        type: "POST",
        url: url,
        data: {"div": div, "caption": caption},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#" + div).html(data);

        }

    });


    return false;


}


function carregaDropGrupsGrupsProfesSubs(div, caption) {

    var url = "php/carregaDropGrupsGrupsProfesSubs.php";

    var profSubs = $("#profSubstTriat").attr("data-codi");

    debugger;
    $.ajax({
        type: "POST",
        url: url,
        data: {"div": div, "caption": caption, "profSubs": profSubs},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#" + div).html(data);

        }

    });


    return false;


}

$(document).ready(function () {

    $("#cercaAssist").click(function () {

        var url = "php/cercaAssist.php";

        var nivell = $("#butDropnivellDropdown").val();
        var grup = $("#butDropgrupDropdown").val();
        var tipusgrup = $("#butDropgrupDropdown").attr('data-tipusgrup');
        var hora = $("#butDrophoraDropdown").val();
        var dia = $("#dataAssist").val();
        var from = dia.split("/");

        var f = from[1] + "/" + from[0] + "/" + from[2];

        //carreguem el combo de les incidències

        carregaDropGeneric('divTipusFalta', 'SELECT ga22_codi_falta as codi,ga22_nom_falta as descripcio FROM ga22_tipus_falta', 'Tipus de falta');

        $("#butDropdivTipusFalta").prop("disabled", true);
        debugger;

        $.ajax({
            type: "POST",
            url: url,
            data: {"nivell": nivell, "grup": grup, "tipusgrup": tipusgrup, "hora": hora, "dia": f},
            //data: ("#form2").serialize,
            success: function (data) {
                $("#divTaulaAssist").html(data);

                var x = document.getElementById('cosTaulaAssist').getAttribute('data-modifi');
                document.getElementById('actualitzacio').value = x;
                $("#desaAssist").prop('disabled', false);
                $("#divTaulaAssitSuport").prop('hidden', false);
                $("#taulaAssistGrup").remove();



               /* if ($("#taulaAssist").attr('data-nou') == "0" && $("#taulaAssist").attr('data-codi-prof-subs') != "") {
                    //marquem el check
                    $("#isGuardiaCheck").prop('checked', true);
                    //posem el prof substitut si n'hi ha
                    $("#profSubstTriat").attr('data-codi', $("#taulaAssist").attr('data-codi-prof-subs'));
                    $("#profSubstTriat").val($("#taulaAssist").attr('data-nom-prof-subs'));

                }*/

            }

        });

        debugger;


        //$("#actualitzacio").val($("#cosTaulaAssist").attr('data-modifi'));

        return false;

    });
});


$(document).ready(function () {

    $("#desaAssist").click(function () {

        var url = "php/desaAssist.php";

        debugger;
        var nivell = $("#butDropnivellDropdown").val();
        var grup = $("#butDropgrupDropdown").val();
        var hora = $("#butDrophoraDropdown").val();
        var dia = $("#dataAssist").val();

        var nivellNom = $("#butDropnivellDropdown").text();
        var grupNom = $("#butDropgrupDropdown").text();
        var nomProfe = $("#dadesCredencials").text();

        debugger;
        var from = dia.split("/");

        if ($("#isGuardiaCheck").prop('checked')) {
            //és una guàrdia
            var profSubstituit = $("#profSubstTriat").attr('data-codi');

        } else {
            var profSubstituit = "";

        }

        var f = from[1] + "/" + from[0] + "/" + from[2];

        var alumnes = [];
        var faltes = [];
        var faltesmotius = [];
        var checkPres = [];
        var checkAbs = [];
        var checkRet = [];
        var avisResponsables = [];
        var avisTutors = [];
        var alumnesNom = [];
        var tipusFaltaNom = [];
        var checkComunica = [];


        var taula = document.getElementById('taulaAssist');
        for (var r = 1, n = taula.rows.length; r < n; r++) {
            //la filera 0 és de la capçalera i no interessa
            //agafem l'alumne
            debugger;
            alumnes[r - 1] = $(taula.rows[r].cells[3]).attr('id').substring(2);
            faltes[r - 1] = $(taula.rows[r].cells[3]).attr('data-tipus');
            faltesmotius[r - 1] = $(taula.rows[r].cells[3]).attr('data-motiu');
            avisResponsables[r - 1] = $(taula.rows[r].cells[3]).attr('data-avisresponsables');
            avisTutors[r - 1] = $(taula.rows[r].cells[3]).attr('data-avistutor');
            alumnesNom[r - 1] = $(taula.rows[r].cells[3]).text();
            tipusFaltaNom[r - 1] = $(taula.rows[r].cells[3]).attr('data-textfalta');

            //agafem el check present
            var checkPresEl = $($(taula.rows[r].cells[4]).children()).first();
            if ($(checkPresEl).prop('checked'))
                checkPres[r - 1] = '1';
            else
                checkPres[r - 1] = '0';


            //agafem el check absent    
            var checkAbsEl = $($(taula.rows[r].cells[5]).children()).first();
            if ($(checkAbsEl).prop('checked'))
                checkAbs[r - 1] = '1';
            else
                checkAbs[r - 1] = '0';

            //agafem el check retard
            var checkRetEl = $($(taula.rows[r].cells[6]).children()).first();
            if ($(checkRetEl).prop('checked'))
                checkRet[r - 1] = '1';
            else
                checkRet[r - 1] = '0';


            //agefem el check comunicacio
            var checkComuniEl = $($(taula.rows[r].cells[8]).children()).first();
            if ($(checkComuniEl).prop('checked'))
                checkComunica[r - 1] = '1';
            else
                checkComunica[r - 1] = '0';


        }
        debugger;


        $.ajax({
            type: "POST",
            url: url,
            data: {"nivell": nivell, "grup": grup, "hora": hora, "dia": f, "alumnes": alumnes, "checkPres": checkPres, "checkAbs": checkAbs, "checkRet": checkRet,
                "faltes": faltes, "faltesmotius": faltesmotius, "profSubstituit": profSubstituit, "avisResponsables": avisResponsables, "avisTutors": avisTutors,
                "nivellNom": nivellNom, "grupNom": grupNom, "nomProfe": nomProfe, "alumnesNom": alumnesNom, "tipusFaltaNom": tipusFaltaNom, "checkComunica": checkComunica},
            //data: ("#form2").serialize,
            success: function (data) {
                $("#divTaulaAssist").html(data);
                //amaguem altres grups i esborrem taula
                $("#taulaAssistGrup").remove();
                $("#divTaulaAssitSuport").prop('hidden', true);
                $("#desaAssist").prop('disabled', true);
                $("#profSubstTriat").attr('data-codi', '');
                $("#isGuardiaCheck").prop('checked', false);
                $("#profSubstTriat").val("");

            }

        });
        return false;

    });
});

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
    var taula = document.getElementById('taulaAssist');

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

}


//esborra els alumnes que s'han marcat del control d'assistència

function esborraAlumnes() {


    debugger;
    var fileres = $("#cosTaulaAssist > tr");

    for (var i = 0; i < fileres.length; i++) {
        //obtenim el check d'esborrar
        var cellaCheck = $(fileres[i]).children();
        var check = $(cellaCheck[0]).children();

        if ($(check[0]).prop('checked')) {
            //s'ha d'esborrar
            $(fileres[i]).remove();
        }

    }

    //si hi ha algun grup seleccionat a la dreta tornem a llençar la cerca
    remostragrupDropdownSuport();
}

function remostragrupDropdownSuport() {
    //recarrega els alumnes del nivell i grup
    debugger;
    //enviem les dades per ajax per a obtener els alumnes del nivell grup
    var nivell = $("#butDropnivellDropdown").val();
    var grup = $("#butDropgrupDropdownSuport").val();
    var grupText = $("#butDropgrupDropdownSuport").text();
    var nivellText = $("#butDropnivellDropdown").text();

    if (grup != "") {

        var url = "php/cercaAlumnesGrup.php";

        //obtenim els codis dels alumnes
        var fileres = $("#cosTaulaAssist").children();
        var alumnes = [];
        var hihaAlumnes = false;


        for (var i = 0; i < fileres.length; i++) {
            var celles = $(fileres[i]).children();
            alumnes[i] = $(celles[3]).attr('id').substring(2);
            hihaAlumnes = true;

        }

        if (hihaAlumnes == false) {
            alumnes[0] = "";

        }




        $.ajax({
            type: "POST",
            url: url,
            data: {"nivell": nivell, "grup": grup, "grupText": grupText, "alumnes": alumnes, "nivellText": nivellText},
            //data: ("#form2").serialize,
            success: function (data) {
                $("#divTaulaAssistGrup").html(data);

            }

        });


        return false;
    }
}


function passaAlumnes(element) {

    var fileres = $("#cosTaulaAssistGrup > tr");

    for (var i = 0; i < fileres.length; i++) {
        //obtenim el check d'esborrar
        var cellaCheck = $(fileres[i]).children();
        var check = $(cellaCheck[0]).children();

        if ($(check[0]).prop('checked')) {
            debugger;
            $(check[0]).prop('checked', false);
            //s'ha d'esborrar de la taula de la dreta
            var fileraNova = fileres[i];

            var comunica = $(fileraNova).attr('data-comunica');

            $(fileres[i]).remove();
            //afegirla a la taula de l'esquerra
            //completem la filera
            var cela1 = document.createElement("td");
            var cela2 = document.createElement("td");
            var cela3 = document.createElement("td");
            var cela4 = document.createElement("td");
            var cela5 = document.createElement("td");

            var cella41 = document.createElement("button");
            cella41.setAttribute("class", "btn form-control");
            cella41.setAttribute("data-toggle", "modal");
            cella41.setAttribute("data-target", "#faltesModalForm");
            cella41.setAttribute("onclick", "carregaFaltes(this);");
            $(cella41).html('<span class="glyphicon glyphicon-pencil"></span>CCC');


            var check1 = document.createElement("input");
            var check2 = document.createElement("input");
            var check3 = document.createElement("input");
            var check4 = document.createElement("input");

            check1.setAttribute("type", "checkbox");
            check1.setAttribute("class", "checkAssist");
            check1.setAttribute("onchange", "comprovaCheck(this);");
            $(check1).prop("checked", true);
            //check1.setAttribute("checked", true);

            check2.setAttribute("type", "checkbox");
            check2.setAttribute("class", "checkAssist");
            check2.setAttribute("onchange", "comprovaCheck(this);");

            check3.setAttribute("type", "checkbox");
            check3.setAttribute("class", "checkAssist");
            check3.setAttribute("onchange", "comprovaCheck(this);");





            check4.setAttribute("type", "checkbox");
            check4.setAttribute("disabled", true);


            if (comunica == '0') {
                $(check4).prop("checked", false);
            } else {
                $(check4).prop("checked", true);
            }


            cela1.appendChild(check1);
            cela2.appendChild(check2);
            cela3.appendChild(check3);
            cela4.appendChild(cella41);
            cela5.appendChild(check4);

            fileraNova.append(cela1);
            fileraNova.append(cela2);
            fileraNova.append(cela3);
            fileraNova.append(cela4);
            fileraNova.append(cela5);

            $("#cosTaulaAssist").append(fileraNova);

        }

    }

}

function carregaFaltes(element) {
    //  carregaDropGeneric('divTipusFalta', 'SELECT ga22_codi_falta as codi,ga22_nom_falta as descripcio FROM ga22_tipus_falta', 'Tipus de falta');
    //seleccionem alumne
    //debugger;

    var filera = $($(element).parent()).parent();
    var alumne = $(filera).children()[3];

    var tipus = $(alumne).attr('data-tipus');
    var textfalta = $(alumne).attr('data-textfalta');
    var motiuFalta = $(alumne).attr('data-motiu').replace(/&quot;/g, '"');
    var alumneCodi = $(alumne).attr("id");
    var alumneNom = $(alumne).text();

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

//desem la incidència

$(document).ready(function () {

    $("#desaButtonFaltes").click(function () {


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


    });
});

function carregaDadesIncialsSessio() {
    //tab de sessions
    carregaDropGeneric('nivellDropdown', 'SELECT distinct(ga35_nivell) as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga35_nivell=ga06_codi_nivell', 'Tria Nivell');

    carregaDropHoraris('horaDropdown');
    //carregaDropGeneric('horaDropdown', "SELECT ga10_hora_inici as codi, concat('de ',ga10_hora_inici,' a ',ga10_hora_fi) as descripcio from ga10_horaris_aula", 'Tram horari');

    carregaDropGeneric('grupDropdownSuport', 'SELECT ga07_codi_grup as codi, ga07_descripcio_grup as descripcio FROM ga07_grup', 'Altres Grups');
    carregaDropGeneric('grupDropdownProfSubst', "select ga17_codi_professor as codi,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio from ga04_professors,ga17_professors_curs where ga04_codi_prof=ga17_codi_professor and ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga04_suspes='0' order by descripcio", "Tria Professor");
    //tab de faltes



}


function cercaMevesSessions() {

    var url = "php/cercaMevesSessions.php";
    debugger;
    $.ajax({
        type: "POST",
        url: url,
        data: {},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divTaulaMevesSessions").html(data);

        }

    });

    return false;

}



function seleccionaTot() {

    if ($("#checkMarcaDesmarca").prop("checked") == true) {
        $(".checkEsborrar").prop("checked", true);

    } else {
        $(".checkEsborrar").prop("checked", false);

    }
}

function obreModalProfSubs() {
    debugger;
    if ($("#isGuardiaCheck").prop('checked')) {
        //si es marca guàrdia s'obre el diàleg modal per a triar el professor
        jQuery.noConflict();
        $('#profSubstModal').modal('show');

    } else {


        //es neteja el camp de text del professor
        $("#profSubstTriat").val("");
        $("#profSubstTriat").attr("data-codi", "");
        //torna a posar els grups del professor titular
        //cercaGrupsNivell('grupDropdown')
        //carregaDropGrupsGrupsProfes('grupDropdown', 'Tria Grup');
        //restaurem l'estat inicial del nivell
        $("#butDropnivellDropdown").html('Tria Nivell' + ' ' + '<span class="caret">');
        $("#butDropnivellDropdown").val('');
        $("#butDropgrupDropdown").prop('disabled', true);
        $("#cercaAssist").prop('disabled', true);
        $("#desaAssist").prop('disabled', true);

    }


}

function triaProfGuardia() {

    //posem les dades al text box
    $("#profSubstTriat").val($("#butDropgrupDropdownProfSubst").text());
    $("#profSubstTriat").attr("data-codi", $("#butDropgrupDropdownProfSubst").val());

    //var nivell=$("#butDropgrupDropdownProfSubst").val();

    //metegem el combobox
    $("#butDropgrupDropdownProfSubst").html('Tria Professor' + ' ' + '<span class="caret">');
    $("#butDropgrupDropdownProfSubst").val('');

    //carreguem els grups del profe substituït al dropdown de grups
    //cercaGrupsNivell('grupDropdown',nivell);
    //carregaDropGrupsGrupsProfesSubs('grupDropdown', 'Tria Grup');
    //restaurem l'estat inicial del nivell
    $("#butDropnivellDropdown").html('Tria Nivell' + ' ' + '<span class="caret">');
    $("#butDropnivellDropdown").val('');
    //desabilitem la cerca
    $("#cercaAssist").prop('disabled', true);
    $("#desaAssist").prop('disabled', true);



}

function renunciaGuardia() {
    //desmarquem check

    //$(isGuardiaCheck).prop('checked', false);


}

function mostraImatgeSessio(element) {

    //obtenim el codi de l'alumne
    var filera = $(element).parent();
    var codi = $(filera).attr('id');

    var codiAlumne = codi.substring(2);
    //obtenim el nom de l'alumne

    var nomComplet = $(element).text();

    //anem a buscar la imatge

    var url = "php/cercaImatgeAlumneBasica.php";

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
            //$("#codiImatgeAlumne").attr("src", $("#codiImatgeAlumne").attr("src") + "?" + new Date().getTime());

        }

    });

    return false;

}

