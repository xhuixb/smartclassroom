/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function carregaDadesInicialsConsHoraris() {
    carregaDropGeneric("divProfessorHorari", "select ga17_codi_professor as codi,concat(ga04_cognom1,' ',ga04_cognom2,', ',ga04_nom) as descripcio from ga04_professors,ga17_professors_curs where ga17_codi_curs=(select ga03_codi_curs from ga03_curs where ga03_actual=1) and ga17_codi_professor=ga04_codi_prof order by descripcio", 'Tria professor');

}

function mostradivProfessorHorari(element) {

    $("#butDropdivProfessorHorari").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDropdivProfessorHorari").val($(element).attr('data-val'));

    var codiProf = $("#butDropdivProfessorHorari").val();

    //anem a buscar l'estat del profe
     var url = "php/comprovaProfeEstat.php";


    $.ajax({
        type: "POST",
        url: url,
        data: {"codiProf": codiProf},
        //data: ("#form2").serialize,
        success: function (data) {
            if(data==='0'){
                $("#estatProfe").text('Actiu');
                $("#estatProfe").removeClass('btn-danger');
                $("#estatProfe").addClass('btn-success');
                
            }else{
                $("#estatProfe").text('Inactiu');
                 $("#estatProfe").removeClass('btn-success');
                $("#estatProfe").addClass('btn-danger');
            }

        }

    });

    
    //anem a buscar l'horari
    cercaHorariProfe('horarisConsultaProfes', codiProf);
}

function cercaHorariProfe(div, codiProf) {
    var url = "php/consultaHorariProfe.php";


    $.ajax({
        type: "POST",
        url: url,
        data: {"codiProf": codiProf},
        //data: ("#form2").serialize,
        success: function (data) {
            debugger;
            $("#" + div).html(data);

        }

    });


    return false;


}
