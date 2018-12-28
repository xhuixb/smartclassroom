/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function carregaDadesCreacio() {


    /*var admin = $("#dadesCredencials").attr('data-admin');
     var profe = $("#dadesCredencials").attr('data-codi-prof');
     
     if (admin == '1') {
     //és administrador
     carregaDropGeneric('profeCreacioHorari', "select ga17_codi_professor as codi,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio from ga04_professors,ga17_professors_curs where ga04_codi_prof=ga17_codi_professor and ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) order by descripcio", 'Tria professor');
     } else {
     carregaDropGeneric('profeCreacioHorari', "select ga17_codi_professor as codi,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio from ga04_professors,ga17_professors_curs where ga04_codi_prof=ga17_codi_professor and ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga17_codi_professor="+profe+" order by descripcio", 'Tria professor');
     }*/

    //carreguem els profes es un dropbox

    carregaDropGeneric('divNivellHorari', 'SELECT distinct(ga35_nivell) as codi, ga06_descripcio_nivell as descripcio FROM ga06_nivell,ga35_curs_nivell_grup where ga35_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga35_nivell=ga06_codi_nivell', 'Tria Nivell');
    carregaDropGeneric('divAulaHorari', 'SELECT ga01_codi_aula as codi,ga01_descripcio_aula as descripcio from ga01_aula', 'Tria Aula');
    carregaDropGeneric('divCarrecHorari', 'SELECT ga27_codi as codi,ga27_descripcio as descripcio from ga27_tipus_carrec', 'Tipus càrrec');
    carregaDropGeneric('divGuardiaHorari', 'SELECT ga36_codi as codi,ga36_descripcio as descripcio from ga36_tipus_guardia', 'Tipus guàrdia');

    //carreguem els profes
    var url = "php/cercaProfesHoraris.php";
    debugger;
    $.ajax({
        type: "POST",
        url: url,
        data: {},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#profeCreacioHorari").html(data);

        }

    });


    return false;


}

function mostradivCarrecHorari(element) {
    $("#butDropdivCarrecHorari").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivCarrecHorari").val($(element).attr('data-val'));

    if ($("#butDropdivCarrecHorari").val() != '') {
        //habilitem desar
        $("#desaHorariButton").prop('disabled', false);

    } else {
        $("#desaHorariButton").prop('disabled', true);
    }

}

function mostradivGuardiaHorari(element) {
    $("#butDropdivGuardiaHorari").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivGuardiaHorari").val($(element).attr('data-val'));


}


function mostraprofeCreacioHorari(element) {
    $("#butDropprofeCreacioHorari").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropprofeCreacioHorari").val($(element).attr('data-val'));

    //esborrem horari anterior
    $("#divtaulaHorarisCreacio").html('');

    if ($("#butDropprofeCreacioHorari").val() == '') {

        $("#cercaHorarisProfeButton").attr('disabled', true);
    } else {
        $("#cercaHorarisProfeButton").attr('disabled', false);
    }

}

function mostradivNivellHorari(element) {
    $("#butDropdivNivellHorari").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivNivellHorari").val($(element).attr('data-val'));

    var nivell = $(element).attr('data-val');
    /*if (nivell != '') {
     //carreguem els grups
     
     cercaGrupsNivell('divGrupHorari', nivell);
     cercaAssigNivell('divAssignaturaHorari', nivell);
     
     }*/

    if (nivell != '') {

        cercaAssigNivell('divAssignaturaHorari', nivell);
        var profe = $("#profeEditHorari").attr('data-codiprof');

        //anem a buscar els grups
        $('#butDropdivGrupHorari').prop('disabled', false);
        //carregaDropGrupsGrupsProfes('grupDropdown','Tria grup')
        //anem a buscar els grups d'aquest nivell
        var url = "php/cercaGrupsProfesHoraris.php";
        debugger;
        $.ajax({
            type: "POST",
            url: url,
            data: {"nivell": nivell, "profe": profe},
            //data: ("#form2").serialize,
            success: function (data) {
                $("#divGrupHorari").html(data);

            }

        });


        return false;


    }



}

function mostradivGrupHorari(element) {
    $("#butDropdivGrupHorari").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivGrupHorari").val($(element).attr('data-val'));

    var tipusgrup = $(element).attr('data-tipusgrup');

    $("#butDropdivGrupHorari").attr('data-tipusgrup', tipusgrup);

}

function mostradivAssignaturaHorari(element) {
    $("#butDropdivAssignaturaHorari").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivAssignaturaHorari").val($(element).attr('data-val'));

    if ($("#butDropdivNivellHorari").val() != '') {
        //habilitem desar
        $("#desaHorariButton").prop('disabled', false);

    } else {
        $("#desaHorariButton").prop('disabled', true);
    }

}

function mostradivAulaHorari(element) {
    $("#butDropdivAulaHorari").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivAulaHorari").val($(element).attr('data-val'));

}



function cercaHorarisProfe() {
    var profe = $("#butDropprofeCreacioHorari").val();

    var url = "php/cercaHorariProfe.php";

    $.ajax({
        type: "POST",
        url: url,
        data: {"profe": profe},
        //data: ("#form2").serialize,
        success: function (data) {
            debugger;
            $("#divtaulaHorarisCreacio").html(data);

        }

    });


    return false;

}

function editaHorarisProfe(element) {
    //posa les dades de capçalera del modal
    var codiProf = $("#butDropprofeCreacioHorari").val();
    var nomProf = $("#butDropprofeCreacioHorari").text();

    debugger;

    $("#profeEditHorari").text(nomProf);
    $("#profeEditHorari").attr('data-codiprof', codiProf);

    //la filera és el besavi
    var filera = $($($(element).parent()).parent()).parent();

    var indexFilera = $(filera).index();

    //posem l'index de la filera com atribut del nom del professor
    $("#profeEditHorari").attr('data-indexfilera', indexFilera);


    var cella = $($(element).parent()).parent();

    var index = $(cella).index();

    switch (index)
    {
        case 1:
            var dia = 'DILLUNS';
            break;
        case 2:
            var dia = 'DIMARTS';
            break;
        case 3:
            var dia = 'DIMECRES';
            break;
        case 4:
            var dia = 'DIJOUS';
            break;
        case 5:
            var dia = 'DIVENDRES';
            break;

    }


    var horaInterval = $($(filera).children()[0]).text();
    var horaInici = $($(filera).children()[0]).attr('data-horainici');

    $("#horaEditHorari").text(horaInterval);
    $("#horaEditHorari").attr('data-horainici', horaInici);

    $("#diaSetmanaEditHorari").text(dia);
    $("#diaSetmanaEditHorari").attr('data-codidia', index);


}

function desaHorari() {
    //agagem le dades
    //professor


    var codiProf = $("#profeEditHorari").attr('data-codiprof');

    //codi del dia de la setmana
    var codiDia = $("#diaSetmanaEditHorari").attr('data-codidia');

    //hora inici

    var horaInici = $("#horaEditHorari").attr('data-horainici');

    //nivell
    var nivell = $("#butDropdivNivellHorari").val();

    //grup
    var grup = $("#butDropdivGrupHorari").val();

    //tipus grup
    var tipusGrup = $("#butDropdivGrupHorari").attr('data-tipusgrup');

    //assignatura

    var assignatura = $("#butDropdivAssignaturaHorari").val();

    //aula
    var aula = $("#butDropdivAulaHorari").val();

    //es lectiva
    if ($("#esLectiva").prop('checked') == true) {
        var horaLectiva = '1';
        var tipusHora = '';
        var tipusGuardia = '';

    } else {
        var horaLectiva = '0';
        var esGuardiaCarrec = $('#esGuardiaCarrec input:radio:checked').val();
        if (esGuardiaCarrec == '0') {
            //es guardia
            var tipusHora = '';
            var tipusGuardia = $("#butDropdivGuardiaHorari").val();
        } else {
            var tipusHora = $("#butDropdivCarrecHorari").val();
        }

    }


    var url = "php/desaHorari.php";

    $.ajax({
        type: "POST",
        url: url,
        data: {"codiProf": codiProf, "codiDia": codiDia, "horaInici": horaInici, "nivell": nivell, "grup": grup, "tipusGrup": tipusGrup, "assignatura": assignatura, "aula": aula, "horaLectiva": horaLectiva, "tipusHora": tipusHora, "tipusGuardia": tipusGuardia},
        //data: ("#form2").serialize,
        success: function (data) {
            //   $("#profeCreacioHorari").html(data);

            //recuperem filera i columna
            //filera
            var filera = $("#profeEditHorari").attr('data-indexfilera');

            //columna
            var columna = $("#diaSetmanaEditHorari").attr('data-codidia');

            var contingutHorari = '';

            if (horaLectiva == '1') {
                var estil = "1";
                contingutHorari += "HORA LECTIVA";
                contingutHorari += "\n";
                contingutHorari += "Nivell: ";
                contingutHorari += $("#butDropdivNivellHorari").text();
                contingutHorari += "\n";
                contingutHorari += "Grup: ";
                if (grup != '') {
                    contingutHorari += $("#butDropdivGrupHorari").text();
                }
                contingutHorari += "\n";
                contingutHorari += "Assignatura: ";
                contingutHorari += $("#butDropdivAssignaturaHorari").text();
                contingutHorari += "\n";
                contingutHorari += "Aula: ";

                if (aula != '') {
                    contingutHorari += $("#butDropdivAulaHorari").text();
                }


            } else {
                //no lectiva
                var estil = "2";
                if (tipusHora == '') {
                    //guàrdia
                    contingutHorari += "GUÀRDIA";
                    contingutHorari += "\n";
                    contingutHorari += $("#butDropdivGuardiaHorari").text();

                } else {
                    //Càrrec
                    var estil = "3";
                    contingutHorari += "REUNIÓ/CÀRREC";
                    contingutHorari += "\n";
                    contingutHorari += $("#butDropdivCarrecHorari").text();

                }

            }
            //hi posem les dades
            $("#ta-" + filera + "-" + columna).val(contingutHorari);
            $($("#ta-" + filera + "-" + columna).parent()).attr('data-estil', estil);

            netejaCampsModal();

        }

    });


    return false;


}


function netejaCampsModal() {
    //esborrem les dades que hi pugui haver als combos
    $("#butDropdivNivellHorari").html('Tria Nivell' + ' ' + '<span class="caret">');
    $("#butDropdivNivellHorari").val('');

    $('#butDropdivGrupHorari').html('Tria Grup' + ' ' + '<span class="caret">');
    $('#butDropdivGrupHorari').val('');

    $('#butDropdivGrupHorari').prop('disabled', true);



    $("#butDropdivAssignaturaHorari").html('Tria Assignatura' + ' ' + '<span class="caret">');
    $("#butDropdivAssignaturaHorari").val('');

    $("#butDropdivAssignaturaHorari").prop('disabled', true);

    $("#butDropdivAulaHorari").html('Tria Aula' + ' ' + '<span class="caret">');
    $("#butDropdivAulaHorari").val('');



    $(".nolectiu").prop('hidden', false);
    //posem la situació inicial dels options
    $("#carrecOption").prop('checked', true);
    $("#guardiaOption").prop('checked', false);



    $("#desaHorariButton").prop('disabled', true);

    $("#esLectiva").prop('checked', false);

    $("#divdivCarrecHorari").prop('hidden', false);
    $("#butDropdivCarrecHorari").html('Tipus càrrec' + ' ' + '<span class="caret">');
    $("butDropdivCarrecHorari").val('');

    $("#butDropdivGuardiaHorari").html('Tipus guàrdia' + ' ' + '<span class="caret">');
    $("butDropdivGuardiaHorari").val('');

    $("#dadesHoraLectiva").removeClass('collapse in');
    $("#dadesHoraLectiva").addClass('collapse');

}

function esborraDadesLectiva() {

    debugger;
    if ($("#esLectiva").prop('checked') == false) {
        //esborrem les dades que hi pugui haver als combos
        $("#butDropdivNivellHorari").html('Tria Nivell' + ' ' + '<span class="caret">');
        $("#butDropdivNivellHorari").val('');

        $('#butDropdivGrupHorari').html('Tria Grup' + ' ' + '<span class="caret">');
        $('#butDropdivGrupHorari').val('');

        $('#butDropdivGrupHorari').prop('disabled', true);



        $("#butDropdivAssignaturaHorari").html('Tria Assignatura' + ' ' + '<span class="caret">');
        $("#butDropdivAssignaturaHorari").val('');

        $("#butDropdivAssignaturaHorari").prop('disabled', true);

        $("#butDropdivAulaHorari").html('Tria Aula' + ' ' + '<span class="caret">');
        $("#butDropdivAulaHorari").val('');

        $(".nolectiu").prop('hidden', false);

        if ($("#butDropdivCarrecHorari").val() != '') {
            $("#desaHorariButton").prop('disabled', false);

        } else {
            $("#desaHorariButton").prop('disabled', true);
        }


        //posem la situació inicial dels options
        $("#carrecOption").prop('checked', true);
        $("#guardiaOption").prop('checked', false);






    } else {

        $(".nolectiu").prop('hidden', true);
        $("#desaHorariButton").prop('disabled', true);
    }

}

function amagaTipusHora() {
    var esGuardiaCarrec = $('#esGuardiaCarrec input:radio:checked').val();
    if (esGuardiaCarrec == '0') {
        $("#divdivCarrecHorari").prop('hidden', true);
        $("#desaHorariButton").prop('disabled', false);
    } else {
        $("#divdivCarrecHorari").prop('hidden', false);
        if ($("#butDropdivCarrecHorari").val() != '') {
            //habilitem desar
            $("#desaHorariButton").prop('disabled', false);

        } else {
            $("#desaHorariButton").prop('disabled', true);
        }
    }

}

function esborraHorarisProfe(element) {


    var confirmacio = confirm("n'estàs segur que vols esborrar aquest element de l'horari?");

    if (confirmacio == true) {
        //agafem les dades necessàries per a fer la baixa
        //profe
        var profe = $("#butDropprofeCreacioHorari").val();
        //dia de la setmana
        var cella = $($(element).parent()).parent();

        var dia = $(cella).index();

        //hora
        var hora = $($($(cella).parent()).children()[0]).attr('data-horainici');

        var url = "php/esborraHorariProfe.php";

        $.ajax({
            type: "POST",
            url: url,
            data: {"profe": profe, "dia": dia, "hora": hora},
            //data: ("#form2").serialize,
            success: function (data) {
                $($(cella).children()[0]).val('');
                $(cella).attr('data-estil', '0');
            }

        });


        return false;

    }
}