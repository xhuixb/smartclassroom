/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function esborraFitxa() {

    debugger;
    var alumne = $("#capfitxa").attr('data-codi-alumne');
    var professor = $("#capfitxa").attr('data-codi-prof');

    var fitxer = "../xml/prof" + professor + "al" + alumne + ".xml";

    var urlFitxa = "../php/esborraFitxaAlumne.php";

    $("#capfitxa").text(urlFitxa + '-' + fitxer);
    $.get(urlFitxa + "?fitxer=" + fitxer);



    //await sleep(2000);

}


/*window.addEventListener("beforeunload", function (e) {
    debugger;

    var alumne = $("#capfitxa").attr('data-codi-alumne');
    var professor = $("#capfitxa").attr('data-codi-prof');

    var fitxer = "../xml/prof" + professor + "al" + alumne + ".xml";

    var urlFitxa = "../php/esborraFitxaAlumne.php";

    $("#capfitxa").text(urlFitxa + '-' + fitxer);
    $.get(urlFitxa + "?fitxer=" + fitxer);

    (e || window.event).returnValue = null;
    return null;
});


function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}*/

function fitxaToPDF(){
    //mostrem el pdf de la fitxa
    var codiAlumne=$("#capfitxa").attr('data-codi-alumne');
    url='../php/reports/tcpdf3.php';
    
    window.open(url+"?codiAlumne="+codiAlumne);
    
}