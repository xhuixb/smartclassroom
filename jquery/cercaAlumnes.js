/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function cercaAlumnesIndepCurs() {

    //prenem les dades de cerca del formulari
    var nom = $("#nomCercaAlumne").val();
    var cognom1 = $("#cognom1CercaAlumne").val();
    var cognom2 = $("#cognom2CercaAlumne").val();
    var mail = $("#mailCercaAlumne").val();

    var url = "php/cercaAlumnes.php";

    //les enviem per ajax

    $.ajax({
        type: "POST",
        url: url,
        data: {"nom": nom, "cognom1": cognom1, "cognom2": cognom2, "mail": mail},
        //data: ("#form2").serialize,
        success: function (data) {
            debugger;
            $("#divTaulaCercaAlumnes").html(data);

        }

    });

    return false;

}

async function mostraFitxaAlumne(element) {
    var urlPhp = "php/creaFitxaAlumne.php";

    var alumne = $($(element).parent()).attr('data-codi-alumne');
    var professor = $("#dadesCredencials").attr('data-codi-prof');
    var urlXml="xml/prof"+professor+"al"+alumne+".xml";

    //var urlXml = "xml/fitxaAlumne.xml";

    $.get(urlPhp + "?alumne=" + alumne);
    await sleep(2000);
    //window.open(urlXml, "_self", "toolbar=no,scrollbars=yes,resizable=yes,width=" + screen.width + ",height=" + screen.height);
    var finestra=window.open(urlXml, "_blank");
    finestra.focus();
    
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
