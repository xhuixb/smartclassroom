<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


echo '<ul>';
//obrim el fitxer on hi ha la configuració dels permisos
$xml = simplexml_load_file("../xml/configuracio/mainMenu.xml") or die("No es pot crear l'objecte");
foreach ($xml->children() as $menuOption) {
    if ($menuOption['referencia'] != "") {
        $butoEdicio = '<button style="width:40px;border-radius: 8px;" data-toggle="modal" data-target="#editaPermisos" onclick="editaPermisos(this);"><span class="glyphicon glyphicon-pencil"></span></button>';
    } else {
        $butoEdicio = '';
    }
    echo '<li data-nivellmenu="0" data-referencia="' . $menuOption['referencia'] . '">' . $menuOption['caption'] . $butoEdicio;
    $cont1 = 0;
    $teFills = false;
    foreach ($menuOption->children() as $menuItem) {
       
        if ($cont1 == 0) {
            echo '<ul>';
            $teFills = true;
        }
        $cont1++;
        if ($menuItem['referencia'] != "") {
            $butoEdicio = '<button style="width:40px;border-radius: 8px;" data-toggle="modal" data-target="#editaPermisos" onclick="editaPermisos(this);"><span class="glyphicon glyphicon-pencil"></span></button>';
        } else {
            $butoEdicio = '';
        }
        echo '<li data-nivellmenu="1" data-referencia="' . $menuItem['referencia'] . '">' . $menuItem['caption'] . $butoEdicio;
        $cont2 = 0;
        $teNets = false;
        foreach ($menuItem->children() as $subMenuItem) {
            if ($cont2 == 0) {
                echo '<ul>';
                $teNets = true;
            }
            $cont2++;
            if ($subMenuItem['referencia'] != "") {
                $butoEdicio = '<button style="width:40px;border-radius: 8px;" data-toggle="modal" data-target="#editaPermisos" onclick="editaPermisos(this);"><span class="glyphicon glyphicon-pencil"></span></button>';
            } else {
                $butoEdicio = '';
            }
            echo '<li data-nivellmenu="2" data-referencia="' . $subMenuItem['referencia'] . '">' . $subMenuItem['caption'] . $butoEdicio . '</li>';
        }

        if ($teNets == true) {
            echo '</ul>';
        }
        echo '</li>';
    }
    if ($teFills == true) {
        echo '</ul>';
    }
    echo '</li>';
}

echo '</ul>';
?>

<script>
    $(document).ready(function () {
        $("#divPermisosTree").jstree({
            "core": {
                "multiple": false
            }
        });

    });

   /* $("#divPermisosTree").on("activate_node.jstree",
            function (evt, data) {
                var element = $("#" + data.node.id)[0];
                var atribut = $(element).attr('data-referencia');
                var sURL =$(element).attr('data-referencia');
                //var sURL = element.attributes["data-referencia"].value;
                
                alert(atribut);
            }
    );*/
</script>

