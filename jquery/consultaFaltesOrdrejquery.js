/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function () {
    $('#dataIniciCerca').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });
});

$(document).ready(function () {
    $('#dataFiCerca').datepicker({
        uiLibrary: 'bootstrap',
        dateFormat: 'dd/mm/yy'
    });
});

function cercaFaltesOrdre() {
    //agafo les dates
    var dataIniciCerca = $('#dataIniciCerca').val();
    var dataFiCerca = $('#dataFiCerca').val();

    debugger;
    //giro les dates
    if (dataIniciCerca != '') {
        dataIniciCerca = dataIniciCerca.substring(6) + '-' + dataIniciCerca.substring(3, 5) + '-' + dataIniciCerca.substring(0, 2);
    }
    if (dataFiCerca != '') {
        dataFiCerca = dataFiCerca.substring(6) + '-' + dataFiCerca.substring(3, 5) + '-' + dataFiCerca.substring(0, 2);
    }
    //faig la cerca per ajax

    $("body").css("cursor", "progress");

    var url = "php/consultaFaltesOrdre.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {"dataIniciCerca": dataIniciCerca, "dataFiCerca": dataFiCerca},
        success: function (data) {
            //rebem les dades
            $("#divTaulaFaltesOrdre").html(data);
            $("body").css("cursor", "default");

        }

    });
    return false;


}