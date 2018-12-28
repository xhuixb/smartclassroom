<?xml version="1.0" encoding="UTF-8"?>

<!--
    Document   : newstylesheet.xsl
    Created on : 11 de noviembre de 2017, 16:41
    Author     : xhuix
    Description:
        Purpose of transformation follows.
-->

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="html"/>

    <!-- TODO customize transformation rules 
         syntax recommendation http://www.w3.org/TR/xslt 
    -->
    <xsl:template match="/">
        <html>
            <head>
                <title>Fitxa d'alumne</title>
                <meta charset="utf-8"/>
                <meta name="viewport" content="width=device-width, initial-scale=1"/>
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
                <link rel="stylesheet" href="../css/fitxaAlumne.css"/>
                <script src="../jquery/fitxaAlumne.js"></script>
              
                       
            </head>
            <body onbeforeunload="esborraFitxa();">
                
                <div class="container-fluid">
                    <div class="page-header">
                     
                            
                       
                        <h1 id="capfitxa" style="text-decoration: underline;">    
                            <xsl:attribute name="data-codi-alumne">
                                <xsl:value-of select="alumne/codi"/>
                            </xsl:attribute>
                            <xsl:attribute name="data-codi-prof">
                                <xsl:value-of select="alumne/profsolici"/>
                            </xsl:attribute>                
                                                                                   
                                                                                                                                                                 
                            FITXA ALUMNE
                        </h1>
                        <br/>
                        <div class="row">
                            <div class="col-sm-3">
                                <h4>
                                    <b>Nom: </b>
                                    <xsl:value-of select="alumne/nom"/>
                                </h4>
                                <h4>
                                    <b>Primer cognom: </b>
                                    <xsl:value-of select="alumne/cognom1"/>
                                </h4>
                                <h4>
                                    <b>Segon cognom: </b>
                                    <xsl:value-of select="alumne/cognom2"/>
                                </h4>
                                <h4>
                                    <b>Mail contacte: </b>
                                    <xsl:value-of select="alumne/mail1"/>
                                </h4>
                                <h4>
                                    <b>Mail contacte: </b>
                                    <xsl:value-of select="alumne/mail2"/>
                                </h4>
                               
                            </div>
                            <div class="col-sm-2">
                                <h4>
                                    <b>Nivell: </b>
                                    <xsl:value-of select="alumne/nivell"/>
                                </h4>
                                <h4>
                                    <b>Grup: </b>
                                    <xsl:value-of select="alumne/grup"/>
                                </h4>
                                <h4>
                                    <b>Tutor: </b>
                                    <xsl:value-of select="alumne/tutor"/>
                                </h4>
                                <h4>
                                    <b>Comunic. actives: </b>
                                    <xsl:value-of select="alumne/comunica"/>
                                </h4>
                           
                                <button type="button" class="btn btn-warning form-control" id="fitxaToPDF" onclick="fitxaToPDF();">
                                    ==> PDF
                                </button>
                             
                            </div>
                            <div class="col-sm-3">
                                
                               
                                <img width="180 px" height="180 px">
                                    <xsl:attribute name="src">
                                        <xsl:value-of select="alumne/imatge"/>
                                    </xsl:attribute>                
                                
                                    
                                </img>
                                
                            </div>
                        </div>   
                    </div>
                </div>
              
             
                <div class="container-fluid">
                    <div>
                        <h3>
                            <u>FALTES D'ORDRE</u>
                        </h3>          
                    </div>   
                </div>
                <div class="container-fluid">
                 
                    <div class="row col-sm-2">
                        <table class="table table-fixed">
                            <thead>
                                <tr bgcolor="#9acd32">                 
                                    <th style="left" class="col-sm-2">Tipus falta</th>    
                                    <th style="left" class="col-sm-1">Quantitat</th>                                                                                  
                                </tr>
                            </thead>
                            <tbody>
                                <xsl:for-each select="alumne/faltaordre">
                                    <tr>                           
                                        <td class="col-sm-1">
                                            <xsl:value-of select="descrfalta"/>
                                        </td>
                                        <td class="col-sm-1">
                                            <xsl:value-of select="numfaltes"/>
                                        </td>
                                    </tr>
                                </xsl:for-each>
                            </tbody>
                        </table>
                        
                    </div>
                </div>
                <div class="container-fluid">
                    <a data-toggle="collapse" data-target="#detallFaltes">Veure detall</a>
                </div>
                <div id="detallFaltes" class="collapse">
                    <div class="container-fluid">
                        <div class="row row col-sm-12">
                        
                            <table class="table table-fixed">
                                <thead>
                                    <tr bgcolor="#9acd32">                 
                                        <th style="left" class="col-sm-2">Professor</th>    
                                        <th style="left" class="col-sm-1">Dia</th>                                               
                                        <th style="left" class="col-sm-1">Hora</th>
                                        <th style="left" class="col-sm-1">Tipus falta</th>
                                        <th style="left" class="col-sm-6">Motiu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <xsl:for-each select="alumne/falta">
                                        <tr>                           
                                            <td class="col-sm-2">
                                                <xsl:value-of select="professor"/>
                                            </td>
                                            <td class="col-sm-1">
                                                <xsl:value-of select="dia"/>
                                            </td>
                                            <td class="col-sm-1">
                                                <xsl:value-of select="hora"/>
                                            </td>
                                            <td class="col-sm-1">
                                                <xsl:value-of select="tipusfalta"/>
                                            </td>
                                            <td class="col-sm-4">
                                                <xsl:value-of select="motiu"/>
                                            </td>
                                        </tr>
                                    </xsl:for-each>
                                </tbody>
                            </table>
                        </div>
                    </div>       
                </div>
                <div class="container-fluid">
                    <div>
                        <h3>
                            <u>ABSÈNCIES I RETARDS</u>
                        </h3>          
                    </div>   
                </div>
                
                <div class="container-fluid">
               
                    <div class="row col-sm-6">
                        <table class="table table-fixed">
                            <thead>
                                <tr bgcolor="#9acd32">                 
                                    <th style="left" class="col-sm-1">Any</th>    
                                    <th style="left" class="col-sm-1">Mes</th>
                                    <th style="left" class="col-sm-1">Total Abs.</th>    
                                    <th style="left" class="col-sm-1">Justificades</th>       
                                    <th style="left" class="col-sm-1">Sense Jutifi</th> 
                                    <th style="left" class="col-sm-1">Retards</th>                          
                                </tr>
                            </thead>
                            <tbody>
                                <xsl:for-each select="alumne/absenciaresum">
                                    <tr>                           
                                        <td class="col-sm-1">
                                            <xsl:value-of select="any"/>
                                        </td>
                                        <td class="col-sm-1">
                                            <xsl:value-of select="mes"/>
                                        </td>
                                        <td class="col-sm-1">
                                            <xsl:value-of select="total"/>
                                        </td>
                                        <td class="col-sm-1 success">
                                            <xsl:value-of select="justifi"/>
                                        </td>
                                        <td class="col-sm-1 danger">
                                            <xsl:value-of select="nojustifi"/>
                                        </td>
                                        <td class="col-sm-1">
                                            <xsl:value-of select="retards"/>
                                        </td>
                                    </tr>
                                </xsl:for-each>
                            </tbody>
                        </table>
                        
                    </div>
                </div>
                
                <div class="container-fluid">
                    <a data-toggle="collapse" data-target="#detallAbsencies">Veure detall</a>
                </div>
                
                <div id="detallAbsencies" class="collapse">
                    <div class="container-fluid">
                        <div class="row col-sm-12">
                    
                            <table class="table table-fixed">
                                <thead>
                                    <tr bgcolor="#9acd32">
                                        <th style="left" class="col-sm-1">Dia</th>                        
                                        <th style="left" class="col-sm-1">Hores</th>                                                   
                               
                                    </tr>
                                </thead>
                                <tbody>
                                    <xsl:for-each select="alumne/absencia"> 
                                        <tr>                         
                                            <td class="col-sm-1">
                                                <xsl:value-of select="dia"/>
                                            </td>
                                             
                                            <xsl:for-each select="hores">
                                                
                                                <xsl:if test="checkabs=1 and justifi=0">
                                                    <td class="col-sm-1 danger">                                               
                                                        <xsl:value-of select="hora"/>
                                                    </td>
                                                </xsl:if>
                                                <xsl:if test="checkabs=1 and justifi=1">
                                                    <td class="col-sm-1 success">                                               
                                                        <xsl:value-of select="hora"/>
                                                    </td>
                                                </xsl:if>
                                                <xsl:if test="retard=1">
                                                    <td class="col-sm-1">                                               
                                                        <xsl:value-of select="hora"/>(R)
                                                    </td>
                                                </xsl:if>
                                                
                                                
                                            </xsl:for-each>  
       
                                        </tr>                                         
                                
                                    </xsl:for-each>
                                </tbody>
                            
                    
                            </table>
                
                
                        </div>
                    </div>
                </div>
            </body>
        </html>
    </xsl:template>

</xsl:stylesheet>
