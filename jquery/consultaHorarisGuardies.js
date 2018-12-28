/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function carregaHorariGuardies(div) {
    var url = "php/consultaHorariGuardies.php";


    $.ajax({
        type: "POST",
        url: url,
        data: {},
        //data: ("#form2").serialize,
        success: function (data) {
            debugger;
            $("#" + div).html(data);

        }

    });


    return false;

}