/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function carregaTaulaAuxiliar(taula, camps, amplades, caption, div, campPrimari, descr, tipusCamp, dadesForana, campsObligatoris, campsCondicio, valorsCondicio, ordenacio) {

    debugger;

    $("#" + div).attr('data-taula', taula);
    $("#" + div).attr('data-camps', camps);
    $("#" + div).attr('data-amplades', amplades);
    $("#" + div).attr('data-caption', caption);
    $("#" + div).attr('data-campPrimari', campPrimari);
    $("#" + div).attr('data-descr', descr);
    $("#" + div).attr('data-tipusCamp', tipusCamp);

    $("#" + div).attr('data-dadesForana', dadesForana);
    $("#" + div).attr('data-campsObligatoris', campsObligatoris);
    $("#" + div).attr('data-campsCondicio', campsCondicio);
    $("#" + div).attr('data-valorsCondicio', valorsCondicio);
    $("#" + div).attr('data-ordenacio', ordenacio);


    var url = "php/gestionaTaulaAuxiliar.php";
    var opcio = '0';


    $.ajax({
        type: "POST",
        url: url,
        data: {"taula": taula, "camps": camps, "amplades": amplades, "caption": caption,
            "campPrimari": campPrimari, "descr": descr, "tipusCamp": tipusCamp, "dadesForana": dadesForana, "div": div, "opcio": opcio, "campsCondicio": campsCondicio, "valorsCondicio": valorsCondicio, "ordenacio": ordenacio},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#" + div).html(data);

        }

    });

    return false;

}

function nouItem(taula, caption) {

    $("#titolTaula").text("Alta d'element: " + caption);
    $("#titolTaula").attr('data-taula', taula);
    $("#titolTaula").attr('data-mode', '0');
    debugger;
    //anem a buscar els camps 
    var div = $($($($("#taula" + taula).parent()).parent()).parent()).attr('id');
    var campsRep = $("#" + div).attr('data-camps').split(',');
    var captionRep = $("#" + div).attr('data-caption').split(',');
    var tipusCamp = $("#" + div).attr('data-tipusCamp').split(',');
    var dadesForana = $("#" + div).attr('data-dadesForana').split(',');


    var totsCamps = '';
    //comencem per 1 perquè el primer camp és la clau primària que no es crea ni es modifica
    var numForana = 0;

    for (var i = 1; i < campsRep.length; i++) {
        var nomCamp = campsRep[i];
        var captionCamp = captionRep[i];
        if (tipusCamp[i] === '0') {
            //camp de text
            totsCamps += '<label>' + captionCamp + '</label><input type="text" class="form-control relCamps" data-camp="' + nomCamp + '" value="" onkeyup="comprovaCamps(&#39;' + div + '&#39;,&#39;relacioCamps&#39;);">';
        } else if (tipusCamp[i] === '1') {
            //checkBox
            totsCamps += '<br><strong>' + captionCamp + ':</strong> <input type="checkbox" class="relCamps" data-camp="' + nomCamp + '" onchange="comprovaCamps(&#39;' + div + '&#39;,&#39;relacioCamps&#39;);"><br>';

        } else if (tipusCamp[i] === '2') {
            //creem el div després afegirem els drop
            var detallForana = dadesForana[numForana].split('<#>');
            totsCamps += '<div id="div' + detallForana[0].replace(/%2C/g, ",") + '" class="divForanes relCamps" data-camp="' + nomCamp + '" data-caption="' + captionCamp + '"></div>';
            numForana++;

        } else if (tipusCamp[i] === '3') {
            totsCamps += '<label>' + captionCamp + '</label><input type="text" class="form-control relCamps datePanellControl" readonly data-camp="' + nomCamp + '" value="" onkeyup="comprovaCamps(&#39;' + div + '&#39;,&#39;relacioCamps&#39;);">';
        }
    }

    $("#relacioCamps").html(totsCamps);

    //omplim els drop
    var divDrops = $(".divForanes");

    for (var i = 0; i < divDrops.length; i++) {

        var detallForana = dadesForana[i].split('<#>');
        var foranaTaula = detallForana[0].replace(/%2C/g, ",");
        var foranaId = detallForana[1].replace(/%2C/g, ",");
        var foranaCamp = detallForana[2].replace(/%2C/g, ",");
        var codiDiv = $(divDrops[i]).attr('id');
        var caption = $(divDrops[i]).attr('data-caption');
        var esOblitagori = $(divDrops[i]).attr('data-obligatori');

        construeixDetallDrop(codiDiv, foranaTaula, foranaId, foranaCamp, caption, '', '', div);
    }
    activaDatePicker();



}

function editaItem(taula, caption, element) {

    debugger;

    $("#titolTaula").text("Edició d'element: " + caption);
    $("#titolTaula").attr('data-taula', taula);
    $("#titolTaula").attr('data-mode', '1');
    $("#titolTaula").attr('data-clau-primaria', $($($($(element).parent()).parent()).children()[1]).text());
    //guardem el div d'on penja tot


    //anem a buscar els camps 
    var div = $($($($("#taula" + taula).parent()).parent()).parent()).attr('id');
    var campsRep = $("#" + div).attr('data-camps').split(',');
    var captionRep = $("#" + div).attr('data-caption').split(',');
    var tipusCamp = $("#" + div).attr('data-tipusCamp').split(',');
    var dadesForana = $("#" + div).attr('data-dadesForana').split(',');


    var totsCamps = '';
    //comencem per 1 perquè el primer camp és la clau primària que no es crea ni es modifica
    var numForana = 0;

    for (var i = 1; i < campsRep.length; i++) {
        var nomCamp = campsRep[i];
        var captionCamp = captionRep[i];
        if (tipusCamp[i] === '0') {
            //camp de text
            //i+1 perquè el primer valor que agafem és el tercer 0 check i 1 1 clau primària
            var dadaCamp = $($($($(element).parent()).parent()).children()[i + 1]).text();
            totsCamps += '<label>' + captionCamp + '</label><input type="text" class="form-control relCamps" data-camp="' + nomCamp + '" value="' + dadaCamp.replace(/"/g, "&quot;") + '" onkeyup="comprovaCamps(&#39;' + div + '&#39;,&#39;relacioCamps&#39;);">';
        } else if (tipusCamp[i] === '1') {
            //esbrinem si el camp està marcat
            var esChecked = $($($($($($(element).parent()).parent()).children()[i + 1]).children()[0]).children()[0]).prop('checked');

            if (esChecked === true) {
                totsCamps += '<br><strong>' + captionCamp + ':</strong> <input type="checkbox" class="relCamps" data-camp="' + nomCamp + '" checked onchange="comprovaCamps(&#39;' + div + '&#39;,&#39;relacioCamps&#39;);"><br>';
            } else {
                totsCamps += '<br><strong>' + captionCamp + ':</strong> <input type="checkbox" class="relCamps" data-camp="' + nomCamp + '" onchange="comprovaCamps(&#39;' + div + '&#39;,&#39;relacioCamps&#39;);"><br>';
            }

        } else if (tipusCamp[i] === '2') {
            //creem el div després afegirem els drop
            //recuperem codi i nom
            var codiCamp = $($($($(element).parent()).parent()).children()[i + 1]).attr('data-codiforana');
            var dadaCamp = $($($($(element).parent()).parent()).children()[i + 1]).text();
            var detallForana = dadesForana[numForana].split('<#>');
            totsCamps += '<div id="div' + detallForana[0].replace(/%2C/g, ",") + '" class="divForanes relCamps" data-camp="' + nomCamp + '" data-caption="' + captionCamp + '" data-codicamp="' + codiCamp + '" data-dadacamp="' + dadaCamp + '"></div>';
            numForana++;

        } else if (tipusCamp[i] === '3') {
            var dadaCamp = $($($($(element).parent()).parent()).children()[i + 1]).text();
            totsCamps += '<label>' + captionCamp + '</label><input type="text" class="form-control relCamps datePanellControl" readonly data-camp="' + nomCamp + '" value="' + dadaCamp.replace(/"/g, "&quot;") + '" onkeyup="comprovaCamps(&#39;' + div + '&#39;,&#39;relacioCamps&#39;);">';
        }



    }

    $("#relacioCamps").html(totsCamps);

    //omplim els drop
    var divDrops = $(".divForanes");

    for (var i = 0; i < divDrops.length; i++) {
        var codiCamp = '';
        var dadaCamp = '';
        var detallForana = dadesForana[i].split('<#>');
        var foranaTaula = detallForana[0].replace(/%2C/g, ",");
        var foranaId = detallForana[1].replace(/%2C/g, ",");
        var foranaCamp = detallForana[2].replace(/%2C/g, ",");
        var codiDiv = $(divDrops[i]).attr('id');
        var caption = $(divDrops[i]).attr('data-caption');
        var esOblitagori = $(divDrops[i]).attr('data-obligatori');
        if ($(divDrops[i]).attr('data-codicamp') !== '') {
            codiCamp = $(divDrops[i]).attr('data-codicamp');
            dadaCamp = $(divDrops[i]).attr('data-dadacamp');
        }

        debugger;
        construeixDetallDrop(codiDiv, foranaTaula, foranaId, foranaCamp, caption, codiCamp, dadaCamp, div);
    }
    activaDatePicker();

}



function desaTaulaAuxiliar() {
    //agafem el mode
    var mode = $("#titolTaula").attr('data-mode');
    //agafem la taula on fer la inserció o la modificació
    var taula = $("#titolTaula").attr('data-taula');
    //agafem els camps i els valors
    var campsItems = $(".relCamps");

    var camps = [];
    var valors = [];


    for (var i = 0; i < campsItems.length; i++) {
        camps[i] = $(campsItems[i]).attr('data-camp');
        if ($(campsItems[i]).attr('type') === 'checkbox') {
            //desem un checkbox
            if ($(campsItems[i]).prop('checked') === true) {
                valors[i] = '1';
            } else {
                valors[i] = '0';
            }

        } else if ($(campsItems[i]).hasClass('divForanes')) {
            //desem dropbox
            valors[i] = $($($(campsItems[i]).children()[1]).children()[0]).val();
            if (valors[i] === '') {
                valors[i] = 'null';
            }
        } else if ($(campsItems[i]).hasClass('datePanellControl')) {
            //camp lliure date
            //girem la data
            valors[i] = $(campsItems[i]).val().substring(6) + '-' + $(campsItems[i]).val().substring(3, 5) + '-' + $(campsItems[i]).val().substring(0, 2);

        } else {
            //desem camp lliure

            valors[i] = $(campsItems[i]).val();
            //valors[i] = $(campsItems[i]).val().replace("'", "&#39;");

        }
    }
    debugger;

    //anem a buscar el div
    var div = $($($($("#taula" + taula).parent()).parent()).parent()).attr('id');
    //anem a buscar les dades per repetir la cerca
    var taulaRep = $("#" + div).attr('data-taula');
    var campsRep = $("#" + div).attr('data-camps').split(',');
    var ampladesRep = $("#" + div).attr('data-amplades').split(',');
    var captionRep = $("#" + div).attr('data-caption').split(',');
    var campPrimariRep = $("#" + div).attr('data-campPrimari');
    var descrRep = $("#" + div).attr('data-descr');
    var tipusCampRep = $("#" + div).attr('data-tipusCamp').split(',');
    var dadesForanaRep = $("#" + div).attr('data-dadesForana').split(',');
    var campsObligatorisRep = $("#" + div).attr('data-campsObligatoris').split(',');
    var campsCondicioRep = $("#" + div).attr('data-campsCondicio').split(',');
    var valorsCondicioRep = $("#" + div).attr('data-valorsCondicio').split(',');
    var ordenacioRep = $("#" + div).attr('data-ordenacio').split(',');

    if (mode == '1') {
        //és una edició anem a buscar la clau primaria
        var campPrimari = $("#taula" + taula).attr('data-camp-primari');
        var clauPrimaria = $("#titolTaula").attr('data-clau-primaria');
    } else {
        var campPrimari = "";
        var clauPrimaria = "";
    }
    //enviem les dades per ajax al servidor
    var url = "php/gestionaTaulaAuxiliar.php";
    var opcio = '1';
    debugger;

    $.ajax({
        type: "POST",
        url: url,
        data: {"mode": mode, "taula": taula, "camps": camps, "valors": valors, "campPrimari": campPrimari, "clauPrimaria": clauPrimaria, "opcio": opcio},
        //data: ("#form2").serialize,
        success: function (data) {

            var resposta = data;
            if (resposta === '0') {
                //viola alguna clau forana
                alert("Claus duplicades o violació de la integritat de les dades");
            } else {
                //$("#divMantClasses").html(data);
                carregaTaulaAuxiliar(taulaRep, campsRep, ampladesRep, captionRep, div, campPrimariRep, descrRep, tipusCampRep, dadesForanaRep, campsObligatorisRep, campsCondicioRep, valorsCondicioRep, ordenacioRep);

            }
        }

    });

    return false;


}

function esborraItems(taula, descr) {

    var resposta = confirm("Estàs a punt d'esborrar elements de la taula " + descr + " n'estàs segur?");

    var codisEsborrat = [];
    j = 0;

    if (resposta === true) {
        //agafem els codis a esborrar
        var campPrimari = $("#taula" + taula).attr('data-camp-primari');
        var fileres = $(".checkEsborrar");


        for (var i = 0; i < fileres.length; i++) {
            if ($(fileres[i]).prop('checked') === true) {
                //és un element seleccionat
                codisEsborrat[j] = $($($($(fileres[i]).parent()).parent()).children()[1]).text();
                j++;

            }

        }
        var url = "php/gestionaTaulaAuxiliar.php";
        var opcio = '2';
        $.ajax({
            type: "POST",
            url: url,
            data: {"taula": taula, "codisEsborrat": codisEsborrat, "campPrimari": campPrimari, "opcio": opcio},
            //data: ("#form2").serialize,
            success: function (data) {

                //$("#divMantUsuaris").html(data);

                var resposta = data;

                if (resposta === '0') {
                    //viola alguna clau forana
                    alert("Esborra totes les referències abans d'esborrar el registre");
                    for (var i = 0; i < fileres.length; i++) {
                        if ($(fileres[i]).prop('checked') === true) {
                            //és un element seleccionat
                            $(fileres[i]).prop('checked', false);

                        }

                    }
                } else {
                    //esborrem les fileres marcades
                    for (var i = 0; i < fileres.length; i++) {
                        if ($(fileres[i]).prop('checked') === true) {
                            //esborrem la filera
                            $($($(fileres[i]).parent()).parent()).remove();

                        }

                    }
                }

            }

        });
        return false;

    }

}

function construeixDetallDrop(codiDiv, foranaTaula, foranaId, foranaCamp, caption, codiCamp, dadaCamp, div) {
    var url = "php/gestionaTaulaAuxiliar.php";
    var opcio = '3';
    $.ajax({
        type: "POST",
        url: url,
        data: {"foranaTaula": foranaTaula, "foranaId": foranaId, "foranaCamp": foranaCamp, "caption": caption, "codiCamp": codiCamp, "dadaCamp": dadaCamp, "div": div, "opcio": opcio},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#" + codiDiv).html(data);

        }

    });
    return false;
}

function mostraDropReferencia(foranaTaula, element) {

    $("#buttonDropRef" + foranaTaula).html($(element).text() + ' ' + '<span class="caret">');
    $("#buttonDropRef" + foranaTaula).val($(element).attr('data-val'));
}

function comprovaCamps(divDades, divCamps) {
    debugger;
    //recuperem els criteris dels camps
    var campsObligatoris = $("#" + divDades).attr('data-campsObligatoris').split(',');
    var tipusCamp = $("#" + divDades).attr('data-tipusCamp').split(',');



    //recuperem els camps
    var relacioCamps = $(".relCamps");


    var esCorrecte = true;
    for (var i = 0; i < campsObligatoris.length; i++) {
        if (tipusCamp[i + 1] === '0') {
            //és un camp lliure
            //anem a comprovar que sigui si es obligatori
            var valorCamp = $(relacioCamps[i]).val();
            if (campsObligatoris[i] === '1' && valorCamp === '') {
                $(relacioCamps[i]).addClass('alert-danger');
                esCorrecte = false;

            } else {
                $(relacioCamps[i]).removeClass('alert-danger');

            }

        } else if (tipusCamp[i + 1] === '2') {
            //és un combobox
            //anem a buscar el valor

            var valorDrop = $($($(relacioCamps[i]).children()[1]).children()[0]).val();

            if (valorDrop === '') {
                //tots els drops han d'estar plems
                $($($(relacioCamps[i]).children()[1]).children()[0]).addClass('alert-danger');
                esCorrecte = false;
            } else {
                $($($(relacioCamps[i]).children()[1]).children()[0]).removeClass('alert-danger');
            }

        }
    }

    if (esCorrecte === true) {
        $("#desaTaulaAuxiliar").prop('disabled', false);
    } else {
        $("#desaTaulaAuxiliar").prop('disabled', true);
    }

}

function ordenaCerca(element, div) {
    //mirem la columna

    debugger;
    var columna = $($(element).parent()).index()-1;

    //agafem els criteris anterior

    var critOrd = $("#" + div).attr('data-ordenacio').split(',');

    if (critOrd[1] === '0') {
        //abans era ascendent ara descendent
        $("#" + div).attr('data-ordenacio',columna+',1');
    } else {
        //abans era descendent ara ascendent
        $("#" + div).attr('data-ordenacio', columna+',0');
    }


    var taulaRep = $("#" + div).attr('data-taula');
    var campsRep = $("#" + div).attr('data-camps').split(',');
    var ampladesRep = $("#" + div).attr('data-amplades').split(',');
    var captionRep = $("#" + div).attr('data-caption').split(',');
    var campPrimariRep = $("#" + div).attr('data-campPrimari');
    var descrRep = $("#" + div).attr('data-descr');
    var tipusCampRep = $("#" + div).attr('data-tipusCamp').split(',');
    var dadesForanaRep = $("#" + div).attr('data-dadesForana').split(',');
    var campsObligatorisRep = $("#" + div).attr('data-campsObligatoris').split(',');
    var campsCondicioRep = $("#" + div).attr('data-campsCondicio').split(',');
    var valorsCondicioRep = $("#" + div).attr('data-valorsCondicio').split(',');
    var ordenacioRep = $("#" + div).attr('data-ordenacio').split(',');

    carregaTaulaAuxiliar(taulaRep, campsRep, ampladesRep, captionRep, div, campPrimariRep, descrRep, tipusCampRep, dadesForanaRep, campsObligatorisRep, campsCondicioRep, valorsCondicioRep, ordenacioRep);

}