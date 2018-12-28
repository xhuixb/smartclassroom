/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function cercaProfes() {
    var profInicial = $("#profInicial").val();
    var profFinal = $("#profFinal").val();
    var urlPhp = "reports/sample2.php";
    
   // var parametres="min-max";
   // var valors=profInicial+"-"+profFinal;
    
   
    debugger;

    
    window.open(urlPhp+"?" + new Date().getTime());




}