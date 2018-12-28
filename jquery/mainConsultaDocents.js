/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function cercaDocents() {

    //obtenim el nom del docent
    var nomDocent = $("#nomCercaDocent").val();
    var url = "php/cercaDocents.php";

    //obgenim el check de tutor
    if ($("#cercaTutor").prop('checked') == true) {
        var checkTutors = 1;

    } else {
        var checkTutors = 0;
    }



    $.ajax({
        type: "POST",
        url: url,
        data: {"nomDocent": nomDocent, "checkTutors": checkTutors},
        //data: ("#form2").serialize,
        success: function (data) {
            $("#divTaulaCercaDocents").html(data);

            if (checkTutors == 1) {
                $("#divTaulaCercaDocents").addClass("col-sm-10");
                $("#divTaulaCercaDocents").removeClass("col-sm-8");
            } else {
                $("#divTaulaCercaDocents").addClass("col-sm-8");
                $("#divTaulaCercaDocents").removeClass("col-sm-10");

            }
        }

    });

}


function netejaCamp() {
    $("#nomCercaDocent").val("");

}

function exportaPdfTutors() {

    var urlTutors = "php/reports/tcpdf1.php";
    var finestra = window.open(urlTutors, "_blank");
    finestra.focus();


}