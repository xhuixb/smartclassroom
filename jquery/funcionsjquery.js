/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function () {

    $("#enviar").click(function () {

        var url = "php/utils1.php";

        var titol = document.getElementById('titol').value;
        var autor = document.getElementById('autor').value;
        var editorial = document.getElementById('editorial').value;
        var idioma = document.getElementById('idiomaCombo').value;
        var disponibilitat = $('#disponibilitat input:radio:checked').val();
        var ordenacio = $('#ordenacio input:radio:checked').val();
        //document.getElementById('pProva').innerHTML=disponibilitat;

        $.ajax({
            type: "POST",
            url: url,
            data: {"titol": titol, "autor": autor, "editorial": editorial, "idioma": idioma, "disponibilitat": disponibilitat, "ordenacio": ordenacio},
            //data: ("#form2").serialize,
            success: function (data) {
                $("#taulaLlibres").html(data);

            }

        });


        return false;
    });
});




function  carregaCombosLlibres() {
    var url = "php/carregaIdiomes.php";
    debugger;
    $.ajax({
        type: "POST",
        url: url,
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divIdioma").html(data);

        }

    });


    return false;

}

function  carregaComboNivell() {
    var url = "php/carregaComboNivell.php";
    debugger;
    $.ajax({
        type: "POST",
        url: url,
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divNivell").html(data);

        }

    });


    return false;

}

function  carregaComboGrup() {
    var url = "php/carregaComboGrup.php";
    debugger;
    $.ajax({
        type: "POST",
        url: url,
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divGrup").html(data);

        }

    });


    return false;

}

function  carregaComboGeneric(div, query) {
    var url = "php/carregaComboGeneric.php";
    debugger;
    $.ajax({
        type: "POST",
        url: url,
        data: {"div": div, "query": query},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#" + div).html(data);

        }

    });


    return false;

}


//busquem les notes d'un nivell i un grup

$(document).ready(function () {

    $("#enviarAvaluacio").click(function () {

        esborraDiv(document.getElementById('divisioAssignatures').id);

        esborraDiv(document.getElementById('divisioNotes').id);

        //es carregaran les assignatures

        var url = "php/carregaAssignatures.php";

        //aconseguim les dades que hem de passar
        var nivell = document.getElementById('iddivNivell').value;
        var grup = document.getElementById('iddivGrup').value;


        //passem les dades al script 

        $.ajax({
            type: "POST",
            url: url,
            data: {"nivell": nivell, "grup": grup},
            //data: ("#form2").serialize,
            success: function (data) {
                $("#divisioAssignatures").html(data);

            }

        });

        //return false;

        //es carregaran les notes
        var url = "php/carregaNotes.php";

        //aconseguim les dades que hem de passar
        var nivell = document.getElementById('iddivNivell').value;
        var grup = document.getElementById('iddivGrup').value;


        //passem les dades al script 

        $.ajax({
            type: "POST",
            url: url,
            data: {"nivell": nivell, "grup": grup},
            //data: ("#form2").serialize,
            success: function (data) {
                $("#divisioNotes").html(data);
                $("#taula2").tableHeadFixer();

            }

        });
        debugger;

        // document.getElementById('pprova').innerHTML = $("#taula2").data("notes");


        return false;

        //obtenim les notes que s'han carregat



    });
});

//login

$(document).ready(function () {

    $("#buttonLogin").click(function () {

        var url = "php/login.php";

        var usuari = $('#inputUsuari').val();
        var password = $('#inputPassword').val();

        debugger;
        $.ajax({
            type: "POST",
            url: url,
            data: {"usuari": usuari, "password": password},
            //data: ("#form2").serialize,
            success: function (data) {
                $("#noUsuari").html(data);
                if (data == 'No') {
                    debugger;
                    alert("dades errònies o usuari desactivat");

                } else {
                    // $("#remember").html(data);
                    window.open("main.html", "_self");
                }
            }

        });

        return false;

    });
});

function logout() {

    debugger;
    var url = "php/logout.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {},
        //data: ("#form2").serialize,
        success: function (data) {
            window.open("index.html", "_self");
        }

    });

    return false;


}


function  mostrausuari() {
    var url = "php/mostrausuari.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#pprova").html(data);
        }

    });

    return false;

}




$(document).ready(function () {

    $("#desaAvaluacio").click(function () {

        var url = "php/desaNotes.php";
        var nivell = document.getElementById('iddivNivell').value;
        var grup = document.getElementById('iddivGrup').value;

        debugger;
        //aconseguim les dades de la graella
        var notes = [];
        var notesInicials = [];
        var comentaris = [];
        var comentarisInicials = [];
        var comentarisGenerals = [];
        var comentarisGeneralsInicials = [];
        var plaLlengua = [];
        var plaLlenguaInicials = [];
        var contador = 0;
        var table = document.getElementById('taula2');
        for (var r = 0, n = table.rows.length; r < n; r++) {
            notes[r] = [];
            notesInicials[r] = [];
            comentaris[r] = [];
            comentarisInicials[r] = [];

            for (var c = 1, m = table.rows[r].cells.length; c < m; c++) {

                if (r === 0) {
                    //primera filera assignatures
                    if (c === 1) {
                        //un valor qualsevol
                        notes[r][c - 1] = 0;
                        notesInicials[r][c - 1] = 0;
                        comentaris[r][c - 1] = 0;
                        comentarisInicials[r][c - 1] = 0;
                    } else {

                        //el codi de les assignatures
                        notes[r][c - 1] = $("#tr" + (c - 2)).attr('data-value');
                        notesInicials[r][c - 1] = $("#tr" + (c - 2)).attr('data-value');
                        //var motiuJustifi = $("#justifiAbsenciaVisualitza").val().replace(/"/g, "&quot;");

                    }
                } else {
                    //les fileres dels alumnes
                    if (c === 1) {
                        //el codi de l'alumne
                        notes[r][c - 1] = $("#al" + (r - 1)).attr('data-codi');
                        notesInicials[r][c - 1] = $("#al" + (r - 1)).attr('data-codi');

                        comentaris[r][c - 1] = $("#al" + (r - 1)).attr('data-codi');
                        comentarisInicials[r][c - 1] = $("#al" + (r - 1)).attr('data-codi');
                        //comentaris generals i pla de llengues
                        comentarisGenerals[r - 1] = $("#al" + (r - 1)).attr('data-coment-general').replace(/"/g, "&quot;");
                        plaLlengua[r - 1] = $("#al" + (r - 1)).attr('data-pla-llengues').replace(/"/g, "&quot;");
                        comentarisGeneralsInicials[r - 1] = $("#al" + (r - 1)).attr('data-coment-general-vell').replace(/"/g, "&quot;");
                        plaLlenguaInicials[r - 1] = $("#al" + (r - 1)).attr('data-pla-llengues-vell').replace(/"/g, "&quot;");

                    } else {
                        //les notes dels alumnes

                        notes[r][c - 1] = table.rows[r].cells[c].firstChild.value;
                        notesInicials[r][c - 1] = $(table.rows[r].cells[c]).attr('data-nota');
                        var prova = "#nota" + contador;
                        var notaItem = document.getElementById(prova);
                        //var motiuJustifi = $("#justifiAbsenciaVisualitza").val().replace(/"/g, "&quot;");
                        comentaris[r][c - 1] = $(prova).attr("data-comentari").replace(/"/g, "&quot;");
                        comentarisInicials[r][c - 1] = $(prova).attr("data-comentari-vell").replace(/"/g, "&quot;");
                        contador++;
                    }
                }
            }
        }


        debugger;
        $.ajax({
            type: "POST",
            url: url,
            data: {"nivell": nivell, "grup": grup, "notes": notes, "comentaris": comentaris, "comentarisGenerals": comentarisGenerals, "plaLlengua": plaLlengua,
                "notesInicials": notesInicials, "comentarisInicials": comentarisInicials, "comentarisGeneralsInicials": comentarisGeneralsInicials, "plaLlenguaInicials": plaLlenguaInicials},
            //data: ("#form2").serialize,
            success: function (data) {
                $("#divisioNotes").html(data);
            }

        });


        return false;


    });
});


//actualitzem els comentariz
$(document).ready(function () {

    $("#desaButton").click(function () {
        //obtenim l'id de l'alumne per poder saber la filera

        var idAlumne = $('#alumneComment').attr("data-codi");

        //obtenim el comentari general
        var comentariGeneral = $("#commentGeneral").val();

        //obtenim el pla de llengues
        var plaLlengues = $("#plaLlengues").val();

        //obtenim els comentaris
        var comentarisFileres = $('#commentTableBody').children();
        var comentarisText = [];
        debugger;
        for (var i = 0; i < comentarisFileres.length; i++) {
            var comentarisCelles = $(comentarisFileres[i]).children();
            var comentarisCellesText = $(comentarisCelles[1]).children();

            comentarisText[i] = $(comentarisCellesText[0]).val();


        }

        //localitzem la filera a modificar
        var alumneAModificar = $('#' + idAlumne);


        //desem elcomentari general
        $(alumneAModificar).attr('data-coment-general', comentariGeneral);

        //desem el pla de llengües
        $(alumneAModificar).attr('data-pla-llengues', plaLlengues);


        //obtenim la resta de celles de la filera;
        var cellesComentaris = $(alumneAModificar).siblings();

        for (var i = 1; i < cellesComentaris.length; i++) {
            //modifiquem el comentaris
            $(cellesComentaris[i]).attr('data-comentari', comentarisText[i - 1]);

        }






    });
});

function controlaCredencials(div) {

    debugger;
    var url = "php/controlaCredencials.php";
    var urlRef = window.location.href;
    var pos = urlRef.lastIndexOf("/");
    urlRef = urlRef.substr(pos + 1);

    $.ajax({
        type: "POST",
        url: url,
        data: {"urlRef": urlRef},
        //data: ("#form2").serialize,
        success: function (data) {
            debugger;
            if (data === "forbidden") {
                alert("No tens credencials per a accedir a aquest lloc");
                window.open("index.html", "_self");
            } else {
                //mostrem professor i curs
                $("#" + div).html(data);


            }
        }

    });

    return false;

}




function exportToExcel() {

    debugger;
    var url = "php/excelexport/exportExcel1.php";
    // var url2 = "php/provaMail3.php";
    var nivell = $('#iddivNivell option:selected').val();
    var grup = $('#iddivGrup option:selected').val();
    var nivellText = $('#iddivNivell option:selected').text();
    var grupText = $('#iddivGrup option:selected').text();

    window.open(url + "?nivell=" + nivell + "&grup=" + grup + "&nivellText=" + nivellText + "&grupText=" + grupText, '_parent');


    return false;


}

