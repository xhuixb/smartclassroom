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
                <title>Incidències diàries</title>
                <meta charset="utf-8"/>
                <meta name="viewport" content="width=device-width, initial-scale=1"/>
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
              
                       
            </head>
            <body>
                <div class="container-fluid">
                    <div class="page-header">
                        <h1>INCIDÈNCIES DIÀRIES</h1>
                        <h3>Data: <xsl:value-of select="dades/data"/></h3>
                        <h3>Tutor: <xsl:value-of select="dades/tutor"/></h3>
                    </div>   
                </div>
              
              
                <div class="container-fluid">
                    <div class="page-header">
                        <h3>FALTES D'ORDRE</h3>          
                    </div>   
                </div>
                
                
                <div class="container-fluid">
                    <div class="row">
                        
                        <table class="table table-fixed">
                            <thead>
                                <tr bgcolor="#9acd32">
                                    <th style="left" class="col-sm-2">Alumne </th>                            
                                    <th style="left" class="col-sm-1">Nivell</th>
                                    <th style="left" class="col-sm-1">Grup</th>                        
                                    <th style="left" class="col-sm-2">Professor</th>
                                    <th style="left" class="col-sm-1">Dia</th>                                                   
                                    <th style="left" class="col-sm-1">Hora</th>
                                    <th style="left" class="col-sm-1">Tipus falta</th>
                                    <th style="left" class="col-sm-3">Motiu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <xsl:for-each select="dades/falta">
                                    <tr>                           
                                        <td class="col-sm-2">
                                            <xsl:value-of select="alumne"/>
                                        </td>
                                        <td class="col-sm-1">
                                            <xsl:value-of select="nivell"/>
                                        </td>
                                        <td class="col-sm-1">
                                            <xsl:value-of select="grup"/>
                                        </td>
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
                <div class="container-fluid">
                    <div class="page-header">
                        <h3>ABSÈNCIES</h3>          
                    </div>   
                </div>
                <div id="divTaulaInciDireccio2" class="container-fluid">
                    <div class="row">
                    
                        <table class="table table-fixed">
                            <thead>
                                <tr bgcolor="#9acd32">
                                    <th style="left" class="col-sm-2">Alumne </th>                            
                                    <th style="left" class="col-sm-1">Nivell</th>
                                    <th style="left" class="col-sm-1">Grup</th>                        
                                    <th style="left" class="col-sm-1">Hores</th>                                                   
                               
                                </tr>
                            </thead>
                            <tbody>
                                <xsl:for-each select="dades/absencia"> 
                                    <tr>                         
                                 
                                        <td class="col-sm-2">
                                            <xsl:value-of select="alumne"/>
                                        </td>
                                        <td class="col-sm-1">
                                            <xsl:value-of select="nivell"/>
                                        </td>
                                        <td class="col-sm-1">
                                            <xsl:value-of select="grup"/>
                                        </td>
                                             
                                        <xsl:for-each select="hores">
                                            <td class="col-sm-1">                                               
                                                <xsl:value-of select="hora"/>
                                            </td>
                                        </xsl:for-each>  
       
                                    </tr>                                         
                                
                                </xsl:for-each>
                            </tbody>
                            
                    
                        </table>
                
                
                    </div>
                </div>
                
            </body>
        </html>
    </xsl:template>

</xsl:stylesheet>
