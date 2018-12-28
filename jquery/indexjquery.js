/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function carregaCursos(){
    //mostrem tots els professors del curs actual
    var url = "php/carregaCursos.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            //mostrem els professor
            $("#cursIndex").html(data);

        }

    });
    return false;
    
}