/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function carregaAlarma(){
    var url = "php/carregaAlarma.php";
        $.ajax({
            type: "POST",
            url: url,
            data: {},
            //data: ("#form2").serialize,
            success: function (data) {
                $("#alarmesFaltesDiv").html(data);
               
            }

        });
        return false;
    
    
}

function carregaDadesInicialsAlarmes(){
    
    carregaDropGeneric('tipusFaltaAlarma', 'SELECT ga22_codi_falta as codi, ga22_nom_falta as descripcio FROM ga22_tipus_falta', 'Tipus Falta');
  
}

function mostratipusFaltaAlarma(element) {
    $("#butDroptipusFaltaAlarma").html($(element).text() + ' ' + '<span class="caret">');
    $("#butDroptipusFaltaAlarma").val($(element).attr('data-val'));

}

function cercaAlarmesTipus(){
    var tipusFalta=$("#butDroptipusFaltaAlarma").val();
    
    debugger;
    
    var url = "php/carregaAlarma.php";
        $.ajax({
            type: "POST",
            url: url,
            data: {"tipusFalta":tipusFalta},
            //data: ("#form2").serialize,
            success: function (data) {
                $("#alarmesFaltesDiv").html(data);
               
            }

        });
        return false;
    
    
}