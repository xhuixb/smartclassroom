<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();

echo '<input type="text" class="form-control" id="cursOrigen" data-codi="'.$_SESSION['curs_actual'].'" readonly value="'.$_SESSION['nom_curs_actual'].'">';