/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var x = 0;
var y = 1;

function carregaDades() {
    document.getElementById('cap1').innerHTML = 'bona nit ximplet';


}

function prova2(x, y) {

    var z = x + y;
    debugger;
    document.getElementById('num3').value = z;


}
function afegeixFilera() {
    //obtenim la taula


    /*var taula = document.getElementById('taula1');
     debugger;
     //afegim una filera
     var filera = taula.insertRow();
     
     var cell1 = filera.insertCell(0);
     var cell2 = filera.insertCell(1);
     
     x++;
     cell1.innerHTML = x;
     cell2.innerHTML = "NEW CELL2";*/

}




function mostraIdioma(sel) {
    debugger;
    //var x = sel.options[sel.selectedIndex].text;
    //var y = document.getElementById('idiomaCombo').value;
    //document.getElementById('pIdioma').innerHTML = y;

}
function prova() {
    debugger;
    document.getElementById('provaLogin').innerHTML = 'ximplet';


}

function esborraDiv(div) {

    document.getElementById(div).innerHTML = "";

}


function carregaComments(element) {


    //esborrem tot el cos de la taula
    $("#commentTableBody tr").remove();

    //anem a buscar el td corresponent al button
    var tdButton = $(element).parent();
    //anem a buscar l'alumne
    var alumneComment = $(tdButton).next();
    //obtenim el nom de l'alumne
    var nomAlumne = $(alumneComment).text();
    //posem el nom de l'alumne al popup modal
    var codiAlumne = $(alumneComment).attr('id');

    debugger;

    document.getElementById('alumneComment').innerHTML = "Alumne: " + nomAlumne;
    document.getElementById('alumneComment').setAttribute("data-codi", codiAlumne);
    
    //posem el comentari general
    $("#commentGeneral").val($(alumneComment).attr('data-coment-general'));
    //posem el pla de llengues
    
    $("#plaLlengues").val($(alumneComment).attr('data-pla-llengues'));
    



    // document.getElementById('alumneComment').setAttribute("data-alumne",alumne);

    //ara obtenim els codis i els noms de les assignatures
    var codisAssig = [];
    var nomsAssig = [];

    var trButton = $(tdButton).parent();
    var tbodyButton = $(trButton).parent();
    var tableButton = $(tbodyButton).parent();


    var capcalera = $($('#' + 'idheader').children()).children();
    var assig1 = '';
    

    for (var cont1 = 2; cont1 < capcalera.length; cont1++) {
        codisAssig[cont1 - 2] = $(capcalera[cont1]).attr('data-value');
        nomsAssig[cont1 - 2] = $(capcalera[cont1]).attr('data-nom');
    }

    //ara obtenim les observacions i si es poden posar comentaris

    var commentsNotes = [];
    var commentsPossibles = [];
    var tdButtonComments = $(tdButton).siblings();

    for (var i = 1; i < tdButtonComments.length; i++) {
        commentsNotes[i - 1] = $(tdButtonComments[i]).attr('data-comentari');
        var nota = $(tdButtonComments[i]).children();
        debugger;
        if ($(nota[0]).val() === '')
            commentsPossibles[i - 1] = true;
        else
            commentsPossibles[i - 1] = false;
    }

    //afegim, primerament, el comentari general i el combo(select del pla de llengües)
    
    
    

    //ara procedim a afegir una filera per cada assignatura amb el comentari corresponent

    var cosTaula = document.getElementById('commentTableBody');

    for (var i = 0; i < codisAssig.length; i++) {

        var filera = document.createElement("tr");

        var cela1 = document.createElement("td");
        var cela2 = document.createElement("td");

        var text1 = document.createTextNode(nomsAssig[i]);
        var textArea = document.createElement("textarea");

        textArea.setAttribute("class", "form-control");
        textArea.setAttribute("rows", 3);
        textArea.setAttribute("id", "comment");
        textArea.setAttribute("data-codi", codisAssig[i]);
        $(textArea).prop("disabled", commentsPossibles[i]);
        textArea.innerHTML = commentsNotes[i];

        cela1.appendChild(text1);
        cela2.appendChild(textArea);

        filera.appendChild(cela1);
        filera.appendChild(cela2);

        cosTaula.appendChild(filera);

    }


}


function isNumber(evt) {

    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;

}